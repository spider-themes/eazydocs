<?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "eazydocs" and copy it there.
 *
 * @package eazydocs
 */
get_header();

$md_content_col = 'col-lg-7';
$cz_options     = '';
$layout         = 'both_sidebar';
if ( class_exists( 'EazyDocsPro' ) ) {
	$cz_options = get_option( 'eazydocs_customizer' ); // prefix of framework
	$layout     = $cz_options['docs-single-layout']; // id of field

	switch ( $layout ) {
		case 'left_sidebar':
			$md_content_col = 'col-lg-9';
			break;

		case 'right_sidebar':
			$md_content_col = 'col-lg-10';
			break;

		case 'both_sidebar':
			$md_content_col = 'col-lg-7';
			break;
	}
}
?>
<section class="doc_documentation_area" id="sticky_doc">
    <div class="overlay_bg"></div>
    <?php eazydocs_get_template_part( 'breadcrumbs' ); ?>
    <div class="container custom_container">
        <div class="row">
            <?php
            while ( have_posts() ) : the_post();
                if ( ! empty( $layout == 'left_sidebar' ) || ! empty( $layout == 'both_sidebar' ) ) {
                    eazydocs_get_template_part( 'docs-sidebar' );
                }
                ?>
                <div class="<?php echo esc_attr( $md_content_col ); ?> doc-middle-content">
                    <?php eazydocs_get_template_part( 'single-doc-content' ); ?>
                </div>
                <?php
                if ( ! empty( $layout == 'right_sidebar' ) || ! empty( $layout == 'both_sidebar' ) ) {
                    eazydocs_get_template_part( 'docs-right-sidebar' );
                }
            endwhile;
            ?>
        </div>
    </div>
</section>
<?php
get_footer();