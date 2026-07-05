<?php
/**
 * @package Zero Configuration with @wordpress/create-block
 *  [boilerplate] && [BOILERPLATE] ===> Prefix
 */

// Stop Direct Access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Blocks Final Class
 */

final class EZD_BLOCKS_CLASS {
    public function __construct() {
        // block initialization
        add_action( 'init', [ $this, 'blocks_init' ] );
        add_action( 'current_screen', [ $this, 'register_toolbar_block' ] );

        // Load the frontend grid styles inside the block editor so the
        // Shortcode (docs grid) block preview matches its frontend design.
        // Uses enqueue_block_assets (not enqueue_block_editor_assets) because
        // the editor canvas is iframed — only block *content* styles reach it.
        add_action( 'enqueue_block_assets', [ $this, 'editor_block_styles' ] );

        // blocks category
        if ( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
            add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
        } else {
            add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
        }
    }

    /**
     * Initialize the plugin
     */
    public static function init(){
        static $instance = false;
        if( ! $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Blocks Registration
     */
    public function register_block( $name, $options = array() ) {
        register_block_type( __DIR__ . '/build/' . $name, $options );
    }

    /**
     * Blocks Initialization
     */
    public function blocks_init() {
        // Always registered. Rendered server-side so the editor preview
        // (ServerSideRender in edit.js) shares the exact same template as the
        // frontend — templates/shortcode.php — and always matches its design.
        $this->register_block( 'shortcode', array(
            'render_callback' => [ $this, 'shortcode_block_render' ],
        ));

        $this->register_block( 'search-banner', array(
            'render_callback' => [ $this, 'search_banner_block_render' ]
        ));

        // Register Tabbed Docs block (freemium - advanced features locked for free users)
        $this->register_block( 'tabbed-docs', array(
            'render_callback' => [ $this, 'tabbed_docs_block_render' ]
        ));
    }

    /**
     * Render callback for the EazyDocs Shortcode (docs grid) block.
     *
     * Rebuilds the [eazydocs] shortcode from the block attributes and returns
     * its rendered output. Both the frontend and the editor preview go through
     * this same path, so the block editor always matches the frontend grid.
     *
     * The attribute-to-shortcode mapping mirrors src/shortcode/save.js exactly
     * so the editor preview is identical to what a saved post renders.
     *
     * @param array $attributes Block attributes.
     * @return string Rendered docs grid HTML.
     */
    public function shortcode_block_render( $attributes ) {
        $atts = is_array( $attributes ) ? $attributes : array();

        $pairs = array();

        // Columns (default 3 in block.json, always present).
        if ( ! empty( $atts['col'] ) ) {
            $pairs['col'] = absint( $atts['col'] );
        }

        // Docs to include / exclude — tokens look like "12 | Title"; keep the ID.
        $include = self::extract_doc_ids( $atts['include'] ?? array() );
        if ( '' !== $include ) {
            $pairs['include'] = $include;
        }

        $exclude = self::extract_doc_ids( $atts['exclude'] ?? '' );
        if ( '' !== $exclude ) {
            $pairs['exclude'] = $exclude;
        }

        if ( ! empty( $atts['show_docs'] ) ) {
            $pairs['show_docs'] = absint( $atts['show_docs'] );
        }

        if ( ! empty( $atts['show_articles'] ) ) {
            $pairs['show_articles'] = absint( $atts['show_articles'] );
        }

        if ( ! empty( $atts['more'] ) ) {
            $pairs['more'] = sanitize_text_field( $atts['more'] );
        }

        // Topic count pill — save.js only emits topic_label when show_topic is on.
        if ( ! empty( $atts['show_topic'] ) ) {
            $pairs['show_topic']  = 'true';
            $pairs['topic_label'] = sanitize_text_field( $atts['topic_label'] ?? '' );
        }

        if ( ! empty( $atts['parent_docs_order'] ) ) {
            $pairs['parent_docs_order'] = sanitize_key( $atts['parent_docs_order'] );
        }

        if ( ! empty( $atts['child_docs_order'] ) ) {
            $pairs['child_docs_order'] = sanitize_key( $atts['child_docs_order'] );
        }

        if ( ! empty( $atts['parent_docs_order_by'] ) ) {
            $pairs['parent_docs_order_by'] = sanitize_key( $atts['parent_docs_order_by'] );
        }

        if ( ! empty( $atts['docs_layout'] ) ) {
            $pairs['docs_layout'] = sanitize_key( $atts['docs_layout'] );
        }

        if ( ! empty( $atts['img_size'] ) ) {
            $pairs['img_size'] = sanitize_key( $atts['img_size'] );
        }

        // Restricted-docs toggles — always emit an explicit yes/no so "off" is honored.
        $pairs['show_private']      = ( isset( $atts['show_private'] ) && false === $atts['show_private'] ) ? 'no' : 'yes';
        $pairs['show_protected']    = ( isset( $atts['show_protected'] ) && false === $atts['show_protected'] ) ? 'no' : 'yes';
        $pairs['show_status_badge'] = ( isset( $atts['show_status_badge'] ) && false === $atts['show_status_badge'] ) ? 'no' : 'yes';
        $pairs['show_lock_icon']    = ( isset( $atts['show_lock_icon'] ) && false === $atts['show_lock_icon'] ) ? 'no' : 'yes';

        $shortcode = '[eazydocs';
        foreach ( $pairs as $key => $value ) {
            $shortcode .= ' ' . $key . '="' . esc_attr( $value ) . '"';
        }
        $shortcode .= ']';

        return do_shortcode( $shortcode );
    }

    /**
     * Extract comma-separated doc IDs from FormTokenField values.
     *
     * Mirrors the doc_ids() helper in src/custom-functions.js: each token may be
     * a bare ID or "ID | Title"; keep only the leading numeric ID.
     *
     * @param array|string $value Token list (array) or raw string.
     * @return string Comma-separated doc IDs.
     */
    private static function extract_doc_ids( $value ) {
        if ( is_array( $value ) ) {
            $items = $value;
        } elseif ( '' !== (string) $value ) {
            $items = array( $value );
        } else {
            $items = array();
        }

        $ids = array();
        foreach ( $items as $item ) {
            $item = (string) $item;
            $pos  = strpos( $item, '|' );
            $id   = absint( trim( false === $pos ? $item : substr( $item, 0, $pos ) ) );
            if ( $id > 0 ) {
                $ids[] = $id;
            }
        }

        return implode( ',', $ids );
    }

    /**
     * Enqueue the frontend docs-grid styles inside the block editor.
     *
     * ServerSideRender injects the real frontend markup into the editor canvas,
     * so it needs the same stylesheets (grid layout, icons, brand colour vars)
     * for the preview to match the frontend design.
     *
     * Runs on enqueue_block_assets, which fires on both the frontend and the
     * editor; the frontend already loads these via Frontend\Assets, so bail
     * unless we are in the admin/editor context.
     */
    public function editor_block_styles() {
        if ( ! is_admin() ) {
            return;
        }

        wp_enqueue_style( 'elegant-icon', EZD_ASSETS . 'vendors/elegant-icon/style.css', array(), EZD_VERSION );
        wp_enqueue_style( 'ezd-docs-widgets', EZD_STYLES . 'ezd-docs-widgets.css', array(), EZD_VERSION );

        // Expose the brand colour (and its RGB triplet for translucent tints) so
        // the preview picks up the same accent the frontend uses.
        $brand_color = ezd_get_opt( 'brand_color' );
        if ( ! empty( $brand_color ) ) {
            $dynamic_css = ':root { --ezd_brand_color: ' . esc_html( $brand_color ) . ';'
                . ' --ezd_brand_color_rgb: ' . esc_html( ezd_hex2rgba( $brand_color ) ) . '; }';
            wp_add_inline_style( 'ezd-docs-widgets', $dynamic_css );
        }
    }

    /**
     * Render callback for Tabbed Docs block.
     *
     * @param array $attributes Block attributes.
     * @return string Rendered block content.
     */
    public function tabbed_docs_block_render( $attributes ) {
        // Enqueue required styles
        wp_enqueue_style( 'elegant-icon' );
        wp_enqueue_style( 'ezd-docs-widgets' );

        // Enqueue Tabbed Docs specific styles
        wp_enqueue_style(
            'ezd-tabbed-docs',
            EZD_URL . '/build/tabbed-docs/frontend.css',
            array(),
            EZD_VERSION
        );

        ob_start();

        // Whitelist valid presets to prevent Local File Inclusion
        $allowed_presets = array( 'flat_tabbed', 'tabbed_doc_list' );
        $preset = isset( $attributes['preset'] ) && in_array( $attributes['preset'], $allowed_presets, true )
            ? $attributes['preset']
            : 'flat_tabbed';

        $file_path = sprintf(
            '%s/includes/block-templates/tabbed-docs/%s.php',
            EZD_PATH,
            $preset
        );

        if ( is_readable( $file_path ) ) {
            require_once $file_path;
        }

        return ob_get_clean();
    }

    /**
     * Register Toolbar Block
     */
    public function register_toolbar_block( $screen ) {
        if ( isset( $screen->post_type ) && $screen->post_type === 'docs' && $screen->base === 'post' && ezd_unlock_themes('docy','docly') ) {
            $this->register_block( 'eazydocs-toolbar' );
        }
    }

    /**
     * Enqueue editor scripts
     */
    function search_banner_block_render( $attributes ) {
	    wp_register_style( 'ezd-search-block', EZD_URL.'/build/search-banner/style-index.css', array(), EZD_VERSION );
        return require_once __DIR__ . '/includes/block-templates/search-banner.php';
    }

    /**
     * Register Block Category
     */
    public function register_block_category( $categories, $post ) {
        return array_merge(
            array(
                array(
                    'slug'  => 'eazydocs',
                    'title' => esc_html__( 'EazyDocs', 'eazydocs' ),
                ),
            ),
            $categories,
        );
    }
}

/**
 * Kickoff
 */
EZD_BLOCKS_CLASS::init();