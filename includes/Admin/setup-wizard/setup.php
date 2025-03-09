<?php
$opt 					= get_option('eazydocs_settings' );
$slugType 				= $opt['docs-url-structure'] ?? '';
$custom_slug 			= $opt['docs-type-slug'] ?? '';
$brand_color 			= $opt['brand_color'] ?? '';
$docs_single_layout 	= $opt['docs_single_layout'] ?? '';
$docs_page_width 		= $opt['docs_page_width'] ?? '';
$customizer_visibility 	= $opt['customizer_visibility'] ?? '';
$docs_archive_page 		= $opt['docs-slug'] ?? '';
?>
<div class="wrap">
    <div class="ezd-setup-wizard-wrapper">
        <div class="ezd-setup-wizard-header">

            <div>
                <img src="<?php echo esc_url( EAZYDOCS_URL . '/src/images/ezd-icon.png' ); ?>" alt="<?php echo esc_attr__( 'crown icon', 'eazydocs' ); ?>" />
                <span><?php esc_html_e( 'EazyDocs', 'eazydocs' ); ?></span>
            </div>

            <div>
                <span class="dashicons dashicons-welcome-write-blog"></span>
                <span>
                    <a target="__blank" href="https://spider-themes.net/eazydocs/changelog/">
                        <?php esc_html_e( "What's New!", 'eazydocs' ); ?>
                    </a>
                </span>
            </div>
            
        </div>
    </div>
            
    <div id="ezd-setup-wizard-wrap">

        <div class="ezd-wizard-head">
            <div class="ezd-wizard-head-left">
                <img src="<?php echo esc_url( EAZYDOCS_IMG . '/eazydocs-favicon.png' ); ?>" alt="<?php esc_attr_e( 'crown icon', 'eazydocs' ); ?>" />
                <span>
                    <?php esc_html_e( 'GETTING STARTED', 'eazydocs' ); ?>
                </span>
            </div>
            <div class="ezd-wizard-head-right">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs' ) ); ?>" class="btn btn-primary">
                    <?php esc_html_e( 'Skip', 'eazydocs' ); ?>
                </a>
            </div>
        </div>

        <div class="sw-toolbar">
            <ul class="nav sr-only">
                <li><a class="nav-link" href="#step-1"></a></li>
                <li><a class="nav-link" href="#step-2"></a></li>
                <li><a class="nav-link" href="#step-3"></a></li>
                <li><a class="nav-link" href="#step-4"></a></li>
                <li><a class="nav-link" href="#step-5"></a></li>
            </ul>
        </div>

        <div class="tab-content">
            <div id="step-1" class="tab-pane" role="tabpanel">
                <h2> <?php esc_html_e( 'Welcome to EazyDocs', 'eazydocs' ); ?> </h2>
                
                <p> <?php esc_html_e( 'Discover EazyDocs by this guide that walks you through creating professional, user-friendly website documentation seamlessly. Then click next to setup initial settings.', 'eazydocs' ) ; ?> </p>

                <iframe width="650" height="350" src="https://www.youtube.com/embed/4H2npHIR2qg?si=ApQh7BL6CL5QM4zX" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                <div class="button-inline">
                    <a class="button button-primary ezd-btn btn-lg" target="_blank" href="https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/">
                        <i class="dashicons dashicons-sos"></i> 
                        <?php esc_html_e( 'Documentation', 'eazydocs' ); ?>
                    </a>

                    <a class="button button-primary ezd-btn btn-lg btn-red" target="_blank" href="https://www.youtube.com/playlist?list=PLeCjxMdg411XgYy-AekTE-bhvCXQguZWJ">
                        <i class="dashicons dashicons-playlist-video"></i> 
                        <?php esc_html_e( 'Video Tutorials', 'eazydocs' ); ?>
                    </a>

                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" target="_blank" href="https://wordpress.org/support/plugin/eazydocs/">
                        <i class="dashicons dashicons-editor-help"></i> 
                        <?php esc_html_e( 'Support', 'eazydocs' ); ?>
                    </a>
                </div>
            </div>

            <div id="step-2" class="tab-pane" role="tabpanel" style="display:none">
                
                <h2> <?php esc_html_e( 'Docs Archive Page', 'eazydocs' ); ?> </h2>
                <p> <?php esc_html_e( 'This page will show on the Doc single page breadcrumb and will be used to show the Docs.', 'eazydocs' ); ?> </p>

                <div class="archive-page-selection-wrap">
                    <select name="docs_archive_page" id="docs_archive_page">
                        <option value=""><?php esc_html_e( 'Select a page', 'eazydocs' ); ?></option>
                        <?php
                        $pages = get_pages();
                        foreach ( $pages as $page ) {
                            $selected = ( $page->ID == $docs_archive_page ) ? 'selected' : '';
                            echo '<option value="' . esc_attr( $page->ID ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $page->post_title ) . '</option>';
                        }
                        ?>
                    </select>
                    <span> <?php esc_html_e( 'You can create this page with using [eazydocs] shortcode or available EazyDocs blocks or Elementor widgets.', 'eazydocs' ); ?> </span>
                </div>

                <h2> <?php esc_html_e( 'Brand Color', 'eazydocs' ); ?> </h2>
                <p> <?php esc_html_e( 'Select the Brand Color for your knowledge base.', 'eazydocs' ); ?> </p>

                <div class="brand-color-picker-wrap">
                    <input type="text" class="brand-color-picker" placeholder="Color Picker" value="<?php echo esc_attr( $brand_color ); ?>">
                </div>
                
                <h2> <?php esc_html_e( 'Doc Root URL Slug', 'eazydocs' ); ?> </h2>
                <p> <?php esc_html_e( 'Select the Docs URL Structure. This will be used to generate the Docs URL.', 'eazydocs' ); ?> </p>

                <div class="root-slug-wrap">
                    <input type="radio" id="post-name" name="slug" value="post-name" <?php checked( $slugType, 'post-name' ); ?>>						 
                    <label for="post-name" class="<?php if ( $slugType == 'post-name' ) { echo esc_attr( 'active' ); } ?>">
                        <?php esc_html_e( 'Default Slug', 'eazydocs' ); ?>
                    </label>

                    <input type="radio" id="custom-slug" name="slug" value="custom-slug" <?php checked( $slugType, 'custom-slug' ); ?>>						 
                    <label for="custom-slug" class="<?php if ( $slugType == 'custom-slug' ) { echo esc_attr( 'active' ); } ?>">
                        <?php esc_html_e( 'Custom Slug', 'eazydocs' ); ?>
                    </label>

                    <input type="text" class="custom-slug-field <?php if ( $slugType == 'custom-slug' ) { echo esc_attr( 'active' ); } ?>" placeholder="Basic Setting" value="<?php echo esc_attr( $custom_slug ); ?>">
                </div>
            </div>

            <div id="step-3" class="tab-pane" role="tabpanel" style="display:none">
                <h2> <?php esc_html_e( 'Select Page Layout', 'eazydocs' ); ?> </h2>

                <div class="page-layout-wrap">
                    <input type="radio" id="both_sidebar" value="both_sidebar" name="docs_single_layout" <?php checked( $docs_single_layout, 'both_sidebar' ); ?>>
                    <label for="both_sidebar" class="<?php if ( $docs_single_layout == 'both_sidebar' ) { echo esc_attr( 'active' ); } ?>">
                        <img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/both_sidebar.jpg' ); ?>" alt="<?php esc_attr_e( 'Both sidebar layout', 'eazydocs' ); ?>" />
                    </label>

                    <input type="radio" id="sidebar_left" value="sidebar_left" name="docs_single_layout" <?php checked( $docs_single_layout, 'sidebar_left' ); ?>>
                    <label for="sidebar_left" class="<?php if ( $docs_single_layout == 'left_sidebar' ) { echo esc_attr( 'active' ); } ?>">
                        <img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/sidebar_left.jpg' ); ?>" alt="<?php esc_attr_e( 'Left sidebar layout', 'eazydocs' ); ?>" />
                    </label>

                    <input type="radio" id="sidebar_right" value="sidebar_right" name="docs_single_layout" <?php checked( $docs_single_layout, 'sidebar_right' ); ?>>
                    <label for="sidebar_right" class="<?php if ( $docs_single_layout == 'right_sidebar' ) { echo esc_attr( 'active' ); } ?>">
                        <img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/sidebar_right.jpg' ); ?>" alt="<?php esc_attr_e( 'Right sidebar layout', 'eazydocs' ); ?>" />
                    </label>
                </div>
                 
                <h2><?php esc_html_e( 'Page Width', 'eazydocs' ); ?></h2>
                <div class="page-width-wrap">
                    <input type="radio" id="boxed" name="docsPageWidth" value="boxed" <?php checked( $docs_page_width, 'boxed' ); ?>>
                    <label for="boxed" class="<?php if ( $docs_page_width == 'boxed' ) { echo esc_attr( 'active' ); } ?>">
                        <?php esc_html_e( 'Boxed Width', 'eazydocs' ); ?>
                    </label>
                    <input type="radio" id="full-width" name="docsPageWidth" value="full-width" <?php checked( $docs_page_width, 'full-width' ); ?>>
                    <label for="full-width" class="<?php if ( $docs_page_width == 'full-width' ) { echo esc_attr( 'active' ); } ?>">
                        <?php esc_html_e( 'Full Width', 'eazydocs' ); ?>
                    </label>
                </div>
                
                <h2><?php esc_html_e( 'Live Customizer', 'eazydocs' ); ?></h2>
                <label>
                    <input type="checkbox" id="live-customizer" name="customizer_visibility" value="1" <?php checked( $customizer_visibility, '1' ); ?>>
                    <?php esc_html_e( 'Enable Live Customizer', 'eazydocs' ); ?>
                </label>
            </div>

            <div id="step-4" class="tab-pane" role="tabpanel" style="display:none">
                <?php
                 // Required to use plugin functions
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

                $plugins = [
                    [
                        'img'          => 'elementor-logo.png',
                        'title'        => __('Elementor', 'eazydocs'),
                        'slug'         => 'elementor',
                        'file'         => 'elementor/elementor.php', // Added the correct file path
                        'description'  => __('Required for Elementor widget', 'eazydocs'),
                        'status'       => __('Required', 'eazydocs')
                    ],
                    [
                        'img'          => 'bbp-core-logo.png',
                        'title'        => __('BBP Core', 'eazydocs'),
                        'slug'         => 'bbp-core',
                        'file'         => 'bbp-core/bbp-core.php', // Added the correct file path
                        'description'  => __('Recommended for Power-up bbPress forum', 'eazydocs'),
                        'status'       => __('Recommended', 'eazydocs')
                    ],
                    [
                        'img'          => 'changeloger-logo.png',
                        'title'        => __('Changeloger', 'eazydocs'),
                        'slug'         => 'changeloger',
                        'file'         => 'changeloger/changeloger.php', // Added the correct file path
                        'description'  => __('Recommended for publishing Software Changelog', 'eazydocs'),
                        'status'       => __('Recommended', 'eazydocs')
                    ]
                ];
                ?>

                <h2> <?php esc_html_e( 'Install Required Plugins', 'eazydocs' ); ?> </h2>

                <ul class="ezd-plugins-wrap">
                    <?php 
                    foreach ( $plugins as $plugin ) : 
                        $plugin_path 	= WP_PLUGIN_DIR . '/' . $plugin['file'];
                        $is_installed 	= file_exists( $plugin_path );
                        $is_active 		= is_plugin_active( $plugin['file'] );
                        $checked 		= $is_active ? 'checked disabled' : '';
                        ?>
                        <li>
                            <img src="<?php echo esc_url( EAZYDOCS_IMG . '/admin/' . $plugin['img']); ?>" alt="<?php echo esc_attr( $plugin['title'] ); ?>" />
                            <h2> <?php echo esc_html( $plugin['title'] ); ?> </h2>
                            <p> <?php echo esc_html( $plugin['description'] ); ?> </p>

                            <div>
                                <span> <?php echo esc_html($plugin['status']); ?> </span>
                                <label class="ezd-switch-toggle">
                                    <input type="checkbox" value="<?php echo esc_attr( $plugin['slug'] ); ?>" name="plugin_slugs[]" <?php echo esc_attr($checked); ?>  />
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </li>
                        <?php 
                    endforeach; 
                    ?>
                </ul>
                
                <p><?php esc_html_e( 'Install the plugins you need. Each one has a short description to help you choose the right ones for better functionality.', 'eazydocs' ); ?></p>
                
                <button id="ezd-install-selected-plugins" class="button button-primary">Install Selected Plugins →</button>
                
            </div>

            <div id="step-5" class="tab-pane" role="tabpanel" style="display:none">
                <div class="swal2-icon swal2-question swal2-icon-show" style="display: flex;">
                    <div class="swal2-icon-content">?</div>
                </div>
                <h2> <?php esc_html_e( 'Review and Confirm', 'eazydocs' ); ?> </h2>

                <p> <?php esc_html_e( 'Take a moment to review all your settings thoroughly before confirming your choices to ensure everything is set up correctly.', 'eazydocs' ); ?> </p>
                
                <button type="button" id="finish-btn" class="btn btn-primary">
                    <?php esc_html_e( 'Confirm', 'eazydocs' ); ?>
                </button>
            </div>

        </div>
    </div>
</div>