<?php

$child_docs_depth  = [];
$depth_two_parents = [];
$depth_three_parents = [];
$ids               = '';
$container         = 1;
if (is_array($depth_one_parents)) :
    foreach ($depth_one_parents as $item) :
        $ids++;
        $container++;
        $active = $ids == 1 ? ' tab-active' : '';
?>
        <div class="easydocs-tab<?php echo esc_attr($active); ?>" id="tab-<?php echo esc_attr($item); ?>">
            <div class="easydocs-filter-container">
                <ul class="single-item-filter">
                    <li class="easydocs-btn easydocs-btn-black-light easydocs-btn-rounded easydocs-btn-sm is-active" data-filter="all">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php esc_html_e('All articles', 'eazydocs'); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-green-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".publish">
                        <span class="dashicons dashicons-admin-site-alt3"></span>
                        <?php esc_html_e('Public', 'eazydocs'); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".private">
                        <span class="dashicons dashicons-privacy"></span>
                        <?php esc_html_e('Private', 'eazydocs'); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-orange-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".protected">
                        <span class="dashicons dashicons-lock"></span>
                        <?php esc_html_e('Protected', 'eazydocs'); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-gray-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".draft">
                        <span class="dashicons dashicons-edit-page"></span>
                        <?php esc_html_e('Draft', 'eazydocs'); ?>
                    </li>
                </ul>
            </div>
            <div class="easydocs-accordion sortabled dd accordionjs nestables-child" id="nestable-<?php echo esc_attr($item); ?>">
                <ol class="dd-list">
                    <?php

                    $children = get_children(array(
                        'post_parent'   => $item,
                        'post_type'     => 'docs',
                        'orderby'       => 'menu_order',
                        'order'         => 'asc',
                        'exclude'      => get_post_thumbnail_id($item)
                    ));

                    if (is_array($children)) :
                        foreach ($children as $child) :

                            $post_status = $child->post_status;

                            $doc_items = get_children(array(
                                'post_parent'   => $child->ID,
                                'orderby'       => 'menu_order',
                                'post_type'     => 'docs',
                                'order'         => 'asc',
                                'exclude'       => get_post_thumbnail_id($child)
                            ));
                            $child_one = get_children([
                                'post_parent'       => $child->ID,
                                'post_type'         => 'docs',
                                'order'             => 'asc',
                                'orderby'           => 'menu_order',
                                'fields'            => 'ids'
                            ]);

                            $depth_two = '';
                            foreach ($doc_items as $doc_item) {
                                $child_depth = get_children(array(
                                    'post_parent'   => $doc_item->ID,
                                    'post_type'     => 'docs',
                                    'fields'        => 'ids',
                                    'orderby'       => 'menu_order',
                                    'order'         => 'asc'
                                ));
                                $depth_two = implode(",", $child_depth);
                            }
                            $depth_docs = implode(",", $child_one) . ',' . $depth_two . $child->ID;
                            if (!empty($child->post_password)) {
                                $post_status = 'protected';
                            }

                            $get_section_child_one = array(
                                'post_parent' => $child->ID, // Current post's ID
                            );
                            $get_section_children_one = get_children($get_section_child_one);
                            $eaz_children = eaz_get_nestable_children($child->ID);
                            $eaz_children_class = $eaz_children ? ' dd3-have-children dd3-has-children ' : ' dd3-have-no-children ';
                    ?>
                            <li <?php post_class("dd-item dd3-item dd-item-parent easydocs-accordion-item accordion ez-section-acc-item mix " . esc_attr($post_status . ' ' . $eaz_children_class . ' child-' . $child->ID)); ?> data-id="<?php echo esc_attr($child->ID); ?>">
                                <div class="dd-handle dd3-handle">
                                    <svg class="dd-handle-icon" width="12px" height="12px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" title="<?php esc_attr_e('Hold the mouse and drag to move this doc.', 'eazydocs'); ?>">
                                        <path fill="none" stroke="#000" stroke-width="2" d="M15,5 L17,5 L17,3 L15,3 L15,5 Z M7,5 L9,5 L9,3 L7,3 L7,5 Z M15,13 L17,13 L17,11 L15,11 L15,13 Z M7,13 L9,13 L9,11 L7,11 L7,13 Z M15,21 L17,21 L17,19 L15,19 L15,21 Z M7,21 L9,21 L9,19 L7,19 L7,21 Z" />
                                    </svg>
                                </div>
                                <div class="dd3-content">
                                    <div class="accordion-title ez-section-title expand--child <?php echo count($doc_items) > 0 ? 'has-child' : ''; ?>">

                                        <?php
                                        $edit_link = 'javascript:void(0)';
                                        $target = '_self';
                                        if (current_user_can('editor') || current_user_can('administrator')) {
                                            $edit_link = get_edit_post_link($child);
                                            $target = '_blank';
                                        }
                                        ?>
                                        <div class="left-content">
                                            <h4>
                                                <a href="<?php echo esc_attr($edit_link); ?>" target="<?php echo esc_attr($target); ?>">
                                                    <?php echo $child->post_title; ?>
                                                </a>
                                                <?php if (count($doc_items) > 0) : ?>
                                                    <span class="count ezd-badge">
                                                        <?php echo count($doc_items) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </h4>
                                            <ul class="actions">
                                                <?php
                                                if (current_user_can('editor') || current_user_can('administrator')) :

                                                    if ( ezd_is_premium() ) : ?>
                                                        <li class="duplicate">
                                                            <?php do_action('eazydocs_section_doc_duplicate', $child->ID, $item); ?>
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
                                                        <a href="<?php echo admin_url('admin.php'); ?>/Create_Post.php?childID=<?php echo $child->ID; ?>&child=" class="child-doc" title="<?php esc_attr_e('Add new doc under this doc', 'eazydocs'); ?>">
                                                            <span class="dashicons dashicons-plus-alt2"></span>
                                                        </a>
                                                    </li>
                                                    <?php endif;

                                                if ( ezd_is_premium() ) :
                                                    if (!empty($get_section_children_one)) :
                                                    ?>
                                                        <li class="visibility">
                                                            <?php do_action('eazydocs_doc_visibility_depth_one', $child->ID); ?>
                                                        </li>
                                                <?php
                                                    endif;
                                                endif;
                                                ?>

                                                <li>
                                                    <a href="<?php echo get_permalink($child); ?>" target="_blank" title="<?php esc_attr_e('View this doc item in new tab', 'easydocs') ?>">
                                                        <span class="dashicons dashicons-external"></span>
                                                    </a>
                                                </li>
                                                <?php
                                                if (current_user_can('editor') || current_user_can('administrator')) :
                                                ?>
                                                    <li class="delete">
                                                        <a href="<?php echo admin_url('admin.php'); ?>/Delete_Post.php?ID=<?php echo $depth_docs; ?>" class="section-delete" title="<?php esc_attr_e('Move to Trash', 'eazydocs'); ?>">
                                                            <span class="dashicons dashicons-trash"></span>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>

                                        <div class="right-content">
                                            <span class="progress-text">
                                                <?php
                                                $positive = (int) get_post_meta($child->ID, 'positive');
                                                $negative = (int) get_post_meta($child->ID, 'negative', true);

                                                $positive_title      = $positive ? sprintf(_n('%d Positive vote, ', '%d Positive votes and ', $positive, 'eazydocs'), number_format_i18n($positive)) : esc_html__('No Positive votes, ', 'eazydocs');
                                                $negative_title      = $negative ? sprintf(_n('%d Negative vote found.', '%d Negative votes found.', $negative, 'eazydocs'), number_format_i18n($negative)) : esc_html__('No Negative votes.', 'eazydocs');

                                                $sum_votes = $positive + $negative;

                                                if ($positive || $negative) {
                                                    echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
                                                } else {
                                                    esc_html_e('No rates', 'eazydocs');
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($eaz_children) : ?>
                                    <ol class="dd-list">
                                        <?php foreach ($eaz_children as $sub_child) :
                                            $sub_post_status = $sub_child->post_status;

                                            $child_depth = get_children(array(
                                                'post_parent'   => $sub_child->ID,
                                                'orderby'       => 'menu_order',
                                                'post_type'     => 'docs',
                                                'order'         => 'asc',
                                                'exclude'       => get_post_thumbnail_id($sub_child)
                                            ));

                                            $last_section_docs = [];
                                            if (is_array($child_depth)) {
                                                foreach ($child_depth as $dep3_docs) {
                                                    $last_section_docs[] = $dep3_docs->ID;
                                                }
                                            }
                                            $last_section_ids = implode(",", $last_section_docs);
                                            $parent = '';
                                            foreach ($depth_two_parents as $sec2) {
                                                $parent = $sec2;
                                            }

                                            // get parent id of the current post
                                            $depth_docs = $sub_child->ID;                                           

                                            $dep2               = $sub_child->ID ?? '';
                                            $section_child      = get_post_parent($dep2, 'docs', 'post_type');
                                            $child_parent_sec   = $section_child->ID ?? '';

                                            $get_section_child = array(
                                                'post_parent' => $dep2, // Current post's ID
                                            );
                                            
                                            $get_section_children_two = get_children($get_section_child);
                                            $child_of = eaz_get_nestable_children($sub_child->ID);
                                            $eaz_children_sub_class = $child_of ? ' dd3-have-sub-children dd3-has-children ' : ' dd3-have-no-sub-children ';
                                        ?>
                                            <li <?php post_class("dd-item dd3-item dd-item-child easydocs-accordion-item " . $sub_post_status . ' ' . $eaz_children_sub_class . ' child-of-' . $sub_child->ID); ?> data-id="<?php echo esc_attr($sub_child->ID); ?>">
                                                <div class="dd-handle dd3-handle">
                                                    <svg class="dd-handle-icon" width="12px" height="12px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" title="<?php esc_attr_e('Hold the mouse and drag to move this doc.', 'eazydocs'); ?>">
                                                        <path fill="none" stroke="#000" stroke-width="2" d="M15,5 L17,5 L17,3 L15,3 L15,5 Z M7,5 L9,5 L9,3 L7,3 L7,5 Z M15,13 L17,13 L17,11 L15,11 L15,13 Z M7,13 L9,13 L9,11 L7,11 L7,13 Z M15,21 L17,21 L17,19 L15,19 L15,21 Z M7,21 L9,21 L9,19 L7,19 L7,21 Z" />
                                                    </svg>
                                                </div>
                                                <div class="dd3-content">
                                                    <div class="accordion-title  expand--child <?php echo count($child_depth) > 0 ? 'has-child' : ''; ?>">
                                                        <?php
                                                        $edit_link = 'javascript:void(0)';
                                                        $target = '_self';
                                                        if (current_user_can('editor') || current_user_can('administrator')) {
                                                            $edit_link = get_edit_post_link($sub_child);
                                                            $target = '_blank';
                                                        }
                                                        ?>
                                                        <div class="left-content">
                                                            <h4>
                                                                <a href="<?php echo esc_attr($edit_link); ?>" target="<?php echo esc_attr($target); ?>" class="section-last-label">
                                                                    <?php echo get_the_title($sub_child); ?>
                                                                </a>
                                                                <?php if (count($child_depth) > 0) : ?>
                                                                    <span class="count ezd-badge">
                                                                        <?php echo count($child_depth) ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </h4>
                                                            <ul class="actions">
                                                                <?php
                                                                if (current_user_can('editor') || current_user_can('administrator')) :
                                                                    if ( ezd_is_premium() ) : ?>
                                                                        <li class="duplicate">
                                                                            <?php do_action('eazydocs_child_section_doc_duplicate', $dep2, $child_parent_sec); ?>
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
                                                                    <a href="<?php echo admin_url('admin.php'); ?>/Create_Post.php?childID=<?php echo $sub_child->ID; ?>&child=" class="child-doc" title="<?php esc_attr_e('Add new doc under this doc', 'eazydocs'); ?>">
                                                                        <span class="dashicons dashicons-plus-alt2"></span>
                                                                    </a>
                                                                </li>
                                                                <?php
                                                                if ( ezd_is_premium() ) :
                                                                    if (!empty($get_section_children_two)) :
                                                                ?>
                                                                        <li class="visibility">
                                                                            <?php do_action('eazydocs_doc_visibility_depth_two', $dep2); ?>
                                                                        </li>
                                                                <?php
                                                                    endif;
                                                                endif;
                                                                ?>
                                                                <li>
                                                                    <a href="<?php echo get_permalink($sub_child); ?>" target="_blank" title="<?php esc_attr_e('View this doc item in new tab', 'easydocs') ?>">
                                                                        <span class="dashicons dashicons-external"></span>
                                                                    </a>
                                                                </li>
                                                                <?php if (current_user_can('editor') || current_user_can('administrator')) : ?>
                                                                    <li class="delete">
                                                                        <a href="<?php echo admin_url('admin.php'); ?>/Delete_Post.php?ID=<?php echo esc_attr($sub_child->ID . ',' . $last_section_ids); ?>" class="section-delete" title="<?php esc_attr_e('Move to Trash', 'eazydocs'); ?>">
                                                                            <span class="dashicons dashicons-trash"></span>
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                        <div class="right-content">
                                                            <span class="progress-text">
                                                                <?php
                                                                $positive = (int) get_post_meta($sub_child->ID, 'positive');
                                                                $negative = (int) get_post_meta($sub_child->ID, 'negative', true);

                                                                $positive_title      = $positive ? sprintf(_n('%d Positive vote, ', '%d Positive votes and ', $positive, 'eazydocs'), number_format_i18n($positive)) : esc_html__('No Positive votes, ', 'eazydocs');
                                                                $negative_title      = $negative ? sprintf(_n('%d Negative vote found.', '%d Negative votes found.', $negative, 'eazydocs'), number_format_i18n($negative)) : esc_html__('No Negative votes.', 'eazydocs');

                                                                $sum_votes = $positive + $negative;

                                                                if ($positive || $negative) {
                                                                    echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
                                                                } else {
                                                                    esc_html_e('No rates', 'eazydocs');
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if ($child_of) : ?>
                                                    <ol class="dd-list">
                                                        <?php foreach ($child_of as $of_sub_child) :
                                                            $sub_post_status = $of_sub_child->post_status;

                                                            $child_depth = get_children(array(
                                                                'post_parent'   => $of_sub_child->ID,
                                                                'orderby'       => 'menu_order',
                                                                'post_type'     => 'docs',
                                                                'order'         => 'asc',
                                                                'exclude'       => get_post_thumbnail_id($of_sub_child)
                                                            ));

                                                            $last_section_docs = [];
                                                            if (is_array($child_depth)) {
                                                                foreach ($child_depth as $dep3_docs) {
                                                                    $last_section_docs[] = $dep3_docs->ID;
                                                                }
                                                            }
                                                            $last_section_ids = implode(",", $last_section_docs);
                                                            $parent = '';
                                                            foreach ($depth_two_parents as $sec2) {
                                                                $parent = $sec2;
                                                            }

                                                            $dep2 = $of_sub_child->ID;

                                                            $get_section_child = array(
                                                                'post_parent' => $dep2, // Current post's ID
                                                            );
                                                            $get_section_children_two = get_children($get_section_child);
                                                        ?>
                                                            <li <?php post_class("dd-item dd3-item child-of-child easydocs-accordion-item accordion mix child-one"); ?> data-id="<?php echo esc_attr($of_sub_child->ID); ?>">
                                                                <div class="dd-handle dd3-handle">
                                                                    <svg class="dd-handle-icon" width="12px" height="12px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" title="<?php esc_attr_e('Hold the mouse and drag to move this doc.', 'eazydocs'); ?>">
                                                                        <path fill="none" stroke="#000" stroke-width="2" d="M15,5 L17,5 L17,3 L15,3 L15,5 Z M7,5 L9,5 L9,3 L7,3 L7,5 Z M15,13 L17,13 L17,11 L15,11 L15,13 Z M7,13 L9,13 L9,11 L7,11 L7,13 Z M15,21 L17,21 L17,19 L15,19 L15,21 Z M7,21 L9,21 L9,19 L7,19 L7,21 Z" />
                                                                    </svg>
                                                                </div>
                                                                <div class="dd3-content">
                                                                    <div class="accordion-title expand--child <?php echo count($child_depth) > 0 ? 'has-child' : ''; ?>">

                                                                        <?php
                                                                        $edit_link = 'javascript:void(0)';
                                                                        $target = '_self';
                                                                        if (current_user_can('editor') || current_user_can('administrator')) {
                                                                            $edit_link = get_edit_post_link($of_sub_child);
                                                                            $target = '_blank';
                                                                        }
                                                                        ?>
                                                                        <div class="left-content">

                                                                            <h4>
                                                                                <a href="<?php echo esc_attr($edit_link); ?>" target="<?php echo esc_attr($target); ?>" class="child-last-label">
                                                                                    <?php echo get_the_title($of_sub_child); ?>
                                                                                </a>
                                                                            </h4>
                                                                            <ul class="actions">
                                                                                <?php
                                                                                if (current_user_can('editor') || current_user_can('administrator')) :
                                                                                    if ( ezd_is_premium() ) : ?>
                                                                                        <li>
                                                                                            <?php do_action('eazydocs_single_duplicate', $of_sub_child->ID); ?>
                                                                                        </li>
                                                                                    <?php
                                                                                    else :
                                                                                    ?>
                                                                                        <li>
                                                                                            <a href="javascript:void(0);" class="eazydocs-pro-notice" title="<?php esc_attr_e('Duplicate this doc with the child docs.', 'easydocs'); ?>">
                                                                                                <span class="dashicons dashicons-admin-page"></span>
                                                                                            </a>
                                                                                        </li>
                                                                                <?php
                                                                                    endif;
                                                                                endif;
                                                                                ?>

                                                                                <li>
                                                                                    <a href="<?php echo get_permalink($of_sub_child); ?>" target="_blank" title="<?php esc_attr_e('View this doc item in new tab', 'easydocs') ?>">
                                                                                        <span class="dashicons dashicons-external"></span>
                                                                                    </a>
                                                                                </li>
                                                                                <?php if (current_user_can('editor') || current_user_can('administrator')) : ?>
                                                                                    <li class="delete">
                                                                                        <a href="<?php echo admin_url('admin.php'); ?>/Delete_Post.php?ID=<?php echo esc_attr($of_sub_child->ID . ',' . $last_section_ids); ?>" class="child-delete" title="<?php esc_attr_e('Move to Trash', 'eazydocs'); ?>">
                                                                                            <span class="dashicons dashicons-trash"></span>
                                                                                        </a>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="right-content">
                                                                            <span class="progress-text">
                                                                                <?php
                                                                                $positive = (int) get_post_meta($of_sub_child->ID, 'positive');
                                                                                $negative = (int) get_post_meta($of_sub_child->ID, 'negative', true);

                                                                                $positive_title      = $positive ? sprintf(_n('%d Positive vote, ', '%d Positive votes and ', $positive, 'eazydocs'), number_format_i18n($positive)) : esc_html__('No Positive votes, ', 'eazydocs');
                                                                                $negative_title      = $negative ? sprintf(_n('%d Negative vote found.', '%d Negative votes found.', $negative, 'eazydocs'), number_format_i18n($negative)) : esc_html__('No Negative votes.', 'eazydocs');

                                                                                $sum_votes = $positive + $negative;

                                                                                if ($positive || $negative) {
                                                                                    echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
                                                                                } else {
                                                                                    esc_html_e('No rates', 'eazydocs');
                                                                                }
                                                                                ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ol>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php endif; ?>
                            </li>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </ol>
            </div>
            <button class="button button-info section-doc" id="section-doc" name="submit" data-url="<?php echo admin_url('admin.php'); ?>/Create_Post.php?parentID=<?php echo $item; ?>&is_section=">
                <?php esc_html_e('Add Section', 'eazydocs'); ?>
            </button>
            <?php
            $current_theme      = get_template();
            if ($current_theme == 'docy' || $current_theme == 'docly' || ezd_is_premium() ) {
                eazydocs_one_page($item);
            }
            ?>
        </div>
<?php
    endforeach;
endif;
?>