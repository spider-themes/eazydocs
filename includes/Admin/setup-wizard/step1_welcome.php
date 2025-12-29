<?php
/**
 * Step 1: Welcome
 *
 * @package EazyDocs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="step-1" class="tab-pane ezd-welcome-step" role="tabpanel">
	<div class="ezd-step-header">
		<div class="ezd-welcome-icon">
			<img src="<?php echo esc_url( EAZYDOCS_IMG . '/eazydocs-favicon.png' ); ?>" alt="<?php echo esc_attr__( 'EazyDocs', 'eazydocs' ); ?>"/>
		</div>
		<h2><?php esc_html_e( 'Welcome to EazyDocs!', 'eazydocs' ); ?></h2>
		<p class="ezd-step-description"><?php esc_html_e( 'Thank you for choosing EazyDocs - the best documentation plugin for WordPress. Let\'s set up your knowledge base in just a few minutes.', 'eazydocs' ); ?></p>
	</div>

	<div class="ezd-welcome-content">
		<!-- Video Section -->
		<div class="ezd-welcome-video">
			<div class="ezd-video-wrapper">
				<iframe 
					width="100%" 
					height="340" 
					src="https://www.youtube.com/embed/4H2npHIR2qg?si=ApQh7BL6CL5QM4zX" 
					title="<?php echo esc_attr__( 'EazyDocs Tutorial', 'eazydocs' ); ?>" 
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
					referrerpolicy="strict-origin-when-cross-origin" 
					allowfullscreen>
				</iframe>
			</div>
			<p class="ezd-video-caption">
				<span class="dashicons dashicons-video-alt3"></span>
				<?php esc_html_e( 'Watch this 3-minute video to learn how to create your first documentation', 'eazydocs' ); ?>
			</p>
		</div>

		<!-- Features Highlight -->
		<div class="ezd-features-grid">
			<div class="ezd-feature-card">
				<div class="ezd-feature-icon">
					<span class="dashicons dashicons-editor-table"></span>
				</div>
				<h4><?php esc_html_e( 'Drag & Drop Builder', 'eazydocs' ); ?></h4>
				<p><?php esc_html_e( 'Easily organize your documentation with our intuitive drag and drop interface.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-feature-card">
				<div class="ezd-feature-icon">
					<span class="dashicons dashicons-search"></span>
				</div>
				<h4><?php esc_html_e( 'Live Search', 'eazydocs' ); ?></h4>
				<p><?php esc_html_e( 'Help users find answers quickly with instant AJAX-powered search.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-feature-card">
				<div class="ezd-feature-icon">
					<span class="dashicons dashicons-chart-bar"></span>
				</div>
				<h4><?php esc_html_e( 'Analytics', 'eazydocs' ); ?></h4>
				<p><?php esc_html_e( 'Track what users search for and improve your documentation.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-feature-card">
				<div class="ezd-feature-icon">
					<span class="dashicons dashicons-admin-customizer"></span>
				</div>
				<h4><?php esc_html_e( 'Customizable', 'eazydocs' ); ?></h4>
				<p><?php esc_html_e( 'Match your brand with extensive styling and layout options.', 'eazydocs' ); ?></p>
			</div>
		</div>

		<!-- Quick Links -->
		<div class="ezd-quick-links">
			<a href="https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/" target="_blank" class="ezd-quick-link">
				<span class="dashicons dashicons-sos"></span>
				<span><?php esc_html_e( 'Documentation', 'eazydocs' ); ?></span>
			</a>
			<a href="https://www.youtube.com/playlist?list=PLeCjxMdg411XgYy-AekTE-bhvCXQguZWJ" target="_blank" class="ezd-quick-link">
				<span class="dashicons dashicons-video-alt3"></span>
				<span><?php esc_html_e( 'Video Tutorials', 'eazydocs' ); ?></span>
			</a>
			<a href="https://wordpress.org/support/plugin/eazydocs/" target="_blank" class="ezd-quick-link">
				<span class="dashicons dashicons-editor-help"></span>
				<span><?php esc_html_e( 'Get Support', 'eazydocs' ); ?></span>
			</a>
		</div>
	</div>
</div>