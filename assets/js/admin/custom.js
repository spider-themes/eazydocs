/**
 * EazyDocs Dashboard JavaScript
 * 
 * This file contains JavaScript functionality for the EazyDocs Dashboard page.
 * It handles NiceSelect initialization and stats filter interactions.
 * 
 * Note: Doc Builder-specific code is now in doc-builder.js
 * Note: Analytics page has its own tab handling in Analytics.php
 * 
 * @package EazyDocs
 * @since 2.7.0
 */
(function ($) {
	'use strict';
	
	$(document).ready(function () {
		// Filter Select (NiceSelect initialization)
		if ($('select').length > 0) {
			$('select').niceSelect();
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