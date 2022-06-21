<?php
$options                = get_option( 'eazydocs_settings' );

$sec_excerpt        = '333';

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
            <div class="documdentation_item" id="<?php echo wp_kses_post( $section->post_name ); ?>">
                <h1>
                    <?php echo wp_kses_post( $section->post_title ); ?>
                </h1>
                <?php echo wpautop( wp_trim_words( $section->post_content, $sec_excerpt, '' ) ); ?>
            </div>
		<?php endforeach; ?>
    </div>
    <?php
endif;