# EazyDocs Engineering Learnings (Consolidated)

Distilled security, performance, accessibility, and code-quality rules from prior EazyDocs audits and automated agent journals (`.jules/`, `.warden/`, `.bolt/`, `.sentinel/`).

## Security & Authorization
- **Object-level checks in bulk/loop handlers:** Generic `edit_posts` / `edit_docs` / `publish_docs` checks are insufficient when modifying existing items; always verify `current_user_can( 'edit_post', $post_id )` or `current_user_can( 'delete_post', $post_id )` for every target ID before any write.
- **Nonce + capability pairing:** Never rely on a nonce alone (`wp_verify_nonce` / `check_ajax_referer`) or capability alone. Admin/state-changing AJAX and `admin_init` handlers must verify both, and `save_post` hooks must check `DOING_AUTOSAVE`, `current_user_can( 'edit_post', $post_id )`, and a dedicated nonce.
- **Never attach admin/user-enumeration actions to `wp_ajax_nopriv_`:** Public `nopriv` endpoints must enforce content access (`ezd_can_read_doc_content()`), password protection (`post_password_required()`), and rate limiting for mail/writes.
- **Per-viewer cache safety:** Never cache rendered HTML across different permission levels. Cache IDs/raw metadata and filter per visitor (`ezd_can_read_doc_content()`) before rendering or paginating.

## Performance
- **Never call `flush_rewrite_rules()` on `init`:** Only flush on activation or explicit slug/rewrite option changes.
- **Avoid N+1 queries and `SELECT *`:** Use `ezd_update_post_meta_cache()` or single aggregated SQL queries (`GROUP BY`), and early-return in cron handlers when no work is due.
- **Guard `user_has_cap`:** `ezd_filter_user_has_cap` runs frequently; bail early for guests/role-less users and never call `is_super_admin()` on single-site inside it (avoids recursion).

## Accessibility & Markup
- Keep interactive retrofits on legacy `<li>`/`<span>` triggers keyboard-accessible (`role="button"`/`role="tab"`, `tabindex="0"`, `aria-expanded`/`aria-selected`, `Enter`/`Space` key handlers).
- Avoid duplicate IDs inside loops (use classes + delegated JS listeners) and provide contextual `aria-label`s on `<progress>` and repeated action buttons.

## Coding Style
- Use strict comparisons (`===`, `!==`), Yoda conditions (`'value' === $var`), short array syntax `[]`, and `if ( ! defined( 'ABSPATH' ) ) { exit; }` at the top of every PHP file (after `namespace` if namespaced).
- Never delete `vendor/` during cleanup (`vendor/freemius/` is committed/required runtime code).
