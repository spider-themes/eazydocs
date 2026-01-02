<?php
/**
 * AI Create Doc popup content (shared)
 *
 * Used by the "Create Doc with AI" button across admin pages.
 *
 * @package EazyDocs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_antimanual_active     = ! empty( $is_antimanual_active );
$antimanual_settings_url  = isset( $antimanual_settings_url ) ? (string) $antimanual_settings_url : '';
$antimanual_docs_url      = isset( $antimanual_docs_url ) ? (string) $antimanual_docs_url : '';
$antimanual_install_url   = isset( $antimanual_install_url ) ? (string) $antimanual_install_url : '';
$antimanual_learn_more    = isset( $antimanual_learn_more ) ? (string) $antimanual_learn_more : '';
$antimanual_demo_url      = isset( $antimanual_demo_url ) ? (string) $antimanual_demo_url : '';
$antimanual_video_mp4_url = isset( $antimanual_video_mp4_url ) ? (string) $antimanual_video_mp4_url : '';
?>
<div class="ezd-ai-popup-content">
	<div class="ezd-ai-header">
		<div class="ezd-ai-badge">
			🤖 <?php esc_html_e( 'Powered by Antimanual AI', 'eazydocs' ); ?>
		</div>
		<h2><?php esc_html_e( 'Create Docs Smarter with AI', 'eazydocs' ); ?></h2>
		<p class="ezd-ai-desc"><?php esc_html_e( 'Transform documentation with structured, accurate, and professional docs in minutes using enterprise-grade AI.', 'eazydocs' ); ?></p>
	</div>

	<div class="ezd-ai-body">
		<div class="ezd-ai-video-wrapper">
			<video autoplay muted loop playsinline>
				<source src="<?php echo esc_url( $antimanual_video_mp4_url ); ?>" type="video/mp4">
			</video>
		</div>

		<div class="ezd-ai-feature-grid">
			<div class="ezd-ai-feature-item">
				<div class="ezd-title-with-icon">
					<span class="ezd-ai-feature-icon">🤖</span>
					<h3><?php esc_html_e( 'Documentation Generator', 'eazydocs' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'Generate comprehensive, SEO-optimized docs with custom tone and language. Choose professional, friendly, or technical styles.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-ai-feature-item">
				<div class="ezd-title-with-icon">
					<span class="ezd-ai-feature-icon">💬</span>
					<h3><?php esc_html_e( '24/7 AI Chatbot', 'eazydocs' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'Reduce support tickets by 70%+ with an intelligent chatbot trained on your EazyDocs knowledge base. Instant, accurate answers.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-ai-feature-item">
				<div class="ezd-title-with-icon">
					<span class="ezd-ai-feature-icon">📊</span>
					<h3><?php esc_html_e( 'Bulk Generation', 'eazydocs' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'Generate multiple docs at once with comprehensive structures. Review and refine outlines before generation.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-ai-feature-item">
				<div class="ezd-title-with-icon">
					<span class="ezd-ai-feature-icon">📄</span>
					<h3><?php esc_html_e( 'Docs from Files', 'eazydocs' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'Upload PDFs, URLs, and custom text to generate context-aware documentation automatically.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-ai-feature-item">
				<div class="ezd-title-with-icon">
					<span class="ezd-ai-feature-icon">🔍</span>
					<h3><?php esc_html_e( 'Semantic AI Search', 'eazydocs' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'Beyond keywords - understand user intent with natural language processing and smart suggestions.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-ai-feature-item">
				<div class="ezd-title-with-icon">
					<span class="ezd-ai-feature-icon">🚀</span>
					<h3><?php esc_html_e( 'GPT-5 & Gemini Support', 'eazydocs' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'Powered by latest AI models including GPT-4, GPT-5, and Google Gemini for superior accuracy.', 'eazydocs' ); ?></p>
			</div>
		</div>
	</div>

	<?php if ( $is_antimanual_active ) : ?>
		<div class="ezd-ai-popup-footer ezd-ai-active">
			<div class="ezd-ai-active-badge">
				<span class="dashicons dashicons-yes-alt"></span>
				<span><?php esc_html_e( 'Antimanual is Active!', 'eazydocs' ); ?></span>
			</div>
			<div class="ezd-ai-footer-btns">
				<a href="<?php echo esc_url( $antimanual_settings_url ); ?>" class="ezd-ai-btn ezd-ai-btn-primary">
					<?php esc_html_e( 'Manage AI Settings', 'eazydocs' ); ?>
					<span class="dashicons dashicons-admin-generic"></span>
				</a>
				<a href="<?php echo esc_url( $antimanual_docs_url ); ?>" target="_blank" rel="noopener" class="ezd-ai-btn ezd-ai-btn-secondary">
					<?php esc_html_e( 'View Documentation', 'eazydocs' ); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
			</div>
		</div>
	<?php else : ?>
		<div class="ezd-ai-popup-footer">
			<div class="ezd-ai-cta-text">
				<span class="dashicons dashicons-info"></span>
				<p><?php esc_html_e( 'Get started with Antimanual to unlock all AI features for your EazyDocs knowledge base!', 'eazydocs' ); ?></p>
			</div>
			<div class="ezd-ai-footer-btns">
				<a href="<?php echo esc_url( $antimanual_install_url ); ?>" class="ezd-ai-btn ezd-ai-btn-primary">
					<span class="dashicons dashicons-download"></span>
					<?php esc_html_e( 'Install Antimanual Free', 'eazydocs' ); ?>
				</a>
				<a href="<?php echo esc_url( $antimanual_learn_more ); ?>" target="_blank" rel="noopener" class="ezd-ai-btn ezd-ai-btn-secondary">
					<?php esc_html_e( 'Learn More', 'eazydocs' ); ?>
					<span class="dashicons dashicons-external"></span>
				</a>
				<a href="<?php echo esc_url( $antimanual_demo_url ); ?>" target="_blank" rel="noopener" class="ezd-ai-btn ezd-ai-btn-link">
					<span class="dashicons dashicons-video-alt3"></span>
					<?php esc_html_e( 'Watch Demo', 'eazydocs' ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>
</div>
