## 2024-05-22 - [Keyboard-Accessible Custom Dropdowns]
**Learning:** Legacy WordPress admin interfaces often use `span` or `div` for dropdown triggers to avoid browser default button styling. To make them accessible without breaking layout, add `role="button"`, `tabindex="0"`, `aria-expanded`, and a keydown handler for Enter/Space, rather than replacing the tag with `<button>`.
**Action:** When retrofitting legacy UI, prefer attribute enhancement over tag replacement to minimize visual regressions while achieving full accessibility.
