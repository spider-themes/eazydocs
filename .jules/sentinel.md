## 2026-01-16 – Insecure Direct Object Reference in Nestable Callbacks
**Vulnerability:** `nestable_callback` and `parent_nestable_callback` in `includes/Admin/Admin.php` allowed any user with `edit_posts` capability to modify the hierarchy and order of any post on the site by supplying arbitrary IDs.
**Learning:** Checking a generic capability like `edit_posts` at the start of a bulk action is insufficient when the action modifies specific objects. The permission must be verified for *each* object being modified.
**Prevention:** Always use `current_user_can('edit_post', $post_id)` inside loops that process user-supplied IDs for modification.

## 2026-01-17 – Stored XSS and IDOR in OnePage Edit
**Vulnerability:** `includes/Edit_OnePage.php` updated post content via GET request without `edit_post` capability check (allowing IDOR) and without `wp_kses_post` (allowing Stored XSS). The content was later output via `html_entity_decode` which bypasses entity-based escaping if not stripped.
**Learning:** Custom edit handlers on `admin_init` must strictly enforce capability checks for the specific object and sanitize all rich text inputs with `wp_kses_post`.
**Prevention:** Use `current_user_can('edit_post', $id)` and `wp_kses_post()` for all content updates. Avoid updating data via GET requests.
