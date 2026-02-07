## 2024-05-23 - Stored XSS in OnePage Docs

**Vulnerability:** Stored XSS in `One_Page.php` and `functions.php` where user input from `$_GET` and `$_POST` was saved to post meta without sanitization and output via `do_shortcode`.
**Learning:** `do_shortcode` does not sanitize output; it only expands shortcodes. Always sanitize input before saving to database, especially for rich text fields using `wp_kses_post`.
**Prevention:** Use `wp_kses_post` for rich text/HTML content and `sanitize_text_field` for plain text inputs.
