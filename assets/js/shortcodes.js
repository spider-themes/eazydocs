(function ($) {
	'use strict';

	$(document).ready(function () {
		// EAZYDOCS FOOTNOTE
		let ezd_note_column = $('.ezd-footnote-footer').attr('ezd-data-column');
		$('.ezd-footnote-footer').css('column-count', ezd_note_column);

		$('.ezd-footnotes-link-item').each(function () {
			let ezd_note_content = $(this).attr('data-bs-original-title');
			let ezd_note_serial = $(this).attr('ezd-note-serial');
			$('.ezd-footnote-footer').append(
				"<div class='note-class-" +
					ezd_note_serial +
					"' id='note-name-" +
					ezd_note_serial +
					"'>" +
					'<span class="ezd-footnotes-serial">' +
					ezd_note_serial +
					'.</span> ' +
					ezd_note_content +
					'</div>'
			);
			$('.note-class-' + ezd_note_serial)
				.not(':first')
				.remove();
		});

		$('.ezd-footnote-footer').children('span').remove();
		let ezd_child_div = $('.ezd-footnote-footer').children('div');
		if (ezd_child_div.length > 0) {
			$('.ezd-footnote-title').show();
		}
	});
})(jQuery);
