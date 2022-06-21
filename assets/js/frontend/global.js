;(function ($) {
    "use strict";

    $(document).ready(function() {

        /**
         * Make the overflow of ancestor Elements to visible of Position Sticky Element
         * @type {HTMLElement}
         */
        let parent_selector = document.querySelector('.sticky-top');
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

        $('.body_wrapper').addClass('eazydocs_assistant_body')

        // Active class [ Book Layout ]
        $('body').scrollspy({
            target: ".doc-book-layout .doc_mobile_menu"
        })
        $(window).scroll(function() {
            $(".doc-book-layout .nav-sidebar li a").filter(".nav-link").index();
        });

    })
})(jQuery);

