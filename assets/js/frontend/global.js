(function ($) {
	'use strict';

	$(document).ready(function () {
		/**
		 * Make the overflow of ancestor Elements to visible of Position Sticky Element
		 * @type {HTMLElement}
		 */
		let parent_selector = document.querySelector('.ezd-sticky-lg-top');
		if (parent_selector) {
			let parent = parent_selector.parentElement;
			while (parent) {
				const hasOverflow = getComputedStyle(parent).overflow;
				if (hasOverflow !== 'visible') {
					parent.style.overflow = 'visible';
				}
				parent = parent.parentElement;
			}
		}

		$('.body_wrapper').addClass('eazydocs_assistant_body');

		$(window).scroll(function () {
			$('.doc-book-layout .nav-sidebar li a').filter('.nav-link').index();
		});

		/**
		 * Left Sidebar Toggle icon
		 */
		if ($('.doc_documentation_area.fullscreen-layout').length > 0) {
			//switcher
			var switchs = true;
			$('#mobile-right-toggle').on('click', function (e) {
				e.preventDefault();
				if (switchs) {
					$('.doc_documentation_area.fullscreen-layout').addClass(
						'overlay'
					);

					$(this).animate(
						{
							right: '250px',
						},
						500
					);

					switchs = false;
				} else {
					$('.doc_documentation_area.fullscreen-layout').removeClass(
						'overlay'
					);

					$(this).animate(
						{
							right: '0px',
						},
						500
					);

					switchs = true;
				}
			});

			$('#mobile-left-toggle').on('click', function (e) {
				e.preventDefault();
				if (switchs) {
					$('.doc_documentation_area.fullscreen-layout').addClass(
						'overlay'
					);
					$(
						'.fullscreen-layout .doc_mobile_menu.left-column'
					).animate(
						{
							left: '0px',
						},
						300
					);
					switchs = false;
				} else {
					$('.doc_documentation_area.fullscreen-layout').removeClass(
						'overlay'
					);
					$(
						'.fullscreen-layout .doc_mobile_menu.left-column'
					).animate(
						{
							left: '-260px',
						},
						300
					);
					switchs = true;
				}
			});
		}

		/** Modal **/
		if ($('.modal-toggle').length > 0) {
			$(document).on('click', '.modal-toggle', function (e) {
				e.preventDefault();
				$('.ezd-modal-overlay').fadeIn();
				let id = $(this).data('id');
				$('.ezd-modal[data-id="modal' + id + '"]').fadeIn();
			});
			$(document).on('click', '.ezd-close', function () {
				$('.ezd-modal-overlay').fadeOut();
				$('.ezd-modal').fadeOut();
			});
			$(document).on('click', '.ezd-modal-overlay', function () {
				$('.ezd-modal-overlay').fadeOut();
				$('.ezd-modal').fadeOut();
			});
		}

		if ($('#eazydocs-toc a,.book-chapter-nav a').length > 0) {
			let spy = new Gumshoe('#eazydocs-toc a,.book-chapter-nav a', {
				nested: true,
			});
		}
	});
})(jQuery);
