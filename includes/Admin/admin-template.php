<?php
/**
 * Docs Builder page template.
 *
 * Previously rendered the full builder UI in PHP. Now just mounts the React app.
 * The original PHP template files (header.php, parent-docs.php, child-docs.php,
 * template-parts.php) are preserved for reference and backward compatibility.
 *
 * @package EazyDocs\Admin
 * @since   2.8.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ezd_docs_builder">
	<div id="ezd-docs-builder-root"></div>
</div>