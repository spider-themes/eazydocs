/*
 *  Project: RV Font Size jQuery Plugin
 *  Description: An easy and flexible jquery plugin to give font size accessibility control.
 *  URL: https://github.com/ramonvictor/rv-jquery-fontsize/
 *  Author: Ramon Victor (https://github.com/ramonvictor/)
 *  License: Licensed under the MIT license:
 *  http://www.opensource.org/licenses/mit-license.php
 *  Any and all use of this script must be accompanied by this copyright/license notice in its present form.
 */

(function ($) {
    "use strict";

    $(document).ready(function () {
        var $speech = $('#post p, #post ul li:not(.process_tab_shortcode ul li), #post ol li, #post table:not(.basic_table_info,.table-dark), #post table tr td, #post .tab-content');
        var $defaultSize = $speech.css('fontSize');
        $('#rvfs-controllers button').click(function () {
            var num = parseFloat($speech.css('fontSize'));
            switch (this.id) {
                case 'switcher-large':
                    num *= 1.1;
                    break;
                case 'switcher-small':
                    num /= 1.1;
                    break;
                default:
                    num = parseFloat($defaultSize);
            }
            $speech.animate({fontSize: num + 'px'});
        });
    })

})(jQuery);