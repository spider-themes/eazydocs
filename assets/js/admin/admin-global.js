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
                $(document).on('click', '.eazydocs-pro-notice', function (e) {
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

        // Remove condition if it has pro notice class
        $('.eazydocs-pro-notice').attr('data-condition', '')
    })
})(jQuery);