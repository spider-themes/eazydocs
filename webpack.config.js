const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

const scssDir = path.resolve(__dirname, 'assets/scss');

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		// Add frontend styles entry point
		'tabbed-docs/frontend': path.resolve(__dirname, 'src/tabbed-docs/frontend.scss'),
		// Add Docs Builder React app entry point
		'docs-builder/index': path.resolve(__dirname, 'src/docs-builder/index.tsx'),

		// SCSS entry points — compiled to build/styles/<name>.css
		'styles/admin': path.resolve(scssDir, 'admin.scss'),
		'styles/admin-global': path.resolve(scssDir, 'admin-global.scss'),
		'styles/admin-settings': path.resolve(scssDir, 'admin-settings.scss'),
		'styles/admin-dashboard': path.resolve(scssDir, 'admin-dashboard.scss'),
		'styles/admin-setup-wizard': path.resolve(scssDir, 'admin_setup_wizard.scss'),
		'styles/frontend': path.resolve(scssDir, 'frontend.scss'),
		'styles/frontend-global': path.resolve(scssDir, 'frontend-global.scss'),
		'styles/frontend-dark-mode': path.resolve(scssDir, 'frontend_dark-mode.scss'),
		'styles/rtl': path.resolve(scssDir, 'rtl.scss'),
		'styles/onepage': path.resolve(scssDir, 'onepage.scss'),
		'styles/shortcodes': path.resolve(scssDir, 'shortcodes.scss'),
		'styles/ezd-docs-widgets': path.resolve(scssDir, 'ezd-docs-widgets.scss'),
		'styles/blocks': path.resolve(scssDir, 'blocks.scss'),
		'styles/ezd-block-editor': path.resolve(scssDir, 'ezd-block-editor.scss'),
		'styles/analytics-presentation': path.resolve(scssDir, 'analytics-presentation.scss'),
		'styles/feedback-presentation': path.resolve(scssDir, 'feedback-presentation.scss'),
	},
};
