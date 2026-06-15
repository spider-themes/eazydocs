(function($){
    'use sticky'
    $(document).ready(function() {

        // Pro notices
        $('body:not(.ezd-premium) .eazydocs-pro-notice ul li:last-child label input').attr('disabled', true);
        
        // Promax notices
        $('body:not(.ezd-promax) .eazydocs-promax-notice .csf-field').attr('disabled', true);

        // eazydocs pro notice
        function eazydocs_pro_notice() {
            if ( $('body').hasClass('valid') ) {
                $('body:not(.ezd-premium) .eazydocs-pro-notice:not(div[class*="active-theme"])').on('click', function (e) {
                    e.preventDefault();
                    let href = $(this).attr('href')
                    Swal.fire({
                        title: 'Premium feature',
                        html: 'Unlock this feature to get more control over your documentation. <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium plan to enable it.',
                        icon: "info",
                        buttons: [false, "Close"],
                        dangerMode: false,
                    })
                })
            } else {
                $('body:not(.ezd-premium) .eazydocs-pro-notice').on('click', function (e) {
                    if ($('body').hasClass('ama') && $(this).hasClass('active-theme-ama')) {
                        return; // skip alert
                    }
                    e.preventDefault();
                    let href = $(this).attr('href')
                    Swal.fire({
                        title: 'Pro feature',
                        html: 'This feature is part of EazyDocs Pro. <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Pro version to enable it.',
                        icon: "info",
                        buttons: [false, "Close"],
                        dangerMode: false
                    })
                })
            }
            
            // eazydocs promax notice
            $('body:not(.ezd-promax) .eazydocs-promax-notice').on('click', function (e) {
                e.preventDefault();
                let href = $(this).attr('href')
                Swal.fire({
                    title: 'Pro Max feature',
                    html: 'This feature is part of EazyDocs Pro Max. <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Pro Max plan to enable it.',
                    icon: "info",
                    buttons: [false, "Close"],
                    dangerMode: false,
                })
            })
        }

         /* ------------------------------
        * Init (Admin + Customizer)
        * ------------------------------ */

        // Normal admin / frontend load
        $(document).ready(function () {
            eazydocs_pro_notice();
        });

        // Customizer support (controls load dynamically)
        if (typeof wp !== 'undefined' && wp.customize) {
            wp.customize.bind('ready', function () {
                eazydocs_pro_notice();
            });
        }

        // Notification pro alert
        $('.easydocs-notification.pro-notification-alert').on('click', function (e) {
            e.preventDefault();
            let href = $(this).attr('href')
            let assets = eazydocs_local_object.EZD_ASSETS;
            Swal.fire({
                title: 'Notification is a Premium feature',
                html: '<span class="pro-notification-body-text">You need to Upgrade the Premium Version to use this feature</span><video height="400px" autoplay="autoplay" loop="loop" src="'+assets+'/videos/noti.mp4"></video>',
                icon: false,
                buttons: false,
                dangerMode: true,
                showCloseButton: true,
                confirmButtonText:
                    '<a href="admin.php?page=eazydocs-pricing">Upgrade to Premium</a>',
                footer: '<a href="https://eazydocs.spider-themes.net/" target="_blank"> Learn More </a>',

                customClass: {
                    title: 'upgrade-premium-heading',
                    confirmButton: 'upgrade-premium-button',
                    footer: 'notification-pro-footer-wrap',
                },
                confirmButtonColor: '#f1bd6c',
                Borderless: true,

            })
        });

        // Select field after text visibility by url structure
        var url_structure = $('select[name="eazydocs_settings[docs-url-structure]"]').val();
        $('select[name="eazydocs_settings[docs-url-structure]"]').on('change', function() {
            url_structure = $(this).val();
            if (url_structure == 'post-name') {
                $('.docs-url-structure .csf-after-text').show();
            } else {
                $('.docs-url-structure .csf-after-text').hide();
            }
        });

        if (url_structure == 'post-name') {
            $('.docs-url-structure .csf-after-text').show();
        } else {
            $('.docs-url-structure .csf-after-text').hide();
        }

        // Notice for customizer
        $('.no-customizer-access').on('click', function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Access Denied',
                html: 'You do not have sufficient permissions to perform this action. Only administrators are allowed to proceed',
                icon: 'warning',
                buttons: false,
                dangerMode: true,
                showCloseButton: true,
                confirmButtonText: 'Got it'
            });
        });

        $('.admin-copy-embed-code').on('click', function(e) {
            e.preventDefault();
            var textarea = $(this).siblings('textarea')[0];
            textarea.select();
            textarea.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            $(this).text('Copied!');
            setTimeout(() => { $(this).text('Copy'); }, 2000);
        });

        // BetterDocs to EazyDocs migration
        $('.ezd-start-migration-btn').on('click', function (e) {
            e.preventDefault();

            var l10n = (typeof eazydocs_local_object !== 'undefined' && eazydocs_local_object.migration) || {};

            Swal.fire({
                title: l10n.confirmTitle || 'Migrate from BetterDocs to EazyDocs?',
                html: l10n.confirmHtml || '',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: l10n.confirmBtn || 'Yes, migrate now',
                cancelButtonText: l10n.cancelBtn || 'Cancel'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: l10n.progressTitle || 'Migrating…',
                    text: l10n.progressText || '',
                    allowOutsideClick: false,
                    didOpen: function () { Swal.showLoading(); }
                });

                $.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'ezd_migrate_to_eazydocs',
                        migrate_from: 'betterdocs',
                        security: eazydocs_local_object.nonce
                    },
                    success: function (response) {
                        if (response && response.success) {
                            var data = response.data || {};
                            Swal.fire({
                                title: l10n.successTitle || 'Migration complete!',
                                html: data.summary || data.message || '',
                                icon: 'success',
                                confirmButtonText: l10n.successBtn || 'Go to EazyDocs'
                            }).then(function () {
                                window.location.href = l10n.eazydocsUrl || 'admin.php?page=eazydocs';
                            });
                        } else {
                            var msg = (response && response.data && response.data.message) || l10n.genericError || 'Something went wrong.';
                            Swal.fire({
                                title: l10n.failTitle || 'Migration failed',
                                text: msg,
                                icon: 'error'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: l10n.failTitle || 'Migration failed',
                            text: l10n.ajaxError || 'The request failed. Please try again.',
                            icon: 'error'
                        });
                    }
                });
            });
        });
        
        // Create Doc with AI Popup (only when Antimanual isn't active)
        $(document).on('click', '#ezd-create-doc-with-ai', function(e) {
			const antimanualActive = !!(
				typeof eazydocs_local_object !== 'undefined' &&
				eazydocs_local_object.antimanualActive
			);

			// When Antimanual is active, the control becomes a normal link to atml-docs.
			// Do not prevent navigation.
			if (antimanualActive) {
				return;
			}

            e.preventDefault();

			const popupContent = (typeof eazydocs_local_object !== 'undefined' && eazydocs_local_object.aiPopupHtml)
				? eazydocs_local_object.aiPopupHtml
				: '';

			if (!popupContent) {
				return;
			}
            
            Swal.fire({
                title: '',
                html: popupContent,
                showConfirmButton: false,
                showCloseButton: true,
                width: '800px',
                padding: '0',
                customClass: {
                    container: 'ezd-ai-popup-container',
                    popup: 'ezd-ai-popup-wrapper',
                    content: 'ezd-ai-popup-body-content',
                    closeButton: 'ezd-ai-popup-close'
                }
            });
        });
        
    });
})(jQuery);