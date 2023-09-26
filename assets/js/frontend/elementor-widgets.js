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
				let noresult = $('#docy-search-result').attr('data-noresult');
				if (keyword === '') {
					$('#docy-search-result')
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
								$('#docy-search-result')
									.addClass('ajax-search')
									.html(data);
								$('.spinner').hide();
							} else {
								var data_error = '<h5>' + noresult + '</h5>';
								$('#docy-search-result')
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
			$('#docy-search-result').css({ 'z-index': '9999' });
		});

		$('.header_search_form_info input[type=search]').focus(function () {
			let ezd_current_theme = $('body').hasClass('ezd-theme-docy');

			if (ezd_current_theme === true) {
				$('body').addClass('search-focused');
				$('.header_search_form_info').css({ 'z-index': '9999' });
			} else {
				$('body').addClass('search-focused');
				$('body.search-focused').prepend(
					'<div class="ezd_click_capture"></div>'
				);
				$('.ezd_click_capture').css({
					visibility: 'visible',
					opacity: '1',
					'z-index': '9999',
				});
				$('.header_search_form_info, #docy-search-result').css({
					'z-index': '9999',
				});
			}
		});

		$('.header_search_form_info input[type=search]').focusout(function () {
			$('body').removeClass('search-focused');
			$('.ezd_click_capture').css({
				visibility: 'hidden',
				opacity: '0',
				'z-index': '',
			});
			$('.header_search_form_info, #docy-search-result').css({
				'z-index': '',
			});
			$('.ezd_click_capture').remove();
		});

		$('#ezd_searchInput').on('input', function (e) {
			if ('' == this.value) {
				$('#docy-search-result').removeClass('ajax-search');
			}
		});
	});
})(jQuery);
