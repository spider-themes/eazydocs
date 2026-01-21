## 2026-01-16 – Insecure Direct Object Reference in Nestable Callbacks
**Vulnerability:** `nestable_callback` and `parent_nestable_callback` in `includes/Admin/Admin.php` allowed any user with `edit_posts` capability to modify the hierarchy and order of any post on the site by supplying arbitrary IDs.
**Learning:** Checking a generic capability like `edit_posts` at the start of a bulk action is insufficient when the action modifies specific objects. The permission must be verified for *each* object being modified.
**Prevention:** Always use `current_user_can('edit_post', $post_id)` inside loops that process user-supplied IDs for modification.

## 2026-01-16 – Unsafe GET-based Content Updates in Edit_OnePage
**Vulnerability:** `includes/Edit_OnePage.php` processes content updates (including HTML) via `$_GET` request parameters on `admin_init`, without sanitization or specific capability checks.
**Learning:** EazyDocs uses `admin_init` hooks to process some "edit" actions via URL parameters. This pattern is risky as it bypasses standard form handling and length limits, and often lacks the scrutiny of POST handlers.
**Prevention:** When encountering `admin_init` hooks, always check if they process `$_GET` or `$_POST` data that modifies state. Ensure rigorous `check_admin_referer` (nonce), `current_user_can` (specific ID), and sanitization (`wp_kses_post`) are applied.
