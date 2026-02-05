<?php
/**
 * Admin Helper Functions for EazyDocs
 * This file contains reusable functions to reduce code duplication in admin templates and classes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get post status information
 *
 * @param string $status Post status
 * @return array Array with icon class and status text
 */
function ezd_get_post_status_info( $status ) {
    $status_info = [
        'publish' => [
            'icon' => 'admin-site-alt3',
            'text' => esc_html__( 'Public Doc', 'eazydocs' ),
            'class' => 'publish'
        ],
        'private' => [
            'icon' => 'privacy',
            'text' => esc_html__( 'Private Doc', 'eazydocs' ),
            'class' => 'private'
        ],
        'draft' => [
            'icon' => 'edit-page',
            'text' => esc_html__( 'Drafted Doc', 'eazydocs' ),
            'class' => 'draft'
        ],
        'protected' => [
            'icon' => 'lock',
            'text' => esc_html__( 'Protected Doc', 'eazydocs' ),
            'class' => 'protected'
        ]
    ];

    return $status_info[ $status ] ?? $status_info['publish'];
}

/**
 * Render post status badge
 *
 * @param int $post_id Post ID
 * @param string $class Additional CSS class
 */
function ezd_render_post_status_badge( $post_id, $class = '' ) {
    $status = get_post_status( $post_id );
    $status_info = ezd_get_post_status_info( $status );

    echo '<span class="ezd-status-badge ' . esc_attr( $status_info['class'] . ' ' . $class ) . '">';
    echo '<span class="dashicons dashicons-' . esc_attr( $status_info['icon'] ) . '"></span>';
    echo esc_html( $status_info['text'] );
    echo '</span>';
}

/**
 * Get child docs count for a parent doc
 *
 * @param int $parent_id Parent post ID
 * @param array $statuses Post statuses to include
 * @return int Count of child docs
 */
function ezd_get_child_docs_count( $parent_id, $statuses = [ 'publish', 'draft', 'private' ] ) {
    $child_docs = get_pages( [
        'child_of' => $parent_id,
        'post_type' => 'docs',
        'post_status' => $statuses
    ] );

    return count( $child_docs );
}

/**
 * Render child docs count badge
 *
 * @param int $parent_id Parent post ID
 * @param string $class Additional CSS class
 * @param array $statuses Post statuses to include
 */
function ezd_render_child_count_badge( $parent_id, $class = 'ezd-badge', $statuses = [ 'publish', 'draft', 'private' ] ) {
    $count = ezd_get_child_docs_count( $parent_id, $statuses );

    if ( $count > 0 ) {
        echo '<span class="' . esc_attr( $class ) . '">' . esc_html( $count ) . '</span>';
    }
}

/**
 * Render action buttons for docs
 *
 * @param int $doc_id Doc post ID
 * @param array $actions Array of actions to include
 * @param array $item Additional item data
 */
function ezd_render_doc_actions( $doc_id, $actions = [ 'duplicate', 'add_child', 'visibility', 'view' ], $item = [] ) {
    if ( ! ezd_is_admin_or_editor( $doc_id, 'edit' ) ) {
        return;
    }

    echo '<ul class="actions">';

    foreach ( $actions as $action ) {
        switch ( $action ) {
            case 'duplicate':
                if ( ezd_is_premium() ) {
                    echo '<li class="duplicate">';
                    do_action( 'eazydocs_duplicate', $doc_id, $item );
                    echo '</li>';
                } else {
                    echo '<li class="duplicate">';
                    echo '<a href="javascript:void(0);" class="eazydocs-pro-notice" title="' . esc_attr__( 'Duplicate this doc with the child docs.', 'eazydocs' ) . '">';
                    echo '<span class="dashicons dashicons-admin-page"></span>';
                    echo '</a>';
                    echo '</li>';
                }
                break;

            case 'add_child':
                $depth = isset( $item['depth'] ) ? $item['depth'] : 1;
                $is_premium = ! ezd_is_premium() && 3 === $depth ? false : ( ezd_is_premium() && 4 === $depth ? false : true );

                if ( $is_premium ) {
                    echo '<li>';
                    echo '<a href="' . esc_url( admin_url( 'admin.php' ) ) . '?Create_Child=yes&childID=' . esc_attr( $doc_id ) . '&_wpnonce=' . esc_attr( wp_create_nonce( $doc_id ) ) . '&child=" class="child-doc" title="' . esc_attr__( 'Add new doc under this doc', 'eazydocs' ) . '">';
                    echo '<span class="dashicons dashicons-plus-alt2"></span>';
                    echo '</a>';
                    echo '</li>';
                }
                break;

            case 'visibility':
                if ( ezd_is_premium() && current_user_can( 'manage_options' ) ) {
                    echo '<li class="visibility">';
                    do_action( 'eazydocs_visibility', $doc_id );
                    echo '</li>';
                }
                break;

            case 'view':
                echo '<li>';
                echo '<a href="' . esc_url( get_permalink( $doc_id ) ) . '" target="_blank" title="' . esc_attr__( 'View this doc item in new tab', 'eazydocs' ) . '">';
                echo '<span class="dashicons dashicons-external"></span>';
                echo '</a>';
                echo '</li>';
                break;
        }
    }

    echo '</ul>';
}

