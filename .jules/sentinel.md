## 2024-05-23 – Information Disclosure in Docs Listing
**Vulnerability:** The `[eazydocs]` shortcode and `ezd_list_pages` function explicitly requested `post_status => ['publish', 'private']`. This forced `WP_Query` to return private documents to unauthenticated users, exposing their titles and existence.
**Learning:** In WordPress, `WP_Query` automatically protects private posts *unless* you explicitly ask for them in `post_status`. When explicitly requested, `WP_Query` assumes the caller handles the permission check.
**Prevention:** Never hardcode `['publish', 'private']`. Instead, build the `post_status` array conditionally:
```php
$statuses = ['publish'];
if ( current_user_can('read_private_docs') ) {
    $statuses[] = 'private';
}
```
