<?php
$opt              = get_option( 'eazydocs_settings' );
$update_text      = $opt['breadcrumb-update-text'] ?? esc_html__( 'Updated on', 'eazydocs' );
$doc_container    = 'container custom_container';
$doc_container    = $opt['docs_page_width'] ?? '';
$doc_container    = $doc_container == 'full-width' ? 'container-fluid px-lg-5' : 'container custom_container';
?>
<section class="page_breadcrumb ezd-breadcrumb">
    <div class="<?php echo esc_attr($doc_container); ?>">
        <div class="row">
            <div class="col-lg-9 col-md-8">
                <nav aria-label="breadcrumb">
					<?php eazydocs_breadcrumbs(); ?>
                </nav>
            </div>
            <div class="col-lg-3 col-md-4">
                <time itemprop="dateModified" datetime="<?php the_modified_time( get_option( 'date_format' ) ); ?>" class="date">
                    <i class="<?php echo is_rtl() ? 'icon_quotations' : 'icon_clock_alt'; ?>"></i>
					<?php echo esc_html( $update_text ); ?>
					<?php the_modified_time( get_option( 'date_format' ) ); ?>
                </time>
            </div>
        </div>
    </div>
</section>