<?php
namespace eazyDocs\Frontend;

class Frontend {
	public function __construct() {
		add_filter( 'template_include', [ $this, 'template_loads' ], 20 );
		add_action( 'eazydocs_footnote', [ $this, 'footnotes' ] );
		add_action( 'eazydocs_related_articles', [ $this, 'related_articles' ], 99, 4 );
		add_action( 'eazydocs_viewed_articles', [ $this, 'recently_viewed_docs' ], 99, 4 );
		add_filter( 'body_class', [ $this, 'body_class' ] );
		add_action( 'eazydocs_prev_next_docs', [ $this, 'prev_next_docs' ] );
	}
	
	/**
	 * Returns template file
	 *
	 * @since 1.0.0
	 */
	public function template_loads( $template ) {
		$file = '';
		if ( is_single() && 'docs' == get_post_type() ) {
			$single_template = 'single-docs.php';
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			if ( $theme_file = locate_template( array( 'eazydocs/' . $single_template ) ) ) {
				$file = $theme_file;
			} else {
				$file = EAZYDOCS_PATH . '/templates' . '//' . $single_template;
			}
		} elseif ( is_single() && 'onepage-docs' == get_post_type() ) {

			$single_template = 'single-onepage-docs.php';
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			if ( $theme_file = locate_template( array( 'eazydocs/' . $single_template ) ) ) {
				$file = $theme_file;
			} else {
				$file = EAZYDOCS_PATH . '/templates' . '//' . $single_template;
			}

		} else {
			return $template;
		}

		return apply_filters( 'eazydocs_template_' . $template, $file );
	}

	
	/**
	 * Footnotes
	 * 
	 * @param $post_id
	 *
	 */
	public function footnotes($post_id){
		$options 				= get_option( 'eazydocs_settings' );		
		$is_notes_title   		= $options['is_footnotes_heading'] ?? '1';
		$footnotes_layout  	 	= $options['footnotes_layout'] ?? 'collapsed';
		$is_footnotes_expand 	= $is_notes_title == 1 ? $footnotes_layout : '';
		$ezd_notes_footer_mt 	= $is_notes_title != '1' ? 'mt-30' : '';
		$notes_title_text 		= $options['footnotes_heading_text'] ?? __( 'Footnotes', 'eazydocs' );
		$footnotes_column 		= $options['footnotes_column'] ?? '1';

		$reference_with_content = ezd_get_footnotes_in_content($post_id);
		$shortcode_counter 		= count($reference_with_content);
		
		if ( $shortcode_counter == 0 ) {
			return;
		}

		if ( ! empty( $notes_title_text ) && $is_notes_title == '1' ):
			?>
			<div class="ezd-footnote-title <?php echo esc_attr( $is_footnotes_expand ); ?>">
				<span class="ezd-plus-minus"> <i class="icon_plus-box"></i><i class="icon_minus-box"></i></span>
				<span class="ezd-title-txt"><?php echo esc_html( $notes_title_text ); ?></span>
                &nbsp; <span class="cite-count">(<?php echo esc_html( $shortcode_counter ); ?>) </span>
			</div>
			<?php 
		endif;
		?>
		
		<div data-column="<?php echo esc_attr( $footnotes_column ); ?>" class="ezd-footnote-footer <?php echo esc_attr( $ezd_notes_footer_mt .' '. $is_footnotes_expand ); ?>">
			<?php
			$i = 0;
			foreach( $reference_with_content as $reference_with_contents ) {
				$i++;
				?>
				<div class="note-class-<?php echo esc_html( $reference_with_contents['id'] ); ?>" id="note-name-<?php echo esc_html( $reference_with_contents['id'] ); ?>">
					<div class="ezd-footnotes-serial"> 
						<span class="ezd-serial"><?php echo esc_html($i); ?></span>
						<a class="ezd-note-indicator" href="#serial-id-<?php echo esc_html( $reference_with_contents['id'] ); ?>"><i class="arrow_carrot-up"></i> </a>
					</div>
					<div class="ezd-footnote-texts"> 
						<?php echo do_shortcode( $reference_with_contents['content'] ); ?>
					</div>
				</div>
				<?php
			}
			?>
		</div>

        <script>
            ;(function ($) {
                'use strict';
                $(document).ready(function () {
                    const $footnoteFooter = $('.ezd-footnote-footer');
                    const $footnoteTitle = $('.ezd-footnote-title');
                    const $footnoteLinks = $('.ezd-footnotes-link-item');
                    if ($footnoteFooter.children('div').length) {
                        $footnoteTitle.css('display', 'flex').on('click', function () {
                            $(this).toggleClass('expanded collapsed');
                            $footnoteFooter.stop(true, true).slideToggle({
                                complete: function () {
                                    $(this).css('display', $(this).is(':visible') ? 'flex' : 'none');
                                }
                            });
                        });
                        $footnoteLinks.on('click', function () {
                            $footnoteTitle.addClass('expanded').removeClass('collapsed');
                            $footnoteFooter.css({ display: 'flex', height: 'auto' });
                        });
                    }
                });
            })(jQuery);
        </script>
	<?php
	}

