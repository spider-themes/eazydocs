<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div id="step-3" class="tab-pane" role="tabpanel" style="display:none"

	<h2> <?php esc_html_e( 'Select Page Layout', 'eazydocs' ); ?> </h2>

	<div class="page-layout-wrap">
		<input type="radio" id="both_sidebar" value="both_sidebar" name="docs_single_layout" <?php checked( $docs_single_layout, 'both_sidebar' ); ?>>
		<label for="both_sidebar" class="<?php if ( $docs_single_layout == 'both_sidebar' ) { echo esc_attr( 'active' ); } ?>">
			<img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/both_sidebar.jpg' ); ?>" alt="<?php esc_attr_e( 'Both sidebar layout', 'eazydocs' ); ?>" />
		</label>

		<input type="radio" id="left_sidebar" value="left_sidebar" name="docs_single_layout" <?php checked( $docs_single_layout, 'left_sidebar' ); ?>>
		<label for="left_sidebar" class="<?php if ( $docs_single_layout == 'left_sidebar' ) { echo esc_attr( 'active' ); } ?>">
			<img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/sidebar_left.jpg' ); ?>" alt="<?php esc_attr_e( 'Left sidebar layout', 'eazydocs' ); ?>" />
		</label>

		<input type="radio" id="right_sidebar" value="right_sidebar" name="docs_single_layout" <?php checked( $docs_single_layout, 'right_sidebar' ); ?>>
		<label for="right_sidebar" class="<?php if ( $docs_single_layout == 'right_sidebar' ) { echo esc_attr( 'active' ); } ?>">
			<img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/sidebar_right.jpg' ); ?>" alt="<?php esc_attr_e( 'Right sidebar layout', 'eazydocs' ); ?>" />
		</label>
	</div>

	<h2><?php esc_html_e( 'Page Width', 'eazydocs' ); ?></h2>
	<div class="page-width-wrap">
		<input type="radio" id="boxed" name="docsPageWidth" value="boxed" <?php checked( $docs_page_width, 'boxed' ); ?>>
		<label for="boxed" class="<?php if ( $docs_page_width == 'boxed' ) { echo esc_attr( 'active' ); } ?>">
			<?php esc_html_e( 'Boxed Width', 'eazydocs' ); ?>
		</label>
		<input type="radio" id="full-width" name="docsPageWidth" value="full-width" <?php checked( $docs_page_width, 'full-width' ); ?>>
		<label for="full-width" class="<?php if ( $docs_page_width == 'full-width' ) { echo esc_attr( 'active' ); } ?>">
			<?php esc_html_e( 'Full Width', 'eazydocs' ); ?>
		</label>
	</div>

	<h2><?php esc_html_e( 'Live Customizer', 'eazydocs' ); ?></h2>
	<label>
		<input type="checkbox" id="live-customizer" name="customizer_visibility" value="1" <?php checked( $customizer_visibility, '1' ); ?>>
		<?php esc_html_e( 'Enable Live Customizer', 'eazydocs' ); ?>
	</label>
</div>