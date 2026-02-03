## 2024-05-23 - Search Performance Bottleneck
**Learning:** The `eazydocs_search_results` AJAX handler executes `SHOW TABLES LIKE ...` queries twice on *every* request (even on every keystroke) to check for logging table existence, adding significant overhead.
**Action:** Cache table existence checks using transients (e.g., `ezd_search_tables_exist`) to bypass schema queries on high-frequency endpoints.
