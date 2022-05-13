(function($){
    'use sticky'
    $(document).ready(function() {

        // Eazydocs Pro notice
        $('.eazydocs-pro-notice ul li:last-child label input').attr('disabled', true);
        // NEW DOC
        function eazydocs_pro_notice() {
            if ( $('body').hasClass('valid') ) {
                $(document).on('click', '.eazydocs-pro-notice:not(.active-theme)', function (e) {
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
            } else {
                $('.eazydocs-pro-notice').on('click', function (e) {
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
        }
        eazydocs_pro_notice();

        // Notification pro alert
        $('.easydocs-notification').on('click', function (e) {
            e.preventDefault();
            let href = $(this).attr('href')
            Swal.fire({
                title: 'Notification is a Premium feature',
                html: '<span class="pro-notification-body-text">You need to Upgrade the Premium Version to use this feature</span><video height="400px" autoplay="autoplay" loop="loop" src="https://i.imgur.com/2JYBv40.mp4"></video>',
                icon: false,
                buttons: false,
                dangerMode: true,
                confirmButtonText:
                    '<a href="admin.php?page=eazydocs-pricing">Upgrade to Premium</a>',
                showCloseButton: false,
                footer: '<a href="https://spider-themes.net/eazydocs/" target="_blank"> Learn More </a>',

                customClass: {
                    title: 'upgrade-premium-heading',
                    confirmButton: 'upgrade-premium-button',
                    footer: 'notification-pro-footer-wrap',
                },
                confirmButtonColor: '#f1bd6c',
                Borderless: true,

            })
        })

        // Remove condition if it has pro notice class
        $('.eazydocs-pro-notice').attr('data-condition', '')
    })
})(jQuery);