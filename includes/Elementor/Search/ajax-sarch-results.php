<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div id="ezd-search-results"
     data-noresult="<?php echo esc_attr( $settings['no_result_title'] ?? '' ?: esc_html__( 'No Results Found', 'eazydocs' ) ); ?>"
     data-noresult-img="<?php echo esc_url( $settings['no_result_image']['url'] ?? '' ); ?>"
     data-noresult-title="<?php echo esc_attr( $settings['no_result_title'] ?? '' ?: esc_html__( 'No Results Found', 'eazydocs' ) ); ?>"
     data-noresult-sub="<?php echo esc_attr( $settings['no_result_subtitle'] ?? '' ?: esc_html__( 'Check the spellings or use a different word or phrase', 'eazydocs' ) ); ?>"> </div>
