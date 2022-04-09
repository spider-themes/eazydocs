<?php
$child_docs_depth  = [];
$depth_two_parents = [];
$depth_three_parents = [];
$ids               = '';
$container         = 1;
if ( is_array( $depth_one_parents ) ) :
	foreach ( $depth_one_parents as $item ) :
		$ids ++;
		$container ++;
		$active = $ids == 1 ? ' tab-active' : '';
		?>
        <div class="easydocs-tab<?php echo $active; ?>" id="tab-<?php echo esc_attr( $item ); ?>">
            <div class="easydocs-filter-container">
                <ul class="single-item-filter">
                    <li class="easydocs-btn easydocs-btn-black-light easydocs-btn-rounded easydocs-btn-sm is-active" data-filter="all">
                        <span class="dashicons dashicons-media-document"></span>
						<?php esc_html_e( 'All articles', 'eazydocs' ); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-green-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".publish">
                        <span class="dashicons dashicons-admin-site-alt3"></span>
						<?php esc_html_e( 'Public', 'eazydocs' ); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".private">
                        <span class="dashicons dashicons-privacy"></span>
						<?php esc_html_e( 'Private', 'eazydocs' ); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-orange-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".protected">
                        <span class="dashicons dashicons-lock"></span>
						<?php esc_html_e( 'Protected', 'eazydocs' ); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-gray-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".draft">
                        <span class="dashicons dashicons-edit-page"></span>
						<?php esc_html_e( 'Draft', 'eazydocs' ); ?>
                    </li>
                </ul>
            </div>

            <ul class="easydocs-accordion sortable accordionjs">
				<?php
				$children = get_children( array(
				        'post_parent'   => $item,
                        'orderby'       => 'menu_order'
                ));
				if ( is_array( $children ) ) :
					foreach ( $children as $child )  :

						$depth_two_parents[] = $child->ID;
						$post_status = $child->post_status;

						$doc_items = get_children( array(
							'post_parent' => $child->ID,
							'orderby'        => 'menu_order'
						) );

						$child_one = get_children( [
							'post_parent'       => $child->ID,
							'fields'            => 'ids',
						] );

						$depth_two = '';
						foreach ( $doc_items as $doc_item ) {
							$child_depth = get_children( array(
							    'post_parent'   => $doc_item->ID,
                                'fields'        => 'ids'
                            ) );
							$depth_two = implode(",", $child_depth) ;
						}
						$depth_docs = implode(",", $child_one) .','. $depth_two .','.$child->ID ;

						if ( ! empty( $child->post_password ) ) {
							$post_status = 'protected';
						}
						?>
                        <li <?php post_class( "easydocs-accordion-item accordion ez-section-acc-item mix ". $post_status ); ?> data-id="<?php echo esc_attr($child->ID); ?>">
                            <div class="accordion-title ez-section-title <?php echo count($doc_items) > 0 ? 'has-child' : ''; ?>">
                                <h4>
                                    <a href="<?php echo get_edit_post_link( $child ); ?>" target="_blank">
										<?php echo get_the_title( $child ); ?>
                                    </a>
                                    <?php if ( count($doc_items) > 0 ) : ?>
                                        <span class="count badge">
                                            <?php echo count($doc_items) ?>
                                        </span>
                                    <?php endif; ?>
                                </h4>
                                <div class="right-content">
                                    <ul>
                                        <li>
                                            <a href="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?childID=<?php echo $child->ID; ?>&child=" class="child-doc">
                                                <img src="<?php echo EAZYDOCS_IMG ?>/admin/plus.svg" alt="<?php esc_attr_e( 'Plus Icon', 'eazydocs' ); ?>">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo get_permalink( $child ); ?>" target="_blank">
                                                <img src="<?php echo EAZYDOCS_IMG ?>/admin/view.svg" alt="<?php esc_attr_e( 'View Icon', 'eazydocs' ); ?>">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo $depth_docs; ?>" class="section-delete">
                                                <img src="<?php echo EAZYDOCS_IMG ?>/admin/delete.svg" alt="<?php esc_attr_e( 'Delete Icon', 'eazydocs' ); ?>">
                                            </a>
                                        </li>
                                    </ul>
                                    <span class="progress-text">
                                        <?php
                                        $positive = (int) get_post_meta( $child->ID, 'positive' );
                                        $negative = (int) get_post_meta( $child->ID, 'negative', true );

                                        $positive_title      = $positive ? sprintf( _n( '%d Positive vote, ', '%d Positive votes and ', $positive, 'eazydocs' ), number_format_i18n( $positive ) ) : esc_html__( 'No Positive votes, ', 'eazydocs' );
                                        $negative_title      = $negative ? sprintf( _n( '%d Negative vote found.', '%d Negative votes found.', $negative, 'eazydocs' ), number_format_i18n( $negative ) ) : esc_html__( 'No Negative votes.', 'eazydocs' );

                                        $sum_votes = $positive + $negative;

                                        if ( $positive || $negative ) {
                                            echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
                                        } else {
                                            esc_html_e('No rates', 'eazydocs');
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="easydocs-accordion-body nesting-accordion child-docs">
                                <div class="nesting-task sortable">

	                                <?php
	                                foreach ( $doc_items as $doc_item ) :
		                                $child_depth = get_children( array(
		                                        'post_parent' => $doc_item->ID,
		                                        'orderby'        => 'menu_order'
                                        ) );

		                                $last_section_docs = [];
		                                if ( is_array( $child_depth ) ) {
			                                foreach ( $child_depth as $dep3_docs ) {
				                                $last_section_docs[] = $dep3_docs->ID;
			                                }
		                                }
		                                $last_section_ids = implode( ",", $last_section_docs );
		                                ?>
                                        <ul class="accordionjs">
                                            <li <?php post_class( "easydocs-accordion-item accordion mix child-one ". $post_status ); ?>>
                                                <div class="accordion-title <?php echo count($child_depth) > 0 ? 'has-child' : ''; ?>">
                                                    <h4>
                                                        <a href="<?php echo get_edit_post_link( $doc_item ); ?>" target="_blank" class="section-last-label">
                                                            <?php echo get_the_title( $doc_item ); ?>
                                                        </a>
	                                                    <?php
                                                        if ( count($child_depth) > 0 ) : ?>
                                                            <span class="count badge">
                                                                <?php echo count($child_depth) ?>
                                                            </span>
	                                                    <?php endif;
	                                                    ?>
                                                    </h4>

                                                    <div class="right-content">
                                                        <ul>
                                                            <li>
                                                                <a href="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?childID=<?php echo $doc_item->ID; ?>&child=" class="child-doc">
                                                                    <img src="<?php echo EAZYDOCS_IMG ?>/admin/plus.svg" alt="<?php esc_attr_e( 'Plus Icon', 'eazydocs' ); ?>">
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php echo get_permalink( $doc_item ); ?>" target="_blank">
                                                                    <img src="<?php echo EAZYDOCS_IMG ?>/admin/view.svg" alt="<?php esc_attr_e( 'View Icon', 'eazydocs' ); ?>">
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo $doc_item->ID . ',' . $last_section_ids; ?>" class="section-delete">
                                                                    <img src="<?php echo EAZYDOCS_IMG ?>/admin/delete.svg" alt="<?php esc_attr_e( 'Delete Icon', 'eazydocs' ); ?>">
                                                                </a>
                                                            </li>
                                                        </ul>
                                                        <span class="progress-text">
                                                            <?php
                                                            $positive = (int) get_post_meta( $doc_item->ID, 'positive' );
                                                            $negative = (int) get_post_meta( $doc_item->ID, 'negative', true );

                                                            $positive_title      = $positive ? sprintf( _n( '%d Positive vote, ', '%d Positive votes and ', $positive, 'eazydocs' ), number_format_i18n( $positive ) ) : esc_html__( 'No Positive votes, ', 'eazydocs' );
                                                            $negative_title      = $negative ? sprintf( _n( '%d Negative vote found.', '%d Negative votes found.', $negative, 'eazydocs' ), number_format_i18n( $negative ) ) : esc_html__( 'No Negative votes.', 'eazydocs' );

                                                            $sum_votes = $positive + $negative;

                                                            if ( $positive || $negative ) {
                                                                echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
                                                            } else {
                                                                esc_html_e('No rates', 'eazydocs');
                                                            }
                                                            ?>
                                                        </span>
                                                    </div>

                                                </div>
                                                <div class="easydocs-accordion-body nesting-accordion">
                                                    <div class="nesting-task sortable">
                                                        <?php
                                                        foreach( $child_depth as $dep3 ) :
                                                            ?>
                                                            <div class="child-docs-wrap d-flex justify-content-between">
                                                                <a href="<?php echo get_edit_post_link( $dep3 ); ?>" target="_blank" class="child-last-label">
                                                                   <?php echo get_the_title( $dep3 ); ?>
                                                                </a>
                                                                <div class="child-right-content d-flex">
                                                                    <a href="<?php echo get_permalink( $dep3 ); ?>" target="_blank" class="child-view-link">
                                                                        <img src="<?php echo EAZYDOCS_IMG ?>/admin/view.svg" alt="<?php esc_attr_e( 'View icon', 'eazydocs' ); ?>">
                                                                    </a>
                                                                    <a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo $dep3->ID; ?>" class="child-delete">
                                                                        <img src="<?php echo EAZYDOCS_IMG ?>/admin/delete.svg" alt="<?php esc_attr_e( 'Delete icon', 'eazydocs' ); ?>">
                                                                    </a>

                                                                    <span class="progress-text">
                                                                        <?php
                                                                        $positive = (int) get_post_meta( $dep3->ID, 'positive' );
                                                                        $negative = (int) get_post_meta( $dep3->ID, 'negative', true );

                                                                        $positive_title      = $positive ? sprintf( _n( '%d Positive vote, ', '%d Positive votes and ', $positive, 'eazydocs' ), number_format_i18n( $positive ) ) : esc_html__( 'No Positive votes, ', 'eazydocs' );
                                                                        $negative_title      = $negative ? sprintf( _n( '%d Negative vote found.', '%d Negative votes found.', $negative, 'eazydocs' ), number_format_i18n( $negative ) ) : esc_html__( 'No Negative votes.', 'eazydocs' );

                                                                        $sum_votes = $positive + $negative;

                                                                        if ( $positive || $negative ) {
                                                                            echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
                                                                        } else {
                                                                            esc_html_e('No rates', 'eazydocs');
                                                                        }
                                                                        ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        endforeach;
                                                        ?>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
	                                <?php
	                                endforeach;
	                                ?>
                                </div>

                            </div>
                        </li>
					    <?php
					endforeach;
				endif;
				?>
            </ul>
            <button class="button button-info section-doc" name="submit" data-url="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?parentID=<?php echo $item; ?>&section=">
				<?php esc_html_e( 'Add Section', 'eazydocs' ); ?>
            </button>
        </div>
	    <?php
	endforeach;
endif;