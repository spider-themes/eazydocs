<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Shared AJAX live-search behaviour (registered in Frontend\Assets). Enqueued here
// so the widget works on any page, not just doc/global-scope pages.
wp_enqueue_script( 'eazydocs-search-banner' );
?>

<div class="focus_overlay"></div>

<form action="<?php echo esc_url(home_url('/')) ?>" role="search" method="get" class="ezd_search_form" >
    <div class="header_search_form_info search_form_wrap">
        <?php
        $filter_enabled = ( $settings['show_post_type_filter'] ?? '' ) === 'yes';
        $raw_type       = $settings['filter_post_types'] ?? 'all';
        // handle legacy array format (old multi-select) → treat as 'all'
        $selected_type  = $filter_enabled && ! is_array( $raw_type ) ? sanitize_key( $raw_type ) : 'all';
        $allowed_types  = [ 'all', 'docs', 'page', 'post' ];
        if ( ! in_array( $selected_type, $allowed_types, true ) ) {
            $selected_type = 'all';
        }
        $type_labels = [
            'all'  => __( 'All', 'eazydocs' ),
            'docs' => __( 'Docs', 'eazydocs' ),
            'page' => __( 'Page', 'eazydocs' ),
            'post' => __( 'Post', 'eazydocs' ),
        ];
        $has_filter_cls = $filter_enabled ? ' has-type-filter' : '';
        ?>
        <div class="form-group ezd-<?php echo esc_attr( $settings['btn-position'] ?? '' ); ?><?php echo esc_attr( $has_filter_cls ); ?>">
            <div class="input-wrapper">
                <input type='search' class="search_field_wrap" id="ezd_searchInput" autocomplete="off" name="s"
                    placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>"
                    aria-label="<?php echo esc_attr( $settings['placeholder'] ?: __( 'Search documentation', 'eazydocs' ) ); ?>"
                    data-post-type="<?php echo esc_attr( $selected_type ); ?>">
                <!-- Ajax Search Loading Spinner -->
                <span class="spinner-border spinner"> </span>
                <button type="submit" class="search_submit_btn">
                    <?php \Elementor\Icons_Manager::render_icon( $settings['submit_btn_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                </button>

                <?php if ( $filter_enabled ) :
                    if ( $selected_type === 'all' ) {
                        // Type switcher only — no post data fetched
                        $has_dropdown = true;
                    } elseif ( $selected_type === 'docs' ) {
                        // Only top-level (parent) docs in the dropdown
                        $browse_posts = get_posts( [
                            'post_type'      => 'docs',
                            'post_parent'    => 0,
                            'posts_per_page' => -1,
                            'orderby'        => 'menu_order',
                            'order'          => 'ASC',
                            'post_status'    => 'publish',
                        ] );
                        $has_dropdown = ! empty( $browse_posts );
                    } else {
                        $browse_posts = get_posts( [
                            'post_type'      => $selected_type,
                            'posts_per_page' => 15,
                            'orderby'        => 'title',
                            'order'          => 'ASC',
                            'post_status'    => 'publish',
                        ] );
                        $has_dropdown = ! empty( $browse_posts );
                    }
                ?>
                <div class="ezd-type-filter">
                    <button type="button" class="ezd-type-filter-btn">
                        <span class="ezd-filter-label"><?php echo esc_html( $type_labels[ $selected_type ] ); ?></span>
                        <svg viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5" width="10" height="10" aria-hidden="true"><path d="M1 1l4 4 4-4"/></svg>
                    </button>
                    <?php if ( $has_dropdown ) : ?>
                    <ul class="ezd-type-filter-dropdown ezd-title-dropdown">
                        <?php if ( $selected_type === 'all' ) : ?>
                            <?php foreach ( [ 'all', 'docs', 'page', 'post' ] as $_pt ) : ?>
                            <li><a href="#" class="ezd-type-option" data-type="<?php echo esc_attr( $_pt ); ?>"><?php echo esc_html( $type_labels[ $_pt ] ); ?></a></li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <?php foreach ( $browse_posts as $p ) : ?>
                            <li><a href="<?php echo esc_url( get_permalink( $p->ID ) ); ?>" data-id="<?php echo esc_attr( $p->ID ); ?>"><?php echo esc_html( $p->post_title ); ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php 
    include('ajax-sarch-results.php');
    include('keywords.php');
    ?>
</form>


