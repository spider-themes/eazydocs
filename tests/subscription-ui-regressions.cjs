/* Run with node tests/subscription-ui-regressions.cjs [pro-path] [jquery-path].
 * Uses the real subscription script in Chromium with intercepted AJAX.
 * No WordPress database, email provider, or external network is used.
 */
const assert = require('node:assert/strict');
const path = require('node:path');
const { chromium } = require('playwright');
const pro = process.argv[2] || path.resolve(__dirname, '../../eazydocs-pro');
const jquery = process.argv[3] || path.resolve(__dirname, '../../../../wp-includes/js/jquery/jquery.min.js');

(async () => {
  const browser = await chromium.launch({
    executablePath: process.env.CHROME_PATH || '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    headless: true,
  });
  let checks = 0;
  async function fixture({ loggedIn = false, confirming = false, subscribed = false } = {}) {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.route('**/*', route => route.abort());
    await page.setContent(`<style>.ezd-subscription-form-wrap{display:none}.ezd-subscription-form-wrap.active{display:block}.hidden{display:none}</style>
      <button class="ezd-subscription-btn ${subscribed ? 'subscribed' : loggedIn ? 'logged-user' : 'subscribe'}" data-id="10" ${subscribed ? 'data-token="canonical-token"' : ''}>${subscribed ? 'Unsubscribe' : 'Subscribe'}</button>
      <div class="ezd-subscription-form-wrap" id="10"><div class="ezd-subscription-inner"><form class="ezd-subscription-form">
      <input name="ezd_subscription_name" value="Reader"><input name="ezd_subscription_email" value="reader@example.test"><button class="ezd-subscription-submit">Subscribe</button></form></div></div>`);
    await page.addScriptTag({ path: jquery });
    await page.evaluate(({ loggedIn, confirming }) => {
      window.requests = [];
      window.eazydocs_subscription = {
        ajax_url: '/intercepted', nonce: 'test-nonce', logged_in: loggedIn,
        subscription_confirmation: confirming ? '<span data-token="pending-token" data-id="10"></span>' : '',
        subscriptions_btn: 'Subscribe', unsubscriptions_btn: 'Unsubscribe',
        unsubscriptions_heading: 'Unsubscribe', unsubscriptions_desc: 'Confirm unsubscribe?',
        unsubscriptions_submit_btn: 'Confirm', unsubscriptions_cancel_btn: 'Cancel',
      };
      jQuery.ajax = options => {
        const data = typeof options.data === 'string' ? Object.fromEntries(new URLSearchParams(options.data)) : options.data;
        window.requests.push(data);
        if (options.beforeSend) options.beforeSend();
        setTimeout(() => {
          if (window.rateLimited && data.action === 'ezd_subscription_form') {
            options.error({ responseJSON: { data: { message: 'Please wait before sending another subscription request.' } } });
          } else if (options.success) {
            options.success({ success: true, data: { token: 'canonical-token', message: 'Confirmation sent' } });
          }
          if (options.complete) options.complete();
        }, 0);
      };
    }, { loggedIn, confirming });
    await page.addScriptTag({ path: path.join(pro, 'includes/features/subscription/subscribe.js') });
    await page.evaluate(() => new Promise(resolve => jQuery(resolve)));
    return { page, errors };
  }
  async function unsubscribe(page) {
    await page.getByRole('button', { name: 'Unsubscribe', exact: true }).click();
    assert.equal(await page.locator('.unsubscription:not(.hidden)').count(), 1, 'Only one confirmation popup should open');
    await page.getByRole('link', { name: 'Confirm', exact: true }).click();
    await page.getByRole('button', { name: 'Subscribe', exact: true }).first().waitFor();
  }
  async function check(name, run) {
    await run(); checks++; console.log('PASS ' + name);
  }
  try {
    await check('confirmation keeps the canonical response token', async () => {
      const { page, errors } = await fixture({ confirming: true });
      await page.locator('.ezd-subscription-btn.subscribed').waitFor();
      assert.equal(await page.locator('.ezd-subscription-btn').getAttribute('data-token'), 'canonical-token');
      await unsubscribe(page);
      assert.deepEqual(await page.evaluate(() => requests.map(r => r.action)), ['ezd_confirm_subscription', 'ezd_unsubscription_create']);
      assert.equal(await page.evaluate(() => requests[1].token), 'canonical-token');
      assert.deepEqual(errors, []);
      await page.close();
    });
    await check('guest unsubscribe restores the guest subscription form', async () => {
      const { page, errors } = await fixture({ subscribed: true });
      await unsubscribe(page);
      await page.locator('.ezd-subscription-btn.subscribe').click();
      await page.locator('.ezd-subscription-form-wrap.active').waitFor();
      await page.locator('.ezd-subscription-submit').click();
      await page.getByText('Confirmation sent', { exact: true }).waitFor();
      assert.deepEqual(await page.evaluate(() => requests.map(r => r.action)), ['ezd_unsubscription_create', 'ezd_subscription_form']);
      assert.equal(await page.evaluate(() => requests[1].nonce), 'test-nonce');
      assert.deepEqual(errors, []);
      await page.close();
    });
    await check('rate limiting restores the submit button and explains the retry', async () => {
      const { page, errors } = await fixture();
      await page.evaluate(() => { window.rateLimited = true; });
      await page.locator('.ezd-subscription-btn').click();
      await page.locator('.ezd-subscription-submit').click();
      await page.getByText('Please wait before sending another subscription request.', { exact: true }).waitFor();
      assert.equal(await page.locator('.ezd-subscription-submit').innerText(), 'Subscribe');
      assert.deepEqual(errors, []);
      await page.close();
    });
    for (const subscribed of [false, true]) {
      await check('logged-in subscription cycle, initially subscribed=' + subscribed, async () => {
        const { page, errors } = await fixture({ loggedIn: true, subscribed });
        if (!subscribed) {
          await page.locator('.ezd-subscription-btn').click();
          await page.locator('.ezd-subscription-btn.subscribed').waitFor();
        }
        await unsubscribe(page);
        await page.locator('.ezd-subscription-btn.logged-user').click();
        await page.locator('.ezd-subscription-btn.subscribed').waitFor();
        const actions = await page.evaluate(() => requests.map(r => r.action));
        assert.deepEqual(actions, subscribed ? ['ezd_unsubscription_create', 'ezd_auto_confirm_subscription'] : ['ezd_auto_confirm_subscription', 'ezd_unsubscription_create', 'ezd_auto_confirm_subscription']);
        assert.deepEqual(errors, []);
        await page.close();
      });
    }
    console.log(checks + ' checks, 0 failures');
  } finally {
    await browser.close();
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
