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


        $('.admin-copy-embed-code').on('click', function(e) {
            e.preventDefault();
            var textarea = $(this).siblings('textarea')[0];
            textarea.select();
            textarea.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            $(this).text('Copied!');
            setTimeout(() => { $(this).text('Copy'); }, 2000);
        });

    });
})(jQuery);