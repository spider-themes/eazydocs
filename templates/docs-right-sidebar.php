<?php
$opt                = get_option( 'eazydocs_settings' );
$widget_sidebar     = $opt['is_widget_sidebar'] ?? '';
$toc_switcher       = $opt['toc_switcher'] ?? '';
$toc_heading        = $opt['toc_heading'] ?? __( 'CONTENTS', 'eazydocs' );
$is_pro_themes      = wp_get_theme();
$toc_auto_numbering = $opt['toc_auto_numbering'] ?? '';
$toc_auto_numbering = $toc_auto_numbering == '1' ? ' toc_auto_numbering' : '';
?>
<div class="ezd-xl-col-2 ezd-lg-col-3 ezd-grid-column-full doc_right_mobile_menu ezd-sticky-lg-top">
    <div class="doc_rightsidebar ezd-scroll">
        <div class="open_icon" id="mobile-right-toggle">
            <i class="arrow_carrot-left"></i>
            <i class="arrow_carrot-right"></i>
        </div>

        <div class="pageSideSection">
			
			<?php			
			/**
			 * Subscription
			 */
			do_action( 'eazydocs_docs_subscription', ezd_get_doc_parent_id(get_the_ID()) );
			/**
			 * Collaboration Buttons
			 */
			eazydocs_get_template_part( 'tools/edit-add-doc' );

			/**
			 * Share Buttons
			 */
			eazydocs_get_template_part( 'tools/share-btns' );

			/**
			 * Conditional Dropdown
			 */
			eazydocs_get_template_part( 'tools/conditional-dropdown' );

			/**
			 * Font Size Switcher & Print Icon
			 */
			eazydocs_get_template_part( 'tools/font-switcher' );

			/**
			 * Dark Mode switcher
			 */
			eazydocs_get_template_part( 'tools/dark-mode-switcher' );

			if ( ! empty ( $toc_switcher ) ) :
				?>
                <div class="table-of-content">
                    <h6><i class="icon_ul"></i> <?php echo esc_html( $toc_heading ); ?></h6>
                    <nav class="ezd-list-unstyled doc_menu toc_right<?php echo esc_attr($toc_auto_numbering) ?>" data-toggle="toc" id="eazydocs-toc"></nav>
                </div>
			<?php
			endif;
			?>

            <div class="ezd-widgets">
				<?php
				// Widgets area
				$parent_doc_id    = function_exists( 'get_root_parent_id' ) ? get_root_parent_id( get_queried_object_id() ) : '';
				$content_type     = get_post_meta( $parent_doc_id, 'ezd_doc_right_sidebar_type', true );
				$ezd_shortcode    = get_post_meta( $parent_doc_id, 'ezd_doc_right_sidebar', true );
				$is_valid_post_id = is_null( get_post( $ezd_shortcode ) ) ? 'No' : 'Yes';

				if ( ezd_is_premium() ) {
					if ( $content_type == 'string_data_right' && ! empty ( $ezd_shortcode ) ) {
						echo do_shortcode( html_entity_decode( $ezd_shortcode ) );
					} elseif ( $content_type == 'shortcode_right' ) {
						if ( is_active_sidebar( 'doc_sidebar' ) && $widget_sidebar == 1 ) {
							dynamic_sidebar( 'doc_sidebar' );
						}
					} else {
						if ( $content_type == 'widget_data_right' && ! empty( $is_valid_post_id ) ) {
							$wp_blocks = new WP_Query( [
								'post_type' => 'wp_block',
								'p'         => $ezd_shortcode
							] );

							if ( $wp_blocks->have_posts() ) {
								while ( $wp_blocks->have_posts() ) : $wp_blocks->the_post();
									the_content();
								endwhile;
								wp_reset_postdata();
							}
						}
					}
				} else {
					if ( is_active_sidebar( 'doc_sidebar' ) && $widget_sidebar == 1 && $is_pro_themes == 'Docy' || $is_pro_themes == 'Docly' ) {
						dynamic_sidebar( 'doc_sidebar' );
					}
				}
				?>
            </div>
        </div>
    </div>
</div>