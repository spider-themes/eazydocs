;(function ($) {
	'use strict';

	$(document).ready(function () {
		// EAZYDOCS FOOTNOTE
		let ezd_note_column = $('.ezd-footnote-footer').attr('ezd-data-column');
		$('.ezd-footnote-footer').css('column-count', ezd_note_column);

		$('.ezd-footnotes-link-item').each(function () {
			let ezd_note_content = $(this).attr('data-bs-original-title');
			let ezd_note_serial = $(this).attr('ezd-note-serial');
			let ezd_note_id = 'note-name-' + ezd_note_serial;
			$('.ezd-footnote-footer').append(
				"<div class='note-class-" + ezd_note_serial + "' id="+ezd_note_id+">" + '<div class="ezd-footnotes-serial">' + ezd_note_serial +
					'. <a href="#serial-id-'+ezd_note_serial+'"><svg fill="#0866ff" width="22px" height="22px" viewBox="0 5 36 5" version="1.1" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><title>arrow-line</title><path d="M27.66,15.61,18,6,8.34,15.61A1,1,0,1,0,9.75,17L17,9.81V28.94a1,1,0,1,0,2,0V9.81L26.25,17a1,1,0,0,0,1.41-1.42Z" class="clr-i-outline clr-i-outline-path-1"></path></svg></a></div><div class="ezd-footnote-texts">' +
					ezd_note_content + '</div></div>'
			);
			$('.note-class-' + ezd_note_serial).not(':first').remove();
		});

		$('.ezd-footnote-footer').children('span').remove();
		let ezd_child_div = $('.ezd-footnote-footer').children('div');

		if ( ezd_child_div.length > 0 ) {
			$('.ezd-footnote-title').css('display','flex');

			$('.ezd-footnote-title').on('click', function(){
				$(this).toggleClass('expanded');

				// Check if the 'expanded' class is present
				if ($(this).hasClass('expanded')) {
					// If 'expanded' class is present, remove 'collapsed' class
					$(this).removeClass('collapsed');
				} else {
					// If 'expanded' class is not present, add 'collapsed' class
					$(this).addClass('collapsed');

					$('.ezd-footnotes-link-item').on('click', function(){
						$('.ezd-footnote-title').addClass('expanded').removeClass('collapsed');
						$('.ezd-footnote-footer').css('display','block');
					});
				}

				$('.ezd-footnote-footer').slideToggle();
			});

			$('.ezd-footnotes-link-item').on('click', function(){
				$('.ezd-footnote-title').addClass('expanded').removeClass('collapsed');
				$('.ezd-footnote-footer').css('display','block');
			});
		}

	});
})(jQuery);