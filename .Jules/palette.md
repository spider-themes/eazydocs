# Palette's Journal

## 2024-05-22 - MixItUp Filters Accessibility
**Learning:** MixItUp filters implemented as `<li>` elements are invisible to keyboard users and screen readers. They need explicit `role="button"`, `tabindex="0"`, and `aria-pressed` state management.
**Action:** When using non-button elements for interactive filters, always add ARIA roles and keyboard event handlers (Enter/Space) to trigger the native click event.
