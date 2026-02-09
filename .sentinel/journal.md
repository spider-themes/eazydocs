## 2026-02-09 - Weak Nonce Actions on Resource IDs

**Vulnerability:** Nonces were generated using only the resource ID (e.g. `wp_create_nonce( $post_id )`) for multiple distinct actions (Delete Doc, Create Child, Create Section).
**Learning:** Using raw IDs as nonce actions creates ambiguity and potential reuse vulnerabilities if an attacker can obtain a nonce for that ID from a different context.
**Prevention:** Always namespace nonce actions with a specific prefix (e.g. `action_name_` . $id) to ensure they are scoped to the intended operation.
