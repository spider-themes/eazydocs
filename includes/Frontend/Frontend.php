<?php
namespace EazyDocs\Frontend;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Frontend {
	/**
	 * Frontend constructor.
	 */
	public function __construct() {
		add_filter( 'template_include', [ $this, 'template_loads' ], 20 );
		add_action( 'eazydocs_footnote', [ $this, 'footnotes' ] );
		add_action( 'eazydocs_related_articles', [ $this, 'related_articles' ], 99, 4 );
		add_action( 'eazydocs_viewed_articles', [ $this, 'recently_viewed_docs' ], 99, 4 );
		add_filter( 'body_class', [ $this, 'body_class' ] );
		add_action( 'eazydocs_prev_next_docs', [ $this, 'prev_next_docs' ] );
	}

	/**
	 * Load template file for specific EazyDocs pages.
	 *
	 * @param string $template The path of the template to include.
	 * @return string The path of the template to include.
	 * @since 1.0.0
	 */
	public function template_loads( $template ) {
		$file = '';
		if ( is_single() && 'docs' === get_post_type() ) {
			$single_template = 'single-docs.php';
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			if ( $theme_file = locate_template( [ 'eazydocs/' . $single_template ] ) ) {
				$file = $theme_file;
			} else {
				$file = EAZYDOCS_PATH . '/templates' . '//' . $single_template;
			}
		} elseif ( is_single() && 'onepage-docs' === get_post_type() ) {

			$single_template = 'single-onepage-docs.php';
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			if ( $theme_file = locate_template( [ 'eazydocs/' . $single_template ] ) ) {
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
	 * Render Footnotes for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public function footnotes( $post_id ) {
		if ( ! ezd_is_footnotes_unlocked() ) {
			return;
		}
		$default_column 		= ezd_get_opt( 'footnotes_column', '4' );		
		$is_notes_title   		= ezd_get_opt( 'is_footnotes_heading', '1' );
		$footnotes_layout  	 	= ezd_get_opt( 'footnotes_layout', 'collapsed' );
		$is_footnotes_expand    = '1' === $is_notes_title ? $footnotes_layout : '';
		$ezd_notes_footer_mt    = '1' !== $is_notes_title ? 'mt-30' : '';
		$notes_title_text       = ezd_get_opt( 'footnotes_heading_text', esc_html__( 'Footnotes', 'eazydocs' ) );

		$meta_options           = get_post_meta( $post_id, 'footnotes_colum_opt', true );
		if ( ! is_array( $meta_options ) ) {
			$meta_options = [];
		}
		$col_meta               = $meta_options['footnotes_column'] ?? '3';
		$source                 = $meta_options['footnotes_column_source'] ?? 'default';
		$footnotes_column       = 'default' === $source ? $default_column : $col_meta;

		$reference_with_content = ezd_get_footnotes_in_content( $post_id );
		$shortcode_counter      = count( $reference_with_content );

		if ( 0 === $shortcode_counter ) {
			return;
		}

		if ( ! empty( $notes_title_text ) && '1' === $is_notes_title ) :
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
				<div class="note-class-<?php echo esc_attr( $i ); ?>" id="note-name-<?php echo esc_attr( $i ); ?>">
					<div class="ezd-footnotes-serial"> 
						<span class="ezd-serial"><?php echo esc_html($i); ?></span>
						<a class="ezd-note-indicator" href="#serial-id-<?php echo esc_attr( $i ); ?>"><i class="arrow_carrot-up"></i> </a>
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
	 * Render Recently Viewed Docs section.
	 *
	 * @param string $title        Section title.
	 * @param string $visibility   CSS visibility class.
	 * @param int    $visible_item Number of items to show initially.
	 * @param string $see_more     Text for 'See More' button.
	 *
	 * @return void
	 */
	public function recently_viewed_docs( $title, $visibility, $visible_item, $see_more ) {
		$ft_cookie_posts = isset( $_COOKIE['eazydocs_recent_posts'] ) ? json_decode( sanitize_text_field( wp_unslash( $_COOKIE['eazydocs_recent_posts'] ) ), true ) : null;
		$ft_cookie_posts = isset( $ft_cookie_posts ) ? array_diff( $ft_cookie_posts, [ get_the_ID() ] ) : '';
		if ( is_array( $ft_cookie_posts ) && count( $ft_cookie_posts ) > 0 && isset( $ft_cookie_posts ) ) :

			global $post;
			$cats            = get_the_terms( get_the_ID(), 'doc_tag' );
			$cat_ids         = ! empty( $cats ) ? wp_list_pluck( $cats, 'term_id' ) : '';

			$doc_posts = new \WP_Query( [
				'post_type'           => 'docs',
				'tax_query'           => [
					[
						'taxonomy' => 'doc_tag',
						'field'    => 'id',
						'terms'    => $cat_ids,
						'operator' => 'IN', // Or 'AND' or 'NOT IN'
					],
				],
				'posts_per_page'      => - 1,
				'ignore_sticky_posts' => 1,
				'orderby'             => 'rand',
				'post__not_in'        => [ $post->ID ],
			] );

			$related_docs  = $doc_posts->post_count ?? 0;
			$viewed_column = $related_docs > 0 ?  ezd_get_opt( 'viewed-doc-column' ) : '12';
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
                                    <a href="<?php the_permalink( $ft_post->ID ) ?>">
                                        <i class="icon_document_alt"></i> <?php echo esc_html(get_the_title( $ft_post->ID )) ?>
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
	 * Render Related Articles section.
	 *
	 * @param string $title        Section title.
	 * @param string $visibility   CSS visibility class.
	 * @param int    $visible_item Number of items to show initially.
	 * @param string $see_more     Text for 'See More' button.
	 *
	 * @return void
	 */
	public function related_articles( $title, $visibility, $visible_item, $see_more ) {
		global $post;
		$cats            = get_the_terms( get_the_ID(), 'doc_tag' );
		$cat_ids         = ! empty( $cats ) ? wp_list_pluck( $cats, 'term_id' ) : '';
		$related_column  = ezd_get_opt( 'related-doc-column', '6' );
		$col_visibility  = $related_column . ' ' . $visibility;
		$doc_posts       = new \WP_Query( [
			'post_type'           => 'docs',
			'tax_query'           => [
				[
					'taxonomy' => 'doc_tag',
					'field'    => 'id',
					'terms'    => $cat_ids,
					'operator' => 'IN', // Or 'AND' or 'NOT IN'
				],
			],
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1,
			'orderby'             => 'rand',
			'post__not_in'        => [ $post->ID ],
		] );

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
                                <a href="<?php the_permalink( get_the_ID() ) ?>">
                                    <i class="icon_document_alt"></i>
									<?php echo esc_html(get_the_title(get_the_ID())) ?>
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
	 * Render Previous & Next Links for single docs.
	 *
	 * @param int $current_post_id The current post ID.
	 * @return void
	 */
	public function prev_next_docs( $current_post_id ) {
		$current_post_id = (int)$current_post_id;
		$prev_next 		 = ezd_prev_next_docs( $current_post_id );
		$get_title 		 = fn($id) => esc_html( ezd_is_premium() && ( $secondary = get_post_meta( $id, 'ezd_doc_secondary_title', true ) ) ? sanitize_text_field( $secondary ) : get_the_title((int)$id ) );
		$current_title 	 = $get_title( $current_post_id );
		?>
		<div class="eazydocs-next-prev-wrap">
			<?php
			foreach ( ['prev', 'next'] as $type ) {
				$post_id = isset( $prev_next[ $type ] ) ? (int) $prev_next[ $type ] : 0;
				if ( ! $post_id ) continue;

				$title = $get_title( $post_id );
				$link  = esc_url( get_permalink( $post_id ) );
				$class = esc_attr( $type === 'prev' ? 'first' : 'second' );
				$label = $type === 'prev'
					? sprintf( '%s - %s', $current_title, esc_html__( 'Previous', 'eazydocs' ) )
					: sprintf( '%s - %s', esc_html__( 'Next', 'eazydocs' ), $current_title );
				printf( '<a class="next-prev-pager %s" href="%s"><span>%s</span>%s</a>', $class, $link, $label, $title );
			}
			?>
		</div>
		<?php
	}
}