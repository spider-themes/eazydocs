# Security fix compatibility review

Reviewed both the EazyDocs and EazyDocs Pro local changes on 2026-11-13.

## Regressions corrected

| Feature | Problem introduced by hardening | Correction |
| --- | --- | --- |
| Core document ordering | A serialized tree containing unchanged documents owned by another author blocked an otherwise authorized reorder. | Validate the complete payload, but require edit access only for items whose parent/order changes. Foreign changes, duplicate IDs and unrelated post types remain rejected. |
| OnePage generation | Configured Documentation Authors with `publish_docs` but no `publish_posts` could not generate their own OnePage docs. | Check document publishing capability and edit access to the source. Draft-only roles and other authors remain blocked. |
| Assistant search | Removing every inaccessible result inside the loop left a blank panel. | Filter candidates before checking whether results exist, preserving the configured empty-state message. |
| Assistant default list | The same filtering problem suppressed the default list's empty state. | Update the filtered query's post count before rendering. |
| Subscription confirmation | Deduplicating two tokens for one email returned the existing token, but the browser retained the removed token. Immediate unsubscribe then failed. | Use the canonical token returned by confirmation, retaining a fallback for compatible responses. |

The four ordering/OnePage/empty-state integration scenarios failed before their
corrections and passed afterward. The token mismatch was reproduced in Chromium
against the actual WordPress AJAX handlers before its correction.

The browser review also found existing subscription issues: an out-of-scope
`ajaxUrl` prevented unsubscribe requests; two handlers could open duplicate
unsubscribe dialogs; and guests were switched to the logged-in subscribe path.
Unsubscribe now has one handler, uses the correct AJAX URL, and restores the
appropriate guest/logged-in state. HTTP 429 responses display the server's retry
message and restore the submit button. The script URL changes when the script
file changes to prevent reuse of its previous `ver=1` cached copy.

## Automated and browser checks

- **45 WordPress integration checks passed** at a 128 MB PHP memory limit, including normal Markdown archive hierarchy/formatting, authorized exports, delegated settings, owned role propagation, configured publishing, and security rejection cases.
- **24 isolated PHP regression checks passed.**
- **5 isolated Chromium subscription checks passed**, running the real script with intercepted AJAX. These cover canonical confirmation tokens, guest re-subscription, rate-limit recovery, and initially subscribed/unsubscribed logged-in cycles, with one dialog/request per action.
- **10 real WordPress browser checks passed:** feedback voting; assistant search; instant answers; Escape closing; guest form submission; guest confirmation; guest unsubscribe; HTTP 429 recovery; two logged-in subscribe/unsubscribe cycles; and absence of browser JavaScript errors in those flows. A separate real-handler check verified canonical-token confirmation and unsubscribe together.
- All **27 changed/test PHP files** passed syntax checking; JavaScript syntax and whitespace checks passed in both repositories. Both plugins reactivated at 128 MB with the large-document fixtures present.

Commands for the test files in this checkout:

```sh
php tests/security-regressions.php
php tests/security-integration.php /path/to/disposable/wp-load.php
node tests/subscription-ui-regressions.cjs
```

The Chromium test requires the local Playwright package and Chrome. Optional
arguments select the Pro checkout and WordPress jQuery file; `CHROME_PATH`
selects the browser executable. The integration runner retains its disposable
database/site URL guard.

## Visual comparison

Baseline plugin PHP was reconstructed from each checkout's `HEAD`. The patched
plugins used the same database, URLs, theme, fixtures and browser. Plugin assets
and templates were unchanged between variants. External resources were blocked,
fonts were settled, and animations were disabled before capture.

| Screen | Desktop viewport 1440 × 1000 | Mobile viewport 390 × 844 |
| --- | --- | --- |
| Docs Builder | 0 differing pixels | 0 differing pixels |
| Public documentation page | 0 differing pixels | 0 differing pixels |
| Documentation grid | 0 differing pixels | 0 differing pixels |
| Assistant with a completed public search | 0 differing pixels | 0 differing pixels |
| Guest subscription popup | 0 differing pixels | 0 differing pixels |

Screenshots and machine-readable pixel counts are retained at:
`/Users/ehjewel/.codex/visualizations/2026/11/12/01a1d0b6-5566-7360-b138-cd0d179bfc26/eazydocs-regression/`.

Password-protected excerpts intentionally show a protection message, and
inaccessible results are intentionally filtered. These security-dependent
content changes are separate from the normal-content visual comparisons.

## Scope and limits

The user's Local site/database was stopped. Tests used an isolated WordPress
7.1.3/MySQL 8.0.35 installation, a minimal test theme and a test Pro license
object. Actual plugin bootstrap, AJAX handlers, scripts and templates ran, while
email was intercepted and external HTTP was blocked. The user's site content
and database were not changed.

These results do not establish compatibility with the user's actual theme,
third-party plugins, production caches, live mail/OAuth providers, multisite or
every plugin feature. Automated drag gestures were inconclusive about UI
reorder persistence; successful pointer/keyboard drag-and-drop is not claimed.
Authorized reorder persistence and rejection behavior were checked directly in
the integration suite. Some horizontal overflow already present in the test
theme/viewport remained identical before and after; this was a comparison of
security changes, not a responsive redesign.

All changes remain local and uncommitted across both plugin repositories.
