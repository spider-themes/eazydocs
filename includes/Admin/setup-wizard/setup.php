<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slugType              = ezd_get_opt( 'docs-url-structure' );
$custom_slug           = ezd_get_opt( 'docs-type-slug' );
$brand_color           = ezd_get_opt( 'brand_color' );
$docs_single_layout    = ezd_get_opt( 'docs_single_layout' );
$docs_page_width       = ezd_get_opt( 'docs_page_width' );
$customizer_visibility = ezd_get_opt( 'customizer_visibility' );
$docs_archive_page     = ezd_get_opt( 'docs-slug' );
?>
<div class="wrap">
    <div class="ezd-setup-wizard-wrapper">
        <div class="ezd-setup-wizard-header">

            <div>
                <img src="<?php echo esc_url( EAZYDOCS_IMG . '/eazydocs-favicon.png' ); ?>" alt="<?php echo esc_attr__( 'Eazydocs icon', 'eazydocs' ); ?>"/>
                <span><?php esc_html_e( 'EazyDocs', 'eazydocs' ); ?></span>
            </div>

            <div>
                <span class="dashicons dashicons-welcome-write-blog"></span>
                <span>
                    <a target="__blank" href="https://spider-themes.net/eazydocs/changelog/">
                        <?php esc_html_e( "What's New!", 'eazydocs' ); ?>
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div id="ezd-setup-wizard-wrap">

        <div class="ezd-wizard-head">
            <div class="ezd-wizard-head-left">
                <img src="<?php echo esc_url( EAZYDOCS_IMG . '/eazydocs-favicon.png' ); ?>" alt="<?php esc_attr_e( 'crown icon', 'eazydocs' ); ?>"/>
                <span>
                    <?php esc_html_e( 'GETTING STARTED', 'eazydocs' ); ?>
                </span>
            </div>
            <div class="ezd-wizard-head-right">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs' ) ); ?>" class="btn btn-primary">
					<?php esc_html_e( 'Skip', 'eazydocs' ); ?>
                </a>
            </div>
        </div>

        <div class="sw-toolbar">
            <ul class="nav sr-only">
                <li><a class="nav-link" href="#step-1"></a></li>
                <li><a class="nav-link" href="#step-2"></a></li>
                <li><a class="nav-link" href="#step-3"></a></li>
                <li><a class="nav-link" href="#step-4"></a></li>
                <li><a class="nav-link" href="#step-5"></a></li>
            </ul>
        </div>

        <div class="tab-content">
			<?php
			require( 'step1_welcome.php' );
			require( 'step2_settings.php' );
			require( 'step3_settings.php' );
			require( 'step4_install_plugins.php' );
			?>
            <div id="step-5" class="tab-pane" role="tabpanel" style="display:none">
                <div class="swal2-icon swal2-question swal2-icon-show" style="display: flex;">
                    <div class="swal2-icon-content">?</div>
                </div>
                <h2> <?php esc_html_e( 'Review and Confirm', 'eazydocs' ); ?> </h2>

                <p> <?php esc_html_e( 'Take a moment to review all your settings thoroughly before confirming your choices to ensure everything is set up correctly.', 'eazydocs' ); ?> </p>

                <button type="button" id="finish-btn" class="btn btn-primary">
					<?php esc_html_e( 'Confirm', 'eazydocs' ); ?>
                </button>
            </div>

        </div>
    </div>
</div>