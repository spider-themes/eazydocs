<div class="ezd-card ezd-grid-col-lg-2">
    <div class="ezd-card-header">
        <h2 class="ezd-card-title"><?php esc_html_e( 'Performance Overview', 'eazydocs' ); ?></h2>
        <div class="ezd-stat-filter-container">
            <ul>
                <li class="is-active" data-filter="weekly" onclick="OverviewWeekly()">
                    <?php esc_html_e( 'This Week', 'eazydocs' ); ?>
                </li>
                <li data-filter=".lastmonth" onclick="OverviewLastmonth()">
                    <?php esc_html_e( 'Last Month', 'eazydocs' ); ?>
                </li>
            </ul>
        </div>
    </div>

    <div class="ezd-chart-container">
        <div id="OvervIewChart"></div>
    </div>
</div>

<script>
    // Fetch apexchartjs area chart in #OvervIewChart
    var options = {
        chart: {
            height: 350,
            type: 'area',
            fontFamily: 'inherit',
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        colors: ['#3b82f6', '#10b981', '#f59e0b'],
        dataLabels: {
            enabled: false
        },
        series: [
            {
                name: "Views",
                data: <?php echo json_encode( $dataCount ); ?>
            },
            {
                name: "Feedback",
                data: <?php echo json_encode( $Liked + $Disliked ); ?>
            },
            {
                name: "Searches",
                data: <?php echo json_encode( $searchCount ); ?>
            }
        ],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: <?php echo json_encode( $labels ); ?>,
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#64748b'
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '14px',
            fontWeight: 500,
            markers: {
                radius: 12
            }
        },
        tooltip: {
            theme: 'light',
            x: {
                show: true
            }
        },
    };


    var Overviewchart = new ApexCharts(document.querySelector("#OvervIewChart"), options);
    Overviewchart.render();

    // Count Overviewchart in positive-feedback
    var positive_feedback = document.querySelector(".positive-feedback");
        positive_feedback = <?php echo esc_js( $total_positive_feedback ); ?>

    // Count and sum negative-feedback of Disliked
    var negative_feedback = document.querySelector(".negative-feedback");    
        negative_feedback = <?php echo esc_js( $total_negative_feedback ); ?>

    // Count and sum total-views of dataCount
    var failed_searches = <?php echo esc_js( $total_failed_search ); ?>

    // Count and sum total-search of total_search
    var total_search = document.querySelector(".total-search");
        total_search = <?php echo esc_js( $total_search ); ?>

    // .ezd-docs-checkbox each input[type=checkbox] check then alert
    var ezd_docs_checkbox = document.querySelectorAll(".chartoverview input[type=checkbox]");
    ezd_docs_checkbox.forEach(function (ezd_docs_checkbox) {
        ezd_docs_checkbox.addEventListener("change", function () {
            if (this.checked) {
                Overviewchart.showSeries(this.value);
            } else {
                Overviewchart.hideSeries(this.value);
            }
        });
    });

    function OverviewWeekly() {
        // Update the apexchart in liked and disliked OverviewTabs
        Overviewchart.updateSeries([
            {
                name: 'Views',
                data: <?php echo json_encode( $dataCount ); ?>
            },
            {
                name: 'Feedback',
                data: <?php echo json_encode( $Liked ); ?>
            },
            {
                name: 'Searches',
                data: <?php echo json_encode( array_reverse( $searchCount ) ); ?>
            }
        ])
    }

    function OverviewLastmonth() {
		<?php
		global $wpdb;
		$date_range = strtotime( '-29 day' );

		//  get views from wp eazy docs views table and post type docs and sum count
		$posts = $wpdb->get_results( "SELECT post_id, SUM(count) AS totalcount, created_at FROM {$wpdb->prefix}eazydocs_view_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY post_id" );

		// get data from wp_eazydocs_search_log base on $date_range with prefix
		$search_keyword = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}eazydocs_search_log WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)" );
		$labels         = [];
		$Liked          = [];
		$Disliked       = [];
		$searchCount    = [];
		$searchCountNotFound = [];

		$m = gmdate( "m" );
		$de = gmdate( "d" );
		$y = gmdate( "Y" );

		for ( $i = 0; $i <= 29; $i ++ ) {
			$labels[]              = gmdate( 'd M, Y', mktime( 0, 0, 0, $m, ( $de - $i ), $y ) );
			$Liked[]               = 0;
			$Disliked[]            = 0;
			$searchCount[]         = 0;
			$searchCountNotFound[] = 0;
		}

		foreach ( $posts as $key => $item ) {
			$dates = gmdate( 'd M, Y', strtotime( $item->created_at ) );
			foreach ( $labels as $datekey => $weekdays ) {
				if ( $weekdays == $dates ) {
					$Liked[ $datekey ]    = $Liked[ $datekey ] + array_sum( get_post_meta( $item->post_id, 'positive', false ) );
					$Disliked[ $datekey ] = $Disliked[ $datekey ] + array_sum( get_post_meta( $item->post_id, 'negative', false ) );
					
					$searchCount[ $datekey ]         = array_sum( array_column( $search_keyword, 'count' ) );
					$searchCountNotFound[ $datekey ] = array_sum( array_column( $search_keyword, 'not_found_count' ) );
				}
			}
		}
		?>

        Overviewchart.updateOptions({
            xaxis: {
                categories: <?php echo json_encode( $labels ); ?>,
            },
            series: [{
                name: 'Views',
                data: <?php echo json_encode( $monthlyViews ); ?>
            },
                {
                    name: 'Feedback',
                    data: <?php echo json_encode( $Liked ); ?>
                },
                {
                    name: 'Searches',
                    data: <?php echo json_encode( $searchCount ); ?>
                }],
        });
    }    
</script>