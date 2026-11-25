const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'node:path' );
const createWebpackConfig = require( './build-tools/webpack-styles' );

module.exports = createWebpackConfig( defaultConfig, {
	root: __dirname,
	styles: {
		'frontend': 'frontend.scss',
		'frontend-global': 'frontend-global.scss',
		'frontend-dark-mode': 'frontend_dark-mode.scss',
		'rtl': 'rtl.scss',
		'onepage': 'onepage.scss',
		'shortcodes': 'shortcodes.scss',
		'ezd-docs-widgets': 'ezd-docs-widgets.scss',
		'blocks': 'blocks.scss',
		'print': 'print.scss',
	},
	entries: {
		'tabbed-docs/frontend': path.join( __dirname, 'src/tabbed-docs/frontend.scss' ),
		'docs-builder/index': path.join( __dirname, 'src/docs-builder/index.tsx' ),
	},
	sassDeprecations: [ 'import', 'global-builtin', 'color-functions', 'if-function' ],
} );
