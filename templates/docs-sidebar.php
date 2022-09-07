<?php
global $post;
$ancestors      = array();
$root           = $parent = false;
if ( $post->post_parent ) {
	$ancestors  = get_post_ancestors( $post->ID );
	$root       = count( $ancestors ) - 1;
	$parent     = $ancestors[ $root ];
} else {
	$parent     = $post->ID;
}

// var_dump( $parent, $ancestors, $root );
$walker         = new eazyDocs\Frontend\Walker_Docs();
$children       = wp_list_pages( array(
	'title_li'  => '',
	'order'     => 'menu_order',
	'child_of'  => $parent,
	'echo'      => false,
	'post_type' => 'docs',
	'walker'    => $walker,
    'post_status' => array( 'publish', 'private' ),
));

$options = get_option( 'eazydocs_settings' );
$sidebar_search = $options['search_visibility'] ?? '1';
$content_layout = $options['docs_content_layout'] ?? '1';

?>

<div class="col-xl-3 col-lg-3 doc_mobile_menu left-column sticky-lg-top">
    <aside class="doc_left_sidebarlist">
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

		if ( $children ) :
			$catgory_layout = '';
			if ( $content_layout == 'category_base' && class_exists('EazyDocsPro') ) {
				$doc_walker = '';
				$catgory_layout = 'content-layout-category';
			} else {
				$doc_walker = $walker;
			}
			?>

            <div class="scroll">
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
            </div>
			<?php
		endif;
		?>
    </aside>
</div>