<?php
global $post;
$opt                    = get_option( 'eazydocs_settings' ); // prefix of framework

$attributes             = '';
if ( has_shortcode( $post->post_content, 'eazydocs' ) ) {
	$attributes         = shortcode_parse_atts( $post->post_content );
}

if ( ! empty ( $attributes['col'] ) ) {
	$doc_col            = $opt['docs-column'] ?? 4;
	$cols               = $attributes['col'] ?? $doc_col;
	$docs_col           = '';
	switch ( $cols ) {
		case 1:
			$docs_col   = '12';
			break;

		case 2:
			$docs_col   = '6';
			break;

		case 3:
			$docs_col   = '4';
			break;

		case 4:
			$docs_col   = '3';
			break;

		case 6:
			$docs_col   = '2';
			break;
	}
} else {
	$docs_col           = $opt['docs-column'] ?? 4;
}

// More Button
$more                   = $opt['docs-view-more'] ?? esc_html__( 'View Details', 'eazydocs' ); // id of field
$btn_text               = $attributes['more'] ?? $more;

// Exclude ids
$exclude                = $attributes['exclude'] ?? '';
$exclude                = explode( ",", $exclude );

// Include ids
$include = $attributes['include'] ?? '';
$include = explode( ",", $include );

// Orderby
$orderby = 'menu_order';

// Show main doc per page
$showdoc  = $opt['docs-number'] ?? -1;
$showpost = $attributes['items'] ?? $showdoc;

// Child docs per page
$articles_number = $opt['articles_number'] ?? - 1;
$layout          = 'grid';
$order           = $opt['docs-order'] ?? 'asc'; // id of field

// Check pro plugin class exists
if ( class_exists( 'EazyDocsPro' ) ) {
	$orderby = $opt['docs-order-by'] ?? $orderby; // id of field
	$layout  = $opt['docs-archive-layout'] ?? $layout; // id of field
}

$depth_one_parents  = [];
$post_in            = [];

// Include & exclude
if ( ! empty ( $attributes['include'] ) ) {
	$post_in        = [
		'post__in'  => $include,
	];
} elseif ( ! empty ( $attributes['exclude'] ) ) {
	$post_in            = [
		'post__not_in'  => $exclude
	];
}

// Query args
$args                   = [
	'post_type'         => 'docs',
	'posts_per_page'    => $showpost,
	'orderby'           => $orderby,
	'post_status'       => [ 'publish' ],
	'order'             => $order,
	'post_parent'       => 0,
];
$merged_query           = array_merge( $post_in, $args );
$query                  = new WP_query( $merged_query );
?>
<div class="eazydocs_shortcode">
        <div class="container">
            <div class="row" <?php do_action( 'eazydocs_masonry_wrap', $layout ); ?>>
				<?php
				$i = 1;

				while ( $query->have_posts() ) : $query->the_post();
					$doc_counter = get_pages( [
						'child_of'  => get_the_ID(),
						'post_type' => 'docs',
						'orderby'   => 'menu_order',
						'order'     => 'asc'
					] );

					$col_wrapper = $i == 1;
					if ( class_exists( 'EazyDocsPro' ) ) {
						do_action( 'before_docs_column_wrapper', $docs_col );
					} else { ?>
                        <div class="col-lg-<?php echo esc_attr( $docs_col ); ?> col-sm-6">
					<?php } ?>

                    <div class="categories_guide_item wow fadeInUp">
                        <div class="doc-top d-flex align-items-start">
							<?php the_post_thumbnail( 'full', array( 'class', 'featured-image' ) ) ?>
                            <a class="doc_tag_title" href="<?php the_permalink(); ?>">
                                <h4 class="title"> <?php the_title(); ?> </h4>
                                <span>
                                <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                                <?php esc_html_e( 'Topics', 'eazyedocs' ) ?>
                            </span>
                            </a>
                        </div>
                        <ul class="list-unstyled article_list">
							<?php
							$children = get_children( [
								'post_parent'    => get_the_ID(),
								'post_status'    => [ 'publish' ],
								'posts_per_page' => $articles_number,
								'orderby'        => 'menu_order',
								'order'          => 'asc'
							] );
							if ( is_array( $children ) ) :
								foreach ( $children as $item ) :
									?>
                                    <li>
                                        <a href="<?php echo get_permalink( $item->ID ); ?>">
											<?php echo esc_html( $item->post_title ); ?>
                                        </a>
                                    </li>
								<?php
								endforeach;
							endif;
							?>
                        </ul>
                        <a href="<?php the_permalink(); ?>" class="doc_border_btn">
							<?php echo esc_html( $btn_text ); ?>
                            <i class="arrow_right"></i>
                        </a>
                    </div>
                 </div>
                <?php $i ++; endwhile; ?>
            </div>
        </div>
    </div>
<?php
wp_reset_postdata();