<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Repeater parts included in template-parts.php
require_once __DIR__ . '/template-parts.php';

$child_docs_depth    = [];
$depth_two_parents   = [];
$depth_three_parents = [];
$ids                 = 0;
$container           = 1;

if ( is_array( $depth_one_parents ) ) :
    foreach ( $depth_one_parents as $item ) :
        $ids++;
        $container++;
        $active = $ids == 1 ? ' tab-active' : '';
        ?>
        <div class="easydocs-tab<?php echo esc_attr( $active ); ?>" id="tab-<?php echo esc_attr( $item ); ?>">
            <div class="easydocs-filter-container">
                <ul class="single-item-filter">
                    <li class="easydocs-btn easydocs-btn-black-light easydocs-btn-rounded easydocs-btn-sm is-active" data-filter="all" role="button" tabindex="0" aria-pressed="true">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php esc_html_e('All articles', 'eazydocs'); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-green-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".publish" role="button" tabindex="0" aria-pressed="false">
                        <span class="dashicons dashicons-admin-site-alt3"></span>
                        <?php esc_html_e('Public', 'eazydocs'); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".private" role="button" tabindex="0" aria-pressed="false">
                        <span class="dashicons dashicons-privacy"></span>
                        <?php esc_html_e('Private', 'eazydocs'); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-orange-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".protected" role="button" tabindex="0" aria-pressed="false">
                        <span class="dashicons dashicons-lock"></span>
                        <?php esc_html_e('Protected', 'eazydocs'); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-gray-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".draft" role="button" tabindex="0" aria-pressed="false">
                        <span class="dashicons dashicons-edit-page"></span>
                        <?php esc_html_e('Draft', 'eazydocs'); ?>
                    </li>
                </ul>
                
                <div class="ezd-toolbar-actions">
                    <button type="button" class="ezd-toggle-expand-btn" data-state="collapsed" title="<?php esc_attr_e( 'Toggle expand/collapse all sections', 'eazydocs' ); ?>">
                        <span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
                        <span class="btn-text"><?php esc_html_e( 'Expand All', 'eazydocs' ); ?></span>
                    </button>
                </div>
            </div>
            <div class="easydocs-accordion sortabled dd accordionjs nestables-child" id="nestable-<?php echo esc_attr( $item ); ?>">
                
                <ol class="dd-list">
                    <?php
                    $children = ezd_child_docs_children( $item );

                    if ( is_array( $children ) ) :
                        foreach ( $children as $child ) :

                            $post_status = $child->post_status;
                            if ( ! empty( $child->post_password ) ) {
                                $post_status = 'protected';
                            }

                            $eaz_children       = eaz_get_nestable_children( $child->ID );
                            $eaz_children_class = $eaz_children ? ' dd3-have-children dd3-has-children ' : ' dd3-have-no-children ';
                            ?>
                            <li <?php post_class("dd-item dd3-item dd-item-parent depth-1 easydocs-accordion-item accordion mix " . esc_attr( $post_status . ' ' . $eaz_children_class . ' child-' . $child->ID ) ); ?> data-id="<?php echo esc_attr( $child->ID ); ?>">

                                <?php 
                                ezd_child_docs_drag_icon();
                                ezd_child_docs_title( $child->ID, 1, $item );
                                
                                if ( $eaz_children ) : 
                                    ?>
                                    <ol class="dd-list">
                                        <?php 
                                        foreach ( $eaz_children as $sub_child ) :

                                            $sub_post_status        = $sub_child->post_status;
                                            $child_of               = eaz_get_nestable_children( $sub_child->ID );
                                            $eaz_children_sub_class = $child_of ? ' dd3-have-sub-children dd3-has-children ' : ' dd3-have-no-sub-children ';

                                            ?>
                                            <li <?php post_class("dd-item dd3-item dd-item-child depth-2 easydocs-accordion-item " . esc_attr( $sub_post_status . ' ' . $eaz_children_sub_class . ' child-of-' . $sub_child->ID ) ); ?> data-id="<?php echo esc_attr( $sub_child->ID ); ?>">

                                                <?php 
                                                ezd_child_docs_drag_icon();
                                                ezd_child_docs_title( $sub_child->ID, 2, $child->ID );
                                                
                                                if ( $child_of ) : 
                                                    ?>
                                                    <ol class="dd-list">
                                                        <?php 
                                                        foreach ( $child_of as $of_sub_child ) :

                                                            $sub_post_status            = $of_sub_child->post_status;
                                                            $child_of_child             = eaz_get_nestable_children( $of_sub_child->ID );
                                                            $eaz_children_sub_sub_class = $child_of_child ? ' dd3-have-sub-sub-children dd3-has-children ' : ' dd3-have-no-sub-sub-children ';

                                                            ?>
                                                            <li <?php post_class("dd-item dd3-item depth-3 child-of-child easydocs-accordion-item accordion mix child-one " . esc_attr( $sub_post_status . ' ' . $eaz_children_sub_sub_class ) ); ?> data-id="<?php echo esc_attr( $of_sub_child->ID ); ?>">

                                                                <?php 
                                                                ezd_child_docs_drag_icon();
                                                                ezd_child_docs_title( $of_sub_child->ID, 3, $sub_child->ID );
                                                                
                                                                if ( $child_of_child && ezd_is_premium() ) : 
                                                                    ?>
                                                                    <ol class="dd-list">
                                                                        <?php 
                                                                        foreach ( $child_of_child as $fourth_level_child ) :
                                                                            $fourth_post_status = $fourth_level_child->post_status;
                                                                            ?>
                                                                            <li <?php post_class("dd-item dd3-item depth-4 child-of-child-of-child easydocs-accordion-item mix child-two " . esc_attr( $fourth_post_status ) ); ?> data-id="<?php echo esc_attr( $fourth_level_child->ID ); ?>">

                                                                                <?php 
                                                                                ezd_child_docs_drag_icon();
                                                                                ezd_child_docs_title( $fourth_level_child->ID, 4, $of_sub_child->ID );
                                                                                ?>

                                                                            </li>
                                                                            <?php 
                                                                        endforeach; 
                                                                        ?>
                                                                    </ol>
                                                                    <?php 
                                                                endif; 
                                                                ?>
                                                            </li>
                                                            <?php 
                                                        endforeach; 
                                                        ?>
                                                    </ol>
                                                    <?php 
                                                endif; 
                                                ?>
                                            </li>
                                            <?php 
                                        endforeach; 
                                        ?>
                                    </ol>
                                    <?php 
                                endif; 
                                ?>
                            </li>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </ol>

            </div>

            <?php 
            if ( current_user_can( 'publish_docs' ) ) :                
                $parent_id   = absint( $item );
                $nonce       = wp_create_nonce( $parent_id );
                ?>
                <button class="button button-info section-doc" name="submit" data-url="<?php echo esc_url( admin_url( 'admin.php' ) . "?Create_Section=yes&_wpnonce={$nonce}&parentID={$parent_id}&is_section=" );; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Add Section to %s', 'eazydocs' ), $parent_title ) ); ?>">
                    <?php esc_html_e( 'Add Section', 'eazydocs' ); ?>
                </button>
                <?php
            endif;
                
            if ( current_user_can( 'manage_options' ) ) : 
                $current_theme = get_template();
                if ( $current_theme == 'docy' || $current_theme == 'docly' || ezd_is_premium() ) {
                    eazydocs_one_page( $item );
                }
            endif;
            ?>
            
        </div>
    <?php
    endforeach;
endif;