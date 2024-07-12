<?php
$query = new \WP_Query( [
    'post_type' => 'elementor_library',
    'p'         => ezd_get_opt('single_layout_id'),
] );

if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        echo esc_html(apply_filters( 'the_content', get_the_content() ));
    }
}
wp_reset_postdata();