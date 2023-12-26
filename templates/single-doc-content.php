<?php
eazydoocs_set_post_view();
$options                    = get_option( 'eazydocs_settings' );
$comment_visibility         = $options['enable-comment'] ?? '1';
$reading_time_visibility    = $options['enable-reading-time'] ?? '1';
$views_visibility           = $options['enable-views'] ?? '1';
$docs_feedback              = $options['docs-feedback'] ?? '1';
$sidebar_toggle             = $options['toggle_visibility'] ?? '1';
$layout                     = $options['docs_single_layout'] ?? 'both_sidebar';

if( $sidebar_toggle == 1 ) :
    if ( ! empty( $layout == 'left_sidebar' ) || ! empty( $layout == 'both_sidebar' ) ) : ?>
        <div class="left-sidebar-toggle">
            <span class="left-arrow arrow_triangle-left" title="<?php esc_attr_e( 'Hide category', 'eazydocs' ); ?>" style="display: block;"></span>
            <span class="right-arrow arrow_triangle-right" title="<?php esc_attr_e( 'Show category', 'eazydocs' ); ?>" style="display: none;"></span>
        </div>
        <?php
    endif;
endif;
?>

<article class="shortcode_info" itemscope itemtype="http://schema.org/Article">
    <div class="doc-post-content" id="post">
        <div class="shortcode_title">
            <?php the_title( '<h1>', '</h1>' ); ?>

            <?php
            if( $reading_time_visibility == '1' ||  $views_visibility == '1' ) : ?>
                <div class="ezd-meta dot-sep">
                    <?php
                        if( $reading_time_visibility == '1') : ?>
                            <span class="read-time">
                                <?php esc_html_e( 'Estimated reading: ', 'eazydocs' ); ezd_reading_time(); ?>
                            </span>
                            <?php   
                        endif;
                        
                        if( $views_visibility == '1') : ?>
                            <span class="views sep">
                                <?php echo eazydocs_get_post_view(); ?>
                            </span> 
                            <?php 
                        endif;
                        
                        do_action('eazydocs_docs_contributor', get_the_ID());
                    ?>
                </div>
                <?php
            endif;
            ?>

        </div>
        <div class="doc-scrollable editor-content">
            <?php
            if ( has_post_thumbnail() && ezd_get_opt('is_featured_image') == '1' ) {
	            the_post_thumbnail( 'full', array( 'class' => 'mb-3' ) );
            }
            if ( ezd_get_opt('is_excerpt') == '1' && has_excerpt() ) {
                ?>
                <p class="doc-excerpt alert alert-info">
                    <strong><?php echo ezd_get_opt('excerpt_label', 'Summary');; ?></strong>
                    <?php echo get_the_excerpt(); ?>
                </p>
                <?php
            }

            the_content();

            eazydocs_get_template_part( 'single-doc-home' );
            $children = ezd_list_pages( "title_li=&order=menu_order&child_of=" . $post->ID . "&echo=0&post_type=" . $post->post_type );
            if ( $children && $post->post_parent != 0 ) {
                echo '<div class="details_cont ent recently_added" id="content_elements">';
                echo '<h4 class="c_head">' . esc_html__( 'Articles', 'eazydocs' ) . '</h4>';
                echo '<ul class="article_list">';
                echo ezd_list_pages( "title_li=&order=menu_order&child_of=" . $post->ID . "&echo=0&post_type=" . $post->post_type );
                echo '</ul>';
                echo '</div>';
            }
            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Docs:', 'eazydocs' ),
                'after'  => '</div>',
            ));
            ?>
        </div>
    </div>
    <?php
    if( $docs_feedback == '1' ) {
        eazydocs_get_template_part('content-feedback');
    }
    ?>
</article>

<?php
eazydocs_get_template_part('content-related');

if ( $comment_visibility == '1' )  :
	if ( comments_open() || get_comments_number() )  :
		?>
<div class="eazydocs-comments-wrap">
    <?php comments_template(); ?>
</div>
<?php
	endif;
endif;