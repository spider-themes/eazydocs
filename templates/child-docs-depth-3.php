<?php 
    $is_child_doc 		= '';
    if( $key > 0 ) {
        $is_child_doc  	= 'has-child-doc';
    }
?>

<li class="easydocs-accordion-item dd-item accordion-title ez-section-title mix <?php echo $is_child_doc . ' doc-'.$post_id; ?> <?php echo esc_attr(get_post_status(  $post_id )); ?>"
post-id="<?php echo $post_id; ?>" doc-parent="<?php echo $parent_id; ?>" doc-order="<?php echo $child_order; ?>">
    <div class="easydocs-accordion-content-wrap">
    <?php if ( eaz_fs()->is__premium_only() ) : ?>
        <svg class="DragHandle-icon dd-handle" width="12px" height="12px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" title="<?php esc_attr_e('Hold the mouse and drag to move this doc.', 'eazydocs'); ?>">
            <path fill="none" stroke="#000" stroke-width="2" d="M15,5 L17,5 L17,3 L15,3 L15,5 Z M7,5 L9,5 L9,3 L7,3 L7,5 Z M15,13 L17,13 L17,11 L15,11 L15,13 Z M7,13 L9,13 L9,11 L7,11 L7,13 Z M15,21 L17,21 L17,19 L15,19 L15,21 Z M7,21 L9,21 L9,19 L7,19 L7,21 Z"/>
        </svg>
    <?php endif; ?>
    
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
            <a href="<?php echo esc_url($edit_link); ?>" target="<?php echo esc_attr($target); ?>" class="section-last-label">
                <?php echo get_the_title($post_id); ?>
            </a>
            <?php 
            if( $key > 0 ) :
                ?>
                <span class="count badge">
                    <?php echo esc_html( $key ); ?>
                </span>
                <?php 
            endif;
            ?>
        </h4>

        <ul class="actions">
            <?php
            if ( current_user_can('editor') || current_user_can('administrator') ) :
                if ( class_exists('EazyDocsPro') && eaz_fs()->can_use_premium_code() ) : ?>
                    <li class="duplicate">
                        <?php do_action('eazydocs_child_section_doc_duplicate', $post_id, $parent_id); ?>
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
                endif;
            ?>

            <li>
                <a href="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?childID=<?php echo $post_id; ?>&child=" class="child-doc" title="<?php esc_attr_e('Add new doc under this doc', 'eazydocs'); ?>">
                    <span class="dashicons dashicons-plus-alt2"></span>
                </a>
            </li>

            <li>
                <a href="<?php echo get_permalink( $post_id ); ?>" target="_blank" title="<?php esc_attr_e('View this doc item in new tab', 'easydocs') ?>">
                    <span class="dashicons dashicons-external"></span>
                </a>
            </li>
            <?php 
                if( current_user_can('editor') || current_user_can('administrator') ) : ?>
                    <li class="delete">
                        <a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo esc_attr( $post_id ); ?>" class="section-delete" title="<?php esc_attr_e('Delete this doc permanently', 'eazydocs'); ?>">
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
    
    <?php 
    $get_child_docs = get_children([
        'post_parent'   => $post_id
    ]);
    ?>
    <ol class="dd-list" doc-parent="<?php echo $post_id; ?>">
        <?php
        foreach( $get_child_docs as $get_child_doc ){
            $get_child_doc_id = $get_child_doc->ID ?? ''; 
            $child_post_order    = get_post($get_child_doc);
            $child_post_order    = $child_post_order->menu_order;
            // call the template
            //do_action('last_depth_docs', $get_child_doc_id, $post_id, $child_post_order);
            eazydocs_get_template( 'child-docs-depth-4.php', [
                'last_post'            => $get_child_doc_id,
                'last_parent'      => $post_id,
                'last_doc_order'    => $child_post_order
            ] );
        }
        ?>
    </ol>
</li>