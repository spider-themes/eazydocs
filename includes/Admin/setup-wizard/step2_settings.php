<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

ezd_render_setup_step_wrapper(2, esc_html__( 'Docs Archive Page', 'eazydocs' ), esc_html__( 'This page will show on the Doc single page breadcrumb and will be used to show the Docs.', 'eazydocs' ));
?>

	<div class="archive-page-selection-wrap">
		<select name="docs_archive_page" id="docs_archive_page">
			<option value=""><?php esc_html_e( 'Select a page', 'eazydocs' ); ?></option>
			<?php
			$pages = get_pages();
			foreach ( $pages as $page ) {
				$selected = ( $page->ID == $docs_archive_page ) ? 'selected' : '';
				echo '<option value="' . esc_attr( $page->ID ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $page->post_title ) . '</option>';
			}
			?>
		</select>
		<span> <?php esc_html_e( 'You can create this page with using [eazydocs] shortcode or available EazyDocs blocks or Elementor widgets.', 'eazydocs' ); ?> </span>
	</div>

	<h2> <?php esc_html_e( 'Brand Color', 'eazydocs' ); ?> </h2>
	<p> <?php esc_html_e( 'Select the Brand Color for your knowledge base.', 'eazydocs' ); ?> </p>

	<div class="brand-color-picker-wrap">
		<input type="text" class="brand-color-picker" placeholder="Color Picker" value="<?php echo esc_attr( $brand_color ); ?>">
	</div>

	<h2> <?php esc_html_e( 'Doc Root URL Slug', 'eazydocs' ); ?> </h2>
	<p> <?php esc_html_e( 'Select the Docs URL Structure. This will be used to generate the Docs URL.', 'eazydocs' ); ?> </p>

	<div class="root-slug-wrap">
		<input type="radio" id="post-name" name="slug" value="post-name" <?php checked( $slugType, 'post-name' ); ?>>
		<label for="post-name" class="<?php if ( $slugType == 'post-name' ) { echo esc_attr( 'active' ); } ?>">
			<?php esc_html_e( 'Default Slug', 'eazydocs' ); ?>
		</label>

		<input type="radio" id="custom-slug" name="slug" value="custom-slug" <?php checked( $slugType, 'custom-slug' ); ?>>
		<label for="custom-slug" class="<?php if ( $slugType == 'custom-slug' ) { echo esc_attr( 'active' ); } ?>">
			<?php esc_html_e( 'Custom Slug', 'eazydocs' ); ?>
		</label>

		<input type="text" class="custom-slug-field <?php if ( $slugType == 'custom-slug' ) { echo esc_attr( 'active' ); } ?>" placeholder="Basic Setting" value="<?php echo esc_attr( $custom_slug ); ?>">
	</div>
</div>