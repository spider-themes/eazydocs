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
                        //footer: '<a href="https://spider-themes.net/eazydocs/" target="_blank"> Learn More </a>',
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
                    //footer: '<a href="https://spider-themes.net/eazydocs/" target="_blank"> Learn More </a>',
                })
            })
        }
        eazydocs_pro_notice();

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
                footer: '<a href="https://spider-themes.net/eazydocs/" target="_blank"> Learn More </a>',

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
        
        // Create Doc with AI Popup
        $(document).on('click', '#ezd-create-doc-with-ai', function(e) {
            e.preventDefault();
            
            // Check if Antimanual is active
            const isAntimanualActive = typeof eazydocs_local_object !== 'undefined' && eazydocs_local_object.antimanualActive;
            
            let popupContent = `
                <div class="ezd-ai-popup-content">
                    <div class="ezd-ai-header">
                        <div class="ezd-ai-badge">🤖 Powered by Antimanual AI</div>
                        <h2>Create Docs Smarter with AI</h2>
                        <p class="ezd-ai-desc">Transform documentation with structured, accurate, and professional docs in minutes using enterprise-grade AI.</p>
                    </div>
                    
                    <div class="ezd-ai-video-wrapper">
                        <video autoplay muted loop playsinline>
                                <source src="https://antimanual.spider-themes.net/wp-content/uploads/2025/08/AI-Doc-generate.mp4" type="video/mp4">
                        </video>
                    </div>

                    <div class="ezd-ai-feature-grid">
                        <div class="ezd-ai-feature-item">
                            <span class="ezd-ai-feature-icon">🤖</span>
                            <h3>AI Documentation Generator</h3>
                            <p>Generate comprehensive, SEO-optimized docs with custom tone and language. Choose professional, friendly, or technical styles.</p>
                        </div>
                        <div class="ezd-ai-feature-item">
                            <span class="ezd-ai-feature-icon">💬</span>
                            <h3>24/7 AI Chatbot</h3>
                            <p>Reduce support tickets by 70%+ with an intelligent chatbot trained on your EazyDocs knowledge base. Instant, accurate answers.</p>
                        </div>
                        <div class="ezd-ai-feature-item">
                            <span class="ezd-ai-feature-icon">📊</span>
                            <h3>Bulk Generation</h3>
                            <p>Generate multiple docs at once with comprehensive structures. Review and refine outlines before generation.</p>
                        </div>
                        <div class="ezd-ai-feature-item">
                            <span class="ezd-ai-feature-icon">📄</span>
                            <h3>Docs from Files</h3>
                            <p>Upload PDFs, URLs, and custom text to generate context-aware documentation automatically.</p>
                        </div>
                        <div class="ezd-ai-feature-item">
                            <span class="ezd-ai-feature-icon">🔍</span>
                            <h3>Semantic AI Search</h3>
                            <p>Beyond keywords - understand user intent with natural language processing and smart suggestions.</p>
                        </div>
                        <div class="ezd-ai-feature-item">
                            <span class="ezd-ai-feature-icon">🚀</span>
                            <h3>GPT-5 & Gemini Support</h3>
                            <p>Powered by latest AI models including GPT-4, GPT-5, and Google Gemini for superior accuracy.</p>
                        </div>
                    </div>

                    <div class="ezd-ai-benefits">
                        <h3>Why Choose Antimanual for EazyDocs?</h3>
                        <ul>
                            <li><strong>Enterprise-Grade AI:</strong> GPT-4, GPT-5, and Gemini support (not just GPT-3.5)</li>
                            <li><strong>Multi-Source Training:</strong> Train on Docs, PDFs, URLs, and custom text</li>
                            <li><strong>Full Context Retention:</strong> Conversation history for smarter, personalized answers</li>
                            <li><strong>Cross-Domain Embed:</strong> Deploy chatbot on external sites easily</li>
                            <li><strong>bbPress Integration:</strong> Convert forum discussions into permanent knowledge base articles</li>
                        </ul>
                    </div>
            `;
            
            if (isAntimanualActive) {
                popupContent += `
                    <div class="ezd-ai-popup-footer ezd-ai-active">
                        <div class="ezd-ai-active-badge">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <span>Antimanual is Active!</span>
                        </div>
                        <a href="${window.location.origin}/wp-admin/admin.php?page=antimanual" class="ezd-ai-btn ezd-ai-btn-primary">
                            Manage AI Settings
                            <span class="dashicons dashicons-admin-generic"></span>
                        </a>
                        <a href="https://helpdesk.spider-themes.net/docs/antimanual" target="_blank" class="ezd-ai-btn ezd-ai-btn-secondary">
                            View Documentation
                            <span class="dashicons dashicons-external"></span>
                        </a>
                    </div>
                `;
            } else {
                popupContent += `
                    <div class="ezd-ai-popup-footer">
                        <div class="ezd-ai-cta-text">
                            <span class="dashicons dashicons-info"></span>
                            <p>Get started with Antimanual to unlock all AI features for your EazyDocs knowledge base!</p>
                        </div>
                        <a href="${window.location.origin}/wp-admin/plugin-install.php?s=antimanual&tab=search&type=term" class="ezd-ai-btn ezd-ai-btn-primary">
                            <span class="dashicons dashicons-download"></span>
                            Install Antimanual Free
                        </a>
                        <a href="https://antimanual.spider-themes.net" target="_blank" class="ezd-ai-btn ezd-ai-btn-secondary">
                            Learn More
                            <span class="dashicons dashicons-external"></span>
                        </a>
                        <a href="https://www.youtube.com/watch?v=X9HMPBkzDeM" target="_blank" class="ezd-ai-btn ezd-ai-btn-link">
                            <span class="dashicons dashicons-video-alt3"></span>
                            Watch Demo
                        </a>
                    </div>
                `;
            }
            
            popupContent += `</div>`;
            
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