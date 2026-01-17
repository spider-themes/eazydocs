## 2026-01-16 – Insecure Direct Object Reference in Nestable Callbacks
**Vulnerability:** `nestable_callback` and `parent_nestable_callback` in `includes/Admin/Admin.php` allowed any user with `edit_posts` capability to modify the hierarchy and order of any post on the site by supplying arbitrary IDs.
**Learning:** Checking a generic capability like `edit_posts` at the start of a bulk action is insufficient when the action modifies specific objects. The permission must be verified for *each* object being modified.
**Prevention:** Always use `current_user_can('edit_post', $post_id)` inside loops that process user-supplied IDs for modification.
