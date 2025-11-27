<?php
global $wpdb;
$search_log_table    = $wpdb->get_var( "SHOW TABLES LIKE '%_eazydocs_search_log'" );
$total_failed_search = $wpdb->get_var( "SELECT count(id) FROM {$search_log_table} WHERE not_found_count > 0" );

if ( empty( $total_failed_search ) ) {
    $total_failed_search = 0;
}

// get total search count from wp_eazydocs_search_log table and check if empty then set 0
$total_search = $wpdb->get_var( "SELECT count(id) FROM {$search_log_table}" );
if ( empty( $total_search ) ) {
	$total_search = 0;
}

// Detect empty / zero search data
$is_empty_data = ( $total_search == 0 && $total_failed_search == 0 );
?>

<div class="ezd-card">
    <h2 class="ezd-card-title"><?php esc_html_e( 'Search Overview', 'eazydocs' ); ?></h2>
    <div class="ezd-search-chart-container">
        <div id="ezd-search-chart"></div>
    </div>
</div>

<script>
    var is_empty = <?php echo $is_empty_data ? 'true' : 'false'; ?>;

    var options;
    
    if (is_empty) {
        // Show EMPTY chart (full gray donut)
        options = {
            series: [1], // single slice
            chart: {
                width: 450,
                type: 'donut',
            },
            labels: ['No Search Data'],
            colors: ['#d0d0d0'], // gray color
            dataLabels: { enabled: false },
            legend: { show: false }
        };
    } else {
        // Show NORMAL chart
        options = {
            series: [
                <?php echo esc_js( $total_search ); ?>,
                <?php echo esc_js( $total_search - $total_failed_search ); ?>,
                <?php echo esc_js( $total_failed_search ); ?>
            ],
            chart: {
                width: 450,
                type: 'donut',
            },
            labels: ['Total Search', 'Successful', 'Failed'],
            colors: ['#ff66b3', '#00cc66', '#ff0000'],
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                markers: { width: 10, height: 10, offsetX: -2 },
                itemMargin: { horizontal: 5, vertical: 0 }
            },
            fill: { type: 'gradient' }
        };
    }

    var search_chart = new ApexCharts(document.querySelector("#ezd-search-chart"), options);
    search_chart.render();
</script>