;(function ($) {
    "use strict";

    $(document).ready(function() {

        /**
         * Print doc
         */
        $('.page-template-page-onepage .pageSideSection .print').on('click', function (e) {
            e.preventDefault()
            $(".page-template-page-onepage #post").printThis()
        })

        $('body').attr({'data-bs-spy': 'scroll', 'data-bs-target': "#eazydocs-toc"})

        // Onepage menu
        $(window);
        var t = $(document.body),
            n = $(".doc-container").find(".doc-nav");
        t.scrollspy({
            target: ".doc-sidebar"
        })
        n.find("> li > a").before($('<span class="docs-progress-bar" />'));
        n.offset().top;
        $(window).scroll(function() {
            $(".doc-nav").height();
            var t = $(this).scrollTop(),
                n = $(this).innerHeight(),
                e = $(".doc-nav li a").filter(".active").index();
            $(".doc-section").each(function(i) {
                var c = $(this).offset().top,
                    s = $(this).height(),
                    a = c + s,
                    r = 0;
                t >= c && t <= a ? (r = (t - c) / s * 100) >= 100 && (r = 100) : t > a && (r = 100), a < t + n - 70 && (r = 100);
                var d = $(".doc-nav .docs-progress-bar:eq(" + i + ")");
                e > i && d.parent().addClass("viewed"), d.css("width", r + "%")
            })
        });
        $('.nav-sidebar.one-page-doc-nav-wrap .dropdown_nav .dropdown_nav').addClass('doc-last-depth');
        $('.nav-sidebar.fullscreen-layout-onepage-sidebar .dropdown_nav .dropdown_nav').addClass('doc-last-depth-fullscreen');
        $('.doc-last-depth-fullscreen').parent('.nav-item').addClass('doc-last-depth-icon');
    })
})(jQuery);

jQuery( window ).on('scroll', function() {
    jQuery(".doc-nav .nav-item .nav-link").each( function() {
        if ( jQuery(this).hasClass('active') ) {
            jQuery(this).parent().addClass('active')
            //jQuery(this).removeClass('active')
        } else {
            jQuery(this).parent().removeClass('active')
        }
    })
    jQuery('.dropdown_nav li.active').parent().closest('li').addClass('active');

    jQuery('.dropdown_nav li.active').parent('.dropdown_nav').closest('li').addClass('active');
 

    //jQuery('.doc-nav .nav-item .nav-link.active').removeClass('active')
});