<?php
$options                = get_option( 'eazydocs_settings' );
$sec_excerpt            = '8';
if ( class_exists( 'EazyDocsPro' ) ) {
	$sec_excerpt        = $options['doc_sec_excerpt_limit'] ?? '8';
}
$sections = get_children( array(
	'post_parent'    => $post->ID,
	'post_type'      => 'docs',
	'post_status'    => 'publish',
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'posts_per_page' => - 1,
) );

if ( $sections ) :
    ?>
    <div class="row doc-items mt-5">
		<?php
		foreach ( $sections as $section ) : ?>
            <div class="col-lg-6 col-sm-6">
                <div class="media documentation_item">
                    <div class="icon">
						<?php
						if ( has_post_thumbnail( $section->ID ) ) {
							echo get_the_post_thumbnail( $section->ID, 'full' );
						} else {
							$default_icon = EAZYDOCS_IMG . '/icon/folder.png';
							echo "<img src='$default_icon' alt='{$section->post_title}'>";
						}
						?>
                    </div>
                    <div class="media-body">
                        <a href="<?php echo get_permalink( $section->ID ); ?>" class="doc-sec title">
                            <?php echo wp_kses_post( $section->post_title ); ?>
                        </a>
                        <p> <?php echo wp_trim_words( $section->post_content, $sec_excerpt, '' ); ?> </p>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
    <?php
endif;