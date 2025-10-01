<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="recommended_topic_area">
    <div class="recommended_topic_inner">
		<?php
        $settings_key = $settings['bg_shape']; $alt = 'curve shape'; $class = 'doc_shap_one';
        if ( ! empty( $settings_key['id'] ) ) {
	        echo wp_get_attachment_image( $settings_key['id'], 'full', '', array( 'class' => $class ) );

        } elseif ( ! empty( $settings_key['url'] ) && empty( $settings_key['id'] ) ) {
			$class_attr = ! empty( $class ) ? 'class="' . esc_attr( $class ) . '"' : '';
			$atts_str   = '';

			if ( ! empty( $atts ) && is_array( $atts ) ) {
				foreach ( $atts as $k => $att ) {
					$atts_str .= ' ' . esc_attr( $k ) . '="' . esc_attr( $att ) . '"';
				}
			}

			$img_url = isset( $settings_key['url'] ) ? esc_url( $settings_key['url'] ) : '';
			$alt     = isset( $alt ) ? esc_attr( $alt ) : '';

			echo wp_kses_post( '<img src="' . $img_url . '" ' . $class_attr . ' alt="' . $alt . '"' . $atts_str . '>' );
        }
        ?>
		<?php if ( $settings['is_bg_objects'] == 'yes' ) : ?>
            <div class="doc_round one" data-parallax='{"x": -80, "y": -100, "rotateY":0}'></div>
            <div class="doc_round two" data-parallax='{"x": -10, "y": 70, "rotateY":0}'></div>
		<?php endif; ?>
		<?php if ( ! empty( $settings['title'] || $settings['subtitle'] ) ) : ?>
            <div class="doc_title text-center">
				<?php
                echo ! empty( $settings['title'] ) ? sprintf( '<%1$s class="title" data-animation="wow fadeInUp" data-wow-delay="0.2s">%2$s</%1$s>',
	                esc_html( $title_tag ),
					nl2br( esc_html( $settings['title'] ) )
				) : '';
                ?>
				<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
                    <p class="subtitle wow fadeInUp" data-wow-delay="0.4s">
						<?php echo wp_kses_post( $settings['subtitle'] ) ?> </p>
				<?php endif; ?>
            </div>
		<?php endif; ?>
        <div class="ezd-container">
            <div class="ezd-grid ezd-mobile-column ezd-column-<?php echo esc_attr( $ppp_column ); ?>">
				<?php
				$delay = 0.2;
				foreach ( $sections as $section ) :
					$doc_items = get_children( array(
						'post_parent'    => $section->ID,
						'post_type'      => 'docs',
						'post_status'    => 'publish',
						'orderby'        => 'menu_order',
						'order'          => 'ASC',
						'posts_per_page' => ! empty( $settings['ppp_doc_items'] ) ? $settings['ppp_doc_items'] : - 1,
					));
					?>
                    <div class="recommended_item box-item wow fadeInUp" data-wow-delay="<?php echo esc_attr( $delay ) ?>s">
						<?php
						if ( has_post_thumbnail( $section->ID ) ) {
							echo get_the_post_thumbnail( $section->ID, 'full' );
						}

						if ( ! empty( $section->post_title ) ) { ?>
                            <a href="<?php the_permalink( $section->ID ); ?>">
                                <h3 class="ct-heading-text"> <?php echo wp_kses_post( $section->post_title ); ?> </h3>
                            </a>
							<?php
						}

						if ( ! empty( $doc_items ) ) : ?>
                            <ul class="ezd-list-unstyled">
								<?php
								foreach ( $doc_items as $doc_item ) :
									?>
                                    <li>
                                        <a class="ct-content-text" href="<?php the_permalink( $doc_item->ID ) ?>">
											<?php echo wp_kses_post( $doc_item->post_title ) ?>
                                        </a>
                                    </li>
								    <?php
								endforeach;
								?>
                            </ul>
						<?php
						endif;
						?>
                    </div>
					<?php
					$delay = $delay + 0.1;
				endforeach;
				?>
            </div>
        </div>

		<?php
		if ( $settings['section_btn'] == 'yes' && ! empty( $settings['section_btn_txt'] ) ) : ?>
            <div class="text-center wow fadeInUp" data-wow-delay="0.2s">
                <a href="<?php echo esc_url( $settings['section_btn_url'] ); ?>" class="question_text">
					<?php echo wp_kses_post( $settings['section_btn_txt'] ) ?>
                </a>
            </div>
		<?php
		endif;
		?>
    </div>
</section>