<?php if ( !empty($sections) ) : ?>
<div class="docs-box-item docs-single-5-wrap">
    <h5 class="docs-5-title">
        <?php echo get_the_title( $settings['doc'] ); ?>
    </h5>
    <div class="dox5-section-item">
        <?php
		foreach ( $sections as $section ) :
			$doc_items = get_children( array(
				'post_parent'    => $section->ID,
				'post_type'      => 'docs',
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => ! empty( $settings['ppp_doc_items'] ) ? $settings['ppp_doc_items'] : - 1,
			) );
			?>
        <div class="section5-article">
            <div class="section5-section-title">
                <h6>
                    <?php echo wp_kses_post( $section->post_title ); ?>
                </h6>
            </div>
            <ul class="navbar-nav docs-single5-nav-wrap">
                <?php
					$doc_count = count($doc_items);
					foreach ( $doc_items as $doc_item ) :
					$doc_count++;
					$li_class = '';
					if($doc_count % 2 == 0){
						$li_class = 'dark_bg';
					}
					?>
                <li>
                    <a href="<?php echo get_permalink( $doc_item->ID ) ?>">
                        <?php echo wp_kses_post( $doc_item->post_title ) ?>
                    </a>
                </li>
                <?php
					endforeach;
					?>
            </ul>
        </div>
        <?php
		endforeach;
		?>
    </div>
</div>
<?php endif; ?>