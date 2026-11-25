<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve the "reference" shortcodes and their content in a post
 *
 * @return string
 *
 * @param int $post_id The ID of the post to retrieve the "reference" shortcodes from
 */
function ezd_get_footnotes_in_content($post_id) {
    // Retrieve the post by its ID
    $post = get_post($post_id);

    // Check if the post exists
    if (!$post) {
        return [];
    }

    // Get the content of the post
    $content = $post->post_content;

    // Regular expressions for [reference] shortcodes and target span elements
    $shortcode_pattern = '/\[reference([^\]]*)\](.*?)\[\/reference\]/s';
    $span_pattern = '/<span id="serial-id-(\d+)" class="ezd-footnotes-link-item" data-bs-original-title=".*?">.*?<span class="ezd-footnote-content">(.*?)<\/span><\/span>/s';

    $references = [];

    // Extract spans
    if (preg_match_all($span_pattern, $content, $span_matches, PREG_SET_ORDER)) {
        foreach ($span_matches as $match) {
            $references[] = [
                'id'      => $match[1], // Serial ID
                'content' => $match[2], // Footnote content
                'source'  => 'span',
            ];
        }
    }

    // Extract [reference] shortcodes
    if (preg_match_all($shortcode_pattern, $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attributes = $match[1];
            $shortcode_content = $match[2];

            // Extract the `number` attribute
            $number = null;
            if (preg_match('/number=["\']?(\d+)["\']?/', $attributes, $number_match)) {
                $number = $number_match[1];
            }

            // Add the reference regardless of the existence of spans
            $references[] = [
                'id'      => $number,
                'content' => $shortcode_content,
                'source'  => 'shortcode',
            ];
        }
    }

    return $references;
}


/**
 * Replace footenote number attribute
 */
function ezd_footnote_number_attribute( $content ) {
    // Runs on every the_content call site-wide, so skip the regex unless needed.
    if ( false === strpos( (string) $content, '[reference number="##"]' ) ) {
        return $content;
    }

    return str_replace( '[reference number="##"]', '[reference number="1"]', $content );
}
add_filter('the_content', 'ezd_footnote_number_attribute');

/**
 * This function dynamically updates footnote content in the single 'docs'
 * It targets <span> elements with specific attributes and checks for <i> tags to add an onclick event.
 */
function ezd_update_footnotes_content($content) {
    // Apply only to single 'docs' post type, and only when footnotes are present.
    if ( is_singular( 'docs' ) && false !== strpos( (string) $content, 'ezd-footnotes-link-item' ) ) {
        // Regular expression to match the required span tag
        $pattern = '/<span id="serial-id-(\d+)" class="ezd-footnotes-link-item" data-bs-original-title="(.*?)">.*?<i(.*?)>(.*?)<\/i>.*?<span class="ezd-footnote-content">(.*?)<\/span><\/span>/s';

        $content = preg_replace_callback($pattern, function ($matches) {
            $id 				= $matches[1];
            $original_title 	= $matches[2];
            $i_attributes 		= $matches[3];
            $i_content 			= $matches[4];
            $footnote_content 	= $matches[5];

            // Check if the <i> tag already has an onclick event
            if (!strpos($i_attributes, 'onclick')) {
                // Add the onclick event
                $i_attributes .= " onclick=\"location.href='#note-name-$id'\"";
            }

            // Return the updated span content
            return sprintf(
                '<span id="serial-id-%d" class="ezd-footnotes-link-item" data-bs-original-title="%s"><i%s>%s</i><span class="ezd-footnote-content">%s</span></span>',
                $id,
                esc_attr($original_title),
                $i_attributes,
                esc_html($i_content),
                wp_kses_post($footnote_content)
            );
        }, $content);
    }

    return $content;
}
add_filter('the_content', 'ezd_update_footnotes_content');

/**
 * Customizer buttons visibility by user role
 * Used in the settings page
 */
function customizer_visibility_callback() {
	$archive_url = 'javascript:void(0)';
	$single_url  = 'javascript:void(0)';
	$target      = '_self';
	$no_access   = 'no-customizer-access';

	if ( current_user_can( 'manage_options' ) ) {
		$archive_id = ezd_get_opt( 'docs-slug' );

		$first_doc = get_posts( [
			'post_type'      => 'docs',
			'posts_per_page' => 1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		] );

		$doc_id = ! empty( $first_doc ) ? $first_doc[0]->ID : $archive_id;

		$customize_base = admin_url( 'customize.php?url=' ) . site_url( '/' );
		$archive_url    = $customize_base . '?p=' . $archive_id . '&autofocus[panel]=docs-page&autofocus[section]=docs-archive-page';
		$single_url     = $customize_base . '?p=' . $doc_id . '&autofocus[panel]=docs-page&autofocus[section]=docs-single-page';
		$no_access      = '';
		$target         = '_blank';
	}
	?>
	<a href="<?php echo esc_attr( $archive_url ); ?>" class="<?php echo esc_attr( $no_access ); ?>" target="<?php echo esc_attr( $target ); ?>" id="get_docs_archive">
		<?php echo esc_html__( 'Docs Archive', 'eazydocs' ); ?>
	</a>
	<a href="<?php echo esc_attr( $single_url ); ?>" class="<?php echo esc_attr( $no_access ); ?>" target="<?php echo esc_attr( $target ); ?>" id="get_docs_single">
		<?php echo esc_html__( 'Single Doc', 'eazydocs' ); ?>
	</a>
	<?php
}

/**
 * Setup wizard save settings
 */
