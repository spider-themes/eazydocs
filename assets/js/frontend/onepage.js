(function ($) {
	'use strict';

	$(document).ready(function () {
		/**
		 * Print doc
		 */
		$('.page-template-page-onepage .pageSideSection .print').on(
			'click',
			function (e) {
				e.preventDefault();
				$('.page-template-page-onepage #post').printThis();
			}
		);

		// $('body').attr({
		// 	'data-bs-spy': 'scroll',
		// 	'data-bs-target': '#eazydocs-toc',
		// });

		// Onepage menu
		$(window);
		var t = $(document.body),
			n = $('.doc-container').find('.doc-nav');
		// t.scrollspy({
		//     target: ".doc-sidebar"
		// })
		var t = new Gumshoe('#eazydocs-toc a', {
			nested: true,
			// nestedClass: 'active-parent',
		});
		n.find('> li > a').before($('<span class="docs-progress-bar" />'));
		n.offset().top;
		$(window).scroll(function () {
			$('.doc-nav').height();
			let t = $(this).scrollTop(),
				n = $(this).innerHeight(),
				e = $('.doc-nav li a').filter('.active').index();
			$('.doc-section').each(function (i) {
				let c = $(this).offset().top,
					s = $(this).height(),
					a = c + s,
					r = 0;
				t >= c && t <= a
					? (r = ((t - c) / s) * 100) >= 100 && (r = 100)
					: t > a && (r = 100),
					a < t + n - 70 && (r = 100);
				let d = $('.doc-nav .docs-progress-bar:eq(' + i + ')');
				e > i && d.parent().addClass('viewed'), d.css('width', r + '%');
			});
		});
		$(
			'.nav-sidebar.one-page-doc-nav-wrap .dropdown_nav .dropdown_nav'
		).addClass('doc-last-depth');
		$(
			'.nav-sidebar.fullscreen-layout-onepage-sidebar .dropdown_nav .dropdown_nav'
		).addClass('doc-last-depth-fullscreen');
		$('.doc-last-depth-fullscreen')
			.parent('.nav-item')
			.addClass('doc-last-depth-icon');
	});
})(jQuery);