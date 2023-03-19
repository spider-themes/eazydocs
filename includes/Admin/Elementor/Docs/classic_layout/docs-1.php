<?php
$opt                    = get_option( 'eazydocs_settings' );
$topics_count           = $opt['topics_count'] ?? '1';
$topics                 = $opt['topics_text'] ?? esc_html__( 'Topics', 'eazydocs' );
$private_doc_mode       = $opt['private_doc_mode'] ?? '';
$child_doc_order        = $opt['docs-order'] ?? 'ASC';
$private_doc_login_page = $opt['private_doc_login_page'] ?? '';

// Child docs per page
$layout                 = 'grid';

// Check pro plugin class exists
if ( ezd_is_premium() ) {
	$layout             = $opt['docs-archive-layout'] ?? $layout; // id of field
}
?>

<div class="eazydocs_shortcode">
    <div class="container">
        <div class="row">
            <?php  
            $terms = get_terms( array(
                'taxonomy'      => 'doc_category',
                'hide_empty'    => true,
                'number'        => 0,
                'orderby'       => 'menu_order',
                'order'         => $doc_order ?? 'ASC',
                'exclude'       => $doc_exclude,
            ) );
            foreach( $terms as $term ) :
                // count posts under category
                $args = array(
                    'post_type'         => 'docs',
                    'posts_per_page'    => $doc_number,
                    'post_status'       => array( 'publish', 'private' ),
                    'orderby'           => 'custom_sort',
                    'order'             => $child_doc_order,
                    'tax_query' => array(
                        array(
                            'taxonomy'          => 'doc_category',
                            'field'             => 'term_id',
                            'terms'             => $term->term_id,
                            'include_children'  => false
                        )
                    )
                );
                $query          = new WP_Query( $args );
                $get_url        = get_term_link($term);
                $count          = $query->found_posts;
                wp_reset_postdata();
                ?>

                <div class="col-lg-4">
                    <div class="categories_guide_item wow fadeInUp">
                        <div class="doc-top d-flex align-items-start">
                            <a class="doc_tag_title" href="<?php echo esc_url($get_url); ?>">
                                <h4 class="title"> <?php echo esc_html($term->name); ?> </h4>
                                <?php 
                                if ( $topics_count == '1' ) :
                                    ?>
                                    <span class="badge"> <?php echo esc_html($count .' '. $topics); ?> </span>
                                    <?php
                                endif;
                                ?>
                            </a>
                        </div>
                        
                        <ul class="list-unstyled article_list">
                            <?php
                            if ( $query->have_posts() ) {
                                while ( $query->have_posts() ) {
                                    $query->the_post();

                                    $main_doc_url = get_permalink( get_the_ID() ); 
                                    if ( ezd_is_premium() ) {
                                        if ( $private_doc_mode == 'login' ) {
                                            if ( get_post_status(get_the_ID()) == 'private' ){
                                                $login_page_id  = get_post_field( 'post_name', $private_doc_login_page );
                                                $current_doc_id = get_post_field( 'post_name', get_the_ID() );
                                                $get_post_type  = get_post_type(get_the_ID());
                                                if ( is_user_logged_in() ) {
                                                    $main_doc_url = site_url($get_post_type.'/'.$current_doc_id);
                                                } else {
                                                    $main_doc_url = site_url($login_page_id.'?after_login=').site_url($get_post_type.'/'.$current_doc_id.'&add_new_doc=yes');
                                                }
                                            } else {
                                                $main_doc_url = get_permalink( get_the_ID() );
                                            }
                                        }
                                    } else {
                                        $main_doc_url = get_permalink( get_the_ID() );
                                    }

                                    ?>
                                    <li>
                                        <a href="<?php echo esc_url($main_doc_url); ?>"> 
                                            <?php the_title(); ?>
                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                            wp_reset_postdata();
                            ?>
                        </ul>

                        <a href="<?php echo esc_url($get_url); ?>" class="doc_border_btn">
                            <?php echo esc_html( $read_more ); ?>
                            <i class="arrow_right"></i>
                        </a>
                        
                    </div>
                </div>
                <?php 
            endforeach;
            ?>
        </div>
    </div>
</div>