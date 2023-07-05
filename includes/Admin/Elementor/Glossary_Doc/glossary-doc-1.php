<div class="spe-list-wrapper">
    <div class="spe-list-filter">
        <a class="filter active mixitup-control-active" data-filter="all">All</a>
		<?php
		$alphabet = range( 'a', 'z' );
		foreach ( $alphabet as $alphabetCharacter ) {
			?>
            <a class="filter" data-filter=".spe-filter-<?php echo esc_html__( $alphabetCharacter ); ?>">
				<?php echo esc_html__( $alphabetCharacter ); ?>
            </a>
			<?php
		}
		?>
    </div>

    <div class="spe-list-search-form spe-list-search-form-position-below">
        <input id="input" type="text" placeholder="Search by Keyword ..." value="">
    </div>

    <div class="spe-list spe-list-template-three-column">
	    <?php
	    $alphabet = range('a', 'z');

	    if (is_array($alphabet)) {
		    foreach ($alphabet as $alphabetCharacter) {
			    $hasItems = false; // Variable to track if there are items for the current alphabet character

			    // Check if there are any sections with corresponding items
			    if (!empty($sections)) {
				    foreach ($sections as $section) {
					    $doc_items = get_children(array(
						    'post_parent'    => $section->ID,
						    'post_type'      => 'docs',
						    'post_status'    => 'publish',
						    'orderby'        => 'menu_order',
						    'order'          => 'ASC',
						    'posts_per_page' => !empty($settings['ppp_doc_items']) ? $settings['ppp_doc_items'] : -1,
					    ));

					    if (!empty($doc_items)) {
						    foreach ($doc_items as $doc_item) {
							    $title = $doc_item->post_title;
							    $firstLetter = substr($title, 0, 1);
							    if (strtolower($firstLetter) === $alphabetCharacter) {
								    $hasItems = true; // Set to true if there is at least one item for the current alphabet character
								    break;
							    }
						    }
					    }
				    }
			    }

			    if ($hasItems) {
				    ?>
                    <div class="spe-list-block spe-filter-<?php echo esc_html__($alphabetCharacter); ?> mix"
                         data-filter-base="<?php echo esc_html__($alphabetCharacter); ?>">
                        <h3 class="spe-list-block-heading"><?php echo esc_html__($alphabetCharacter); ?></h3>
                        <ul class="spe-list-items list-unstyled tag_list">
						    <?php
						    foreach ($sections as $section) {
							    $doc_items = get_children(array(
								    'post_parent'    => $section->ID,
								    'post_type'      => 'docs',
								    'post_status'    => 'publish',
								    'orderby'        => 'menu_order',
								    'order'          => 'ASC',
								    'posts_per_page' => !empty($settings['ppp_doc_items']) ? $settings['ppp_doc_items'] : -1,
							    ));

							    if (!empty($doc_items)) {
								    foreach ($doc_items as $doc_item) {
									    $title = $doc_item->post_title;
									    $firstLetter = substr($title, 0, 1);
									    if (strtolower($firstLetter) === $alphabetCharacter) {
										    ?>
                                            <li class="spe-list-item">
                                                <a class="spe-list-item-title ct-content-text"
                                                   href="<?php echo get_permalink($doc_item->ID) ?>">
												    <?php echo wp_kses_post($doc_item->post_title) ?>
                                                </a>
                                            </li>
										    <?php
									    }
								    }
							    }
						    }
						    ?>
                        </ul>
                    </div>
				    <?php
			    }
		    }
	    }
	    ?>
    </div>
</div>