(function ($) {
	$(document).ready(function () {
		/**
		 * Search results
		 */
		function fetchDelay(callback, ms) {
			var timer = 0;
			return function () {
				var context = this,
					args = arguments;
				clearTimeout(timer);
				timer = setTimeout(function () {
					callback.apply(context, args);
				}, ms || 0);
			};
		}

		$('#ezd_searchInput').keyup(
			fetchDelay(function (e) {
				let keyword = $('#ezd_searchInput').val();
				let noresult = $('#ezd-search-results').attr('data-noresult');
				if (keyword === '') {
					$('#ezd-search-results')
						.removeClass('ajax-search')
						.html('');
				} else {
					$.ajax({
						url: eazydocs_local_object.ajaxurl,
						type: 'post',
						data: {
							action: 'eazydocs_search_results',
							keyword: keyword,
						},
						beforeSend: function () {
							$('.spinner').css('display', 'block');
						},
						success: function (data) {
							if (data.length > 0) {
								$('#ezd-search-results')
									.addClass('ajax-search')
									.html(data);
								$('.spinner').hide();
							} else {
								var data_error = '<h5>' + noresult + '</h5>';
								$('#ezd-search-results')
									.removeClass('ajax-search')
									.html(data_error);
							}
						},
					});
				}
			}, 500)
		);

		$('.header_search_keyword ul li a').on('click', function (e) {
			e.preventDefault();
			var content = $(this).text();
			$('#ezd_searchInput').val(content).focus();
			$('#ezd_searchInput').keyup();
			$('#ezd-search-results').css({ 'z-index': '9999' });
		});

		$('.header_search_form_info input[type=search]').focus(function () {
			let ezd_current_theme = $('body').hasClass('ezd-theme-docy');

			if (ezd_current_theme === true) {
				$('body').addClass('ezd-search-focused');
				$('.header_search_form_info').css({ 'z-index': '9999' });
			} else {
				$('body').addClass('ezd-search-focused');
				$('body.ezd-search-focused').prepend(
					'<div class="ezd_click_capture"></div>'
				);
				$('.ezd_click_capture').css({
					visibility: 'visible',
					opacity: '1',
					'z-index': '9999',
				});
				$('.header_search_form_info, #ezd-search-results').css({
					'z-index': '9999',
				});
			}
		});

		$('.header_search_form_info input[type=search]').focusout(function () {
			$('body').removeClass('ezd-search-focused');
			$('.ezd_click_capture').css({
				visibility: 'hidden',
				opacity: '0',
				'z-index': '',
			});
			$('.header_search_form_info, #ezd-search-results').css({
				'z-index': '',
			});
			$('.ezd_click_capture').remove();
		});

		$('#ezd_searchInput').on('input', function (e) {
			if ('' == this.value) {
				$('#ezd-search-results').removeClass('ajax-search');
			}
		});
	});
})(jQuery);
