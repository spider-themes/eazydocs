## 2024-10-24 - Code Quality Observations
**Learning:** The codebase consistently uses short array syntax `[]` but frequently mixes loose/strict comparisons and Yoda/non-Yoda conditions.
**Action:** Enforce strict comparisons (`===`, `!==`) and Yoda conditions (`'value' === $var`) in touched files to align with WPCS.

## 2024-05-22 - Missing Dev Dependencies
**Learning:** The project's `composer.json` lacks development dependencies like `phpcs` or `wp-coding-standards/wpcs`.
**Action:** I must rely on manual review and `php -l` for syntax checking.
