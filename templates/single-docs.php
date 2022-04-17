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
$search_banner      = '0';
if ( class_exists( 'EazyDocsPro' ) ) {
	$cz_options = get_option( 'eazydocs_customizer' ); // prefix of framework
	$layout     = $cz_options['docs-single-layout'] ?? '';
    $doc_width          = $cz_options['docs-page-width'] ?? '';
    $search_banner      = $cz_options['docs-search-banner'] ?? '';
    $doc_container      = $doc_width == 'full-width' ? 'container-fluid pl-60 pr-60' : 'container custom_container';
	$content_wrapper    = $doc_width == 'full-width' ? 'doc_documentation_full_area' : '';

	$settings_options = get_option( 'eazydocs_settings' ); // prefix of framework
	$breadcrumb       = $settings_options['docs-breadcrumb'] ?? '';

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

if( $search_banner == '1') : ?>
    <section class="doc_banner_area search-banner-light sbnr-global">
        <div class="container">
            <div class="doc_banner_content">
                <form action="<?php echo esc_url(home_url('/')) ?>" role="search" method="get" class="header_search_form">
                    <div class="header_search_form_info">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <label for="searchInput">
                                    <i class="icon_search"></i>
                                </label>
                                <input type='search' id="searchInput" name="s" onkeyup="fetchResults()" placeholder='Search ("/" to focus)' autocomplete="off" value="<?php echo get_search_query() ?>"/>
								<?php include('search-spinner.php'); ?>

								<?php if ( defined('ICL_LANGUAGE_CODE') ) : ?>
                                    <input type="hidden" name="lang" value="<?php echo(ICL_LANGUAGE_CODE); ?>"/>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>

					<?php
					include('ajax-search-results.php');
					include('keywords.php');
					?>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>

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
    <div class="section eazydocx-credit-text">
        <div class="row">
            <div class="col-lg-12 text-center">
                <p>Powered By <a href="">EazyDocs</a></p>
            </div>
        </div>
    </div>
<?php
get_footer();