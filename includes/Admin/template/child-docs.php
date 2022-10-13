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
        <div class="easydocs-tab<?php echo esc_attr($active); ?>" id="tab-<?php echo esc_attr($item); ?>">
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
            
            <div class="dd nestable easydocs-accordion">
                <ol class="dd-list" doc-parent="<?php echo esc_attr($item) ?>">

                <?php                
                $child_docs = new WP_Query([
                    'post_type'         => 'docs',
                    'posts_per_page'    => 6,
                    'post_parent'       => $item,
                    'order'             => 'asc',
                    'orderby'           => 'menu_order', 
                ]);
                $serial_key = 0;
                while($child_docs->have_posts($serial_key++)) : $child_docs->the_post();
                    $post_id            = get_the_ID();
                    $post_order         = get_post($post_id);
                    $child_docd         = get_children([
                        'post_parent'   => $post_id
                    ]);
                    ?>
                    <li class="easydocs-accordion-item dd-item accordion-title ez-section-title mix <?php echo 'doc-'.$post_id; ?> <?php echo get_post_status(get_the_ID()); ?>" post-id="<?php echo $post_id; ?>" serial-id=<?php echo $serial_key; ?> doc-order="<?php echo $post_order->menu_order; ?>">
                    <div class="easydocs-accordion-content-wrap">
                        
                        <?php 
                            if ( eaz_fs()->is__premium_only() ) :
                            ?>
                            <svg class="DragHandle-icon dd-handle" width="12px" height="12px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" title="<?php esc_attr_e('Hold the mouse and drag to move this doc.', 'eazydocs'); ?>">
                                <path fill="none" stroke="#000" stroke-width="2" d="M15,5 L17,5 L17,3 L15,3 L15,5 Z M7,5 L9,5 L9,3 L7,3 L7,5 Z M15,13 L17,13 L17,11 L15,11 L15,13 Z M7,13 L9,13 L9,11 L7,11 L7,13 Z M15,21 L17,21 L17,19 L15,19 L15,21 Z M7,21 L9,21 L9,19 L7,19 L7,21 Z"/>
                            </svg>
                            <?php 
                        endif; 
                        ?>
                        
                        <div class="left-content">
                            <?php
                                $edit_link = 'javascript:void(0)';
                                $target = '_self';
                                if ( current_user_can('editor') || current_user_can('administrator') ) {
                                    $edit_link = get_edit_post_link( $post_id );
                                    $target = '_blank';
                                }
                            ?>
                            <h4>
                                <a href="<?php echo esc_attr($edit_link); ?>" target="<?php echo esc_attr($target); ?>" class="section-last-label">
                                    <?php echo get_the_title($post_id); ?>
                                </a>
                                <?php 
                                    if ( count($child_docd) > 0 ) :
                                    ?>
                                        <span class="count badge">
                                            <?php echo count($child_docd); ?>
                                        </span>
                                    <?php 
                                endif;
                                ?>
                            </h4>
                            
                            <ul class="actions">

                                <?php
                                if( current_user_can('editor') || current_user_can('administrator') ) :
                                    if ( class_exists('EazyDocsPro') && eaz_fs()->can_use_premium_code() ) : ?>
                                        <li class="duplicate">
                                            <?php do_action('eazydocs_section_doc_duplicate', $post_id, $item); ?>
                                        </li>
                                        <?php
                                    else :
                                    ?>
                                        <li class="duplicate">
                                            <a href="javascript:void(0);" class="eazydocs-pro-notice" title="<?php esc_attr_e('Duplicate this doc with the child docs.', 'easydocs'); ?>">
                                                <span class="dashicons dashicons-admin-page"></span>
                                            </a>
                                        </li>
                                        <?php
                                    endif;
                                    ?>
                                    <li>
                                        <a href="<?php echo admin_url('admin.php'); ?>/Create_Post.php?childID=<?php echo $post_id; ?>&child=" class="child-doc" title="<?php esc_attr_e('Add new doc under this doc', 'eazydocs'); ?>">
                                            <span class="dashicons dashicons-plus-alt2"></span>
                                        </a>
                                    </li>
                                    <?php
                                endif;
                                ?>

                                <li>
                                    <a href="<?php echo get_permalink( $post_id ); ?>" target="_blank" title="<?php esc_attr_e('View this doc item in new tab', 'easydocs') ?>">
                                        <span class="dashicons dashicons-external"></span>
                                    </a>
                                </li>

                                <?php
                                if( current_user_can('editor') || current_user_can('administrator') ) :
                                    ?>
                                    <li class="delete">
                                        <a href="<?php echo admin_url('admin.php'); ?>/Delete_Post.php?ID=<?php echo $post_id; ?>" class="section-delete" title="<?php esc_attr_e('Delete this doc permanently', 'eazydocs'); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                        </a>
                                    </li>
                                    <?php 
                                endif;
                                ?>
                            </ul>

                        </div>

                        <div class="right-content">
                            <span class="progress-text">
                                <?php
                                $positive = (int) get_post_meta( $post_id, 'positive' );
                                $negative = (int) get_post_meta( $post_id, 'negative', true );

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
                            
                    <ol class="dd-list" doc-parent="<?php echo $post_id; ?>">                        
                        <?php
                        foreach( $child_docd as $child_doc_item ) :                            
                            $child_doc_item      = $child_doc_item->ID ?? ''; 
                            $child_post_order    = get_post($child_doc_item);
                            $child_post_order    = $child_post_order->menu_order;
                            $count_child = get_children([
                                'post_parent'    => $child_doc_item
                            ]);
                            $key                 =   count($count_child);
                            // call the template
                            eazydocs_get_template( 'child-docs-depth-3.php', [
                                'key'            => $key,
                                'post_id'        => $child_doc_item,
                                'parent_id'      => $post_id,
                                'child_order'    => $child_post_order
                            ] );
                        endforeach; 
                        ?>
                    </ol>
                </li>
                <?php 
                endwhile; 
                wp_reset_postdata();
                ?>
            </ol>
        </div>
        
        <button class="button button-info section-doc" id="section-doc" name="submit" data-url="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?parentID=<?php echo $item; ?>&is_section=">
			<?php esc_html_e( 'Add Section', 'eazydocs' ); ?>
        </button>

        <?php
        $current_theme      = get_template();
        if ( $current_theme == 'docy' || $current_theme == 'docly' || class_exists('EazyDocsPro')) {
            eazydocs_one_page( $item );
        }
        ?>
        
    </div>
	<?php
	endforeach;
endif;
?>