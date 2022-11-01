<?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "eazydocs" and copy it there.
 *
 * @package eazydocs
 */
get_header();

$theme_data = wp_get_theme();
$theme_name = $theme_data->get( 'Name' ); 
?>
<p class="d-none"><?php echo $theme_name; ?></p>
<?php
$options = get_option( 'eazydocs_settings' );

$cz_options      = '';
$doc_container   = 'container custom_container';
$content_wrapper = '';
$credit_enable   = '1';
$credit_text     = sprintf( __( "%s", 'eazydocs' ), 'Powered By <a href="https://wordpress.org/plugins/eazydocs/" target="_blank">EazyDocs</a>' );

$layout          = $options['docs_single_layout'] ?? 'both_sidebar';
$doc_width       = $options['docs_page_width'] ?? '';
$doc_container   = $doc_width == 'full-width' ? 'container-fluid px-lg-5' : 'container custom_container';
$content_wrapper = $doc_width == 'full-width' ? 'doc_documentation_full_area' : '';

$credit_text = $options['eazydocs-credit-text'] ?? sprintf( __( "%s", 'eazydocs' ), 'Powered By <a href="https://wordpress.org/plugins/eazydocs/" target="_blank">EazyDocs</a>' );
$breadcrumb  = $options['docs-breadcrumb'] ?? '1';

if ( class_exists( 'EazyDocsPro' ) ) {
	$credit_enable = $options['eazydocs-enable-credit'] ?? '1';
}

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

$current_theme = get_template();

if ( $current_theme != 'docly' && $current_theme != 'docy' ) {
	eazydocs_get_template_part( 'search-banner' );
}
    ?>
    <section class="doc_documentation_area <?php echo esc_attr( $content_wrapper ); ?>" id="sticky_doc">
	<div class="ezd-link-copied-wrap"></div>
        <div class="overlay_bg"></div>
		<?php
		if ( $breadcrumb == '1' ) {
			if ( $current_theme != 'docy' && $current_theme != 'docly' ) {
				eazydocs_get_template_part( 'breadcrumbs' );
			}
		}
		?>
        <div class="<?php echo esc_attr( $doc_container ); ?>">
            <div class="row">
				<?php
				while ( have_posts() ) : the_post();
					if ( $layout == 'left_sidebar' || $layout == 'both_sidebar' ) {
						eazydocs_get_template_part( 'docs-sidebar' );
					}
					?>
                    <div class="<?php echo esc_attr( $md_content_col ); ?> doc-middle-content">
						<?php eazydocs_get_template_part( 'single-doc-content' ); ?>
                    </div>
					<?php
					if ( $layout == 'right_sidebar' || $layout == 'both_sidebar' ) {
						eazydocs_get_template_part( 'docs-right-sidebar' );
					}
				endwhile;
				?>
            </div>

        </div>
    </section>

<?php if ( $credit_enable == '1' ) : ?>
    <div class="section eazydocs-footer">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="eazydocx-credit-text">
					<?php echo wp_kses_post( wpautop( $credit_text ) ); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
get_footer();