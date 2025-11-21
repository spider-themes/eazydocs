<div class="ezd-card">
    <h2 class="ezd-card-title"><?php esc_html_e( 'Search Overview', 'eazydocs' ); ?></h2>
    <div class="ezd-search-chart-container">
        <div id="ezd-search-chart"></div>
    </div>
</div>

<script>
    var options = {
        series: [<?php echo esc_js( $total_search ); ?>, <?php echo esc_js( $total_search - $total_failed_search ); ?>, <?php echo esc_js( $total_failed_search ); ?>],
        chart: {
            width: 450,
            type: 'donut',
        },
        labels: ['Total Search', 'Successful', 'Failed'],
        colors: ['#ff66b3', '#00cc66', '#ff0000'],
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            markers: {
                width: 10,
                height: 10,
                offsetX: -2
            },
            itemMargin: {
                horizontal: 5,
                vertical: 0
            }
        },
        fill: {
            type: 'gradient',
        }
    };

    var search_chart = new ApexCharts(document.querySelector("#ezd-search-chart"), options);
    search_chart.render();
</script>