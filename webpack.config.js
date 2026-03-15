const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const webpack = require('webpack');

const scssDir = path.resolve(__dirname, 'assets/scss');
const styleEntries = {
	'styles/admin': path.resolve(scssDir, 'admin.scss'),
	'styles/admin-global': path.resolve(scssDir, 'admin-global.scss'),
	'styles/admin-settings': path.resolve(scssDir, 'admin-settings.scss'),
	'styles/docs-builder': path.resolve(scssDir, 'docs-builder.scss'),
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
};
const styleEntryNames = Object.keys( styleEntries );

const serializePhpValue = ( value, indent = 0 ) => {
	const indentation = '\t'.repeat( indent );
	const nextIndentation = '\t'.repeat( indent + 1 );

	if ( Array.isArray( value ) ) {
		if ( value.length === 0 ) {
			return 'array()';
		}

		const items = value.map(
			( item ) => `${ nextIndentation }${ serializePhpValue( item, indent + 1 ) }`
		);

		return `array(\n${ items.join( ',\n' ) }\n${ indentation })`;
	}

	if ( value && typeof value === 'object' ) {
		const entries = Object.entries( value );

		if ( entries.length === 0 ) {
			return 'array()';
		}

		const items = entries.map(
			( [ key, item ] ) =>
				`${ nextIndentation }'${ key.replace( /'/g, "\\'" ) }' => ${ serializePhpValue( item, indent + 1 ) }`
		);

		return `array(\n${ items.join( ',\n' ) }\n${ indentation })`;
	}

	if ( typeof value === 'string' ) {
		return `'${ value.replace( /'/g, "\\'" ) }'`;
	}

	if ( typeof value === 'number' ) {
		return String( value );
	}

	if ( typeof value === 'boolean' ) {
		return value ? 'true' : 'false';
	}

	if ( value === null || typeof value === 'undefined' ) {
		return 'null';
	}

	return `'${ String( value ).replace( /'/g, "\\'" ) }'`;
};

class StylesBuildArtifactsPlugin {
	apply( compiler ) {
		const { Compilation, sources } = compiler.webpack;

		compiler.hooks.thisCompilation.tap(
			'StylesBuildArtifactsPlugin',
			( compilation ) => {
				compilation.hooks.processAssets.tap(
					{
						name: 'StylesBuildArtifactsPlugin',
						stage: Compilation.PROCESS_ASSETS_STAGE_SUMMARIZE,
					},
					() => {
						const manifest = {
							version: compilation.hash,
							entries: {},
						};

						styleEntryNames.forEach( ( entryName ) => {
							const entryKey = entryName.replace( /^styles\//, '' );
							const cssAssetName = `${ entryName }.css`;
							const rtlAssetName = `${ entryName }-rtl.css`;
							const cssMapAssetName = `${ entryName }.css.map`;

							if ( compilation.getAsset( cssAssetName ) ) {
								manifest.entries[ entryKey ] = {
									css: `${ entryKey }.css`,
								};

								if ( compilation.getAsset( rtlAssetName ) ) {
									manifest.entries[ entryKey ].rtl = `${ entryKey }-rtl.css`;
								}

								if ( compilation.getAsset( cssMapAssetName ) ) {
									manifest.entries[ entryKey ].map = `${ entryKey }.css.map`;
								}
							}

							[
								`${ entryName }.js`,
								`${ entryName }.js.map`,
								`${ entryName }.js.LICENSE.txt`,
								`${ entryName }.asset.php`,
							].forEach( ( assetName ) => {
								if ( compilation.getAsset( assetName ) ) {
									compilation.deleteAsset( assetName );
								}
							} );
						} );

						const manifestSource = `<?php\nreturn ${ serializePhpValue( manifest ) };\n`;
						compilation.emitAsset(
							'styles/styles.asset.php',
							new sources.RawSource( manifestSource )
						);
					}
				);
			}
		);
	}
}

const matchesStyleRule = ( rule ) => {
	if ( ! rule?.test ) {
		return false;
	}

	if ( rule.test instanceof RegExp ) {
		return [ 'file.css', 'file.pcss', 'file.scss', 'file.sass' ].some( ( file ) =>
			rule.test.test( file )
		);
	}

	const test = String( rule.test );

	return /css|pcss|s[ac]ss/i.test( test );
};

const enableStyleSourceMaps = ( rules = [] ) =>
	rules.map( ( rule ) => {
		if ( ! Array.isArray( rule.use ) ) {
			return rule;
		}

		if ( ! matchesStyleRule( rule ) ) {
			return rule;
		}

		return {
			...rule,
			use: rule.use.map( ( use ) => {
				if ( typeof use === 'string' ) {
					return use;
				}

				if ( ! use?.loader ) {
					return use;
				}

				const loader = use.loader;
				const options = { ...( use.options || {} ) };

				if (
					loader.includes( 'css-loader' ) ||
					loader.includes( 'postcss-loader' ) ||
					loader.includes( 'sass-loader' )
				) {
					options.sourceMap = true;
				}

				if ( loader.includes( 'postcss-loader' ) && options.postcssOptions ) {
					options.postcssOptions = {
						...options.postcssOptions,
						sourceMap: true,
					};
				}

				return {
					...use,
					options,
				};
			} ),
		};
	} );

module.exports = {
	...defaultConfig,
	devtool: 'source-map',
	module: {
		...( defaultConfig.module || {} ),
		rules: enableStyleSourceMaps( defaultConfig.module?.rules || [] ),
	},
	plugins: [
		...( defaultConfig.plugins || [] ),
		new StylesBuildArtifactsPlugin(),
		new webpack.SourceMapDevToolPlugin( {
			filename: '[file].map[query]',
			test: /\.css($|\?)/i,
			append: '\n/*# sourceMappingURL=[url] */',
		} ),
	],
	entry: {
		...defaultConfig.entry(),
		// Add frontend styles entry point
		'tabbed-docs/frontend': path.resolve(__dirname, 'src/tabbed-docs/frontend.scss'),
		// Add Docs Builder React app entry point
		'docs-builder/index': path.resolve(__dirname, 'src/docs-builder/index.tsx'),

		// SCSS entry points — compiled to build/styles/<name>.css
		...styleEntries,
	},
};
