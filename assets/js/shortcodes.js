;(function ($) {
    'use strict';

    $(document).ready(function () {
        const $footnoteFooter = $('.ezd-footnote-footer');
        const $footnoteTitle = $('.ezd-footnote-title');
        const $footnoteLinks = $('.ezd-footnotes-link-item');

        if ($footnoteFooter.children('div').length) {
            $footnoteTitle.css('display', 'flex').on('click', function () {
                $(this).toggleClass('expanded collapsed');
                $footnoteFooter.stop(true, true).slideToggle({
                    complete: function () {
                        $(this).css('display', $(this).is(':visible') ? 'flex' : 'none');
                    }
                });
            });

            $footnoteLinks.on('click', function () {
                $footnoteTitle.addClass('expanded').removeClass('collapsed');
                $footnoteFooter.css({ display: 'flex', height: 'auto' });
            });
        }
    });
})(jQuery);
