<?php
$opt = '';
$update_text      = '';
$doc_container    = 'container custom_container';
if ( class_exists( 'EazyDocsPro' ) ) {
	$opt = get_option( 'eazydocs_settings' );
	$update_text      = $opt['breadcrumb-update-text'] ?? '';
	$cz_options       = get_option( 'eazydocs_customizer' );
	$doc_container    = $cz_options['doc_elements']['docs-page-width'] ?? '';
	$doc_container    = $doc_container == 'full-width' ? 'container-fluid pl-60 pr-60' : 'container custom_container';
}
?>
<section class="page_breadcrumb">
    <div class="<?php echo esc_attr($doc_container); ?>">
        <div class="row">
            <div class="col-lg-9 col-md-8">
                <nav aria-label="breadcrumb">
					<?php eazydocs_breadcrumbs(); ?>
                </nav>
            </div>
            <div class="col-lg-3 col-md-4">
                <time itemprop="dateModified" datetime="<?php the_modified_time( get_option( 'date_format' ) ); ?>" class="date">
                    <i class="icon_clock_alt"></i>
					<?php echo esc_html( $update_text ); ?>
					<?php the_modified_time( get_option( 'date_format' ) ); ?>
                </time>
            </div>
        </div>
    </div>
</section>