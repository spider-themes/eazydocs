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
				"<div class='note-class-" + ezd_note_serial + "' id="+ezd_note_id+">" +
					'<div class="ezd-footnotes-serial"> ' +
						'<spna class="ezd-serial">' + ezd_note_serial + '. </spna> ' +
						'<a class="ezd-note-indicator" href="#serial-id-'+ezd_note_serial+'">' +
							'<i class="arrow_carrot-up"></i> ' +
						'</a>' +
					'</div>' +
					'<div class="ezd-footnote-texts">' + ezd_note_content + '</div>' +
				'</div>'
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