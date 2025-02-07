(function($){
    'use sticky'
    $(document).ready(function() {

        // Pro notices
        $('body:not(.ezd-premium) .eazydocs-pro-notice ul li:last-child label input').attr('disabled', true);
        $('body:not(.ezd-premium) .eazydocs-pro-notice input').attr('disabled', true);
        
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
                    e.preventDefault();
                    let href = $(this).attr('href')
                    Swal.fire({
                        title: 'Opps...',
                        html: 'This is a PRO feature. You need to <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium Version to use this feature',
                        icon: "warning",
                        buttons: [false, "Close"],
                        dangerMode: true,
                        //footer: '<a href="https://spider-themes.net/eazydocs/" target="_blank"> Learn More </a>',
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
        

        // Setup wizard scripts start
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.brand-color-picker').wpColorPicker({
                change: function(event, ui) {
                    var color = ui.color.toString();
                    $('.brand-color-picker').val(color);
                }
            });
        }
        
        $('.root-slug-wrap input[type="radio"]').on('change', function() {
            var name = $(this).attr('value');            
            $('.root-slug-wrap label[for="'+name+'"]').addClass('active').siblings().removeClass('active');

            var root_slug = $(this).val();
            if (root_slug == 'custom-slug') {
                $('.custom-slug-field').show();
            } else {
                $('.custom-slug-field').hide();
            }
        });

        
        $('.page-width-wrap input[type="radio"]').on('change', function() {
            var name = $(this).attr('value');
            $('.page-width-wrap label[for="'+name+'"]').addClass('active').siblings().removeClass('active');
        });
        $('.page-layout-wrap input[type="radio"]').on('change', function() {
            var name = $(this).attr('value');
            $('.page-layout-wrap label[for="'+name+'"]').addClass('active').siblings().removeClass('active');
        });
        
        // Smart Wizard
        if (typeof $.fn.smartWizard !== 'undefined') {

            $('#ezd-setup-wizard-wrap').smartWizard({
                keyboard: {
                    keyNavigation: true, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
                    keyLeft: [37], // Left key code
                    keyRight: [39] // Right key code
                },
                lang: { // Language variables for button
                    next: ' Next →',
                    previous: '← Previous',
                }
            });
        }
        
        $('#finish-btn').on('click', function() {
            var customSlug  = $('.custom-slug-field').val();            
            var customSlug  = customSlug.replace(/[^a-zA-Z0-9-_]/g, '-');
            customSlug      = customSlug.toLowerCase();

            var brandColor  = $('.brand-color-picker').val();
            var slugType    = $('.root-slug-wrap input[name="slug"]:checked').val();
            var live_customizer = $('input[name="customizer_visibility"]:checked').val();

            // select field .archive-page-selection-wrap > select
            var archivePage = $('.archive-page-selection-wrap select').val();
            

            // doc single layout
            var docSingleLayout = $('.page-layout-wrap input[name="docs_single_layout"]:checked').val();
            var docsPageWidth = $('.page-width-wrap input[name="docsPageWidth"]:checked').val();

            // make hypen to underscore
            $.ajax({
                url: eazydocs_local_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'ezd_setup_wizard_save_settings',
                    rootslug: customSlug,
                    brandColor: brandColor,
                    slugType: slugType,
                    docSingleLayout: docSingleLayout,
                    docsPageWidth: docsPageWidth,
                    live_customizer: live_customizer,
                    archivePage: archivePage,
                },
                beforeSend: function() {
                    $('div#ezd-setup-wizard-wrap .tab-content #step-4 h2').html('Submitting...');
                },
                success: function(response) {
                    if (response.success) {
                        $('.swal2-icon .swal2-icon-content').html('<img src="'+eazydocs_local_object.ezd_plugin_url+'/assets/images/wizard-success.png"  />');
                        $('div#ezd-setup-wizard-wrap .tab-content #step-4 h2').text('Thank you');
                        $('div#ezd-setup-wizard-wrap .tab-content #step-4 p').text('You have setup everything successfully');
                        // redirect
                        setTimeout(function() {
                            window.location.href = 'admin.php?page=eazydocs';
                        }, 100);

                    } else {
                        alert('Error saving settings');
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX error: ' + status + ' - ' + error);
                }
            });
            
        });
        // Setup wizard scripts end        
        
    });
})(jQuery);