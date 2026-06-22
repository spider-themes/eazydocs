<?php
/**
 * Dashboard Header Template
 * Header with quick navigation and primary actions.
 *
 * @package EazyDocs
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a time-based greeting.
 *
 * @return string
 */
if ( ! function_exists( 'ezd_get_greeting' ) ) {
	function ezd_get_greeting() {
		$hour = (int) current_time( 'G' );
		if ( $hour >= 5 && $hour < 12 ) {
			return __( 'Good morning', 'eazydocs' );
		} elseif ( $hour >= 12 && $hour < 17 ) {
			return __( 'Good afternoon', 'eazydocs' );
		} elseif ( $hour >= 17 && $hour < 21 ) {
			return __( 'Good evening', 'eazydocs' );
		}
		return __( 'Good night', 'eazydocs' );
	}
}

/**
 * Whether a submenu slug is actually registered and accessible for the
 * current user. WordPress only adds capability-passing items to $submenu,
 * so this prevents the nav from rendering dead links.
 *
 * @param string $slug Page slug to look for.
 * @return bool
 */
if ( ! function_exists( 'ezd_dashboard_nav_available' ) ) {
	function ezd_dashboard_nav_available( $slug ) {
		global $submenu;
		if ( empty( $submenu['eazydocs'] ) ) {
			return false;
		}
		foreach ( $submenu['eazydocs'] as $item ) {
			if ( isset( $item[2] ) && $item[2] === $slug ) {
				return true;
			}
		}
		return false;
	}
}

// Get current user info.
$current_user = wp_get_current_user();
$user_name    = $current_user->display_name ?: $current_user->user_login;
$greeting     = ezd_get_greeting();

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$ezd_antimanual_active = is_plugin_active( 'antimanual/antimanual.php' ) || is_plugin_active( 'antimanual-pro/antimanual.php' );

// Build the nav items, keeping only those the user can reach.
$ezd_nav_items = [
	[
		'slug'    => 'eazydocs',
		'url'     => admin_url( 'admin.php?page=eazydocs' ),
		'icon'    => 'dashicons-dashboard',
		'label'   => __( 'Overview', 'eazydocs' ),
		'active'  => true,
		'always'  => true,
	],
	[
		'slug'   => 'eazydocs-builder',
		'url'    => admin_url( 'admin.php?page=eazydocs-builder' ),
		'icon'   => 'dashicons-hammer',
		'label'  => __( 'Docs Builder', 'eazydocs' ),
		'active' => false,
	],
	[
		'slug'   => 'ezd-analytics',
		'url'    => admin_url( 'admin.php?page=ezd-analytics' ),
		'icon'   => 'dashicons-chart-area',
		'label'  => __( 'Analytics', 'eazydocs' ),
		'active' => false,
	],
	[
		'slug'   => 'eazydocs-settings',
		'url'    => admin_url( 'admin.php?page=eazydocs-settings' ),
		'icon'   => 'dashicons-admin-settings',
		'label'  => __( 'Settings', 'eazydocs' ),
		'active' => false,
	],
];
?>
<div class="ezd-header">
	<div class="ezd-header-left">
		<div class="ezd-logo-container" title="<?php esc_attr_e( 'EazyDocs Dashboard', 'eazydocs' ); ?>">
			<img src="<?php echo esc_url( EZD_IMG . 'eazydocs-logo.png' ); ?>" alt="<?php esc_attr_e( 'EazyDocs Logo', 'eazydocs' ); ?>">
			<div class="ezd-logo-info">
				<h1 class="ezd-logo-text"><?php esc_html_e( 'Dashboard', 'eazydocs' ); ?></h1>
				<p class="ezd-greeting">
					<?php
					printf(
						/* translators: 1: greeting, 2: user display name */
						esc_html__( '%1$s, %2$s', 'eazydocs' ),
						esc_html( $greeting ),
						'<strong>' . esc_html( $user_name ) . '</strong>'
					);
					?>
				</p>
			</div>
		</div>
	</div>

	<div class="ezd-header-center">
		<nav class="ezd-header-nav" aria-label="<?php esc_attr_e( 'EazyDocs sections', 'eazydocs' ); ?>">
			<?php
			foreach ( $ezd_nav_items as $item ) :
				if ( empty( $item['always'] ) && ! ezd_dashboard_nav_available( $item['slug'] ) ) {
					continue;
				}
				$is_active = ! empty( $item['active'] );
				?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"
					class="ezd-nav-item<?php echo $is_active ? ' is-active' : ''; ?>"
					<?php echo $is_active ? 'aria-current="page"' : ''; ?>>
					<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
	</div>

	<div class="ezd-header-actions">
		<div class="ezd-action-item">
			<?php
			if ( current_user_can( 'publish_docs' ) ) :
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
			<?php if ( $ezd_antimanual_active ) : ?>
				<a id="ezd-create-doc-with-ai"
					href="<?php echo esc_url( admin_url( 'admin.php?page=atml-docs' ) ); ?>"
					class="easydocs-btn easydocs-btn-ai-gold" role="button">
					<span aria-hidden="true">🪄</span> <?php esc_html_e( 'Create Doc with AI', 'eazydocs' ); ?>
				</a>
			<?php else : ?>
				<button type="button" id="ezd-create-doc-with-ai"
					class="easydocs-btn easydocs-btn-ai-gold">
					<span aria-hidden="true">🪄</span> <?php esc_html_e( 'Create Doc with AI', 'eazydocs' ); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
</div>
