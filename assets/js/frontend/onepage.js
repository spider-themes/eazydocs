(function ($) {
	'use strict';

	$(document).ready(function () {
		// Note: the Print button (.pageSideSection .print) is handled globally
		// in docs-single.js, which builds a clean printout and guards against
		// repeat clicks — including for one-page docs. A second handler here used
		// to double-fire the print dialog on the page-onepage template.

		// Highlight the active nav item while scrolling, and animate a progress
		// bar across each top-level section link as it is read.
		var n = $('.doc-container').find('.doc-nav');

		new Gumshoe('#eazydocs-toc a', {
			nested: true,
		});

		n.find('> li > a').before($('<span class="docs-progress-bar" />'));

		$(window).scroll(function () {
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

		/**
		 * Classic One-Page layout: off-canvas navigation sidebar (mobile) and
		 * smooth in-page scrolling. The fullscreen layout is handled separately
		 * in global.js, so this block is scoped to the classic layout only.
		 */
		var $classic = $('.classic-onepage');

		if ($classic.length) {
			var $trigger = $('#mobile-left-toggle');
			var $close = $('#mobile-left-close');
			var $overlay = $('#ezd-classic-overlay');
			var $panel = $classic.find('.doc_mobile_menu.left-column');
			var mobileQuery = window.matchMedia('(max-width: 991px)');

			var openSidebar = function () {
				// The tools (right) sidebar is managed by docs-single.js; close
				// it first so the two off-canvas panels never overlap (this
				// mirrors the mutual-exclusion docs-single.js applied before we
				// took ownership of the left toggle below).
				if ($('.doc_rightsidebar').hasClass('opened')) {
					$('#mobile-right-toggle').trigger('click');
				}
				$panel.addClass('opened');
				$classic.addClass('sidebar-open');
				// Lift the themed content wrapper above the site header so the
				// drawer is not trapped behind the navbar (see onepage.scss).
				$('body').addClass('ezd-onepage-drawer-open');
				$trigger.attr('aria-expanded', 'true');
			};

			var closeSidebar = function () {
				$panel.removeClass('opened');
				$classic.removeClass('sidebar-open');
				$('body').removeClass('ezd-onepage-drawer-open');
				$trigger.attr('aria-expanded', 'false');
			};

			$trigger.on('click', function (e) {
				e.preventDefault();
				// Stop the click from also reaching docs-single.js, which binds
				// the same #mobile-left-toggle and would run a second, conflicting
				// open/close animation on the inner <aside> (leaving a stray
				// inline `left` style). onepage.js is the sole owner here.
				e.stopPropagation();
				if ($panel.hasClass('opened')) {
					closeSidebar();
				} else {
					openSidebar();
				}
			});

			$close.on('click', function (e) {
				e.preventDefault();
				closeSidebar();
			});

			$overlay.on('click', closeSidebar);

			$(document).on('keyup', function (e) {
				if (e.key === 'Escape' || e.keyCode === 27) {
					closeSidebar();
				}
			});

			// Smooth-scroll to the target section (accounting for sticky offset)
			// and close the off-canvas panel after a choice is made on mobile.
			$classic
				.find('.op-docs-sidebar a[href^="#"]')
				.on('click', function (e) {
					var hash = $(this).attr('href');
					var $target =
						hash && hash.length > 1 ? $(hash) : $();

					if ($target.length) {
						e.preventDefault();
						$('html, body')
							.stop()
							.animate(
								{ scrollTop: $target.offset().top - 90 },
								450
							);
					}

					if (mobileQuery.matches) {
						closeSidebar();
					}
				});
		}
	});
})(jQuery);