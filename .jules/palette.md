## 2024-03-24 - Accessibility of Bulk Actions
**Learning:** Legacy WordPress plugins often use `<span>` elements for dropdown triggers to preserve specific CSS hierarchies, making accessibility retrofits challenging. Converting these to `<button>` can break layout.
**Action:** In these cases, use `role="button"`, `tabindex="0"`, and explicitly handle `keydown` (Enter/Space) and `aria-expanded` states via JavaScript to ensure keyboard accessibility without refactoring CSS.

## 2024-05-23 - Nested Interactive Elements in ARIA Tabs
**Learning:** When turning list items into ARIA tabs (`role="tab"`) that contain other interactive elements (links/buttons), a simple `click` handler on the parent is insufficient. It can hijack clicks on children.
**Action:** In the event handler, check `$(e.target).closest('a, button, input').length` before executing the tab switch logic. This allows the parent to be a tab while still letting children function independently.
