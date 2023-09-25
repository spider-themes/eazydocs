 <?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "eazydocs" and copy it there.
 *
 * @package eazydocs
 */
// If block theme is active, load the header
ezd_header_with_block_theme();

// If block theme is not active, load the header
get_header();

$theme_data = wp_get_theme();
$theme_name = $theme_data->get( 'Name' ); 
?>
 <p class="d-none"><?php echo $theme_name; ?></p>
 <?php
$options         = get_option( 'eazydocs_settings' );
$single_layout   = $options['single_doc_layout'] ?? 'default';
$cz_options      = '';
$doc_container   = 'ezd-container ezd-custom-container';
$content_wrapper = '';
$credit_enable   = '1';

$layout          = $options['docs_single_layout'] ?? 'both_sidebar';
$doc_width       = $options['docs_page_width'] ?? '';
$doc_container   = $doc_width == 'full-width' ? 'container-fluid px-lg-5' : 'ezd-container ezd-custom-container';
$content_wrapper = $doc_width == 'full-width' ? 'doc_documentation_full_area' : '';

$credit_text     = $options['eazydocs-credit-text'] ?? sprintf( __( "Powered By %s EazyDocs %s", 'eazydocs' ), '<a href="https://wordpress.org/plugins/eazydocs/" target="_blank">', '</a>' );

if ( ezd_is_premium() ) {
	$credit_enable = $options['eazydocs-enable-credit'] ?? '1';
}

switch ( $layout ) {
	case 'left_sidebar':
		$md_content_col = '.ezd-lg-col-9';
		break;

	case 'right_sidebar':
		$md_content_col = '.ezd-lg-col-10';
		break;

	default:
		$md_content_col = '.ezd-xl-col-7 .ezd-lg-col-6';
}

$current_theme = get_template();

if ( $single_layout == 'default' ) {
    if ( $current_theme != 'docly' ) {
        eazydocs_get_template_part( 'search-banner' );
    }
} else {
    if ( $current_theme != 'docy' ) {
        eazydocs_get_template_part( 'custom-banner' );
    }
}
?>
 <section class="doc_documentation_area <?php echo esc_attr( $content_wrapper ); ?>" id="sticky_doc">

     <div class="ezd-link-copied-wrap"></div>
     <div class="overlay_bg"></div>
     <?php
    if ( ezd_get_opt('docs-breadcrumb', '1') == '1' && $current_theme != 'docy' ) {
        eazydocs_get_template_part( 'breadcrumbs' );
    }
    ?>
     <div class="position-relative <?php echo esc_attr( $doc_container ); ?>">
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

 <?php 
if ( $credit_enable == '1' ) : ?>
 <div class="section eazydocs-footer">
     <div class="ezd-grid ezd-grid-cols-12">
         <div class="ezd-grid-column-full text-center">
             <div class="eazydocx-credit-text">
                 <?php echo wp_kses_post( wpautop( $credit_text ) ); ?>
             </div>
         </div>
     </div>
 </div>
 <?php 
endif;


ezd_footer_with_block_theme();
get_footer();