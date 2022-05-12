<?php
eazydocs_set_post_view();
$cz_options             = '';
$layout                 = 'both_sidebar';
$cz_options             = '';
$options                = get_option( 'eazydocs_settings' );
$comment_visibility          = $options['enable-comment'] ?? '1';
$reading_time_visibility     = $options['enable-reading-time'] ?? '1';
$views_visibility            = $options['enable-views'] ?? '1';
$docs_feedback          = '1';
if ( class_exists( 'EazyDocsPro' ) ) {
	$cz_options         = get_option( 'eazydocs_customizer' ); // prefix of framework
	$layout             = $cz_options['docs-single-layout'] ?? ''; // id of field
	$docs_feedback      = $options['docs-feedback'] ?? '1';
}

if ( ! empty( $layout == 'left_sidebar' ) || ! empty( $layout == 'both_sidebar' ) ) : ?>
    <div class="left-sidebar-toggle">
        <span class="left-arrow arrow_triangle-left" title="<?php esc_attr_e( 'Hide category', 'eazydocs' ); ?>" style="display: block;"></span>
        <span class="right-arrow arrow_triangle-right" title="<?php esc_attr_e( 'Show category', 'eazydocs' ); ?>" style="display: none;"></span>
    </div>
<?php endif; ?>

    <article class="shortcode_info" id="post" itemscope itemtype="http://schema.org/Article">
        <div class="doc-post-content">
            <div class="shortcode_title">
				<?php the_title( '<h1>', '</h1>' ); ?>

                <?php
                if( $reading_time_visibility == '1' ||  $views_visibility == '1' ) : ?>
                    <div class="meta dot-sep">
                        <?php
                        if( $reading_time_visibility == '1') : ?>
                            <span class="read-time">
                                <?php esc_html_e( 'Estimated reading: ', 'eazydocs' );
                                eazydocs_reading_time();
                                ?>
                            </span>
                            <?php
                        endif;
                        if( $views_visibility == '1') : ?>
                            <span class="views sep">
                                <?php echo eazydocs_get_post_view(); ?>
                            </span>
                            <?php
                        endif;
                        ?>
                    </div>
                    <?php
                endif;
                ?>

            </div>
            <div class="doc-scrollable">
				<?php
				the_content();
				eazydocs_get_template_part( 'single-doc-home' );
				$children = eazydocs_list_pages( "title_li=&order=menu_order&child_of=" . $post->ID . "&echo=0&post_type=" . $post->post_type );
				if ( $children && $post->post_parent != 0 ) {
					echo '<div class="details_cont ent recently_added" id="content_elements">';
					echo '<h4 class="c_head">' . esc_html__( 'Articles', 'eazydocs' ) . '</h4>';
					echo '<ul class="article_list tag_list">';
					echo eazydocs_list_pages( "title_li=&order=menu_order&child_of=" . $post->ID . "&echo=0&post_type=" . $post->post_type );
					echo '</ul>';
					echo '</div>';
				}
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Docs:', 'eazydocs' ),
					'after'  => '</div>',
				) );
				?>
            </div>
        </div>
		<?php
        if( $docs_feedback == '1' ) {
	        eazydocs_get_template_part( 'content-feedback' );
        }
        ?>
    </article>

<?php
eazydocs_get_template_part( 'content-related' );

if ( $comment_visibility == '1' )  :
	if ( comments_open() || get_comments_number() )  :
		?>
        <div class="eazydocs-comments-wrap">
			<?php comments_template(); ?>
        </div>
	<?php
	endif;
endif;