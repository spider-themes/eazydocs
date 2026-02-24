/**
 * Cookie utility functions.
 *
 * @package EazyDocs
 * @since   2.8.0
 */

/**
 * Create a cookie.
 *
 * @param name  Cookie name.
 * @param value Cookie value.
 * @param days  Expiry in days.
 */
export function setCookie( name: string, value: string, days: number ): void {
	let expires = '';
	if ( days ) {
		const date = new Date();
		date.setTime( date.getTime() + days * 24 * 60 * 60 * 1000 );
		expires = '; expires=' + date.toUTCString();
	}
	document.cookie = name + '=' + value + expires + '; path=/';
}

/**
 * Read a cookie by name.
 *
 * @param name Cookie name.
 * @return Cookie value or null.
 */
export function getCookie( name: string ): string | null {
	const nameEQ = name + '=';
	const ca = document.cookie.split( ';' );
	for ( let i = 0; i < ca.length; i++ ) {
		let c = ca[ i ];
		while ( c.charAt( 0 ) === ' ' ) {
			c = c.substring( 1, c.length );
		}
		if ( c.indexOf( nameEQ ) === 0 ) {
			return c.substring( nameEQ.length, c.length );
		}
	}
	return null;
}

/**
 * Delete a cookie.
 *
 * @param name Cookie name.
 */
export function eraseCookie( name: string ): void {
	setCookie( name, '', -1 );
}
