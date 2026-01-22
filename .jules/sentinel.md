## 2026-01-16 – Insecure Direct Object Reference in Nestable Callbacks
**Vulnerability:** `nestable_callback` and `parent_nestable_callback` in `includes/Admin/Admin.php` allowed any user with `edit_posts` capability to modify the hierarchy and order of any post on the site by supplying arbitrary IDs.
**Learning:** Checking a generic capability like `edit_posts` at the start of a bulk action is insufficient when the action modifies specific objects. The permission must be verified for *each* object being modified.
**Prevention:** Always use `current_user_can('edit_post', $post_id)` inside loops that process user-supplied IDs for modification.

## 2026-01-20 – Stored XSS in OnePage Docs
**Vulnerability:** `Edit_OnePage::edit_doc_one_page` in `includes/Edit_OnePage.php` allowed authenticated users to update post meta via `$_GET` with unsanitized content, leading to Stored XSS.
**Learning:** Accepting rich text content via GET parameters and custom encoding (`ezd_chrEncode`) can mask the need for proper output sanitization or input sanitization.
**Prevention:** Always use `wp_kses_post()` or similar sanitization functions before saving rich text content to post meta, regardless of how it is received (GET/POST).
