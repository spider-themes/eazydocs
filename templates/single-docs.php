<?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "eazydocs" and copy it there.
 *
 * @package eazydocs
 */
get_header();

$md_content_col     = 'col-lg-7';
$cz_options         = '';
$layout             = 'both_sidebar';
$doc_container      = 'container custom_container';
$content_wrapper    = '';
$breadcrumb         = '1';
$credit_enable      = '1';
$credit_text        = sprintf(__("%s", 'eazydocs'), 'Powered By <a href="https://wordpress.org/plugins/eazydocs/" target="_blank">EazyDocs</a>');
if ( class_exists('EazyDocsPro') ) {
	$cz_options = get_option( 'eazydocs_customizer' ); // prefix of framework
	$layout     = $cz_options['doc_elements']['docs_single_layout'] ?? 'both_sidebar';
    $doc_width          = $cz_options['doc_elements']['docs_page_width'] ?? '';
    $doc_container      = $doc_width == 'full-width' ? 'container-fluid pl-60 pr-60' : 'container custom_container';
	$content_wrapper    = $doc_width == 'full-width' ? 'doc_documentation_full_area' : '';

	$settings_options   = get_option( 'eazydocs_settings' ); // prefix of framework
	$credit_enable      = $settings_options['eazydocs-enable-credit'] ?? '1';
	$credit_text        = $settings_options['eazydocs-credit-text'] ?? sprintf(__("%s", 'eazydocs'), 'Powered By <a href="https://wordpress.org/plugins/eazydocs/" target="_blank">EazyDocs</a>');
	$breadcrumb         = $settings_options['docs-breadcrumb'] ?? '';

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

<?php eazydocs_get_template_part('search-banner') ?>
<section class="doc_documentation_area <?php echo esc_attr($content_wrapper); ?>" id="sticky_doc">
    <div class="overlay_bg"></div>
    <?php
    if ( $breadcrumb == '1' ) {
        eazydocs_get_template_part( 'breadcrumbs' );
    }
    ?>
    <div class="<?php echo esc_attr($doc_container); ?>">
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

<div class="section eazydocs-footer">
    <div class="row">
        <div class="col-lg-12 text-center">
            <?php if( $credit_enable == '1' ) : ?>
            <div class="eazydocx-credit-text">
                <?php echo wp_kses_post(wpautop($credit_text)); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
get_footer();