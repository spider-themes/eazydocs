## 2024-05-23 - WPCS Refactoring in Large Files
**Learning:** When refactoring large files like `includes/functions.php` for WPCS compliance (array syntax, Yoda conditions), automated tools like `sed` or simple regex replacement are risky due to context sensitivity (e.g., matching parentheses, differentiating `is_array` from `array(`). Custom scripts must be extremely precise about token boundaries.
**Action:** For large-scale refactoring, verify token boundaries explicitly (e.g., ensure `array` is not preceded by `_` or alphanumeric characters) and prefer manual batching or robust parsers over simple regex. Also, `replace_with_git_merge_diff` works best with unique context or small chunks.

## 2024-05-23 - Strict Comparisons and Type Casting
**Learning:** Functions like `ceil()` return float, and `get_var()` often returns string. When converting to strict comparisons (`===`), explicit casting (e.g., `(int)`) is crucial to avoid breaking logic that previously relied on loose comparison.
**Action:** Always check the return type of the function being compared before switching to `===`. Use casting to enforce the expected type.
