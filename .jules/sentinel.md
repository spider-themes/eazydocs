## 2026-01-16 – Insecure Direct Object Reference in Nestable Callbacks
**Vulnerability:** `nestable_callback` and `parent_nestable_callback` in `includes/Admin/Admin.php` allowed any user with `edit_posts` capability to modify the hierarchy and order of any post on the site by supplying arbitrary IDs.
**Learning:** Checking a generic capability like `edit_posts` at the start of a bulk action is insufficient when the action modifies specific objects. The permission must be verified for *each* object being modified.
**Prevention:** Always use `current_user_can('edit_post', $post_id)` inside loops that process user-supplied IDs for modification.

## 2026-02-18 – IDOR and Stored XSS in OnePage Docs Editor
**Vulnerability:** `edit_doc_one_page` in `includes/Edit_OnePage.php` checked generic `edit_posts` capability but allowed modifying any `onepage-docs` post. It also stored content from `$_GET` without sanitization.
**Learning:** Legacy or custom edit handlers often miss the standard `edit_post` capability check found in core handlers. `$_GET` parameters can be vector for Stored XSS if not sanitized.
**Prevention:** Enforce `current_user_can('edit_post', $id)` for all specific post updates. Sanitize all content with `wp_kses_post` or similar before saving to post meta.

## 2026-02-28 – Unauthorized Private Doc Access in AJAX Search
**Vulnerability:** `eazydocs_search_results` in `includes/Frontend/Ajax.php` explicitly allowed `private` posts to be searched by any logged-in user (Subscribers), leaking titles and existence of admin-only content.
**Learning:** Developers often confuse `is_user_logged_in()` with specific permission checks. Relying on "logged in" for access control exposes private data to low-privileged users.
**Prevention:** Always check specific capabilities (`read_private_posts` or custom caps) before including privileged content types in queries.

## 2024-05-23 – Rate Limiting Missing on Public Form
**Vulnerability:** The feedback form handler `eazydocs_feedback_email` allowed unlimited email submissions from unauthenticated users, leading to potential email spam and database flooding.
**Learning:** Publicly accessible AJAX actions (`nopriv`) that trigger resource-intensive operations (sending emails, DB writes) must have rate limiting or CAPTCHA.
**Prevention:** Implement IP-based transient rate limiting for all public-facing form handlers.
