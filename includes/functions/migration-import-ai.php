<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX handler to migrate BetterDocs content into EazyDocs.
 *
 * Converts every doc_category into a parent doc and nests existing docs beneath
 * the parent that matches their deepest category. The run is idempotent: parent
 * docs created by an earlier migration (flagged _ezd_migrated_parent) are removed
 * and rebuilt, while user-authored docs are never deleted. Returns a count
 * summary so the UI can report exactly what changed.
 */
add_action( 'wp_ajax_ezd_migrate_to_eazydocs', 'ezd_migrate_betterdocs_to_eazydocs' );
function ezd_migrate_betterdocs_to_eazydocs() {

	check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

	// Mirror the capability the Migrate tab itself is gated behind.
	$settings_cap = function_exists( 'ezd_get_opt' ) ? ezd_get_opt( 'settings-edit-access', 'manage_options' ) : 'manage_options';
	if ( ! current_user_can( $settings_cap ) && ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => __( 'You do not have permission to run a migration.', 'eazydocs' ) ] );
	}

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( ! is_plugin_active( 'betterdocs/betterdocs.php' ) ) {
		wp_send_json_error( [ 'message' => __( 'BetterDocs is not active. Please activate it first.', 'eazydocs' ) ] );
	}

	$from = isset( $_POST['migrate_from'] ) ? sanitize_text_field( wp_unslash( $_POST['migrate_from'] ) ) : '';
	if ( 'betterdocs' !== $from ) {
		wp_send_json_error( [ 'message' => __( 'Only BetterDocs migration is supported currently.', 'eazydocs' ) ] );
	}

	// A whole-library migration can be slow on shared hosting.
	wp_raise_memory_limit( 'admin' );
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- disabled on some hosts.
	}

	/**
	 * Cleanup: remove only the category parent docs a previous migration created
	 * (flagged _ezd_migrated_parent) so re-running stays clean. -1 is intentional —
	 * a one-time admin migration must see the whole library.
	 */
	$old_parent_docs = get_posts( [
		'post_type'              => 'docs',
		'post_status'            => 'any',
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'meta_key'               => '_ezd_migrated_parent',
		'meta_value'             => 'yes',
	] );
	foreach ( $old_parent_docs as $pid ) {
		wp_delete_post( $pid, true );
	}

	// PASS 1 — one parent doc per category (recursive); returns the count created.
	$created_docs    = []; // term_id => parent_doc_id.
	$parents_created = ezd_create_parent_docs_from_terms( $created_docs, 0 );

	// PASS 2 — nest existing docs under the parent for their deepest category.
	$docs_reparented = 0;
	if ( ! empty( $created_docs ) ) {
		$created_parent_doc_ids = array_values( $created_docs );

		$posts = get_posts( [
			'post_type'              => 'docs',
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'post__not_in'           => $created_parent_doc_ids,
			'tax_query'              => [
				[
					'taxonomy' => 'doc_category',
					'operator' => 'EXISTS',
				],
			],
		] );

		foreach ( $posts as $post ) {
			// Leave docs that already sit under a parent untouched.
			if ( 0 !== (int) $post->post_parent ) {
				continue;
			}

			$terms = wp_get_post_terms( $post->ID, 'doc_category' );
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				continue;
			}

			// Deepest (most specific) category wins.
			$deepest_term = null;
			$max_depth    = -1;
			foreach ( $terms as $term ) {
				$depth = count( get_ancestors( $term->term_id, 'doc_category' ) );
				if ( $depth > $max_depth ) {
					$max_depth    = $depth;
					$deepest_term = $term;
				}
			}

			if ( ! $deepest_term || ! isset( $created_docs[ $deepest_term->term_id ] ) ) {
				continue;
			}

			$parent_doc_id = $created_docs[ $deepest_term->term_id ];

			wp_update_post( [
				'ID'          => $post->ID,
				'post_parent' => $parent_doc_id,
				'menu_order'  => $post->menu_order,
			] );

			// Flags for future cleanups / debugging.
			update_post_meta( $post->ID, '_ezd_migrated', 'yes' );
			update_post_meta( $post->ID, '_ezd_parent_doc', $parent_doc_id );
			update_post_meta( $post->ID, '_ezd_parent_term', $deepest_term->term_id );

			$docs_reparented++;
		}
	}

	// Build a translated, pluralised summary for the success dialog.
	$summary_parts   = [];
	/* translators: %d: number of categories converted into parent docs. */
	$summary_parts[] = sprintf( _n( '%d category converted into a parent doc.', '%d categories converted into parent docs.', $parents_created, 'eazydocs' ), $parents_created );
	/* translators: %d: number of docs nested under their category. */
	$summary_parts[] = sprintf( _n( '%d doc organised under its category.', '%d docs organised under their categories.', $docs_reparented, 'eazydocs' ), $docs_reparented );

	wp_send_json_success( [
		'message'    => __( 'Migration completed.', 'eazydocs' ),
		'categories' => $parents_created,
		'docs'       => $docs_reparented,
		'summary'    => implode( '<br>', array_map( 'esc_html', $summary_parts ) ),
	] );
}

