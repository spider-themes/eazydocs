<?php
global $post;
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
        $permalink = get_permalink( $section->ID );
        ?>
        <a href="<?php echo esc_url( $permalink ); ?>" class="media documentation_item">
            <div class="icon">
                <?php
                if ( has_post_thumbnail( $section->ID ) ) {
                    echo get_the_post_thumbnail( $section->ID, 'full' );
                } else {
                    $default_icon = esc_url(EAZYDOCS_IMG) . '/icon/folder.png';
	                echo '<img src="' . esc_url( $default_icon ) . '" alt="' . esc_attr( $section->post_title ) . '">';

                }
                ?>
            </div>
            <div class="media-body">
                <div class="doc-sec-header">
                    <div class="doc-sec title">
                        <?php echo esc_html($section->post_title); ?>
                    </div>
                    <?php echo function_exists('ezdpro_badge') ? ezdpro_badge( $section->ID ) : ''; ?>
                </div>
                <p> 
                <?php
                if ( ! $is_full_excerpt ) {
                    echo has_excerpt( $section->ID ) ? wp_kses_post(wp_trim_words( get_the_excerpt( $section->ID ), $sec_excerpt, '' )) : '';
                } else {
                    echo has_excerpt( $section->ID ) ? wp_kses_post(get_the_excerpt( $section->ID )) : '';
                }
                ?>
                </p>
            </div>
        </a>
        <?php
    endforeach;
    ?>
    </div>
    <?php
endif;
?>
