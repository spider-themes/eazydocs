# Local security audit

The review covered the EazyDocs and sibling EazyDocs Pro checkouts. The supplied
disclosure screenshot contains no technical report, endpoint, or reproduction.
These findings were established independently; they do not identify or resolve a
particular researcher's report by themselves.

## Additional findings fixed in the deeper audit

| Area | Verified behavior before the fix | Change |
| --- | --- | --- |
| Legacy visibility actions | A documentation author with a valid builder nonce could change another author's document visibility. The handler accepted arbitrary statuses. | Require administrative access, a docs target, and an allowed visibility value. |
| Section visibility | The descendant traversal could collect attached media and convert attachments into docs. | Traverse docs only, check target permissions before writes, and preserve titles and media types. |
| Role propagation and hierarchy | Applying roles to children overwrote another author's child metadata. Forged ordering payloads could move a doc under an unrelated post type. | Check each descendant's edit permission; validate the complete proposed ordering before updating it. Add cycle protection to role inheritance and propagation. |
| REST and builder reads | Core REST single-item reads returned role-restricted published content. REST collections, counts, and builder tree endpoints could reveal restricted entries. | Gate single REST reads before controller execution, filter REST candidates before pagination, and check builder read access. |
| Exports | The `read_post` capability alone allowed a doc writer to export password-protected content they could not edit. Descendants had the same gap. | Apply content-access checks to CSV and Markdown roots and descendants. Authorized editors retain exports. |
| Profile feeds | An activity cache populated for an authorized viewer could later expose restricted document activity to another viewer. Contribution lists also lacked a content-access check. | Store document IDs in cached activities and recheck access and current status per viewer before pagination. Filter contribution results and bound page sizes. |
| Assistant default list | Shared rendered HTML could retain a privileged viewer's restricted excerpts. The default excerpt builder read password-protected bodies directly. | Render the bounded list per visitor, check role access, and protect excerpts. Keep WordPress query caching without sharing personalized HTML. |
| Public feedback | Voting accepted unrelated post types. Mail feedback accepted unreadable private docs. Selected-text feedback ignored its configured role restriction. | Validate document type and content access before writing or mailing; enforce selected-feedback roles. Reject empty mail messages. |
| Subscriptions | Direct requests bypassed disabled-feature and document-access checks. Automatic subscriptions overwrote other visitors' pending confirmations. Replayed confirmations duplicated records. | Validate enabled/readable docs roots; preserve pending data; make confirmations idempotent and deduplicate email addresses. Rate-limit confirmation-mail requests. |
| Subscription notifications | A newly published role-restricted child could be emailed to subscribers who lacked access. The sender also loaded the entire published library, exhausting memory in the large-document test. | Check recipient-specific access, suppress protected content, and read subscriber records only from the relevant root. |
| Settings saves | The framework's options-save path relied on its nonce without independently enforcing the configured settings capability. | Add tracked application-level authorization before AJAX dispatch and before settings persistence. Preserve intentionally delegated settings access. |
| Markdown imports | Compressed uploads could expand into very large imports or deeply nested folder structures. | Limit accepted decompressed content to 20 MB, folder depth to 20, individual Markdown files to 2 MB, and parser nesting to 50. Bound archive reads. |
| Startup and reactivation | The assistant scope picker loaded complete document bodies during startup. With large fixtures this exhausted memory. Reinitializing existing analytics tables also generated invalid SQL from a foreign-key clause. | Fetch only scope IDs/titles. Include the foreign-key declaration only when creating the table; preserve existing constraints during migrations. |

The earlier patches for tree deletion, duplication, Pro sorting, AJAX visibility,
protected excerpts, OnePage creation, and builder publishing remain in place.
Existing user changes to OnePage metadata saving were preserved. Roles explicitly
selected as Documentation Authors retain the publishing rights described by the
settings screen.

## Validation

- **45 integration checks passed** against a disposable WordPress 7.1.3/MySQL 8.0.35 installation using PHP 8.4.12 with a 128 MB memory limit. The REST checks cover role inheritance/overrides and verify excluded document bodies are not loaded into memory. The assistant check switches between privileged and public viewers. Follow-up checks cover normal imports, delegated settings, owned role propagation, mixed-author ordering, OnePage publishing, and filtered empty states.
- Before the final REST load/inheritance and assistant checks were added, the 35-check suite produced **26 failures** against a snapshot taken at the start of the deeper audit. These are failing scenarios, not 26 distinct vulnerabilities.
- **24 isolated regression checks passed** for the earlier fixes.
- PHP syntax checks passed for all 27 changed/test PHP files; both repository diffs passed whitespace checks.
- Both plugins loaded, deactivated, and reactivated successfully in the disposable installation at a 128 MB memory limit, including with the large-document fixtures present. Repeated analytics schema initialization completed without SQL errors.
- OAuth checks verified signed state handling, browser-token matching, and rejection of unverified email addresses. No real Google credentials or token exchanges were used.
- Integration mail was intercepted and external HTTP was blocked.
- The follow-up compatibility review fixed five hardening regressions and existing subscription JavaScript problems. Five isolated Chromium subscription checks and ten real WordPress browser flow checks passed. Ten desktop/mobile screenshot pairs matched exactly for normal public/authorized content. See [REGRESSION-CHECK.md](REGRESSION-CHECK.md) for the fixture scope and remaining limits.

Commands:

```sh
php tests/security-regressions.php
php tests/security-integration.php /path/to/disposable/wp-load.php
```

The integration runner refuses to execute its fixtures unless the database is
named `ezd_audit` and the site URL is `http://ezd-audit.invalid`. Its Pro feature
checks use a test license object; plugin activation was checked separately with
the actual bootstrap. Fixtures and service setup were isolated from the user's
site and its database.

## Limits and delivery

This is a targeted source review and local reproduction exercise, not a security
certification. Production behavior, browser interactions, multisite, other PHP/
WordPress versions, live mail/OAuth providers, and the full third-party dependency
codebase were not comprehensively tested. The original disclosure remains
unconfirmed without its technical report.

Changes are local and uncommitted across **both** plugin repositories. No release
was published, no message was sent to the disclosure contact, and the user's
existing site content was not changed.
