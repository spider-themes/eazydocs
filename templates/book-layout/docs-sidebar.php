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
$walker         = new eazyDocs\Frontend\Walker_Book_Layout();
$children       = wp_list_pages( array(
	'title_li'  => '',
	'order'     => 'menu_order',
	'child_of'  => $parent,
	'echo'      => false,
	'post_type' => 'docs',
	'walker'    => $walker,
));

$options                    = get_option( 'eazydocs_settings' );
$sidebar_search             = '1';
if ( class_exists( 'EazyDocsPro' ) ) {
	$sidebar_search         = $options['search_visibility'] ?? '1';
}
?>
<div class="col-xl-3 col-lg-3 doc_mobile_menu left-column sticky-top">
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
			?>
            <div class="scroll">
                <ul class="list-unstyled nav-sidebar left-sidebar-results">
					<?php
					echo wp_list_pages( array(
						'title_li'  => '',
						'order'     => 'menu_order',
						'child_of'  => $parent,
						'echo'      => false,
						'post_type' => 'docs',
						'walker'    => $walker,
					) );
					?>
                </ul>
            </div>
		<?php
		endif;
		?>
    </aside>
</div>