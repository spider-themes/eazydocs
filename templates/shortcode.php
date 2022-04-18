<?php
$orderby  = 'ID';
$order    = 'desc';
$showpost = - 1;
if ( class_exists( 'EazyDocsPro' ) ) {
	$settings_options = get_option( 'eazydocs_settings' ); // prefix of framework
	$orderby          = $settings_options['docs-order-by'] ?? 'ID'; // id of field
	$order            = $settings_options['docs-order'] ?? 'desc'; // id of field
	$showpost         = $settings_options['docs-number'] ?? - 1; // id of field
}

$depth_one_parents = [];
$btn_text          = esc_html__( 'View Details', 'eazydocs' );
$query             = new WP_Query( [
	'post_type'      => 'docs',
	'posts_per_page' => $showpost,
	'orderby'        => $orderby,
	'order'          => $order,
	'post_parent'    => 0
] );
?>
    <div class="container">
        <div class="row">
			<?php
			$i = 1;
			while ( $query->have_posts() ) : $query->the_post();
				$col_wrapper         = $i == 1;
				if ( class_exists( 'EazyDocsPro' ) ) {
					$cz_options = get_option( 'eazydocs_customizer' ); // prefix of framework
					$docs_col   = $cz_options['docs-column']; // id of field
					$btn_text   = $cz_options['docs-view-more']; // id of field
					do_action( 'before_docs_column_wrapper', $docs_col );
				} else { ?>
                    <div class="col-lg-4 col-sm-6">
				<?php } ?>

                <div class="categories_guide_item wow fadeInUp">
					<?php the_post_thumbnail( 'full' ) ?>
                    <a class="doc_tag_title" href="<?php the_permalink(); ?>">
                        <h4 class="title"> <?php the_title(); ?> </h4>
                    </a>
                    <ul class="list-unstyled tag_list">
						<?php
						$children = get_children( ['post_parent' => get_the_ID()] );
						if ( is_array( $children ) ) :
							foreach ( $children as $item ) :
								?>
                                <li>
                                    <a href="<?php echo get_permalink( $item->ID ); ?>">
										<?php echo $item->post_title; ?>
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
				<?php
				$i ++;
			endwhile;
			?>
        </div>
    </div>
<?php
wp_reset_postdata();