/**
 * Recursively create a parent doc for each doc_category term.
 *
 * @param array $created_docs   Map of term_id => created parent doc ID (by reference).
 * @param int   $parent_term_id Term to start from (0 = top level).
 * @return int  Number of parent docs created within this subtree.
 */
if ( ! function_exists( 'ezd_create_parent_docs_from_terms' ) ) {
	function ezd_create_parent_docs_from_terms( &$created_docs, $parent_term_id = 0 ) {
		$created = 0;

		$categories = get_categories( [
			'taxonomy'   => 'doc_category',
			'hide_empty' => false,
			'parent'     => $parent_term_id,
		] );

		foreach ( $categories as $cat ) {
			$parent_doc_parent_id = ( $cat->parent && isset( $created_docs[ $cat->parent ] ) ) ? $created_docs[ $cat->parent ] : 0;

			$parent_doc_id = wp_insert_post( [
				'post_type'   => 'docs',
				'post_title'  => $cat->name,
				'post_name'   => $cat->slug,
				'post_status' => 'publish',
				'post_parent' => $parent_doc_parent_id,
				'meta_input'  => [
					'_ezd_migrated_parent' => 'yes',
					'_ezd_parent_term'     => $cat->term_id,
				],
			] );

			if ( is_wp_error( $parent_doc_id ) ) {
				continue;
			}

			// Keep the original taxonomy term attached to its new parent doc.
			wp_set_post_terms( $parent_doc_id, [ $cat->term_id ], 'doc_category', false );

			$created_docs[ $cat->term_id ] = $parent_doc_id;
			$created++;

			$created += ezd_create_parent_docs_from_terms( $created_docs, $cat->term_id );
		}

		return $created;
	}
}


/**
 * AJAX handler to install and activate Advanced Accordion Block plugin
 */
add_action('wp_ajax_ezd_install_advanced_accordion', 'ezd_install_advanced_accordion');
function ezd_install_advanced_accordion() {
    check_ajax_referer( 'ezd_install_accordion_nonce', 'nonce' );

    if ( ! current_user_can( 'install_plugins' ) ) {
        return;
    }

    $plugin_basename = 'advanced-accordion-block' . '/' . 'advanced-accordion-block.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    include_once ABSPATH . 'wp-admin/includes/file.php';
    if (!function_exists('WP_Filesystem')) { require_once ABSPATH . 'wp-admin/includes/file.php'; }
    WP_Filesystem();

    if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_basename ) ) {
        $api = plugins_api( 'plugin_information', [ 'slug' => 'advanced-accordion-block', 'fields' => [ 'sections' => false ] ] );
        if ( ! is_wp_error( $api ) ) {
            $upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
            $upgrader->install( $api->download_link );
        }
    }

    if ( ! is_plugin_active( $plugin_basename ) ) {
        activate_plugin( $plugin_basename );
    }
}

/**
 * AJAX handler to import sample data from demo.xml
 *
 * This function imports the sample documentation data from the
 * sample-data/demo.xml file using WordPress Importer.
 *
 * @since 2.8.3
 */
add_action( 'wp_ajax_ezd_import_sample_data', 'ezd_import_sample_data' );

/**
 * Import sample data from demo.xml.
 *
 * @return void
 */
function ezd_import_sample_data() {
	// Verify nonce for security.
	check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to import data.', 'eazydocs' ) ] );
	}

	// Path to the sample data XML file.
	$sample_data_file = EZD_PATH . '/sample-data/demo.xml';

	// Check if the file exists.
	if ( ! file_exists( $sample_data_file ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Sample data file not found.', 'eazydocs' ) ] );
	}

	// Include WordPress importer files.
	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', true );
	}

	// Load WordPress Importer class.
	require_once ABSPATH . 'wp-admin/includes/import.php';

	// Check if the WordPress Importer class exists.
	if ( ! class_exists( 'WP_Import' ) ) {
		// Try to load the importer plugin.
		$importer_plugin = ABSPATH . 'wp-content/plugins/wordpress-importer/wordpress-importer.php';

		if ( file_exists( $importer_plugin ) ) {
			require_once $importer_plugin;
		} else {
			// Importer not available, use manual import.
			$result = ezd_manual_import_sample_data( $sample_data_file );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( [ 'message' => $result->get_error_message() ] );
			}
			wp_send_json_success( [ 'message' => esc_html__( 'Sample data imported successfully!', 'eazydocs' ) ] );
		}
	}

	// If WP_Import class exists, use it.
	if ( class_exists( 'WP_Import' ) ) {
		$wp_import                    = new WP_Import();
		$wp_import->fetch_attachments = false;

		ob_start();
		$wp_import->import( $sample_data_file );
		ob_end_clean();

		wp_send_json_success( [ 'message' => esc_html__( 'Sample data imported successfully!', 'eazydocs' ) ] );
	} else {
		// Fallback to manual import.
		$result = ezd_manual_import_sample_data( $sample_data_file );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}
		wp_send_json_success( [ 'message' => esc_html__( 'Sample data imported successfully!', 'eazydocs' ) ] );
	}
}

