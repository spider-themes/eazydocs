<?php
eazydocs_set_post_view();
$options                    = get_option( 'eazydocs_settings' );
$layout                     = 'both_sidebar';
$cz_options                 = '';
$comment_visibility         = $options['enable-comment'] ?? '1';
$reading_time_visibility    = $options['enable-reading-time'] ?? '1';
$views_visibility           = $options['enable-views'] ?? '1';
$docs_feedback              = $options['docs-feedback'] ?? '1';
$sidebar_toggle             = '1';
if ( class_exists( 'EazyDocsPro' ) ) {
	$layout                     = $options['docs_single_layout'] ?? 'both_sidebar';
	$sidebar_toggle         = $options['toggle_visibility'] ?? '1';
}
if( $sidebar_toggle         == 1 ) :
    if ( ! empty( $layout   == 'left_sidebar' ) || ! empty( $layout == 'both_sidebar' ) ) : ?>
        <div class="left-sidebar-toggle">
            <span class="left-arrow arrow_triangle-left" title="<?php esc_attr_e( 'Hide category', 'eazydocs' ); ?>" style="display: block;"></span>
            <span class="right-arrow arrow_triangle-right" title="<?php esc_attr_e( 'Show category', 'eazydocs' ); ?>" style="display: none;"></span>
        </div>
        <?php
    endif;
endif;
?>
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
				eazydocs_get_template_part( 'book-layout/single-doc-home' );

				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Docs:', 'eazydocs' ),
					'after'  => '</div>',
				));
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