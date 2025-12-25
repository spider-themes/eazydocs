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
                width: '100%',
                height: 300,
                type: 'donut',
                fontFamily: 'inherit',
            },
            labels: ['No Search Data'],
            colors: ['#f1f5f9'], // gray color
            dataLabels: { enabled: false },
            legend: { show: false },
            stroke: { show: false }
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
                width: '100%',
                height: 300,
                type: 'donut',
                fontFamily: 'inherit',
            },
            labels: ['Total Search', 'Successful', 'Failed'],
            colors: ['#6366f1', '#10b981', '#ef4444'],
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '14px',
                markers: { width: 10, height: 10, radius: 12 },
                itemMargin: { horizontal: 10, vertical: 5 }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['#fff']
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '16px',
                                fontWeight: 600,
                                color: '#64748b'
                            }
                        }
                    }
                }
            }
        };
    }


    var search_chart = new ApexCharts(document.querySelector("#ezd-search-chart"), options);
    search_chart.render();
</script>