	/**
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function body_class( $classes ) {

		$classes[] = 'ezd-body-docs';

		if ( eazydocs_has_shortcode() ) {
			$classes[] = 'eazydocs_shortcode';
		}

		return $classes;
	}

	/**
	 * Recently Viewed Docs [ Single Docs ]
	 *
	 * @param $title
	 * @param $visibility
	 * @param $visible_item
	 * @param $see_more
	 */
	public function recently_viewed_docs( $title, $visibility, $visible_item, $see_more ) {
		$ft_cookie_posts = isset( $_COOKIE['eazydocs_recent_posts'] ) ? json_decode( htmlspecialchars( $_COOKIE['eazydocs_recent_posts'], true ) ) : null;
		$ft_cookie_posts = isset( $ft_cookie_posts ) ? array_diff( $ft_cookie_posts, array( get_the_ID() ) ) : '';
		if ( is_array( $ft_cookie_posts ) && count( $ft_cookie_posts ) > 0 && isset( $ft_cookie_posts ) ) :
			$eazydocs_option = get_option( 'eazydocs_settings' );

			global $post;
			$cats            = get_the_terms( get_the_ID(), 'doc_tag' );
			$cat_ids         = ! empty( $cats ) ? wp_list_pluck( $cats, 'term_id' ) : '';

			$doc_posts = new \WP_Query( array(
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

			$related_docs  = $doc_posts->post_count ?? 0;
			$viewed_column = $related_docs > 0 ? $eazydocs_option['viewed-doc-column'] : '12';
			?>
            <div class="ezd-lg-col-<?php echo esc_attr( $viewed_column . ' ' . $visibility ); ?>">
                <div class="topic_list_item">
					<?php if ( ! empty( $title ) ) : ?>
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
                                    <a href="<?php echo esc_url(get_the_permalink( $ft_post->ID )) ?>"> <i
                                                class="icon_document_alt"></i>
										<?php echo get_the_title( $ft_post->ID ) ?>
                                    </a>
                                </li>
								<?php
							}
						endforeach;
						if ( ! empty ( $see_more ) ) : ?>
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
            ;
            (function ($) {
                "use strict";
                $(document).ready(function () {
                    $("ul.recent-doc-list li").slice(0, <?php echo esc_js( $visible_item ) ?>).show()
                    $("#more-recent").click(function (e) {
                        e.preventDefault()
                        $(".recent-doc-list li:hidden").slice(0, <?php echo esc_js( $visible_item ) ?>).fadeIn(
                            "slow");
                        if ($("ul.recent-doc-list li:hidden").length == 0) {
                            $("#more-recent").fadeOut("slow")
                        }
                    });
                });
            })(jQuery);
        </script>
		<?php
	}

	/**
	 * @param $title
	 * @param $visibility
	 * @param $visible_item
	 * @param $see_more
	 */
	public function related_articles( $title, $visibility, $visible_item, $see_more ) {
		global $post;
		$cats            = get_the_terms( get_the_ID(), 'doc_tag' );
		$cat_ids         = ! empty( $cats ) ? wp_list_pluck( $cats, 'term_id' ) : '';
		$eazydocs_option = get_option( 'eazydocs_settings' );
		$related_column  = $eazydocs_option['related-doc-column'] ?? '6';
        $col_visibility = $related_column.' '.$visibility;
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

		if ( $doc_posts->have_posts() ) :

			?>
            <div class="ezd-grid-column-full ezd-lg-col-<?php echo esc_attr( $col_visibility ); ?> ezd-grid-column-full">
                <div class="topic_list_item related-docs">
					<?php if ( ! empty( $title ) ) : ?>
                        <h4> <?php echo esc_html( $title ); ?> </h4>
					<?php endif; ?>
                    <ul class="navbar-nav related-doc-list">
						<?php
						while ( $doc_posts->have_posts() ) : $doc_posts->the_post();
							?>
                            <li>
                                <a href="<?php echo esc_url(get_the_permalink( get_the_ID() )) ?>">
                                    <i class="icon_document_alt"></i>
									<?php echo get_the_title(get_the_ID()) ?>
                                </a>
                            </li>
						<?php
						endwhile;
						wp_reset_postdata();
						if ( ! empty ( $see_more ) ) : ?>
                            <li id="more-related" class="load-more">
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
            ;
            (function ($) {
                "use strict";
                $(document).ready(function () {
                    $('.topic_list_item ul.navbar-nav li:not(.load-more)').hide()

                    $("ul.related-doc-list li").slice(0, <?php echo esc_js( $visible_item ) ?>).show()
                    $("#more-related").click(function (e) {
                        e.preventDefault()
                        $(".related-doc-list li:hidden").slice(0, <?php echo esc_js( $visible_item ) ?>).fadeIn(
                            "slow");
                        if ($("ul.related-doc-list li:hidden").length == 0) {
                            $("#more-related").fadeOut("slow")
                        }
                    });
                })
            })(jQuery);
        </script>
		<?php
	}

	/**
	 * @param $get_id
	 * Single docs Previous & Next Link
	 **/
	public function prev_next_docs( $current_post_id ) {
		$current_parent_id  = wp_get_post_parent_id( $current_post_id );

		global $post, $wpdb;
		$next_query = "SELECT ID FROM {$wpdb->posts}
        WHERE post_parent = {$post->post_parent} and post_type = 'docs' and post_status = 'publish' and menu_order > {$post->menu_order}
        ORDER BY menu_order ASC
        LIMIT 0, 1";

		$prev_query = "SELECT ID FROM {$wpdb->posts}
        WHERE post_parent = {$post->post_parent} and post_type = 'docs' and post_status = 'publish' and menu_order < {$post->menu_order}
        ORDER BY menu_order DESC
        LIMIT 0, 1";

		$next_post_id = (int) $wpdb->get_var( $next_query );
		$prev_post_id = (int) $wpdb->get_var( $prev_query );

        // If the queries return null or empty, ensure these variables are still defined.
		$next_post_id = $next_post_id ? $next_post_id : 0;
		$prev_post_id = $prev_post_id ? $prev_post_id : 0;
		?>
        <div class="eazydocs-next-prev-wrap">
			<?php
			if ( $prev_post_id != 0 ) :
				?>
                <a class="next-prev-pager first" href="<?php echo get_permalink( $prev_post_id ); ?>">
                    <span> <?php echo get_the_title( $current_parent_id ); esc_html_e( ' - Previous', 'eazydocs' ); ?> </span>
					<?php echo get_the_title( $prev_post_id ); ?>
                </a>
			<?php
			endif;

			if ( $next_post_id != 0 ) :
				?>
                <a class="next-prev-pager second" href="<?php echo get_permalink( $next_post_id ); ?>">
                    <span> <?php esc_html_e( 'Next - ', 'eazydocs' ); echo get_the_title( $current_parent_id ); ?> </span>
					<?php echo get_the_title( $next_post_id ); ?>
                </a>
			<?php
			endif;
			?>
        </div>
		<?php

	}
}