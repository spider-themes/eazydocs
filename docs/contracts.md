# EazyDocs Free & Pro Compatibility Contracts

Treat every symbol, hook, route, option key, meta key, and table below as a backwards-compatibility contract between `eazydocs` (Free) and `eazydocs-pro` (Pro), as well as themes and user template overrides.

## Global PHP Helpers

| Plugin | Function | Purpose |
| --- | --- | --- |
| Free | `ezd_get_opt( $option, $default = '' )` | Read a field from `eazydocs_settings`. |
| Free | `ezd_is_premium()`, `ezd_is_promax()` | Freemius license/plan checks. |
| Free | `ezd_can_read_doc_content( $post_id )` | Canonical read-access check (status, password, role). |
| Free | `ezd_internal_doc_security( $doc_id = 0 )` | Private doc access check against settings/roles. |
| Free | `ezd_perform_edit_delete_actions( $action, $docID )` | Object-level edit/delete authorization helper. |
| Free | `ezd_get_doc_parent_id( $doc_id )`, `ezd_get_doc_tree_ids( $doc_id )`, `ezd_flush_docs_tree_cache()` | Hierarchy queries and navigation cache (`includes/Docs/hierarchy.php`). |
| Free | `ezd_docs_slug()`, `ezd_get_doc_author_roles()`, `ezd_sync_docs_capabilities( $force = false )` | Slug resolution and dynamic `docs` capabilities. |
| Free | `ezd_encrypt( $value )`, `ezd_decrypt( $value )`, `ezd_update_post_meta_cache( $post_ids )` | Secret encryption (`ezd_enc::`) and meta cache priming. |
| Pro | `ezdpro_can_read_doc_content( $post_id )` | Pro content read-access wrapper (with fallback when older Free is active). |
| Pro | `eazydocspro_get_option( $option, $default = '' )` | Pro non-empty option reader from `eazydocs_settings`. |
| Pro | `ezd_user_can_view_doc( $doc_id )` | Role-based visibility check (`includes/features/role-visibility/`). |
| Pro | `ezdpro_badge( $post )`, `ezd_flush_assistant_posts_cache()` | Pro doc badge HTML and Assistant cache invalidation. |
| Pro | `ezdpro_author_url( $username )`, `ezdpro_is_profile_page()`, `ezdpro_get_contributions_by_user()`, `ezdpro_get_activities_by_user()` | Pro user profile and activity feeds. |

## Shared WordPress Hooks & Filters

### Actions
- **Licensing / Boot:** `eaz_fs_loaded`
- **Docs Builder & Admin:** `eazydocs_duplicate`, `eazydocs_visibility`, `eazydocs_doc_sidebar`, `eazydocs_notification`, `ezd_pro_admin_menu`, `eazydocs_parent_doc_drag`, `eazydocs_social_share`, `eazydocs_additoinal_csf_fields`, `csf_eazydocs_settings_saved`
- **Frontend & Assistant:** `eazydocs_docs_subscription`, `eazydocs_suscription_modal_form`, `eazydocs_assistant`, `eazydocs_assistant_tab`, `assistant_tabs`, `eazydocs_send_message_once`

### Filters
- **Capabilities:** `eazydocs_docs_capability` (default `'edit_docs'`), `eazydocs_publish_capability` (default `'publish_docs'`)
- **Templates & Themes:** `eazydocs_template_{$template}`, `eazydocs_theme_dir_path` (default `'eazydocs/'`), `eazydocspro_theme_dir_path` (default `'eazydocs-pro/'`)
- **Navigation & Breadcrumbs:** `ezd_list_pages`, `ezd_list_pages_onepage`, `ezd_list_pages_onepage_others`, `eazydocs_breadcrumbs`, `eazydocs_breadcrumbs_html`, `ezd_kses_allowed_docs_nav_html`
- **Post Types & UI:** `eazydocs_post_type`, `eazydocs_onepage_post_type`, `eazydocs_render_dark_switcher`, `ezd_support_assistant_enabled`, `ezd_support_assistant_args`

## REST API (`eazydocs/v1`)

Defined in `includes/Admin/Api/DocsBuilderController.php`:
- `GET` `/docs-builder`, `/docs-builder/settings`, `/docs-builder/parents`, `/docs-builder/children`, `/docs-builder/counts`, `/docs-builder/notifications`
- `POST` `/docs-builder/mark-read`, `/docs-builder/create-parent`, `/docs-builder/create-section`, `/docs-builder/create-child`, `/docs-builder/delete`, `/docs-builder/rename`, `/docs-builder/update-visibility`, `/docs-builder/duplicate`, `/docs-builder/update-sidebar`

## AJAX Actions (`wp_ajax_*`)

- **Free:** `eazydocs_handle_feedback` (+`nopriv`), `eazydocs_search_results` (+`nopriv`), `docs_single_content` (+`nopriv`), `eazydocs_child_docs` (+`nopriv`), `eazydocs_feedback_email` (+`nopriv`), `eaz_nestable_docs`, `eaz_parent_nestable_docs`, `ezd_google_test_connection`, `ezd_setup_wizard_save_settings`, `ezd_migrate_to_eazydocs`, `ezd_install_advanced_accordion`, `ezd_import_sample_data`, `ezd_plugin_action`, `csf_eazydocs_settings_ajax_save`
- **Pro:** `eazydocs_sortable_docs`, `ezd_load_more_notifications`, `ezd_bulk_feedback_action`, `ezd_toggle_feedback_star`, `ezd_record_feedback_reply`, `eazydocs_ajax_search_result` (+`nopriv`), `eazydocs_kbase_instant` (+`nopriv`), `eazydocs_send_message` (+`nopriv`), `ezdpro_contributions_by_user` (+`nopriv`), `ezdpro_activities_by_user` (+`nopriv`), `ezd_bulk_role_visibility`, `ezd_selected_save_comment` (+`nopriv`), `ezd_login_check` (+`nopriv`), `ezd_lost_password` (+`nopriv`), `ezd_register` (+`nopriv`), `ezd_doc_contributor`, `load_more_contributors`, `ezd_load_analytics_tab`, `ezd_filter_date_from_search`, `ezd_search_helpful_docs_paginate`, `ezd_overview_reset`, `ezd_reset_feedback`, `ezd_reset_views`, `ezd_search_table_reset`, `ezd_export_doc_ranks`, `ezd_analytics_overview_last_month`, `ezd_resolve_failed_search`, `ezd_get_resolved_keywords`, `ezd_send_test_report`, `ezd_send_report_now`, `ezd_preview_report`, `EazyDocsPro_Install_Core_installer`

## Data Model (Post Types, Taxonomies, Options, Tables)

- **Post Types:** `docs`, `onepage-docs`, `api_docs` (Pro), `ezd_feedback` (Pro)
- **Taxonomies:** `doc_tag`, `doc_badge` (Pro)
- **Custom Tables:** `{$wpdb->prefix}eazydocs_search_keyword`, `{$wpdb->prefix}eazydocs_search_log`, `{$wpdb->prefix}eazydocs_view_log`
- **Key Options:** `eazydocs_settings`, `ezd_get_setup_wizard`, `ezd_docs_caps_signature`, `ezd_analytics_db_version`, `ezd_assistant_cache_ver`, `EazyDocs_version`, `EazyDocsPro_version`
- **Key Post Meta:** `ezd_doc_reviewer_id`, `ezd_doc_secondary_title`, `ezd_doc_layout`, `ezd_doc_content_type`, `ezd_doc_left_sidebar`, `ezd_doc_content_type_right`, `ezd_doc_content_box_right`, `_iframe_assistant_page`
