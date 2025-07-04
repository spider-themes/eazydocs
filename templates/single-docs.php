<?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "eazydocs" and copy it there.
 *
 * @package eazydocs
 */
// If block theme is active, load the header


// If block theme is not active, load the header
get_header();
ezd_header_with_block_theme();
$theme_data      = wp_get_theme();
$single_layout   = ezd_get_opt( 'search_banner_layout', 'default' );
$cz_options      = '';
$doc_container   = 'ezd-container ezd-custom-container';
$content_wrapper = '';
$credit_enable   = '1';

$layout          = ezd_get_opt( 'docs_single_layout', 'both_sidebar' );
$doc_width       = ezd_get_opt( 'docs_page_width' );
$doc_container   = $doc_width == 'full-width' ? 'ezd-container-fluid px-lg-5' : 'ezd-container ezd-custom-container';
$content_wrapper = $doc_width == 'full-width' ? 'doc_full_width' : '';

$credit_text 	= ezd_get_opt( 'eazydocs-credit-text', sprintf( __( "Powered By %s EazyDocs %s", 'eazydocs' ), '<a href="https://wordpress.org/plugins/eazydocs/" target="_blank">', '</a>' ) );

if ( ezd_is_premium() ) {
	$credit_enable = ezd_get_opt( 'eazydocs-enable-credit', '1' );
}

switch ( $layout ) {
	case 'left_sidebar':
		$md_content_col = 'ezd-lg-col-9 ezd-grid-column-full';
		break;
	case 'right_sidebar':
		$md_content_col = 'ezd-lg-col-10 ezd-grid-column-full';
		break;
	default:
		$md_content_col = 'ezd-xl-col-7 ezd-lg-col-6 ezd-grid-column-full';
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
		if ( ezd_get_opt( 'docs-breadcrumb', '1' ) == '1' && $current_theme != 'docy' ) {
			eazydocs_get_template_part( 'breadcrumbs' );
		}
		do_action( 'ezd_before_single_content' );
		?>
        <div class="position-relative <?php echo esc_attr( $doc_container ); ?>">
            <div class="ezd-grid ezd-grid-cols-12">
				<?php
				while ( have_posts() ) : the_post();
					if ( $layout == 'left_sidebar' || $layout == 'both_sidebar' ) {
						eazydocs_get_template_part( 'docs-sidebar' );
					}

					if ( ezd_internal_doc_security( get_the_ID() ) == true ) :
						?>
                        <div class="<?php echo esc_attr( $md_content_col ); ?> doc-middle-content">
							<?php eazydocs_get_template_part( 'single-doc-content' ); ?>
                        </div>
						<?php
						if ( $layout == 'right_sidebar' || $layout == 'both_sidebar' ) {
							eazydocs_get_template_part( 'docs-right-sidebar' );
						}
					endif;
				endwhile;
				?>
            </div>
        </div>
		<?php do_action( 'ezd_after_single_content' ); ?>
    </section>

<?php
if ( $credit_enable == '1' ) : ?>
    <div class="section eazydocs-footer">
        <div class="<?php echo ezd_container(); ?>">
            <div class="ezd-grid ezd-grid-cols-12">
                <div class="ezd-xl-col-3 ezd-lg-col-3 ezd-grid-column-full"></div>
                <div class="ezd-grid ezd-lg-col-9 ezd-grid-column-full">
                    <div class="ezd-grid-column-full">
                        <div class="eazydocx-credit-text">
                            <?php echo wp_kses_post( wpautop( $credit_text ) ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
endif;


ezd_footer_with_block_theme();
get_footer();

// Subscription modal form
do_action( 'eazydocs_suscription_modal_form', ezd_get_doc_parent_id( get_the_ID() ) );