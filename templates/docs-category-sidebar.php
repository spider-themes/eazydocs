 
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

$options            = get_option( 'eazydocs_settings' );
$sidebar_search 	= $options['search_visibility'] ?? '1';
$content_layout 	= $options['docs_content_layout'] ?? '1';
$nav_sidebar_active = '';

if ( class_exists('EazyDocsPro') && $content_layout == 'category_base' ){
	$nav_sidebar_active = 'nav_category_layout';
}

$credit_enable   	= '1';
$credit_text_wrap 	= '';
if ( ezd_is_premium() ) {
	$credit_enable 	= $options['eazydocs-enable-credit'] ?? '1';
}

if ( $credit_enable == '1' ) {
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
        
        <div class="scroll">
            <ul class="list-unstyled nav-sidebar left-sidebar-results">
                <?php
                $terms = get_terms( array(
                    'taxonomy'      => 'doc_category',
                    'hide_empty'    => true,
                    'parent'        => 0, // get top level categories only
                ) );
                foreach( $terms as $term ) :
                ?>
                <li <?php echo post_class("nav-item has_child page_item"); ?>> 
                    <div class="doc-link"> 
                        <a href="javascript:void(0);" data-postid="<?php echo $term->term_id; ?>" class="nav-link">
                            <img class="closed" src="<?php echo EAZYDOCS_IMG; ?>/icon/folder-closed.png" alt="<?php echo esc_attr_e( 'Folder icon closed', 'eazydocs' ); ?>"> 
                            <img class="open" src="<?php echo EAZYDOCS_IMG; ?>/icon/folder-open.png" alt="<?php echo esc_attr_e( 'Folder open icon', 'eazydocs' ); ?>">                        
                            <?php echo esc_html($term->name); ?>
                        </a>
                        <span class="icon"><i class="arrow_carrot-down"></i></span>
                    </div>
                    <ul class="dropdown_nav" style="display: none;">

                        <?php
                        // get posts under category
                        $args = array(
                            'post_type'         => 'docs',
                            'posts_per_page'    => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy'          => 'doc_category',
                                    'field'             => 'term_id',
                                    'terms'             => $term->term_id,
                                    'include_children'  => false
                                )
                            )
                        );
                        $query = new WP_Query( $args );
                        if ( $query->have_posts() ) {
                            while ( $query->have_posts() ) {
                                $query->the_post();

                                // if it's current post, add active class
                                $active = '';
                                if ( get_the_ID() == get_queried_object_id() ) {
                                   $active = ' actived';
                                }
                                ?>
                                <li <?php echo post_class("nav-item no_icon page_item depth2".$active); ?>> 
                                    <a href="<?php the_permalink(); ?>" data-postid="<?php the_ID(); ?>"><?php the_title(); ?></a>
                                </li>
                                <?php
                            }
                        }
                        wp_reset_postdata();
                        
                        $child_terms = get_terms( array(
                            'taxonomy'      => 'doc_category',
                            'hide_empty'    => true,
                            'parent'        => $term->term_id,
                        ) );
                        foreach( $child_terms as $term2 ) : 
                            ?> 
                            <li <?php echo post_class("nav-item no_icon has_child page_item"); ?>>
                                <div class="doc-link"> 
                                    <a href="javascript:void(0);" data-postid="2255" class="nav-link">
                                        <?php echo $term2->name; ?>
                                    </a>
                                    <span class="icon"><i class="arrow_carrot-down"></i></span> 
                                </div>
                                <ul class="dropdown_nav" style="display: none;">

                                    <?php
                                    // get posts under category
                                    $args = array(
                                        'post_type'         => 'docs',
                                        'posts_per_page'    => -1,
                                        'tax_query'         => array(
                                            array(
                                                'taxonomy'          => 'doc_category',
                                                'field'             => 'term_id',
                                                'terms'             => $term2->term_id,
                                                'include_children'  => false
                                            )
                                        )
                                    );
                                    $query = new WP_Query( $args );
                                    if ( $query->have_posts() ) {
                                        while ( $query->have_posts() ) {
                                            $query->the_post();

                                            $active     = '';
                                            if ( get_the_ID() == get_queried_object_id() ) {
                                                $active = ' actived';
                                            }

                                            ?>
                                            <li <?php echo post_class("nav-item no_icon page_item depth3". $active); ?>> 
                                                <a href="<?php the_permalink(); ?>" data-postid="<?php the_ID(); ?>"><?php the_title(); ?></a>
                                            </li>
                                            <?php
                                        }
                                    }
                                    wp_reset_postdata();
                                    
                                    // get child categories
                                    $child_terms2 = get_terms( array(
                                        'taxonomy'      => 'doc_category',
                                        'hide_empty'    => true,
                                        'parent'        => $term2->term_id,
                                    ) );
                                    foreach( $child_terms2 as $term3 ) : 
                                        ?>                                    
                                        <li <?php echo post_class("nav-item no_icon has_child page_item"); ?>>
                                            <div class="doc-link"> 
                                                <a href="javascript:void(0);" class="nav-link">
                                                    <?php echo $term3->name; ?>
                                                </a>
                                                <span class="icon"><i class="arrow_carrot-down"></i></span> 
                                            </div>
                                            
                                            <ul class="dropdown_nav" style="display: none;">
                                                <?php
                                                // get posts under category
                                                $args = array(
                                                    'post_type'         => 'docs',
                                                    'posts_per_page'    => -1,
                                                    'tax_query' => array(
                                                        array(
                                                            'taxonomy'          => 'doc_category',
                                                            'field'             => 'term_id',
                                                            'terms'             => $term3->term_id,
                                                            'include_children'  => false
                                                        )
                                                    )
                                                );

                                                $query = new WP_Query( $args );
                                                if ( $query->have_posts() ) {
                                                    while ( $query->have_posts() ) {
                                                        $query->the_post();

                                                        $active     = '';
                                                        if ( get_the_ID() == get_queried_object_id() ) {
                                                            $active = ' actived';
                                                        }
                                                        ?>
                                                        <li <?php echo post_class("nav-item no_icon page_item depth4". $active); ?>> 
                                                            <a href="<?php the_permalink(); ?>" data-postid="<?php the_ID(); ?>"><?php the_title(); ?></a>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                wp_reset_postdata();
                                                ?>
                                                </ul>
                                                <?php 
                                            endforeach;
                                            ?>
                                        </ul>
                                    </li>
                                <?php 
                            endforeach; 
                            ?>
                        </ul>
                    </li>
                    <?php 
                endforeach;
                ?>
            </ul>
            <div class="additional-content"></div>		
        </div>
    </aside>
</div>