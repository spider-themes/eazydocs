<?php
$update_text      = ezd_get_opt( 'breadcrumb-update-text', esc_html__( 'Updated on', 'eazydocs' ) );
$doc_container    = 'ezd-container ezd-custom-container';
$doc_container    = ezd_get_opt( 'docs_page_width' );
$doc_container    = $doc_container == 'full-width' ? 'ezd-container-fluid px-lg-5' : 'ezd-container ezd-custom-container';
?>
<section class="page_breadcrumb ezd-breadcrumb">
    <div class="<?php echo ezd_container(); ?>">
        <div class="ezd-grid ezd-grid-cols-12">
            <div class="ezd-lg-col-9 ezd-md-col-8 ezd-grid-column-full">
                <nav aria-label="breadcrumb">
                    <?php eazydocs_breadcrumbs(); ?>
                </nav>
            </div>
            <div class="ezd-lg-col-3 ezd-md-col-4 ezd-grid-column-full">
                <time itemprop="dateModified" datetime="<?php the_modified_time( get_option( 'date_format' ) ); ?>"
                    class="date">
                    <i class="<?php echo is_rtl() ? 'icon_quotations' : 'icon_clock_alt'; ?>"></i>
                    <?php echo esc_html( $update_text ); ?>
                    <span><?php the_modified_time( get_option( 'date_format' ) ); ?></span>
                </time>
            </div>
        </div>
    </div>
</section>