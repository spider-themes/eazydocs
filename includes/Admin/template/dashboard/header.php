<?php
/**
 * Dashboard Header Template
 * Enhanced header with navigation and quick actions
 *
 * @package EazyDocs
 */

// Get current user info
$current_user = wp_get_current_user();
$user_name    = $current_user->display_name ?: $current_user->user_login;
$greeting     = ezd_get_greeting();

/**
 * Get time-based greeting
 *
 * @return string
 */
function ezd_get_greeting() {
	$hour = (int) current_time( 'G' );
	if ( $hour >= 5 && $hour < 12 ) {
		return __( 'Good morning', 'eazydocs' );
	} elseif ( $hour >= 12 && $hour < 17 ) {
		return __( 'Good afternoon', 'eazydocs' );
	} elseif ( $hour >= 17 && $hour < 21 ) {
		return __( 'Good evening', 'eazydocs' );
	} else {
		return __( 'Good night', 'eazydocs' );
	}
}
?>
<div class="ezd-header">
	<div class="ezd-header-left">
		<div class="ezd-logo-container" title="EazyDocs Dashboard">
			<img src="<?php echo esc_url( EAZYDOCS_IMG . '/eazydocs-logo.png' ); ?>" alt="EazyDocs Logo">
			<div class="ezd-logo-info">
				<h1 class="ezd-logo-text"><?php esc_html_e( 'Dashboard', 'eazydocs' ); ?></h1>
				<p class="ezd-greeting"><?php echo esc_html( $greeting ); ?>, <strong><?php echo esc_html( $user_name ); ?></strong></p>
			</div>
		</div>
	</div>
	
	<div class="ezd-header-center">
		<nav class="ezd-header-nav">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs' ) ); ?>" class="ezd-nav-item is-active">
				<span class="dashicons dashicons-dashboard"></span>
				<?php esc_html_e( 'Overview', 'eazydocs' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-builder' ) ); ?>" class="ezd-nav-item">
				<span class="dashicons dashicons-hammer"></span>
				<?php esc_html_e( 'Docs Builder', 'eazydocs' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics' ) ); ?>" class="ezd-nav-item">
				<span class="dashicons dashicons-chart-area"></span>
				<?php esc_html_e( 'Analytics', 'eazydocs' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-settings' ) ); ?>" class="ezd-nav-item">
				<span class="dashicons dashicons-admin-settings"></span>
				<?php esc_html_e( 'Settings', 'eazydocs' ); ?>
			</a>
		</nav>
	</div>

	<div class="ezd-header-actions">
		<div class="ezd-action-item">
			<?php
			if ( current_user_can( 'edit_posts' ) ) :
				$nonce = wp_create_nonce( 'parent_doc_nonce' );
				?>
				<button type="button"
					data-url="<?php echo esc_url( admin_url( 'admin.php' ) . "?Create_doc=yes&_wpnonce={$nonce}&parent_title=" ); ?>"
					id="parent-doc" class="easydocs-btn easydocs-btn-outline-blue">
					<span class="dashicons dashicons-plus-alt2"></span>
					<?php esc_html_e( 'Add Doc', 'eazydocs' ); ?>
				</button>
				<?php
			endif;
			?>
			<button type="button" id="ezd-create-doc-with-ai"
				class="easydocs-btn easydocs-btn-ai-gold">
				<span>🪄</span> <?php esc_html_e( 'Create Doc with AI', 'eazydocs' ); ?>
			</button>
		</div>
	</div>
</div>
