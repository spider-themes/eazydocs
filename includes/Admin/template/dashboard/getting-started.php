<?php
/**
 * First-run Onboarding Banner
 * Shown on the dashboard when no docs exist yet, to orient new users.
 *
 * @package EazyDocs
 */

defined( 'ABSPATH' ) || exit;

$ezd_can_publish = current_user_can( 'publish_docs' );
$ezd_doc_nonce   = wp_create_nonce( 'parent_doc_nonce' );
$ezd_create_url  = admin_url( 'admin.php' ) . '?Create_doc=yes&_wpnonce=' . $ezd_doc_nonce . '&parent_title=';
?>
<div class="ezd-onboarding">
	<div class="ezd-onboarding__content">
		<span class="ezd-onboarding__icon dashicons dashicons-welcome-learn-more" aria-hidden="true"></span>
		<div class="ezd-onboarding__text">
			<h2 class="ezd-onboarding__title"><?php esc_html_e( 'Welcome to EazyDocs!', 'eazydocs' ); ?></h2>
			<p class="ezd-onboarding__subtitle">
				<?php esc_html_e( 'Create your first document to start building your knowledge base. Analytics and insights will appear here as visitors read and search your docs.', 'eazydocs' ); ?>
			</p>
		</div>
	</div>
	<div class="ezd-onboarding__actions">
		<?php if ( $ezd_can_publish ) : ?>
			<a href="<?php echo esc_url( $ezd_create_url ); ?>" class="ezd-onboarding__btn ezd-onboarding__btn--primary">
				<span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
				<?php esc_html_e( 'Create Your First Doc', 'eazydocs' ); ?>
			</a>
		<?php endif; ?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-initial-setup' ) ); ?>" class="ezd-onboarding__btn ezd-onboarding__btn--secondary">
			<span class="dashicons dashicons-admin-settings" aria-hidden="true"></span>
			<?php esc_html_e( 'Run Setup Wizard', 'eazydocs' ); ?>
		</a>
		<a href="https://helpdesk.spider-themes.net/docs/eazydocs/" target="_blank" rel="noopener" class="ezd-onboarding__btn ezd-onboarding__btn--link">
			<span class="dashicons dashicons-book" aria-hidden="true"></span>
			<?php esc_html_e( 'Read the Docs', 'eazydocs' ); ?>
		</a>
	</div>
</div>
