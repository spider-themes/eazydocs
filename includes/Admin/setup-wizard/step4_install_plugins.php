<div id="step-4" class="tab-pane ezd-install-plugins" role="tabpanel" style="display:none">
	<?php
	// Include necessary WordPress functions for plugin management
	if ( !function_exists('is_plugin_active') ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugins = [
		[
			'slug'        => 'elementor',
			'img'         => 'elementor-logo.png',
			'title'       => esc_html__( 'Elementor', 'eazydocs' ),
			'description' => esc_html__( 'Required for Elementor widget. Install this plugin if you want to use the Elementor widgets of EazyDocs.', 'eazydocs' ),
			'status'      => esc_html__( 'Required', 'eazydocs' )
		],
		[
			'slug'        => 'advanced-accordion-block',
			'img'         => 'AAGB-logo.svg',
			'title'       => esc_html__( 'Advanced Accordion Gutenberg Block', 'eazydocs' ),
			'description' => esc_html__( 'A highly advanced and lightweight accordion plugin, perfect for creating FAQs and accordions in the block editor.', 'eazydocs' ),
			'status'      => esc_html__( 'Recommended', 'eazydocs' )
		],
		[
			'img'         => 'bbp-core-logo.png',
			'title'       => esc_html__( 'BBP Core', 'eazydocs' ),
			'slug'        => 'bbp-core',
			'description' => esc_html__( 'Enhance your bbPress forum with powerful features. This plugin is designed exclusively for bbPress users.', 'eazydocs' ),
			'status'      => esc_html__( 'Recommended', 'eazydocs' )
		],
		[
			'img'         => 'changeloger-logo.png',
			'title'       => esc_html__( 'Changeloger', 'eazydocs' ),
			'slug'        => 'changeloger',
			'description' => esc_html__( 'Ideal for publishing software changelogs, this plugin converts plain text into visually rich formats in the WordPress block editor.', 'eazydocs' ),
			'status'      => esc_html__( 'Recommended', 'eazydocs' )
		]
	];
	?>

	<h2><?php esc_html_e('Install Recommended Plugins', 'eazydocs'); ?></h2>

	<ul class="ezd-plugins-wrap">
		<?php
		foreach ($plugins as $plugin) :
			$plugin_file = $plugin['slug'] . '/' . $plugin['slug'] . '.php';
			$is_active = is_plugin_active($plugin_file);
			$is_installed = file_exists(WP_PLUGIN_DIR . '/' . $plugin['slug']);

			$button_text = $is_active ? esc_html__( 'Activated', 'eazydocs') : ($is_installed ? esc_html__( 'Activate', 'eazydocs') : esc_html__( 'Install', 'eazydocs'));
			$btn_icon = $is_active ? 'dashicons-yes' : ($is_installed ? 'dashicons-update' : 'dashicons-download');
			$button_class = $is_active ? 'button-disabled' : 'button-action';
			$button_attr = $is_active ? 'disabled' : sprintf('data-plugin="%s" data-action="%s"', esc_attr($plugin['slug']), $is_installed ? 'activate' : 'install');
			?>
			<li>
				<div class="has-light-bg">
					<img src="<?php echo esc_url(EAZYDOCS_IMG . '/admin/' . $plugin['img']); ?>" alt="<?php echo esc_attr($plugin['title']); ?>" />
					<h2><?php echo esc_html($plugin['title']); ?></h2>
					<p><?php echo esc_html($plugin['description']); ?></p>
				</div>
				<div class="action-btn-wrap has-light-bg">
					<span> <?php echo esc_html($plugin['status']); ?> </span>
                    <div class="right-btns">
                        <a href="https://wordpress.org/plugins/<?php echo esc_attr($plugin['slug']); ?>" target="_blank" class="button button-info">
                            <i class="dashicons dashicons-info"></i> <?php esc_html_e('More Info', 'eazydocs'); ?>
                        </a>
                        <button class="button-install-plugin <?php echo esc_attr($button_class); ?>" <?php echo esc_attr($button_attr); ?>>
                            <i class="dashicons <?php echo esc_attr($btn_icon) ?>"></i> <?php echo esc_html($button_text); ?>
                        </button>
                    </div>

				</div>
			</li>
		<?php endforeach; ?>
	</ul>

	<p><?php esc_html_e( 'Install the plugins you need. Each one has a short description to help you choose the right ones for better functionality.', 'eazydocs' ); ?></p>

</div>
