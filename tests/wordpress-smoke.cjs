const { test, expect } = require( '@playwright/test' );

const site = process.env.EZD_SITE_URL || 'http://127.0.0.1:38765';
const publicDoc = process.env.EZD_PUBLIC_DOC_URL;

test.use( { launchOptions: { executablePath: process.env.CHROME_PATH } } );

test( 'packaged WordPress plugin serves settings, Builder, and public assets', async ( { page } ) => {
	await login( page );

	await expectStylesheet( page, '/wp-admin/admin.php?page=eazydocs-settings', /\/build\/styles\/admin\/admin-settings\.css/ );
	await expectStylesheet( page, '/wp-admin/admin.php?page=eazydocs-builder', /\/build\/styles\/admin\/docs-builder\.css/ );
	await expect( page.getByRole( 'tab', { name: /Fixture Alpha/ } ) ).toBeVisible();

	const publicUrl = new URL( publicDoc, site );
	await expectStylesheet( page, publicUrl.href, /\/eazydocs\/build\/styles\/frontend\.css/ );
	await expect( page.locator( '#post' ).getByText( 'Fixture Alpha public fixture content.', { exact: true } ) ).toBeVisible();

	publicUrl.searchParams.set( 'ezd_smoke_rtl', '1' );
	await expectStylesheet( page, publicUrl.href, /\/eazydocs\/build\/styles\/rtl\.css/ );
	if ( process.env.EZD_EXPECT_PRO === '1' ) {
		await expectStylesheet( page, publicUrl.href, /\/eazydocs-pro\/build\/styles\/ezd-pro\.css/ );
	}
} );

test( 'pointer and keyboard reorder both survive a fresh Builder load', async ( { page } ) => {
	await login( page );
	const builder = new URL( '/wp-admin/admin.php?page=eazydocs-builder', site );
	await page.goto( builder.href );

	const rows = page.getByRole( 'tablist', { name: 'Documentation' } ).getByRole( 'tab' );
	const names = [ /Fixture Alpha/, /Fixture Bravo/, /Fixture Charlie/ ];
	await expect( rows ).toHaveText( names );

	const first = rows.nth( 0 );
	const handle = first.getByRole( 'button', { name: 'Drag to reorder' } );
	await first.hover();
	const from = await handle.boundingBox();
	const to = await rows.nth( 1 ).boundingBox();
	expect( from ).not.toBeNull();
	expect( to ).not.toBeNull();

	await expectSaved( page, async () => {
		const x = from.x + from.width / 2;
		const y = from.y + from.height / 2;
		await page.mouse.move( x, y );
		await page.mouse.down();
		await page.mouse.move( x, y + 10, { steps: 4 } );
		await expect( page.locator( 'body' ) ).toHaveClass( /ezd-is-dragging/ );
		await page.mouse.move( x, to.y + to.height / 2, { steps: 10 } );
		await page.mouse.up();
	} );
	await page.reload();
	await expect( rows ).toHaveText( [ names[1], names[0], names[2] ] );

	const activeId = await rows.nth( 0 ).getAttribute( 'data-id' );
	const targetId = await rows.nth( 1 ).getAttribute( 'data-id' );
	const keyboardHandle = rows.nth( 0 ).getByRole( 'button', { name: 'Drag to reorder' } );
	await page.mouse.move( 0, 0 );
	await rows.nth( 0 ).focus();
	await page.keyboard.press( 'Tab' );
	await expect( keyboardHandle ).toBeFocused();
	await expectSaved( page, async () => {
		await page.keyboard.press( 'Space' );
		const announcement = page.getByRole( 'status' ).filter( { hasText: /droppable area/ } );
		await expect( announcement ).toContainText( `droppable area ${ activeId }.` );
		await page.keyboard.press( 'ArrowDown' );
		await expect( announcement ).toContainText( `droppable area ${ targetId }.` );
		await page.keyboard.press( 'Space' );
	} );
	await page.reload();
	await expect( rows ).toHaveText( names );
} );

test( 'Pro can duplicate a doc over plain REST permalinks', async ( { page } ) => {
	test.skip( process.env.EZD_EXPECT_PRO !== '1', 'Requires both plugin ZIPs.' );
	await login( page );
	await page.goto( new URL( '/wp-admin/admin.php?page=eazydocs-builder', site ).href );
	const row = page.getByRole( 'tab', { name: /Fixture Alpha/ } );
	await expect( row ).toBeVisible();
	const docId = Number( await row.getAttribute( 'data-id' ) );
	const nonce = await page.evaluate( () => window.ezdDocsBuilderData.nonce );
	const endpoint = new URL( '/index.php', site );
	endpoint.searchParams.set( 'rest_route', '/eazydocs/v1/docs-builder/duplicate' );

	const response = await page.request.post( endpoint.href, {
		headers: { 'X-WP-Nonce': nonce },
		data: { doc_id: docId },
	} );
	await expect( response ).toBeOK();
	const result = await response.json();
	expect( result ).toMatchObject( { success: true, parentId: 0 } );
	expect( result.newId ).toBeGreaterThan( 0 );
	expect( result.newId ).not.toBe( docId );
} );

async function login( page ) {
	await page.route( '**/*', route => new URL( route.request().url() ).origin === new URL( site ).origin
		? route.continue()
		: route.abort() );
	const loginUrl = new URL( '/wp-login.php', site ).href;
	expect( ( await page.request.get( loginUrl ) ).ok() ).toBe( true );
	const response = await page.request.post( loginUrl, {
		form: { log: 'ezd_test_admin', pwd: 'ezd-disposable-test', testcookie: '1' },
		maxRedirects: 0,
	} );
	expect( response.status() ).toBe( 302 );
	expect( response.headers().location ).toContain( '/wp-admin/' );
}

async function expectStylesheet( page, path, pattern ) {
	const stylesheet = page.waitForResponse( response => pattern.test( response.url() ) );
	await page.goto( new URL( path, site ).href );
	const response = await stylesheet;
	expect( response.ok() ).toBe( true );
	expect( response.headers()['content-type'] ).toMatch( /text\/css/ );
}

async function expectSaved( page, gesture ) {
	const [ response ] = await Promise.all( [
		page.waitForResponse( response => {
			const request = response.request();
			if ( request.method() !== 'POST' || ! new URL( response.url() ).pathname.endsWith( '/admin-ajax.php' ) ) {
				return false;
			}
			const body = request.postData() || '';
			return new URLSearchParams( body ).get( 'action' ) === 'eaz_parent_nestable_docs'
				|| /name="action"\r?\n\r?\neaz_parent_nestable_docs\r?\n/.test( body );
		}, { timeout: 10_000 } ),
		gesture(),
	] );
	expect( response.ok() ).toBe( true );
	expect( ( await response.json() ).success ).toBe( true );
}
