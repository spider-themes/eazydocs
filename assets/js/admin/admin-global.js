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
                        title: 'Opps...',
                        html: 'This is a Premium feature. You need to <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium plan to use this feature',
                        icon: "warning",
                        buttons: [false, "Close"],
                        dangerMode: true,
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
                        title: 'Opps...',
                        html: 'This is a PRO feature. You need to <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium Version to use this feature',
                        icon: "warning",
                        buttons: [false, "Close"],
                        dangerMode: true
                    })
                })
            }
            
            // eazydocs promax notice
            $('body:not(.ezd-promax) .eazydocs-promax-notice').on('click', function (e) {
                e.preventDefault();
                let href = $(this).attr('href')
                Swal.fire({
                    title: 'Opps...',
                    html: 'This is a Promax feature. You need to <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium Version to use this feature',
                    icon: "warning",
                    buttons: [false, "Close"],
                    dangerMode: true,
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
            let assets = eazydocs_local_object.EAZYDOCS_ASSETS;
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
        $('.ezd-migration-wrapper button').on('click', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure to migrate?',
                html: `
                    <div class="migration-alert-info">
                        <p>We're committed to ensuring a smooth and successful migration to EazyDocs.</p>
                        <label><strong>Choose migration source</strong></label>
                        <div class="migration-field-wrap">
                            <select id="ezd_migration_options">
                                <option value="betterdocs">BetterDocs</option>
                            </select>
                            <fieldset>To EazyDocs</fieldset>
                        </div>
                        <p class="migration-alert-text">
                            <strong>⚠️ Migration Notice:</strong><br>
                            Before migrating, we recommend exporting your docs from <b>Tools > Export</b>. After migration, review your content—if anything's off, you can easily re-import the backup.
                        </p>
                    </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Yes, I'm sure",
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const migrationOption = $('#ezd_migration_options').val();
                    if (!migrationOption) {
                        Swal.showValidationMessage('Please select BetterDocs to migrate from.');
                        return false;
                    }
                    return { migrationOption };
                }
            }).then((result) => {
                if (!result.isConfirmed || !result.value) return;

                const migrationFrom = result.value.migrationOption;

                Swal.fire({
                    title: 'Migrating...',
                    text: `Migrating from ${migrationFrom} to EazyDocs...`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'ezd_migrate_to_eazydocs',
                        migrate_from: migrationFrom
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Migration Complete!',
                                text: 'You have successfully migrated your knowledgebase to EazyDocs.',
                                icon: 'success',
                                confirmButtonText: 'Go to EazyDocs'
                            }).then(() => {
                                window.location.href = 'admin.php?page=eazydocs';
                            });
                        } else {
                            const msg = (response.data && response.data.message) || response.data || 'Something went wrong.';
                            Swal.fire({
                                title: 'Migration Failed',
                                text: msg,
                                icon: 'error'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'AJAX request failed. Please try again.', 'error');
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
        

        // ADD PARENT DOC
        function add_parent_doc() {
            $(document).on('click', '#parent-doc', function (e) {
                e.preventDefault();
                let href = $(this).attr('data-url');
                Swal.fire({
                    title: eazydocs_local_object.create_prompt_title,
                    input: 'text',
                    showCancelButton: true,
                    inputAttributes: {
                        name: 'parent_title',
                    },
                }).then((result) => {
                    if (result.value) {
                        let results = result.value.replaceAll(
                            '&',
                            'ezd_ampersand'
                        );
                        results = results.replaceAll('#', 'ezd_hash');
                        results = results.replaceAll('+', 'ezd_plus');

                        document.location.href = href + results;
                    }
                });
            });
        }

        add_parent_doc();
        
    });
})(jQuery);