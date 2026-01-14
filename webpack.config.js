const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		// Add frontend styles entry point
		'tabbed-docs/frontend': path.resolve(__dirname, 'src/tabbed-docs/frontend.scss'),
	},
};
