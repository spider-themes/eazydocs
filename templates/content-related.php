<?php
global $post;
$cats            = get_the_terms( get_the_ID(), 'doc_tag' );
$cat_ids         = ! empty( $cats ) ? wp_list_pluck( $cats, 'term_id' ) : '';
$doc_posts       = new WP_Query( array(
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
$more_toggle_ppp = '4';
?>
<div class="row topic_item_tabs inner_tab_list related-recent-docs">
	<?php if ( $doc_posts->have_posts() ) : ?>
        <div class="col-lg-6">
            <div class="topic_list_item related-docs">
                <h4> <?php esc_html_e( 'Related articles', 'eazydocs' ); ?> </h4>
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
					?>
                    <li id="more-related" class="load-more">
                        <div class="fadeGradient"></div>
                        <ion-icon name="chevron-down-outline"></ion-icon>
						<?php esc_html_e( ' See More...', 'eazydocs' ); ?>
                    </li>
                </ul>
            </div>
        </div>
	<?php endif;

	$ft_cookie_posts = isset( $_COOKIE['eazydocs_recent_posts'] ) ? json_decode( $_COOKIE['eazydocs_recent_posts'], true ) : null;
	$ft_cookie_posts = isset( $ft_cookie_posts ) ? array_diff( $ft_cookie_posts, array( get_the_ID() ) ) : '';
	if ( is_array( $ft_cookie_posts ) && count( $ft_cookie_posts ) > 0 && isset( $ft_cookie_posts ) ) :
		?>
        <div class="col-lg-6">
            <div class="topic_list_item">
                <h4> <?php esc_html_e( 'Recently viewed articles', 'eazydocs' ); ?> </h4>
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
					?>
                    <li id="more-recent" class="load-more">
                        <div class="fadeGradient"></div>
                        <ion-icon name="chevron-down-outline"></ion-icon>
						<?php esc_html_e( ' See More...', 'eazydocs' ); ?>
                    </li>
                </ul>
            </div>
        </div>
	<?php
	endif;
	?>
</div>

<script>
    ;(function ($) {
        "use strict";

        $(document).ready(function () {
            $('.topic_list_item ul.navbar-nav li:not(.load-more)').hide()

            $("ul.related-doc-list li").slice(0, <?php echo esc_js( $more_toggle_ppp ) ?>).show()
            $("#more-related").click(function (e) {
                e.preventDefault()
                $(".related-doc-list li:hidden").slice(0,<?php echo esc_js( $more_toggle_ppp ) ?>).fadeIn("slow");
                if ($("ul.related-doc-list li:hidden").length == 0) {
                    $("#more-related").fadeOut("slow")
                }
            });

            $("ul.recent-doc-list li").slice(0,<?php echo esc_js( $more_toggle_ppp ) ?>).show()
            $("#more-recent").click(function (e) {
                e.preventDefault()
                $(".recent-doc-list li:hidden").slice(0,<?php echo esc_js( $more_toggle_ppp ) ?>).fadeIn("slow");
                if ($("ul.recent-doc-list li:hidden").length == 0) {
                    $("#more-recent").fadeOut("slow")
                }
            })

        })
    })(jQuery);
</script>