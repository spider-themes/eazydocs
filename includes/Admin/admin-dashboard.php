<?php
/**
 * EazyDocs Dashboard
 * Main dashboard page with improved UI/UX
 *
 * @package EazyDocs
 */

global $wpdb;

// Get total failed searches from wp_eazydocs_search_log.
$total_failed_search = $wpdb->get_var( "SELECT count(id) FROM {$wpdb->prefix}eazydocs_search_log WHERE not_found_count > 0" );
if ( empty( $total_failed_search ) ) {
	$total_failed_search = 0;
}

$date_range = strtotime( '-7 day' );
// Get views from wp eazy docs views table and post type docs and sum count.
$posts = $wpdb->get_results( "SELECT post_id, SUM(count) AS totalcount, created_at FROM {$wpdb->prefix}eazydocs_view_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY post_id" );

// Get data from wp_eazydocs_search_log base on $date_range with prefix.
$search_keyword = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}eazydocs_search_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)" );

// Get total search count from wp_eazydocs_search_log table and check if empty then set 0.
$total_search = $wpdb->get_var( "SELECT count(id) FROM {$wpdb->prefix}eazydocs_search_log" );
if ( empty( $total_search ) ) {
	$total_search = 0;
}

// Get total positive feedback count from wp meta table and check if empty then set 0.
$total_positive_feedback = $wpdb->get_var( "SELECT SUM(meta_value) FROM {$wpdb->prefix}postmeta WHERE meta_key = 'positive'" );
if ( empty( $total_positive_feedback ) ) {
	$total_positive_feedback = 0;
}

// Get total negative feedback count from wp meta table and check if empty then set 0.
$total_negative_feedback = $wpdb->get_var( "SELECT SUM(meta_value) FROM {$wpdb->prefix}postmeta WHERE meta_key = 'negative'" );
if ( empty( $total_negative_feedback ) ) {
	$total_negative_feedback = 0;
}

$labels              = array();
$dataCount           = array();
$Liked               = array();
$Disliked            = array();
$searchCount         = array();
$searchCountNotFound = array();

$m  = gmdate( 'm' );
$de = gmdate( 'd' );
$y  = gmdate( 'Y' );

for ( $i = 0; $i <= 6; $i++ ) {
	$labels[]              = gmdate( 'd M, Y', mktime( 0, 0, 0, $m, ( $de - $i ), $y ) );
	$dataCount[]           = 0;
	$Liked[]               = 0;
	$Disliked[]            = 0;
	$searchCount[]         = 0;
	$searchCountNotFound[] = 0;
}

// Get 7 data date wise.
foreach ( $posts as $key => $item ) {
	$dates = gmdate( 'd M, Y', strtotime( $item->created_at ) );

	foreach ( $labels as $datekey => $weekdays ) {
		if ( $weekdays === $dates ) {
			$Liked[ $datekey ]    = $Liked[ $datekey ] + array_sum( get_post_meta( $item->post_id, 'positive', false ) );
			$Disliked[ $datekey ] = $Disliked[ $datekey ] + array_sum( get_post_meta( $item->post_id, 'negative', false ) );
			
			$searchCount[ $datekey ]         = array_sum( array_column( $search_keyword, 'count' ) );
			$searchCountNotFound[ $datekey ] = array_sum( array_column( $search_keyword, 'not_found_count' ) );
		}
	}
}

// Re-tweaked the views.
$eazydocs_view_table = $wpdb->prefix . 'eazydocs_view_log';
$results             = $wpdb->get_results( "SELECT `count`, `created_at` FROM $eazydocs_view_table", ARRAY_A );

$rowValues = array();
foreach ( $results as $row ) {
	$rowValues[ $row['created_at'] ] = $row['count'];
}

$totalValues = array();

// Loop through the data and sum up the values for each date.
foreach ( $rowValues as $dateTime => $value ) {
	$date = explode( ' ', $dateTime )[0];
	if ( isset( $totalValues[ $date ] ) ) {
		$totalValues[ $date ] += $value;
	} else {
		$totalValues[ $date ] = $value;
	}
}

$dataCounts = array();
foreach ( $totalValues as $date => $total ) {
	$dataCounts[ $date ] = $total;
}

$currentDate = gmdate( 'Y-m-d' );

// Iterate over the past 7 days.
for ( $i = 0; $i <= 6; $i++ ) {
	$date = gmdate( 'Y-m-d', strtotime( "-$i days" ) );

	if ( isset( $dates[ $date ] ) ) {
		if ( ! isset( $dataCounts[ $i ] ) ) {
			$dataCounts[ $i ] = 0;
		}
		$dataCounts[ $i ] += $dates[ $date ];
	}
}

