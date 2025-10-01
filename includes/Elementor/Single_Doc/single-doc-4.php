<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="ezd-grid ezd-column-<?php echo esc_attr( $ppp_column ); ?> h_content_items">
    <?php
	foreach ( $sections as $section ) :
		?>
        <a href="<?php the_permalink( $section->ID ) ?>">
            <div class="h_item">
                <?php echo ! empty( $section->ID ) ? get_the_post_thumbnail( $section->ID, 'full' ) : ''; ?>
                <h4 class="ct-heading-text"><?php echo wp_kses_post( $section->post_title ); ?></h4>
                <div class="ct-content-text">
                    <?php
                        if ( strlen( trim( $section->post_excerpt ) ) != 0 ) {
	                        echo wp_kses_post( wpautop( $section->post_excerpt ) );
                        } else {
	                        echo wp_kses_post( wpautop( wp_trim_words( $section->post_content, $settings['doc_sec_excerpt'], '' ) ) );
                        }
                    ?>
                </div>
            </div>
        </a>
        <?php
	endforeach;
	?>
</div>

<?php
$sections2 = get_children( array(
	'post_parent'    => $settings['doc'],
	'post_type'      => 'docs',
	'post_status'    => 'publish',
	'orderby'        => 'menu_order',
	'order'          => $settings['order'],
	'posts_per_page' => ! empty( $settings['ppp_sections2'] ) ? $settings['ppp_sections2'] : - 1,
	'offset'         => ! empty( $settings['ppp_sections'] ) ? $settings['ppp_sections'] : 8
) );
?>
<div class="h_content_items box-item collapse-wrap">
    <div class="ezd-grid <?php echo esc_attr( $ppp_column ); ?>">
        <?php
		foreach ( $sections2 as $section ) :
			?>
            <a href="<?php the_permalink( $section->ID ); ?>">
                <div class="h_item">
                    <?php echo ! empty( $section->ID ) ? get_the_post_thumbnail( $section->ID, 'full' ) : ''; ?>
                    <h4 class="ct-heading-text"><?php echo wp_kses_post( $section->post_title ); ?></h4>
                    <div class="ct-content-text">
                        <?php
                            if ( strlen( trim( $section->post_excerpt ) ) != 0 ) {
                                echo wp_kses_post( wpautop( $section->post_excerpt ) );
                            } else {
                                echo wp_kses_post( wpautop( wp_trim_words( $section->post_content, $settings['doc_sec_excerpt'], '' ) ) );
                            }
                        ?>
                    </div>
                </div>
            </a>
            <?php
		endforeach;
		?>
    </div>
</div>

<?php 
if ( ! empty( $settings['show_more_btn'] ) ) : 
	?>
    <div class="more text-center">
        <a class="icon_btn2 blue collapse-btn" href="#">
            <span>
                <ion-icon name="caret-down-circle-outline"></ion-icon>
                <?php echo wp_kses_post( $settings['show_more_btn'] ); ?>
            </span>
            <span>
                <ion-icon name="caret-up-circle-outline"></ion-icon>
                <?php echo wp_kses_post( $settings['show_less_btn'] ); ?>
            </span>
        </a>
    </div>
    <script type="text/javascript">
        ;(function($) {
            "use strict";
            
            $(document).ready(function() {
                function general() {
                    if ($('.collapse-btn').length > 0) {
                        $('.collapse-btn').on('click', function(e) {
                            e.preventDefault();
                            $(this).toggleClass('active');
                            $('.collapse-wrap').slideToggle(500);
                        });
                    }
                }
                general();
            });

        })(jQuery);
    </script>
    <?php 
endif;