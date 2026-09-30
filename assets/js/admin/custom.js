/**
 * EazyDocs Dashboard JavaScript
 * 
 * This file contains JavaScript functionality for the EazyDocs Dashboard page.
 * It handles NiceSelect initialization and stats filter interactions.
 * 
 * Note: Docs Builder-specific code is now in docs-builder.js
 * Note: Analytics page has its own tab handling in Analytics.php
 * 
 * @package EazyDocs
 * @since 2.7.0
 */
(function ($) {
	'use strict';
	
	$(document).ready(function () {
		// Filter Select (NiceSelect initialization - scoped to EazyDocs containers)
		var $ezdSelects = $('.ezd-admin, .ezd-container, .eazydocs-dashboard-wrap, .ezd-setup-content, .ezd-stat-filter-container').find('select');
		if ($ezdSelects.length > 0 && $.fn.niceSelect) {
			$ezdSelects.niceSelect();
		}

		// Dashboard Stats Filter Active Class Toggle
		$(".ezd-stat-filter-container ul li").on("click", function() {
			// Remove active class from all
			$(".ezd-stat-filter-container ul li").removeClass("active");
			
			// Add active class to the clicked one
			$(this).addClass("active");
		});
	});

})(jQuery);