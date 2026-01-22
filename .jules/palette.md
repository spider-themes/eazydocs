## 2024-03-24 - Accessibility of Bulk Actions
**Learning:** Legacy WordPress plugins often use `<span>` elements for dropdown triggers to preserve specific CSS hierarchies, making accessibility retrofits challenging. Converting these to `<button>` can break layout.
**Action:** In these cases, use `role="button"`, `tabindex="0"`, and explicitly handle `keydown` (Enter/Space) and `aria-expanded` states via JavaScript to ensure keyboard accessibility without refactoring CSS.
