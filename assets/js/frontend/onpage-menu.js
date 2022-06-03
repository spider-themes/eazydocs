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

    })

    $(document).ready(function() {
        $(window);
        var t = $(document.body),
            n = $(".doc-container").find(".doc-nav");
        t.scrollspy({
            target: ".doc-sidebar"
        }),
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
        })
    })

    var originalAddClassMethod = jQuery.fn.addClass;

    jQuery.fn.addClass = function(){
        // Execute the original method.
        var result = originalAddClassMethod.apply( this, arguments );

        // trigger a custom event
        jQuery(this).trigger('cssClassChanged');

        // return the original result
        return result;
    }
    $( window ).on('scroll', function() {
        $(".doc-nav .nav-link").bind('cssClassChanged' , function(e) {
            $(".doc-nav .nav-item").each( function() {
                if( $(this).hasClass("active") == true ) {
                    $(this).removeClass("active");
                    $('.dropdown_nav li').parent().closest('li').removeClass('active');
                }
            });
            $(this).removeClass("active").parent().addClass("active");
            $('.dropdown_nav li.active').parent().closest('li').addClass('active');
        });
    });

})(jQuery);