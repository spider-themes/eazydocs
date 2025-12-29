<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$depth_one_parents = [];
$depth_two_parents = [];
$posts             = get_posts( [ 'post_type' => 'docs',  'post_status' => ['publish', 'draft', 'private'] ] );
$docs_num          = count( $posts );
?>
<div class="ezd_doc_builder">
	<?php
		if ( $docs_num > 0 ) :
			require_once __DIR__ . '/template/header.php';
			?>
            <main>
                <div class="easydocs-sidebar-menu">
                    <div class="tab-container">
						<?php require_once __DIR__ . '/template/parent-docs.php'; ?>
                        <div class="easydocs-tab-content">
							<?php require_once __DIR__ . '/template/child-docs.php'; ?>
                        </div>
                    </div>
                </div>
            </main>
		    <?php
		else :
		    ?>
            <div class="eazydocs-no-content-wrapper">
                <div class="ezd-empty-state-card">
                    <div class="ezd-empty-icon-box">
                        <img src="<?php echo esc_url( EAZYDOCS_IMG . '/icon/folder-open.png' ); ?>" alt="<?php esc_attr_e('Folder Open', 'eazydocs' ); ?>" class="ezd-empty-icon">
                    </div>
                    <h2 class="ezd-empty-title"><?php esc_html_e( 'Ready to Start Your Knowledge Base?', 'eazydocs' ); ?></h2>
                    <p class="ezd-empty-desc"> <?php esc_html_e( 'It looks like you haven\'t created any documentation yet. Get started by creating your first doc or import our sample data to see how it works.', 'eazydocs' ); ?> </p>
                    
                    <div class="ezd-empty-actions">
                        <a class="ezd-btn-premium ezd-btn-primary-gradient" href="<?php echo esc_url( admin_url( 'admin.php' ) . '?Create_doc=yes&_wpnonce=' . wp_create_nonce( 'create_new_doc_nonce' ) . '&new_doc=' ); ?>" id="new-doc">
                            <span class="dashicons dashicons-plus"></span>
                            <?php esc_html_e( 'Create First Doc', 'eazydocs' ); ?>
                        </a>
                        
                        <button type="button" class="ezd-btn-premium ezd-btn-import-sample" id="ezd-import-sample-data">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e( 'Import Sample Data', 'eazydocs' ); ?>
                        </button>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#ezd-import-sample-data').on('click', function(e) {
                    e.preventDefault();
                    var $button = $(this);
                    var originalHtml = $button.html();

                    // Confirmation dialog
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '<?php echo esc_js( __( 'Import Sample Data?', 'eazydocs' ) ); ?>',
                            text: '<?php echo esc_js( __( 'This will populate your knowledge base with a complete set of demo documentation items.', 'eazydocs' ) ); ?>',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#5A50F9',
                            cancelButtonColor: '#e0e0e0',
                            confirmButtonText: '<?php echo esc_js( __( 'Yes, Import Demo', 'eazydocs' ) ); ?>',
                            cancelButtonText: '<?php echo esc_js( __( 'Cancel', 'eazydocs' ) ); ?>',
                            customClass: {
                                container: 'ezd-swal2-container',
                                popup: 'ezd-swal2-popup'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                importSampleData($button, originalHtml);
                            }
                        });
                    } else {
                        if (confirm('<?php echo esc_js( __( 'This will import demo documentation to help you get started. Do you want to continue?', 'eazydocs' ) ); ?>')) {
                            importSampleData($button, originalHtml);
                        }
                    }
                });

                function importSampleData($button, originalHtml) {
                    // Show loading state
                    $button.addClass('is-loading').prop('disabled', true)
                           .html('<span class="dashicons dashicons-update ezd-spin"></span> <?php echo esc_js( __( 'Importing...', 'eazydocs' ) ); ?>');

                    $.ajax({
                        url: eazydocs_local_object.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'ezd_import_sample_data',
                            security: eazydocs_local_object.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: '<?php echo esc_js( __( 'Success!', 'eazydocs' ) ); ?>',
                                        text: response.data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#4C4CF1'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    alert(response.data.message);
                                    location.reload();
                                }
                            } else {
                                showError(response.data.message || '<?php echo esc_js( __( 'Failed to import sample data.', 'eazydocs' ) ); ?>');
                                resetBtn($button, originalHtml);
                            }
                        },
                        error: function() {
                            showError('<?php echo esc_js( __( 'An error occurred while importing. Please try again.', 'eazydocs' ) ); ?>');
                            resetBtn($button, originalHtml);
                        }
                    });
                }

                function showError(msg) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '<?php echo esc_js( __( 'Error', 'eazydocs' ) ); ?>',
                            text: msg,
                            icon: 'error',
                            confirmButtonColor: '#4C4CF1'
                        });
                    } else {
                        alert(msg);
                    }
                }

                function resetBtn($btn, html) {
                    $btn.removeClass('is-loading').prop('disabled', false).html(html);
                }
            });
            </script>
            <style>
                .eazydocs-no-content-wrapper {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 400px;
                    padding: 40px 20px;
                    background: radial-gradient(circle at top right, rgba(76, 76, 241, 0.03), transparent 40%),
                                radial-gradient(circle at bottom left, rgba(76, 76, 241, 0.03), transparent 40%);
                }
                .ezd-empty-state-card {
                    background: #fff;
                    padding: 60px 40px;
                    border-radius: 20px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.04);
                    text-align: center;
                    max-width: 600px;
                    width: 100%;
                    border: 1px solid rgba(76, 76, 241, 0.08);
                }
                .ezd-empty-icon-box {
                    width: 80px;
                    height: 80px;
                    background: rgba(76, 76, 241, 0.05);
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin: 0 auto 25px;
                }
                .ezd-empty-icon {
                    width: 40px;
                    height: 40px;
                    opacity: 0.8;
                }
                .ezd-empty-title {
                    font-size: 24px;
                    font-weight: 700;
                    color: #1a1a1a;
                    margin-bottom: 15px;
                }
                .ezd-empty-desc {
                    font-size: 16px;
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 35px;
                    max-width: 450px;
                    margin-left: auto;
                    margin-right: auto;
                }
                .ezd-empty-actions {
                    display: flex;
                    gap: 15px;
                    justify-content: center;
                    flex-wrap: wrap;
                }
                .ezd-btn-premium {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    padding: 14px 28px;
                    border-radius: 12px;
                    font-size: 15px;
                    font-weight: 600;
                    text-decoration: none;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    border: none;
                    cursor: pointer;
                    line-height: 1.2;
                }
                .ezd-btn-primary-gradient {
                    background: linear-gradient(135deg, #4C4CF1 0%, #3434bb 100%);
                    color: #fff !important;
                    box-shadow: 0 4px 15px rgba(76, 76, 241, 0.25);
                }
                .ezd-btn-primary-gradient:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(76, 76, 241, 0.35);
                    filter: brightness(1.1);
                }
                .ezd-btn-import-sample {
                    background: #f8f9ff;
                    color: #4C4CF1;
                    border: 1px solid rgba(76, 76, 241, 0.15);
                }
                .ezd-btn-import-sample:hover {
                    background: #eff1ff;
                    border-color: #4C4CF1;
                    transform: translateY(-2px);
                }
                .ezd-btn-premium .dashicons {
                    font-size: 18px;
                    width: 18px;
                    height: 18px;
                    line-height: 1;
                }
                .ezd-spin {
                    animation: ezd-spin 1s linear infinite;
                }
                @keyframes ezd-spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                /* Loading state styling */
                .ezd-btn-premium.is-loading {
                    opacity: 0.8;
                    cursor: not-allowed;
                    transform: none !important;
                }
                /* SWAL2 Custom classes */
                .ezd-swal2-popup {
                    border-radius: 20px !important;
                    padding: 2em !important;
                }
                .ezd-swal2-popup .swal2-title {
                    font-weight: 700 !important;
                }
                .ezd-swal2-popup .swal2-confirm {
                    border-radius: 10px !important;
                    padding: 12px 30px !important;
                }
                .ezd-swal2-popup .swal2-cancel {
                    border-radius: 10px !important;
                    background: #f5f5f5 !important;
                    color: #666 !important;
                }
            </style>
		    <?php
		endif;
	?>
</div>