/**
 * Create (or reuse) a documentation archive page with the [eazydocs] shortcode.
 *
 * The setup wizard auto-saves on every step change, so this must be idempotent —
 * it tracks the created page in an option and reuses it instead of duplicating.
 *
 * @return int The archive page ID, or 0 on failure.
 */
function ezd_create_docs_archive_page() {
	// Reuse a page created by a previous run to avoid duplicates.
	$existing_id = absint( get_option( 'ezd_wizard_archive_page_id' ) );
	if ( $existing_id && 'page' === get_post_type( $existing_id ) && 'trash' !== get_post_status( $existing_id ) ) {
		return $existing_id;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => __( 'Documentation', 'eazydocs' ),
			'post_name'    => 'documentation',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '[eazydocs]',
		)
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	update_option( 'ezd_wizard_archive_page_id', $page_id, false );

	return $page_id;
}

/**
 * Save the EazyDocs setup wizard settings via AJAX.
 */
function ezd_setup_wizard_save_settings() {

	check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized user' );
	}

	$rootslug        = isset( $_POST['rootslug'] ) ? sanitize_text_field( wp_unslash( $_POST['rootslug'] ) ) : '';
	$brandColor      = isset( $_POST['brandColor'] ) ? sanitize_hex_color( wp_unslash( $_POST['brandColor'] ) ) : '';
	$slugType        = isset( $_POST['slugType'] ) ? sanitize_text_field( wp_unslash( $_POST['slugType'] ) ) : '';
	$docSingleLayout = isset( $_POST['docSingleLayout'] ) ? sanitize_text_field( wp_unslash( $_POST['docSingleLayout'] ) ) : '';
	$docsPageWidth   = isset( $_POST['docsPageWidth'] ) ? sanitize_text_field( wp_unslash( $_POST['docsPageWidth'] ) ) : '';
	$live_customizer = isset( $_POST['live_customizer'] ) ? sanitize_text_field( wp_unslash( $_POST['live_customizer'] ) ) : '';
	$is_dark_switcher = isset( $_POST['is_dark_switcher'] ) ? sanitize_text_field( wp_unslash( $_POST['is_dark_switcher'] ) ) : '';

	// Default appearance is allowlisted; anything unexpected falls back to "system".
	$ezd_dark_default_raw = isset( $_POST['ezd_dark_default'] ) ? sanitize_key( wp_unslash( $_POST['ezd_dark_default'] ) ) : 'system';
	$ezd_dark_default     = in_array( $ezd_dark_default_raw, [ 'light', 'dark', 'system' ], true ) ? $ezd_dark_default_raw : 'system';
	$is_dark_accent       = isset( $_POST['is_dark_accent'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['is_dark_accent'] ) ) ? '1' : '';
	$brand_color_dark     = isset( $_POST['brand_color_dark'] ) ? sanitize_hex_color( wp_unslash( $_POST['brand_color_dark'] ) ) : '';

	// Archive page can be an existing page ID or the "create_new" sentinel.
	$archive_raw = isset( $_POST['archivePage'] ) ? sanitize_text_field( wp_unslash( $_POST['archivePage'] ) ) : '';
	$archivePage = ( 'create_new' === $archive_raw )
		? ezd_create_docs_archive_page()
		: absint( $archive_raw );

	$options = get_option( 'eazydocs_settings' );

	// Check if the option exists and is an array
	if ( is_array( $options ) ) {

		// Update the specific setting
		$options['docs-type-slug']         = $rootslug;
		$options['brand_color']            = $brandColor;
		$options['docs-url-structure']     = $slugType;
		$options['docs_single_layout']     = $docSingleLayout;
		$options['docs_page_width']        = $docsPageWidth;
		$options['customizer_visibility']  = $live_customizer;
		$options['is_dark_switcher']       = $is_dark_switcher;
		$options['ezd_dark_default']       = $ezd_dark_default;
		$options['is_dark_accent_color']   = $is_dark_accent;
		if ( $brand_color_dark ) {
			$options['ezd_brand_color_dark'] = $brand_color_dark;
		}
		$options['setup_wizard_completed'] = true;

		// Only overwrite the archive page when a valid one was provided/created.
		if ( $archivePage ) {
			$options['docs-slug'] = $archivePage;
		}

		// Update the option in the database
		update_option( 'eazydocs_settings', $options );
	}

	wp_send_json_success( 'Settings saved' );
}

add_action( 'wp_ajax_ezd_setup_wizard_save_settings', 'ezd_setup_wizard_save_settings' );

/**
 * Add the post thumbnail to the RSS feed for 'docs'
 */
function ezd_add_thumbnail_to_rss_feed() {
    $post_id = get_the_ID();

    // Check if the post type is 'docs' and it has a thumbnail.
    if ('docs' === get_post_type($post_id) && has_post_thumbnail($post_id)) {
        printf(
            '<thumbnail url="%s" />',
            esc_url(get_the_post_thumbnail_url($post_id, 'full'))
        );
    }
}
add_action('rss2_item', 'ezd_add_thumbnail_to_rss_feed');

/*
 * Check if has selected comment class exists 
 */
function has_ezd_mark_text_class() {
    global $post;

    if ( is_singular('docs') && isset($post->post_content) ) {
        return false !== strpos($post->post_content, 'class="ezd_mark_text"');
    }

    return false;
}

/**
 * Render comments template for docs pages
 */
function ezd_get_comments_template() {
	
	// Block theme → render comments block (loads correct styles)
	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
		echo do_blocks( '<!-- wp:comments /--><!-- wp:post-comments-form /-->' );
	} else {
		// Classic theme → native comments.php
		comments_template();
	}
}
