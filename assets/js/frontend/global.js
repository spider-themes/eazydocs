;(function ($) {
    "use strict";

    $(document).ready(function() {
        /**
         * Make the overflow of ancestor Elements to visible of Position Sticky Element
         * @type {HTMLElement}
         */
        let parent_selector = document.querySelector('.sticky-lg-top');
        if ( parent_selector ) {
            let parent = parent_selector.parentElement;
            while (parent) {
                const hasOverflow = getComputedStyle(parent).overflow;
                if (hasOverflow !== 'visible') {
                    parent.style.overflow = 'visible';
                }
                parent = parent.parentElement;
            }
        }

        $('.body_wrapper').addClass('eazydocs_assistant_body');
        
        $(window).scroll(function() {
            $(".doc-book-layout .nav-sidebar li a").filter(".nav-link").index();
        });

        /**
         * Left Sidebar Toggle icon
         */
        if ($(".doc_documentation_area.fullscreen-layout").length > 0) {
            //switcher
            var switchs = true;
            $("#right").on("click", function (e) {
                e.preventDefault();
                if (switchs) {
                    $(".doc_documentation_area.fullscreen-layout").addClass("overlay");

                    $(this).animate({
                        "right": "250px"
                    }, 500);

                    switchs = false;

                } else {
                    $(".doc_documentation_area.fullscreen-layout").removeClass("overlay");

                    $(this).animate({
                        "right": "0px"
                    }, 500);

                    switchs = true;
                }
            })

            $("#left").on("click", function (e) {
                e.preventDefault()
                if (switchs) {
                    $(".doc_documentation_area.fullscreen-layout").addClass("overlay");
                    $(".fullscreen-layout .doc_mobile_menu.left-column").animate({
                        "left": "0px"
                    }, 300);
                    switchs = false;
                } else {
                    $(".doc_documentation_area.fullscreen-layout").removeClass("overlay");
                    $(".fullscreen-layout .doc_mobile_menu.left-column").animate({
                        "left": "-260px"
                    }, 300);
                    switchs = true
                }
            });
        }
    })
})(jQuery);