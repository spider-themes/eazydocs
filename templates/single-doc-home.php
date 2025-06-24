<?php
$is_full_excerpt 		 = ezd_get_opt( 'is_full_excerpt', false );
$sec_excerpt 			 = ezd_get_opt( 'doc_sec_excerpt_limit', '8' );

$sections = get_children( array(
	'post_parent'    => $post->ID,
	'post_type'      => 'docs',
	'post_status'    => [ 'publish', 'private' ],
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'posts_per_page' => -1,
    ''
) );

if ( $sections && $post->post_parent === 0 ) :
    ?>
    <div class="d-items doc-items">
    <?php
    foreach ( $sections as $section ) : 
        ?>
        <div class="media documentation_item">
            <div class="icon bs-sm">
                <?php
                if ( has_post_thumbnail( $section->ID ) ) {
                    echo get_the_post_thumbnail( $section->ID, 'full' );
                } else {
                    $default_icon = EAZYDOCS_IMG . '/icon/folder.png';
	                echo '<img src="' . esc_url( $default_icon ) . '" alt="' . esc_attr( $section->post_title ) . '">';

                }
                ?>
            </div>
            <div class="media-body">
                <a href="<?php echo get_permalink( $section->ID ); ?>" class="doc-sec title">
                    <?php echo esc_html($section->post_title); ?>
                </a>
                <p> 
                <?php 
                if ( $is_full_excerpt == false ) {
                    echo has_excerpt( $section->ID ) ? wp_trim_words( get_the_excerpt( $section->ID ), $sec_excerpt, '' ) : '';
                } else {
                    echo has_excerpt( $section->ID ) ? get_the_excerpt( $section->ID ) : '';
                }
                ?>
                </p>
            </div>
        </div>
        <?php 
    endforeach;
    ?>
    </div>
    <?php
endif;