for ( $i = 0; $i <= 29; $i++ ) {
	$labels[]       = gmdate( 'd M, Y', mktime( 0, 0, 0, $m, ( $de - $i ), $y ) );
	$monthlyViews[] = 0;
}

foreach ( $dataCounts as $day => $sum ) {
	$dataCounts[ $day ] = $sum;
}

$currentDate = gmdate( 'Y-m-d' );
// Iterate over the dates and reorganize the keys.
foreach ( $dataCounts as $date => $value ) {
	$daysDifference = floor( ( strtotime( $currentDate ) - strtotime( $date ) ) / ( 60 * 60 * 24 ) );
	if ( $daysDifference >= 0 && $daysDifference <= 6 ) {
		$dataCount[ $daysDifference ] = $value;
	}

	if ( $daysDifference >= 0 && $daysDifference <= 29 ) {
		$monthlyViews[ $daysDifference ] = $value;
	}
}

// Get total positive & negative count.
$results = $wpdb->get_results( "SELECT meta_key, COUNT(*) as total FROM {$wpdb->postmeta} WHERE post_id IN ( SELECT ID FROM {$wpdb->posts} WHERE post_type = 'docs' ) AND meta_key IN ('positive_time', 'negative_time') GROUP BY meta_key", OBJECT_K );

// Set counts with fallback 0.
$liked    = $results['positive_time']->total ?? 0;
$disliked = $results['negative_time']->total ?? 0;

// Calculate %.
$total          = $liked + $disliked;
$total_liked    = $total ? round( ( $liked / $total ) * 100 ) : 0;
$total_disliked = $total ? round( ( $disliked / $total ) * 100 ) : 0;
?>
<div class="ezd-dashboard-container">
	<?php
	// Include header.php.
	include __DIR__ . '/template/dashboard/header.php';
	?>

	<main class="ezd-main-content">
		<?php
		// Include enhanced card-info.php with stat cards and quick actions.
		include __DIR__ . '/template/dashboard/card-info.php';
		?>

		<!-- First Row: Main Analytics + Health Score + Failed Searches -->
		<div class="ezd-grid ezd-grid-cols-lg-3 ezd-mt-6">
			<?php
			// Include Antimanual AI Integration.
			include __DIR__ . '/template/dashboard/antimanual-ai.php';
			
			// Include analytics.php.
			include __DIR__ . '/template/dashboard/analytics.php';
			?>
		</div>

		<!-- Second Row: Documentation Health + Search Overview + Failed Searches -->
		<div class="ezd-grid ezd-grid-cols-lg-3 ezd-mt-6">
			<?php
			// Include documentation health score.
			include __DIR__ . '/template/dashboard/doc-health.php';

			// Include search-data.php.
			include __DIR__ . '/template/dashboard/search-data.php';

			// Include failed searches quick view.
			include __DIR__ . '/template/dashboard/failed-searches.php';
			?>
		</div>

		<!-- Third Row: Recent Activity + Top Ranked + Top Viewed + System Status -->
		<div class="ezd-grid ezd-grid-cols-lg-4 ezd-mt-6">
			<?php
			// Include recent-activity.php.
			include __DIR__ . '/template/dashboard/recent-activity.php';
			
			// Include top-ranked-docs.php.
			include __DIR__ . '/template/dashboard/top-ranked-docs.php';
			
			// Include top-viewed-docs.php.
			include __DIR__ . '/template/dashboard/top-viewed-docs.php';

			// Include system-status.php.
			include __DIR__ . '/template/dashboard/system-status.php';
			?>
		</div>
	</main>
	
	<!-- Footer -->
	<footer class="ezd-dashboard-footer">
		<div class="ezd-footer-content">
			<p class="ezd-footer-text">
				<?php
				printf(
					/* translators: %s: EazyDocs link */
					esc_html__( 'Thank you for using %s!', 'eazydocs' ),
					'<a href="https://eazydocs.spider-themes.net/" target="_blank">EazyDocs</a>'
				);
				?>
			</p>
			<p class="ezd-footer-links">
				<a href="https://wordpress.org/support/plugin/eazydocs/reviews/#new-post" target="_blank">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e( 'Rate Us', 'eazydocs' ); ?>
				</a>
				<span class="ezd-footer-divider">|</span>
				<a href="https://wordpress.org/support/plugin/eazydocs//" target="_blank">
					<span class="dashicons dashicons-sos"></span>
					<?php esc_html_e( 'Get Support', 'eazydocs' ); ?>
				</a>
			</p>
		</div>
	</footer>
</div>