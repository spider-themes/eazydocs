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
		<?php ezd_render_bg_shape( $settings ); ?>
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
					$doc_items = ezd_get_doc_items( $section->ID, $settings );
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

						ezd_render_doc_items_list( $doc_items, 'ezd-list-unstyled', 'ct-content-text' );
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