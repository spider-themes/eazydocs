<?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "eazydocs" and copy it there.
 *
 * @package eazydocs
 */
get_header();

$cz_options         = '';
$doc_container      = 'container custom_container';
$content_wrapper    = '';
$breadcrumb         = '1';
$credit_enable      = '1';
$credit_text        = sprintf(__("%s", 'eazydocs'), 'Powered By <a href="https://wordpress.org/plugins/eazydocs/" target="_blank">EazyDocs</a>');

$cz_options = get_option( 'eazydocs_customizer' ); // prefix of framework
$layout             = $cz_options['doc_elements']['docs_single_layout'] ?? 'both_sidebar';
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

    default:
        $md_content_col = 'col-xl-7 col-lg-6';
}
?>

<?php
$theme = wp_get_theme();
$docy = trim( get_option('docy_purchase_code_status') );
$docly = trim( get_option('docly_purchase_code_status') );

if ( $docy != 'valid' || $docly != 'valid' ) {
    eazydocs_get_template_part('search-banner');
}
?>
<section class="doc_documentation_area <?php echo esc_attr($content_wrapper); ?>" id="sticky_doc">
    <div class="overlay_bg"></div>
    <?php
    if( $breadcrumb == '1' ) {
	    if ( $docy != 'valid' || $docly != 'valid' ) {
		    eazydocs_get_template_part( 'breadcrumbs' );
	    }
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

<?php if( $credit_enable == '1' ) : ?>
    <div class="section eazydocs-footer">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="eazydocx-credit-text">
                    <?php echo wp_kses_post(wpautop($credit_text)); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
get_footer();