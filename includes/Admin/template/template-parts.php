<?php

// Displays a drag icon for child docs
function ezd_child_docs_drag_icon(){
	if ( current_user_can('manage_options') ) :
		?>
		<div class="dd-handle dd3-handle">
			<svg class="dd-handle-icon" width="12px" height="12px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" title="<?php esc_attr_e('Hold the mouse and drag to move this doc.', 'eazydocs'); ?>">
				<path fill="none" stroke="#000" stroke-width="2" d="M15,5 L17,5 L17,3 L15,3 L15,5 Z M7,5 L9,5 L9,3 L7,3 L7,5 Z M15,13 L17,13 L17,11 L15,11 L15,13 Z M7,13 L9,13 L9,11 L7,11 L7,13 Z M15,21 L17,21 L17,19 L15,19 L15,21 Z M7,21 L9,21 L9,19 L7,19 L7,21 Z" />
			</svg>
		</div>
		<?php 
	endif;														
}

// Retrieves child docs by parent id
function ezd_child_docs_children($parent){
    return get_children(array(
         'post_parent'   => $parent,
         'post_type'     => 'docs',
         'orderby'       => 'menu_order',
         'order'         => 'asc',
         'exclude'       => get_post_thumbnail_id( $parent )
     ));
 }

// Displays a progress bar based on positive and negative votes
function ezd_child_docs_progress_bar( $post_id ) {
     $positive = (int) get_post_meta( $post_id, 'positive', true );
     $negative = (int) get_post_meta( $post_id, 'negative', true );
 
     $positive_title = $positive 
         ? sprintf(_n('%d Positive vote, ', '%d Positive votes and ', $positive, 'eazydocs'), number_format_i18n($positive)) 
         : esc_html__('No Positive votes, ', 'eazydocs');
 
     $negative_title = $negative 
         ? sprintf(_n('%d Negative vote found.', '%d Negative votes found.', $negative, 'eazydocs'), number_format_i18n($negative)) 
         : esc_html__('No Negative votes.', 'eazydocs');
 
     $sum_votes = $positive + $negative;
 
     echo '<span class="progress-text">';
     if ($positive || $negative) {
         echo '<progress id="file" value="' . esc_attr($positive) . '" max="' . esc_attr($sum_votes) . '" title="' . esc_attr($positive_title . $negative_title) . '"> </progress>';
     } else {
         esc_html_e('No rates', 'eazydocs');
     }
     echo '</span>';
} 
 
 // Displays the left-side content of docs
 function ezd_child_docs_left_content( $doc_item, $depth = 1, $item = []) {
     if ( ! $doc_item ) {
         return;
     }
 
     $edit_link     = get_edit_post_link( $doc_item, $item );
     $target        = '_blank';
     $has_child     = eaz_get_nestable_children( $doc_item );
     $child_count   = count( $has_child );
     $is_premium    = ! ezd_is_premium() && $depth === 3 ? false : ( ezd_is_premium() && $depth === 4 ? false : true);
     ?>
     <div class="left-content left-content-<?php echo esc_attr( $depth ); ?>">
         <h4>
             <a href="<?php echo esc_attr($edit_link); ?>" target="<?php echo esc_attr( $target ); ?>">
                 <?php echo esc_html( get_the_title( $doc_item ) ); ?>
             </a>
             <?php 
             if ( $child_count > 0 ) : 
                ?>
                 <span class="count ezd-badge">
                    <?php echo esc_html( $child_count ); ?>
                </span>
                <?php 
             endif; 
             ?>
         </h4>
 
         <ul class="actions">
             <?php if (ezd_is_admin_or_editor($doc_item, 'edit')) : ?>
                 <?php if (ezd_is_premium()) : ?>
                     <li class="duplicate">
                         <?php do_action('eazydocs_section_doc_duplicate', $doc_item, $item); ?>
                     </li>
                 <?php else : ?>
                     <li class="duplicate">
                         <a href="javascript:void(0);" class="eazydocs-pro-notice" title="<?php esc_attr_e('Duplicate this doc with the child docs.', 'eazydocs'); ?>">
                             <span class="dashicons dashicons-admin-page"></span>
                         </a>
                     </li>
                 <?php endif; 
                 
                 if ( $is_premium ) :
                     ?>
                     <li>
                         <a href="<?php echo admin_url('admin.php'); ?>?Create_Child=yes&childID=<?php echo esc_attr($doc_item); ?>&_wpnonce=<?php echo esc_attr(wp_create_nonce($doc_item)); ?>&child=" class="child-doc" title="<?php esc_attr_e('Add new doc under this doc', 'eazydocs'); ?>">
                             <span class="dashicons dashicons-plus-alt2"></span>
                         </a>
                     </li>
                     <?php 
                 endif;
                 
                 if ( ezd_is_premium() && current_user_can( 'manage_options' ) ) :
                     if ( $child_count > 0 ) : 
                         ?>
                         <li class="visibility">
                             <?php do_action( 'eazydocs_doc_visibility_depth_one', $doc_item ); ?>
                         </li>
                         <?php
                     endif;
                 endif;
         
             endif; 
             ?>
 
             <li>
                 <a href="<?php echo get_permalink( $doc_item ); ?>" target="_blank" title="<?php esc_attr_e('View this doc item in new tab', 'eazydocs'); ?>">
                     <span class="dashicons dashicons-external"></span>
                 </a>
             </li>
 
             <?php 
             if ( ezd_is_admin_or_editor( $doc_item, 'delete' ) ) : 
                ?>
                 <li class="delete">
                     <a href="<?php echo admin_url('admin.php'); ?>?Section_Delete=yes&_wpnonce=<?php echo esc_attr( wp_create_nonce( $doc_item ) ); ?>&ID=<?php echo esc_attr( $doc_item ); ?>" class="section-delete" title="<?php esc_attr_e( 'Move to Trash', 'eazydocs' ); ?>">
                         <span class="dashicons dashicons-trash"></span>
                     </a>
                 </li>
                <?php 
            endif; 
            ?>
         </ul>
     </div>
     <?php
 }

// Displays the title of docs
function ezd_child_docs_title($doc_id, $depth, $parent){
    $is_premium         = ! ezd_is_premium() && $depth === 3 ? '' : 'has-child';
    $has_child 		    = eaz_get_nestable_children( $doc_id );
    $is_section_title 	= ! empty ( $is_premium ) && count( $has_child ) > 0 ? 'ez-section-title ' : '';
    ?>
    <div class="dd3-content"> 
        <div class="accordion-title expand--child <?php echo esc_attr( $is_section_title ); if ( count( $has_child ) > 0 ) { echo esc_attr( $is_premium ); } ?>">
 
            <?php
            $edit_link  = 'javascript:void(0)';
            $target     = '_self';
            if ( ezd_is_admin_or_editor( $doc_id, 'edit' ) ) {
                $edit_link = admin_url('post.php').'?post='.$doc_id.'&action=edit';
                $target = '_blank';
            }

            ezd_child_docs_left_content( $doc_id, $depth, $parent );
            ?>

            <div class="right-content">
                <?php ezd_child_docs_progress_bar( $doc_id ); ?>
            </div>
        </div>
     </div>
     <?php
 }