/**
 * Render filter buttons for doc status
 *
 * @param array $filters Array of filters to include
 */
function ezd_render_doc_filters( $filters = [ 'all', 'publish', 'private', 'protected', 'draft' ] ) {
    $filter_config = [
        'all' => [
            'label' => esc_html__( 'All articles', 'eazydocs' ),
            'icon' => 'media-document',
            'class' => 'easydocs-btn-black-light is-active'
        ],
        'publish' => [
            'label' => esc_html__( 'Public', 'eazydocs' ),
            'icon' => 'admin-site-alt3',
            'class' => 'easydocs-btn-green-light'
        ],
        'private' => [
            'label' => esc_html__( 'Private', 'eazydocs' ),
            'icon' => 'privacy',
            'class' => 'easydocs-btn-blue-light'
        ],
        'protected' => [
            'label' => esc_html__( 'Protected', 'eazydocs' ),
            'icon' => 'lock',
            'class' => 'easydocs-btn-orange-light'
        ],
        'draft' => [
            'label' => esc_html__( 'Draft', 'eazydocs' ),
            'icon' => 'edit-page',
            'class' => 'easydocs-btn-gray-light'
        ]
    ];

    echo '<ul class="single-item-filter">';

    foreach ( $filters as $filter ) {
        if ( isset( $filter_config[ $filter ] ) ) {
            $config = $filter_config[ $filter ];
            echo '<li class="easydocs-btn easydocs-btn-rounded easydocs-btn-sm ' . esc_attr( $config['class'] ) . '" data-filter="' . ( 'all' === $filter ? 'all' : '.' . $filter ) . '">';
            echo '<span class="dashicons dashicons-' . esc_attr( $config['icon'] ) . '"></span>';
            echo esc_html( $config['label'] );
            echo '</li>';
        }
    }

    echo '</ul>';
}

/**
 * Render setup wizard step wrapper
 *
 * @param int $step Step number
 * @param string $title Step title
 * @param string $description Step description
 * @param bool $is_active Whether this step is active
 * @param string $additional_classes Additional CSS classes
 */
function ezd_render_setup_step_wrapper( $step, $title, $description = '', $is_active = false, $additional_classes = '' ) {
    $display = $is_active ? '' : 'style="display:none"';
    $classes = 'tab-pane';
    if ( $additional_classes ) {
        $classes .= ' ' . esc_attr( $additional_classes );
    }

    echo '<div id="step-' . esc_attr( $step ) . '" class="' . $classes . '" role="tabpanel" ' . $display . '>';

    if ( $title ) {
        echo '<h2>' . esc_html( $title ) . '</h2>';
    }

    if ( $description ) {
        echo '<p>' . esc_html( $description ) . '</p>';
    }
}

/**
 * Render setup wizard navigation buttons
 *
 * @param array $buttons Array of button configurations
 */
function ezd_render_setup_buttons( $buttons ) {
    echo '<div class="button-inline">';

    foreach ( $buttons as $button ) {
        $href = $button['href'] ?? '#';
        $target = isset( $button['target'] ) ? 'target="' . esc_attr( $button['target'] ) . '"' : '';
        $class = 'button button-primary ezd-btn btn-lg ' . ( $button['class'] ?? '' );
        $icon = $button['icon'] ?? '';
        $text = $button['text'] ?? '';

        echo '<a class="' . esc_attr( $class ) . '" ' . $target . ' href="' . esc_url( $href ) . '">';
        if ( $icon ) {
            echo '<i class="dashicons dashicons-' . esc_attr( $icon ) . '"></i>';
        }
        echo esc_html( $text );
        echo '</a>';
    }

    echo '</div>';
}