/**
 * Manual import of sample data by parsing XML.
 *
 * @param string $file Path to the XML file.
 * @return bool|WP_Error True on success, WP_Error on failure.
 */
function ezd_manual_import_sample_data( $file ) {
	// Load the XML file.
	$xml_content = file_get_contents( $file );

	if ( empty( $xml_content ) ) {
		return new WP_Error( 'empty_file', esc_html__( 'The sample data file is empty.', 'eazydocs' ) );
	}

	// Parse XML.
	libxml_use_internal_errors( true );
	$xml = simplexml_load_string( $xml_content );

	if ( ! $xml ) {
		return new WP_Error( 'xml_parse_error', esc_html__( 'Failed to parse the sample data file.', 'eazydocs' ) );
	}

	// Get namespaces.
	$namespaces = $xml->getNamespaces( true );
	$wp         = $xml->channel->children( isset( $namespaces['wp'] ) ? $namespaces['wp'] : '' );
	$content_ns = isset( $namespaces['content'] ) ? $namespaces['content'] : '';

	// Track old ID to new ID mapping for parent relationships.
	$id_mapping = [];

	// First pass: Create all docs without parent relationships.
	foreach ( $xml->channel->item as $item ) {
		$wp_data = $item->children( isset( $namespaces['wp'] ) ? $namespaces['wp'] : '' );

		// Only import 'docs' post type.
		if ( 'docs' !== (string) $wp_data->post_type ) {
			continue;
		}

		$old_id = (int) $wp_data->post_id;
		$title  = (string) $item->title;

		// Check if a doc with the same title already exists.
		$existing = get_posts(
			[
				'post_type'   => 'docs',
				'title'       => $title,
				'post_status' => 'any',
				'numberposts' => 1,
			]
		);

		if ( ! empty( $existing ) ) {
			$id_mapping[ $old_id ] = $existing[0]->ID;
			continue;
		}

		// Get content.
		$content_data = $item->children( $content_ns );
		$post_content = isset( $content_data->encoded ) ? (string) $content_data->encoded : '';

		// Create the doc post.
		$post_data = [
			'import_id'    => $old_id, // Preserve original post ID to avoid occupying IDs other plugins rely on.
			'post_title'   => sanitize_text_field( $title ),
			'post_content' => wp_kses_post( $post_content ),
			'post_status'  => ( 'private' === (string) $wp_data->status ) ? 'publish' : sanitize_text_field( (string) $wp_data->status ),
			'post_type'    => 'docs',
			'menu_order'   => (int) $wp_data->menu_order,
			'post_parent'  => 0, // Will be updated in second pass.
		];

		$new_id = wp_insert_post( $post_data );

		if ( ! is_wp_error( $new_id ) ) {
			$id_mapping[ $old_id ] = $new_id;

			// Store the original parent ID for later.
			$original_parent = (int) $wp_data->post_parent;
			if ( $original_parent > 0 ) {
				update_post_meta( $new_id, '_ezd_temp_parent', $original_parent );
			}
		}
	}

	// Second pass: Update parent relationships.
	foreach ( $id_mapping as $old_id => $new_id ) {
		$temp_parent = get_post_meta( $new_id, '_ezd_temp_parent', true );

		if ( ! empty( $temp_parent ) && isset( $id_mapping[ $temp_parent ] ) ) {
			wp_update_post(
				[
					'ID'          => $new_id,
					'post_parent' => $id_mapping[ $temp_parent ],
				]
			);
		}

		// Clean up temp meta.
		delete_post_meta( $new_id, '_ezd_temp_parent' );
	}

	return true;
}

/**
 * EazyDocs_Article_Walker Class.
 *
 * @since 1.0.0
 */
class EazyDocs_Article_Walker extends Walker_Page {

