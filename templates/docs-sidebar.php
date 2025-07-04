<?php
global $post;
$ancestors      	= array();
$root           	= $parent = false;
if ( $post->post_parent ) {
	$ancestors  	= get_post_ancestors( $post->ID );
	$root       	= count( $ancestors ) - 1;
	$parent     	= $ancestors[ $root ];
} else {
	$parent     	= $post->ID;
}

$walker = new eazyDocs\Frontend\Walker_Docs();
$children = array(
    'title_li'    => '',
    'order'       => 'menu_order',
    'echo'        => false,
    'post_type'   => 'docs',
    'walker'      => $walker,
    'post_status' => current_user_can( 'read_private_docs' ) ? [ 'publish', 'private' ] : ['publish'],
);

// If 'Self Docs' is selected, set 'child_of' to filter by the current doc.
$sidebar_source = ezd_get_opt('docs_to_view', 'self_docs');
if ( $sidebar_source === 'self_docs' || ! class_exists( 'EZD_EazyDocsPro' )  ) {
    $children['child_of'] = $parent;
}

$children           = wp_list_pages($children);
$sidebar_search 	= ezd_get_opt( 'search_visibility', '1' );
$content_layout 	= ezd_get_opt( 'docs_content_layout', '1' );
$nav_sidebar_active = '';

if ( class_exists('EZD_EazyDocsPro') && $content_layout == 'category_base' ){
	$nav_sidebar_active = 'nav_category_layout';
}

$credit_enable   	= '1';
$credit_text_wrap 	= '';
if ( ezd_is_premium() ) {
	$credit_enable 	= ezd_get_opt( 'eazydocs-enable-credit', '1' );
}

if ( $credit_enable == '1' ) {
	$credit_text_wrap = 'credit-text-container';
}
?>
<div class="ezd-xl-col-3 ezd-lg-col-3 ezd-grid-column-full doc_mobile_menu left-column ezd-sticky-lg-top">
    <aside class="doc_left_sidebarlist <?php echo esc_attr( $credit_text_wrap .' '. $nav_sidebar_active ); ?>">
        <div class="open_icon" id="mobile-left-toggle">
            <i class="arrow_carrot-right"></i>
            <i class="arrow_carrot-left"></i>
        </div>
        <h2 class="doc-title">
            <?php 
            if ( ezd_is_premium() && ! empty ( get_post_meta( $parent, 'ezd_doc_secondary_title', true ) ) ) {
                echo esc_html( get_post_meta( $parent, 'ezd_doc_secondary_title', true ) );
            } else {
                echo get_post_field( 'post_title', $parent, 'display' );
            }
            ?>
        </h2>
        <?php
        if ( $sidebar_search == 1 ) :
            ?>
        <div class="filter_form">
            <div class="filterform">
                <input id="doc_filter" type="search" name="filter" placeholder="<?php esc_attr_e( 'Filter', 'eazydocs' ); ?>" data-uw-styling-context="true">
            </div>
        </div>
        <?php
        endif;
		?>

        <div class="ezd-scroll">
            <?php
            if ( $children ) :
                if ( $content_layout == 'category_base' && ezd_is_premium() ) {
                    $doc_walker = '';
                } else {
                    $doc_walker = $walker;
                }
            ?>
            <ul class="ezd-list-unstyled nav-sidebar left-sidebar-results ezd-list-unstyled">
                <?php
                $args = [
                    'title_li'    => '',
                    'order'       => 'menu_order',
                    'echo'        => false,
                    'post_type'   => 'docs',
                    'walker'      => $doc_walker,
                    'post_status' => current_user_can( 'read_private_docs' ) ? [ 'publish', 'private' ] : ['publish'],
                ];

                // If 'Self Docs' is selected, set 'child_of' to filter by the current doc.
                if ( $sidebar_source === 'self_docs' || ! class_exists( 'EZD_EazyDocsPro' )  ) {
                    $args['child_of'] = $parent;
                }
                echo wp_kses_post(wp_list_pages($args));
                ?>
            </ul>
            <?php
            endif;

            echo '<div class="additional-content">';
                $parent_doc_id_left      = function_exists('get_root_parent_id') ? get_root_parent_id( get_queried_object_id() ) : '';
                $content_type_left       = get_post_meta( $parent_doc_id_left, 'ezd_doc_left_sidebar_type', true );
                $ezd_shortcode_left      = get_post_meta( $parent_doc_id_left, 'ezd_doc_left_sidebar', true );
                $is_valid_post_id   	 = is_null( get_post( $ezd_shortcode_left ) ) ? 'No' : 'Yes';

                if ( $content_type_left  == 'string_data'  && ! empty ( $ezd_shortcode_left ) ) {
                    echo do_shortcode( html_entity_decode( $ezd_shortcode_left ) );
                } else {
                    if( $content_type_left == 'widget_data' && ! empty( $is_valid_post_id ) ) {
                        $wp_blocks = new WP_Query([
                            'post_type' 	=> 'wp_block',
                            'p'				=> $ezd_shortcode_left
                        ]);
                        if ( $wp_blocks->have_posts() ) {
                            while( $wp_blocks->have_posts() ) : $wp_blocks->the_post();
                            the_content();
                            endwhile;
                        wp_reset_postdata();
                        }
                    }
                }
            echo '</div>';
            ?>
        </div>
    </aside>
</div>