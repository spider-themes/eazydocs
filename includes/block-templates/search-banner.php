<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$has_cs_color = false;
$has_cs_image = false;
if ( ezd_is_premium() ) {
	$custom_banner = ezd_get_opt( 'doc_banner_bg' );
	$has_cs_color  = ! empty( $custom_banner['background-color'] );
	$has_cs_image  = ! empty( $custom_banner['background-image']['url'] );
}

// Apply the dark theme (white keyword text) only when the banner is actually dark.
$is_dark_banner = ( $has_cs_color && ! $has_cs_image ) ? ezd_is_dark_color( $custom_banner['background-color'] ) : true;

$banner_classes = [ 'ezd_search_banner' ];
if ( $is_dark_banner ) {
	$banner_classes[] = 'has_bg_dark';
}
$banner_classes[] = ( $has_cs_color || $has_cs_image ) ? 'has_cs_bg' : 'no_cs_bg';
if ( ezd_get_opt( 'is_keywords' ) != '1' ) {
	$banner_classes[] = 'no_keywords';
}

$banner_title    = ezd_get_opt( 'search_banner_title', '' );
$banner_subtitle = ezd_get_opt( 'search_banner_subtitle', '' );

ob_start();
?>
<div class="focus_overlay"></div>
<section class="<?php echo esc_attr( implode( ' ', $banner_classes ) ); ?>">
    <div class="container">
        <div class="row doc_banner_content">
            <div class="col-md-12">
                <?php if ( $banner_title || $banner_subtitle ) : ?>
                <div class="ezd_search_banner_heading">
                    <?php if ( $banner_title ) : ?>
                        <h2 class="ezd_search_banner_title"><?php echo esc_html( $banner_title ); ?></h2>
                    <?php endif; ?>
                    <?php if ( $banner_subtitle ) : ?>
                        <p class="ezd_search_banner_subtitle"><?php echo esc_html( $banner_subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <form action="<?php echo esc_url( home_url('/') ); ?>" role="search" method="get" class="ezd_search_form">
                    <div class="header_search_form_info">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input type='search' class="search_field_wrap" id="ezd_searchInput" name="s" placeholder='<?php esc_attr_e( 'Search here', 'eazydocs' ); ?>' autocomplete="off" aria-label="<?php esc_attr_e( 'Search documentation', 'eazydocs' ); ?>" value="<?php echo get_search_query(); ?>"/>
                                <label for="ezd_searchInput">
                                    <i class="left-icon icon_search"></i>
                                </label>
                                <div class="spinner-border spinner" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
                                    <input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>"/>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div id="ezd-search-results" class="eazydocs-search-tree" role="region" aria-label="<?php esc_attr_e( 'Search results', 'eazydocs' ); ?>" data-noresult="<?php esc_attr_e( 'No Results Found', 'eazydocs' ); ?>" data-noresult-title="<?php esc_attr_e( 'No Results Found', 'eazydocs' ); ?>" data-noresult-sub="<?php esc_attr_e( 'Check the spelling or try a different word or phrase.', 'eazydocs' ); ?>"></div>
	                <?php
	                if ( ( ezd_is_premium() || eaz_fs()->is_paying_or_trial() ) && ezd_get_opt('is_keywords') == '1' ) {
		                eazydocs_get_template_part('keywords');
	                }
	                ?>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$html = ob_get_clean();
return $html;
