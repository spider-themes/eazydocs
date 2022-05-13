<?php
namespace eazyDocs\Frontend;

class Frontend {
	public function __construct() {

		add_filter( 'template_include', [ $this, 'template_loades' ], 20 );
		add_action('eazydocs_related_articles', [$this, 'eazydocs_related_articles'], 99, 4);
		add_action('eazydocs_viewed_articles', [$this, 'recently_viewed_docs'], 99, 4);
        //add_filter( 'body_class', [ $this, 'body_class' ] );
	}

	/**
	 * Returns template file
	 *
	 * @since 1.0.0
	 */
	public function template_loades( $template ) {
		$file = '';
		if ( is_single() && 'docs' == get_post_type() ) {
			$single_template = 'single-docs.php';
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			if ( $theme_file = locate_template( array( 'eazydocs/' . $single_template ) ) ) {
				$file = $theme_file;
			} else {
				$file = EAZYDOCS_PATH . '/templates' .'//'. $single_template;
			}
		}else {
			return $template;
		}
		return apply_filters( 'eazydocs_template_' . $template, $file );
	}

	/**
	 * @return array
	 *
	 * @since 1.0.0
	 */
    public function body_class() {
        $classes = [];
        $theme = wp_get_theme();

        if ( $theme->Name == 'Docy' ) {
            $classes[]= strtolower($theme->Name);
        }

        return $classes;
    }

	/**
	 * Recently Viewed Docs [ Single Docs ]
	 * @param $title
	 * @param $visibility
	 * @param $visible_item
	 * @param $see_more
	 */
	public function recently_viewed_docs( $title, $visibility, $visible_item, $see_more ){
		$ft_cookie_posts = isset( $_COOKIE['eazydocs_recent_posts'] ) ? json_decode( htmlspecialchars( $_COOKIE['eazydocs_recent_posts'], true ) ) : null;
		$ft_cookie_posts = isset( $ft_cookie_posts ) ? array_diff( $ft_cookie_posts, array( get_the_ID() ) ) : '';
		if ( is_array( $ft_cookie_posts ) && count( $ft_cookie_posts ) > 0 && isset( $ft_cookie_posts ) ) :
			?>
			<div class="col-lg-6 <?php echo esc_attr($visibility); ?>">
				<div class="topic_list_item">
					<?php if( ! empty( $title ) ) : ?>
						<h4> <?php echo esc_html( $title ); ?> </h4>
					<?php endif; ?>
					<ul class="navbar-nav recent-doc-list">
						<?php
						$count = 0;
						foreach ( $ft_cookie_posts as $postId ) :
							$ft_post = get_post( absint( $postId ) ); // Get the post
							// Condition to display a post
							if ( isset( $ft_post ) && in_array( $ft_post->post_type, [ 'docs' ] ) ) {
								$count ++;
								?>
								<li>
									<a href="<?php echo get_the_permalink( $ft_post->ID ) ?>"> <i class="icon_document_alt"></i>
										<?php echo get_the_title( $ft_post->ID ) ?>
									</a>
								</li>
								<?php
							}
						endforeach;
						if( ! empty ( $see_more ) ) : ?>
							<li id="more-recent" class="load-more">
								<div class="fadeGradient"></div>
								<ion-icon name="chevron-down-outline"></ion-icon>
								<?php echo esc_html( $see_more ); ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>

		<script>
            ;(function ($) {
                "use strict";
                $(document).ready(function () {
                    $("ul.recent-doc-list li").slice(0,<?php echo esc_js( $visible_item ) ?>).show()
                    $("#more-recent").click(function (e) {
                        e.preventDefault()
                        $(".recent-doc-list li:hidden").slice(0,<?php echo esc_js( $visible_item ) ?>).fadeIn("slow");
                        if ($("ul.recent-doc-list li:hidden").length == 0) {
                            $("#more-recent").fadeOut("slow")
                        }
                    });

                });
            })(jQuery);
		</script>

	<?php }

	/**
	 * @param $title
	 * @param $visibility
	 * @param $visible_item
	 * @param $see_more
	 */
	public function eazydocs_related_articles( $title, $visibility, $visible_item, $see_more ){
		global $post;
		$cats            = get_the_terms( get_the_ID(), 'doc_tag' );
		$cat_ids         = ! empty( $cats ) ? wp_list_pluck( $cats, 'term_id' ) : '';
		$doc_posts       = new \WP_Query( array(
			'post_type'           => 'docs',
			'tax_query'           => array(
				array(
					'taxonomy' => 'doc_tag',
					'field'    => 'id',
					'terms'    => $cat_ids,
					'operator' => 'IN' //Or 'AND' or 'NOT IN'
				)
			),
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1,
			'orderby'             => 'rand',
			'post__not_in'        => array( $post->ID )
		) );

		if ( $doc_posts->have_posts() ) : ?>
			<div class="col-lg-6 <?php echo esc_attr($visibility); ?>">
				<div class="topic_list_item related-docs">
					<?php if( ! empty( $title ) ) : ?>
						<h4> <?php echo esc_html( $title ); ?> </h4>
					<?php endif; ?>
					<ul class="navbar-nav related-doc-list">
						<?php
						while ( $doc_posts->have_posts() ) : $doc_posts->the_post();
							?>
							<li>
								<a href="<?php echo get_the_permalink( get_the_ID() ) ?>">
									<i class="icon_document_alt"></i>
									<?php echo get_the_title( get_the_ID() ) ?>
								</a>
							</li>
						<?php
						endwhile;
						wp_reset_postdata();
						if( ! empty ( $see_more) ) : ?>
							<li id="more-related" class="load-more">
								<div class="fadeGradient"></div>
								<ion-icon name="chevron-down-outline"></ion-icon>
								<?php echo esc_html($see_more); ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>

		<script>
            ;(function ($) {
                "use strict";
                $(document).ready(function () {
                    $('.topic_list_item ul.navbar-nav li:not(.load-more)').hide()

                    $("ul.related-doc-list li").slice(0, <?php echo esc_js( $visible_item ) ?>).show()
                    $("#more-related").click(function (e) {
                        e.preventDefault()
                        $(".related-doc-list li:hidden").slice(0,<?php echo esc_js( $visible_item ) ?>).fadeIn("slow");
                        if ($("ul.related-doc-list li:hidden").length == 0) {
                            $("#more-related").fadeOut("slow")
                        }
                    });
                })
            })(jQuery);
		</script>
	<?php }
}