	/**
	 * Outputs the beginning of the current element in the tree.
	 *
	 * @since 1.0.0
	 *
	 * @see Walker::start_el()
	 *
	 * @param string  $output            Used to append additional content. Passed by reference.
	 * @param WP_Post $data_object       Page data object.
	 * @param int     $depth             Optional. Depth of page. Used for padding. Default 0.
	 * @param array   $args              Optional. Array of arguments. Default empty array.
	 * @param int     $current_object_id Optional. ID of the current page. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = [], $current_object_id = 0 ) {
		$page      = $data_object;
		$css_class = [ 'page_item', 'page-item-' . $page->ID ];

		if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
			$css_class[] = 'page_item_has_children';
		}

		if ( ! empty( $current_object_id ) ) {
			$_current_page = get_post( $current_object_id );
			if ( $_current_page && in_array( $page->ID, $_current_page->ancestors, true ) ) {
				$css_class[] = 'current_page_ancestor';
			}
			if ( (int) $page->ID === (int) $current_object_id ) {
				$css_class[] = 'current_page_item';
			} elseif ( $_current_page && (int) $page->ID === (int) $_current_page->post_parent ) {
				$css_class[] = 'current_page_parent';
			}
		} elseif ( (int) get_option( 'page_for_posts' ) === (int) $page->ID ) {
			$css_class[] = 'current_page_parent';
		}

		$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_object_id ) );
		$css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

		if ( '' === $page->post_title ) {
			/* translators: %d: ID of a post */
			$page->post_title = sprintf( __( '#%d (no title)', 'eazydocs' ), $page->ID );
		}

		$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
		$args['link_after']  = empty( $args['link_after'] ) ? '' : $args['link_after'];

		$badge_html = '';
		if ( function_exists( 'ezdpro_badge' ) && ezd_is_premium() ) {
			$badge_html = ezdpro_badge( $page->ID );
		}

		$atts                 = [];
		$atts['href']         = get_permalink( $page->ID );
		$atts['aria-current'] = ( (int) $page->ID === (int) $current_object_id ) ? 'page' : '';

		$atts = apply_filters( 'page_menu_link_attributes', $atts, $page, $depth, $args, $current_object_id );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$output .= sprintf(
			'<li%s><a%s>%s%s%s%s</a>',
			$css_classes,
			$attributes,
			$args['link_before'],
			apply_filters( 'the_title', $page->post_title, $page->ID ),
			$args['link_after'],
			$badge_html
		);
	}
}

/**
 * Return default AI Summary providers.
 *
 * @return array
 */
function ezd_get_default_ai_summary_providers() {
	return array(
		array(
			'enabled'     => true,
			'name'        => 'ChatGPT',
			'url'         => 'https://chat.openai.com/?q=',
			'icon'        => '',
			'icon_class'  => '',
			'description' => '',
		),
		array(
			'enabled'     => true,
			'name'        => 'Claude',
			'url'         => 'https://www.claude.ai/new?q=',
			'icon'        => '',
			'icon_class'  => '',
			'description' => '',
		),
		array(
			'enabled'     => true,
			'name'        => 'Grok',
			'url'         => 'https://www.grok.com/?q=',
			'icon'        => '',
			'icon_class'  => '',
			'description' => '',
		),
		array(
			'enabled'     => true,
			'name'        => 'Perplexity',
			'url'         => 'https://www.perplexity.ai/search/new?q=',
			'icon'        => '',
			'icon_class'  => '',
			'description' => '',
		),
		array(
			'enabled'     => true,
			'name'        => 'Google AI',
			'url'         => 'https://www.google.com/search?udm=50&aep=11&q=',
			'icon'        => '',
			'icon_class'  => '',
			'description' => '',
		),
		array(
			'enabled'     => true,
			'name'        => 'Mistral AI',
			'url'         => 'https://chat.mistral.ai/chat?q=',
			'icon'        => '',
			'icon_class'  => '',
			'description' => '',
		),
	);
}

/**
 * Retrieve configured AI Summary providers, falling back to defaults.
 *
 * @param bool $active_only Whether to return only enabled providers.
 *
 * @return array
 */
function ezd_get_ai_summary_providers( $active_only = false ) {
	$defaults  = ezd_get_default_ai_summary_providers();
	$providers = ezd_get_opt( 'ai_summary_providers', $defaults );

	if ( empty( $providers ) || ! is_array( $providers ) ) {
		$providers = $defaults;
	}

	if ( $active_only ) {
		$providers = array_filter(
			$providers,
			function ( $item ) {
				if ( ! is_array( $item ) ) {
					return false;
				}
				if ( isset( $item['enabled'] ) ) {
					return filter_var( $item['enabled'], FILTER_VALIDATE_BOOLEAN );
				}
				return true;
			}
		);
	}

	return array_values( $providers );
}
