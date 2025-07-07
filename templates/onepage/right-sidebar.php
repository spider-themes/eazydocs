<?php
$ezd_content		= get_the_content( get_the_ID() );
$ezd_content_none 	= ! empty( $ezd_content ) ? 'mt-5' : '';
$layout 			= get_post_meta( get_the_ID(), 'ezd_doc_layout', true );
$sticky_class		= $layout == 'classic-onepage-layout' ? 'ezd-sticky-lg-top' : '';
$widget_sidebar 	= ezd_get_opt( 'is_widget_sidebar' );
?>

<div class="ezd-xl-col-2 ezd-lg-col-3 ezd-grid-column-full doc_right_mobile_menu sticky-top <?php echo esc_attr($sticky_class) ?>">

    <div class="doc_rightsidebar ezd-scroll one-page-docs-right-sidebar">
        <div class="open_icon" id="mobile-right-toggle">
            <i class="arrow_carrot-left"></i>
            <i class="arrow_carrot-right"></i>
        </div>
        <div class="pageSideSection">
			<?php
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
			?>

            <div class="onepage-sidebar doc_sidebar <?php echo esc_attr( $ezd_content_none ); ?>">
				<?php
				// Widgets area
				$parent_doc_id    = get_the_ID();
				$content_type     = get_post_meta( $parent_doc_id, 'ezd_doc_content_type_right', true );
				$ezd_shortcode    = get_post_meta( $parent_doc_id, 'ezd_doc_content_box_right', true );
				$is_valid_post_id = is_null( get_post( $ezd_shortcode ) ) ? 'No' : 'Yes';

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
						]);

						if ( $wp_blocks->have_posts() ) {
							while ( $wp_blocks->have_posts() ) : $wp_blocks->the_post();
								the_content();
							endwhile;
							wp_reset_postdata();
						}
					}
				}
				?>
            </div>
        </div>
    </div>
</div>
