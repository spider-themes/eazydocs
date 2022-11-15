<div class="row h_content_items">
	<?php
	foreach ( $sections as $section ) :
		?>
        <div class="col-lg-3 col-sm-6">
            <a href="<?php echo get_permalink( $section->ID ) ?>">
                <div class="h_item">
					<?php echo ! empty( $section->ID ) ? get_the_post_thumbnail( $section->ID, 'full' ) : ''; ?>
                    <h4 class="ct-heading-text"><?php echo wp_kses_post( $section->post_title ); ?></h4>
                    <div class="ct-content-text">
						<?php
						if ( strlen( trim( $section->post_excerpt ) ) != 0 ) {
							echo wpautop( $section->post_excerpt );
						} else {
							echo wpautop( wp_trim_words( $section->post_content, $settings['doc_sec_excerpt'], '' ) );
						}
						?>
                    </div>
                </div>
            </a>
        </div>
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
	'offset'         => ! empty( $settings['ppp_sections'] ) ? $settings['ppp_sections'] : 8,
) );
?>
<div class="h_content_items box-item collapse-wrap">
    <div class="row">
		<?php
		foreach ( $sections2 as $section ) :
			?>
            <div class="col-lg-3 col-sm-6">
                <a href="<?php echo get_permalink( $section->ID ) ?>">
                    <div class="h_item">
						<?php echo ! empty( $section->ID ) ? get_the_post_thumbnail( $section->ID, 'full' ) : ''; ?>
                        <h4 class="ct-heading-text"><?php echo wp_kses_post( $section->post_title ); ?></h4>
                        <div class="ct-content-text">
							<?php
							if ( strlen( trim( $section->post_excerpt ) ) != 0 ) {
								echo wpautop( $section->post_excerpt );
							} else {
								echo wpautop( wp_trim_words( $section->post_content, $settings['doc_sec_excerpt'], '' ) );
							}
							?>
                        </div>
                    </div>
                </a>
            </div>
		<?php
		endforeach;
		?>
    </div>
</div>
<?php if ( ! empty( $settings['show_more_btn'] ) ) : ?>
    <div class="more text-center">
        <a class="icon_btn2 blue collapse-btn" href="#">
            <span> <ion-icon name="caret-down-circle-outline"></ion-icon><?php echo $settings['show_more_btn'] ?></span>
            <span> <ion-icon name="caret-up-circle-outline"></ion-icon><?php echo $settings['show_less_btn'] ?></span>
        </a>
    </div>
<?php endif; ?>