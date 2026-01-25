## 2024-03-24 - Accessibility of Bulk Actions
**Learning:** Legacy WordPress plugins often use `<span>` elements for dropdown triggers to preserve specific CSS hierarchies, making accessibility retrofits challenging. Converting these to `<button>` can break layout.
**Action:** In these cases, use `role="button"`, `tabindex="0"`, and explicitly handle `keydown` (Enter/Space) and `aria-expanded` states via JavaScript to ensure keyboard accessibility without refactoring CSS.

## 2024-05-21 - Accessible Progress Bars in Lists
**Learning:** Native `<progress>` elements are great but often lack accessible context when used in lists or tables (like voting results). The `title` attribute is insufficient for screen readers. Also, duplicate IDs (like `id="file"` in a loop) are common in legacy loops.
**Action:** Always add `aria-label` with the full context (e.g., "5 Positive votes, 2 Negative votes") to `<progress>` elements. Ensure IDs are unique or removed if not needed by JS/CSS.

## 2024-05-22 - Keyboard Access for Semantic-less Filters
**Learning:** Admin dashboards often use `<ul>`/`<li>` for filter tabs with inline `onclick` handlers, rendering them inaccessible to keyboard users.
**Action:** Retrofit these with `role="button"`, `tabindex="0"`, and inline `onkeydown` handlers (for Enter/Space) to enable keyboard access without altering the markup structure or breaking existing styles.

## 2024-05-22 - Context for Repeated Actions
**Learning:** In EazyDocs admin templates (like `child-docs.php`), action buttons inside loops often use generic text ("Add Section") and sometimes duplicate IDs, confusing screen readers and breaking JS event delegation.
**Action:** When refactoring repeated actions, replace duplicate IDs with classes, update JS delegates, and add `aria-label` including the parent item's name (e.g., "Add Section to [Doc Name]") to provide necessary context.

## 2024-05-23 - Avoiding Roving Tabindex Complexity
**Learning:** When retrofitting filters, attempting a full "Roving Tabindex" pattern (arrow key navigation) on non-standard elements is error-prone and complex to maintain. Simple implementations (tabindex="-1" on inactive items) create keyboard traps if arrow keys aren't handled.
**Action:** Prefer the simpler "Toolbar" or "Group" pattern where all items are focusable (`tabindex="0"`) with `role="button"` and `aria-pressed`. It's safer, easier to implement, and sufficiently accessible for small filter sets.
