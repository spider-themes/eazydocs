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

// var_dump( $parent, $ancestors, $root );
$walker         	= new eazyDocs\Frontend\Walker_Docs();
$children 			= wp_list_pages( array(
	'title_li'  	=> '',
	'order'     	=> 'menu_order',
	'child_of'  	=> $parent,
	'echo'      	=> false,
	'post_type' 	=> 'docs',
	'walker'    	=> $walker,
    'post_status' 	=> array( 'publish', 'private' ),
));

$options = get_option( 'eazydocs_settings' );
$sidebar_search 	= $options['search_visibility'] ?? '1';
$content_layout 	= $options['docs_content_layout'] ?? '1';
$nav_sidebar_active = '';
if( class_exists( 'EazyDocsPro') && $content_layout == 'category_base' ){
	$nav_sidebar_active = 'nav_category_layout';
}
$credit_enable   	= '1';
$credit_text_wrap 	= '';
if ( class_exists( 'EazyDocsPro' ) ) {
	$credit_enable 	= $options['eazydocs-enable-credit'] ?? '1';
}
if( $credit_enable == '1' ){
	$credit_text_wrap = 'credit-text-container';
}
?>

<div class="col-xl-3 col-lg-3 doc_mobile_menu left-column sticky-lg-top">
    <aside class="doc_left_sidebarlist <?php echo esc_attr( $credit_text_wrap .' '. $nav_sidebar_active ); ?>">
        <div class="open_icon" id="left">
            <i class="arrow_carrot-right"></i>
            <i class="arrow_carrot-left"></i>
        </div>
        <h2 class="doc-title">
			<?php echo get_post_field( 'post_title', $parent, 'display' ); ?>
        </h2>
        <?php
        if( $sidebar_search == 1 ) :
            ?>
            <div class="filter_form">
                <div class="filterform">
                    <input id="doc_filter" type="search" name="filter" placeholder="<?php esc_attr_e( 'Filter', 'eazydocs' ); ?>" data-uw-styling-context="true">
                </div>
            </div>
            <?php
        endif;
		?>

		<div class="scroll">
		<?php
			if ( $children ) :
				$catgory_layout = '';
				if ( $content_layout == 'category_base' && class_exists('EazyDocsPro') ) {
					$doc_walker = '';
					$catgory_layout = 'content-layout-category';
				} else {
					$doc_walker = $walker;
				}
			?>
			<ul class="list-unstyled nav-sidebar left-sidebar-results <?php echo esc_attr($catgory_layout); ?>">
				<?php
				echo wp_list_pages( array(
					'title_li'  => '',
					'order'     => 'menu_order',
					'child_of'  => $parent,
					'echo'      => false,
					'post_type' => 'docs',
					'walker'    => $doc_walker,
					'post_status' => array( 'publish', 'private' ),
				) );
				?>
			</ul>
			<?php
		endif;

        echo '<div class="additional-content">';
            $parent_doc_id_left      = function_exists('get_root_parent_id') ? get_root_parent_id( get_queried_object_id() ) : '';
            $content_type_left       = get_post_meta( $parent_doc_id_left, 'ezd_doc_left_sidebar_type', true );
            $ezd_shortcode_left      = get_post_meta( $parent_doc_id_left, 'ezd_doc_left_sidebar', true );
			$is_valid_post_id   	 = is_null( get_post( $ezd_shortcode_left ) ) ? 'No' : 'Yes';

            if ( $content_type_left  == 'string_data' ) {
                echo html_entity_decode( $ezd_shortcode_left ) ?? '';
            } elseif ( $content_type_left == 'shortcode' ) {
                echo do_shortcode( html_entity_decode( $ezd_shortcode_left ) );
            } else {
            $ezd_shortcode_left      = get_post_meta( $parent_doc_id_left, 'ezd_doc_left_sidebar', true );
			if( $is_valid_post_id == 'Yes' ) {
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