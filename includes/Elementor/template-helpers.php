<?php
/**
 * Template Helper Functions for EazyDocs Elementor Widgets
 * This file contains reusable functions to reduce code duplication in template files.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render a list of doc items
 *
 * @param array $doc_items Array of doc post objects
 * @param string $class CSS class for the ul element
 * @param string $link_class CSS class for the a elements
 */
function ezd_render_doc_items_list( $doc_items, $class = 'ezd-list-unstyled tag_list', $link_class = 'ezd_item_list_title' ) {
    if ( empty( $doc_items ) ) {
        return;
    }

    echo '<ul class="' . esc_attr( $class ) . '">';
    foreach ( $doc_items as $doc_item ) {
        echo '<li>';
        echo '<a href="' . esc_url( get_permalink( $doc_item->ID ) ) . '" class="' . esc_attr( $link_class ) . '">';
        echo esc_html( $doc_item->post_title );
        if ( function_exists( 'ezdpro_badge' ) && ezd_is_premium() ) {
            echo ezdpro_badge( $doc_item->ID );
        }
        echo '</a>';
        echo '</li>';
    }
    echo '</ul>';
}

/**
 * Render section title with badge
 *
 * @param object $section Section post object
 * @param string $title_class CSS class for title
 * @param string $badge_text Badge text (e.g., 'Topics')
 * @param bool $show_count Show count of child docs
 */
function ezd_render_section_title( $section, $title_class = 'title', $badge_text = 'Topics', $show_count = true ) {
    if ( empty( $section->post_title ) ) {
        return;
    }

    $doc_counter = get_pages( [
        'child_of'  => $section->ID,
        'post_type' => 'docs',
    ] );

    echo '<h4 class="' . esc_attr( $title_class ) . '">';
    echo esc_html( $section->post_title );
    echo '</h4>';

    if ( $show_count && count( $doc_counter ) > 0 ) {
        echo '<span class="ezd-badge">';
        echo count( $doc_counter ) . ' ' . esc_html( $badge_text );
        echo '</span>';
    }
}

/**
 * Render read more button
 *
 * @param string $url URL for the button
 * @param string $text Button text
 * @param string $class CSS class for button
 * @param string $icon Icon HTML
 */
function ezd_render_read_more_btn( $url, $text, $class = 'doc_border_btn', $icon = '<i class="arrow_right"></i>' ) {
    if ( empty( $text ) || empty( $url ) ) {
        return;
    }

    echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">';
    echo esc_html( $text );
    echo wp_kses_post( $icon );
    echo '</a>';
}

/**
 * Render private/protected doc indicators
 *
 * @param int $post_id Post ID
 */
function ezd_render_doc_indicators( $post_id ) {
    if ( get_post_status( $post_id ) === 'private' ) {
        echo '<div class="private" title="' . esc_attr__( 'Private Doc', 'eazydocs' ) . '"><i class="icon_lock"></i></div>';
    }

    if ( ! empty( get_post( $post_id )->post_password ) ) {
        echo '<div class="private" title="' . esc_attr__( 'Password Protected Doc', 'eazydocs' ) . '">';
        echo '<svg width="50px" height="50px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#4e5668">';
        echo '<g><path fill="none" d="M0 0h24v24H0z"/><path d="M18 8h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h2V7a6 6 0 1 1 12 0v1zm-2 0V7a4 4 0 1 0-8 0v1h8zm-5 6v2h2v-2h-2zm-4 0v2h2v-2H7zm8 0v2h2v-2h-2z"/></g>';
        echo '</svg>';
        echo '</div>';
    }
}

/**
 * Get doc items for a section
 *
 * @param int $section_id Section post ID
 * @param array $settings Widget settings
 * @return array
 */
function ezd_get_doc_items( $section_id, $settings = [] ) {
    return get_children( [
        'post_parent'    => $section_id,
        'post_type'      => 'docs',
        'post_status'    => ['publish', 'private'],
        'orderby'        => $settings['order_by'] ?? 'menu_order',
        'order'          => $settings['child_order'] ?? 'ASC',
        'posts_per_page' => ! empty( $settings['ppp_doc_items'] ) ? $settings['ppp_doc_items'] : -1,
    ] );
}

/**
 * Render masonry JavaScript
 *
 * @param string $selector CSS selector for masonry containers
 */
function ezd_render_masonry_script( $selector = '.ezd-masonry' ) {
    ?>
    <script>
        ;(function ($) {
            'use strict';

            $(document).ready(function () {
                function ezd_masonry() {
                    $('<?php echo esc_js( $selector ); ?>').each(function () {
                        var $masonry = $(this);
                        var masonryCols = $masonry.attr('ezd-massonry-col');
                        var masonryColumns = parseInt(masonryCols);

                        if ($(window).width() <= 1024) {
                            masonryColumns = 2;
                        }
                        if ($(window).width() <= 768) {
                            masonryColumns = 1;
                        }

                        var count = 0;
                        var content = $masonry.children();

                        var $columnsContainer = $('<div class="ezd-masonry-columns"></div>');

                        content.each(function (index) {
                            count = count + 1;
                            $(this).addClass('ezd-masonry-sort-' + count);

                            if (count === masonryColumns) {
                                count = 0;
                            }
                        });

                        for (var i = 1; i <= masonryColumns; i++) {
                            $columnsContainer.append('<div class="ezd-masonry-' + i + '"></div>');
                        }

                        for (var i = 1; i <= masonryColumns; i++) {
                            $masonry.find('.ezd-masonry-sort-' + i).appendTo($columnsContainer.find('.ezd-masonry-' + i));
                        }

                        $masonry.empty().append($columnsContainer);
                    });
                }
                ezd_masonry();
            });
        })(jQuery);
    </script>
    <?php
}

/**
 * Render background shape/image
 *
 * @param array $settings Settings array containing bg_shape
 * @param string $class CSS class for the image
 * @param string $alt Alt text
 */
function ezd_render_bg_shape( $settings, $class = 'doc_shap_one', $alt = 'curve shape' ) {
    $settings_key = $settings['bg_shape'];

    if ( ! empty( $settings_key['id'] ) ) {
        echo wp_get_attachment_image( $settings_key['id'], 'full', '', [ 'class' => $class ] );
    } elseif ( ! empty( $settings_key['url'] ) && empty( $settings_key['id'] ) ) {
        $class_attr = ! empty( $class ) ? 'class="' . esc_attr( $class ) . '"' : '';
        $img_url = isset( $settings_key['url'] ) ? esc_url( $settings_key['url'] ) : '';
        $alt_attr = isset( $alt ) ? esc_attr( $alt ) : '';

        echo '<img src="' . $img_url . '" ' . $class_attr . ' alt="' . $alt_attr . '">';
    }
}

/**
 * Render scroller buttons for tab navigation
 *
 * @param string $direction 'left' or 'right'
 * @param string $icon_class Icon class for the arrow
 */
function ezd_render_scroller_btn( $direction = 'left', $icon_class = '' ) {
    if ( empty( $icon_class ) ) {
        $icon_class = ( $direction === 'left' ) ? 'arrow_carrot-left' : 'arrow_carrot-right';
    }

    echo '<span class="scroller-btn ' . esc_attr( $direction ) . '"><i class="' . esc_attr( $icon_class ) . '"></i></span>';
}