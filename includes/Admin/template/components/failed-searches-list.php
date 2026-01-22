<?php
/**
 * Failed Searches List Component
 * Shared template for displaying failed search keywords
 * Used in Dashboard and Analytics > Search page
 *
 * @package EazyDocs
 *
 * @var array  $failed_searches Array of failed search keyword objects
 * @var int    $total_failed    Total count of unique failed search keywords
 * @var string $context         Context where the component is used: 'dashboard' or 'analytics'
 * @var int    $limit           Maximum number of items to display (0 = all)
 */

// Set defaults.
$failed_searches = isset( $failed_searches ) ? $failed_searches : array();
$total_failed    = isset( $total_failed ) ? (int) $total_failed : count( $failed_searches );
$context         = isset( $context ) ? $context : 'dashboard';
$limit           = isset( $limit ) ? (int) $limit : 0;

// Apply limit if set.
if ( $limit > 0 && count( $failed_searches ) > $limit ) {
	$failed_searches = array_slice( $failed_searches, 0, $limit );
}

$has_failed_searches = ! empty( $failed_searches );
$is_dashboard        = 'dashboard' === $context;
$is_analytics        = 'analytics' === $context;
?>

<div class="ezd-card ezd-failed-searches-card<?php echo $is_analytics ? ' ezd-failed-searches-card--analytics' : ''; ?>">
	<div class="ezd-card-header">
		<h2 class="ezd-card-title">
			<span class="dashicons dashicons-search"></span>
			<?php esc_html_e( 'Failed Searches', 'eazydocs' ); ?>
		</h2>
		<?php if ( $has_failed_searches ) : ?>
			<span class="ezd-card-badge ezd-badge-warning"><?php echo esc_html( $total_failed ); ?></span>
		<?php endif; ?>
	</div>

	<?php if ( $has_failed_searches ) : ?>
		<?php if ( $is_analytics ) : ?>
			<div class="ezd-failed-searches-info">
				<span class="dashicons dashicons-lightbulb"></span>
				<p><?php esc_html_e( 'These keywords yielded no results. Consider creating content to address these searches.', 'eazydocs' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="ezd-failed-searches-list" id="<?php echo $is_analytics ? 'failed-keywords-list' : ''; ?>">
			<?php foreach ( $failed_searches as $search ) : ?>
				<div class="ezd-failed-search-item" data-keyword-id="<?php echo esc_attr( $search->keyword_id ?? '' ); ?>">
					<div class="ezd-failed-search-info">
						<span class="ezd-failed-search-keyword"><?php echo esc_html( $search->keyword ); ?></span>
						<span class="ezd-failed-search-count">
							<span class="dashicons dashicons-chart-bar"></span>
							<?php
							$count = isset( $search->total_failed ) ? (int) $search->total_failed : ( isset( $search->not_found_count ) ? (int) $search->not_found_count : 0 );
							printf(
								/* translators: %d: number of failed search attempts */
								esc_html__( '%d attempts', 'eazydocs' ),
								$count
							);
							?>
						</span>
					</div>
					<div class="ezd-failed-search-actions">
						<?php
						$nonce      = wp_create_nonce( 'parent_doc_nonce' );
						$create_url = admin_url( 'admin.php' ) . '?Create_doc=yes&_wpnonce=' . $nonce . '&parent_title=' . rawurlencode( $search->keyword );
						?>
						<a href="<?php echo esc_url( $create_url ); ?>"
						   class="ezd-btn-create-doc"
						   title="<?php esc_attr_e( 'Create doc for this keyword', 'eazydocs' ); ?>">
							<span class="dashicons dashicons-plus"></span>
							<?php esc_html_e( 'Create', 'eazydocs' ); ?>
						</a>
						<button type="button"
							class="ezd-btn-resolve-search"
							data-keyword-id="<?php echo esc_attr( $search->keyword_id ?? '' ); ?>"
							data-action="resolve"
							title="<?php esc_attr_e( 'Mark as resolved', 'eazydocs' ); ?>">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php esc_html_e( 'Resolve', 'eazydocs' ); ?>
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $is_dashboard && $total_failed > $limit ) : ?>
			<div class="ezd-card-footer">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-search' ) ); ?>" class="ezd-view-all-link">
				<?php
				printf(
					/* translators: %d: total number of failed search keywords */
					esc_html__( 'View all %d Keywords', 'eazydocs' ),
					$total_failed
				);
				?>
				<span class="dashicons dashicons-arrow-right-alt"></span>
				</a>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<div class="ezd-empty-state">
			<span class="ezd-empty-icon">
				<span class="dashicons dashicons-yes-alt"></span>
			</span>
			<p class="ezd-empty-text"> <?php esc_html_e( 'Great! No failed searches recorded.', 'eazydocs' ); ?> </p>
			<p class="ezd-empty-subtext"> <?php esc_html_e( 'Your documentation is covering user needs well.', 'eazydocs' ); ?> </p>
		</div>
	<?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
	// Only initialize once per page
	if (window.ezdFailedSearchesInitialized) return;
	window.ezdFailedSearchesInitialized = true;

	// Create floating toast container if not exists
	if (!$('#ezd-toast-container').length) {
		$('body').append('<div id="ezd-toast-container" class="ezd-toast-container"></div>');
	}

	// Helper function to show floating toast notification
	function showEzdResolveNotice(message, type) {
		var iconClass = type === 'success' ? 'yes-alt' : 'warning';
		var $toast = $('<div class="ezd-toast ezd-toast--' + type + '">' +
			'<span class="ezd-toast__icon dashicons dashicons-' + iconClass + '"></span>' +
			'<span class="ezd-toast__message">' + message + '</span>' +
			'<button type="button" class="ezd-toast__dismiss"><span class="dashicons dashicons-no-alt"></span></button>' +
			'</div>');
		
		// Add to container
		$('#ezd-toast-container').append($toast);
		
		// Trigger animation
		setTimeout(function() {
			$toast.addClass('ezd-toast--visible');
		}, 10);
		
		// Auto-dismiss after 5 seconds for success, 8 seconds for errors
		var dismissTimeout = setTimeout(function() {
			dismissToast($toast);
		}, type === 'success' ? 5000 : 8000);
		
		// Click to dismiss
		$toast.find('.ezd-toast__dismiss').on('click', function() {
			clearTimeout(dismissTimeout);
			dismissToast($toast);
		});
	}

	// Helper function to dismiss toast with animation
	function dismissToast($toast) {
		$toast.removeClass('ezd-toast--visible');
		setTimeout(function() {
			$toast.remove();
		}, 300);
	}

	// Resolve Failed Search Keyword
	$(document).on('click', '.ezd-btn-resolve-search', function(e) {
		e.preventDefault();
		
		var $btn = $(this);
		var $item = $btn.closest('.ezd-failed-search-item');
		var keywordId = $btn.data('keyword-id');
		var action = $btn.data('action');
		
		if (!keywordId) {
			console.error('No keyword ID found');
			return;
		}
		
		// Disable button and show loading state
		$btn.prop('disabled', true).addClass('loading');
		
		$.ajax({
			url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
			type: 'POST',
			data: {
				action: 'ezd_resolve_failed_search',
				nonce: '<?php echo wp_create_nonce( 'ezd_analytics_nonce' ); ?>',
				keyword_id: keywordId,
				resolve_action: action
			},
			success: function(response) {
				if (response.success) {
					showEzdResolveNotice(response.data.message || '<?php echo esc_js( __( 'Keyword resolved successfully.', 'eazydocs' ) ); ?>', 'success');
					
					// Add fade out animation
					$item.addClass('resolving').fadeOut(400, function() {
						$(this).remove();
						
						// Update badge count
						var $card = $('.ezd-failed-searches-card').first();
						var $badge = $card.find('.ezd-card-badge');
						var currentCount = parseInt($badge.text()) || 0;
						if (currentCount > 1) {
							$badge.text(currentCount - 1);
						} else {
							$badge.remove();
						}
						
						// Check if list is empty now
						var $list = $card.find('.ezd-failed-searches-list');
						if ($list.find('.ezd-failed-search-item').length === 0) {
							$card.find('.ezd-card-footer').remove();
							$list.replaceWith(
								'<div class="ezd-empty-state">' +
								'<span class="ezd-empty-icon"><span class="dashicons dashicons-yes-alt"></span></span>' +
								'<p class="ezd-empty-text"><?php echo esc_js( __( 'Great! No failed searches recorded.', 'eazydocs' ) ); ?></p>' +
								'<p class="ezd-empty-subtext"><?php echo esc_js( __( 'Your documentation is covering user needs well.', 'eazydocs' ) ); ?></p>' +
								'</div>'
							);
						}
					});
				} else {
					showEzdResolveNotice(response.data?.message || '<?php echo esc_js( __( 'Failed to resolve keyword.', 'eazydocs' ) ); ?>', 'error');
					$btn.prop('disabled', false).removeClass('loading');
				}
			},
			error: function() {
				showEzdResolveNotice('<?php echo esc_js( __( 'An error occurred. Please try again.', 'eazydocs' ) ); ?>', 'error');
				$btn.prop('disabled', false).removeClass('loading');
			}
		});
	});
});
</script>
