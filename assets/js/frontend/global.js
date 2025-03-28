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
		 * Left Sidebar Toggle Icon
		 */
		if ($('.doc_documentation_area.fullscreen-layout').length > 0) {
			let leftSidebarOpen = false;

			$('#mobile-left-toggle').on('click', function (e) {
				e.preventDefault();

				if (!leftSidebarOpen) {
					$('.doc_documentation_area.fullscreen-layout').addClass('overlay');
					$('.fullscreen-layout .doc_mobile_menu.left-column')
						.addClass('opened')
						.animate({ left: '0px' }, 300);
					leftSidebarOpen = true;
				} else {
					closeLeftSidebar();
				}
			});

			// Close sidebar when clicking outside
			$(document).on('click', function (e) {
				if (
					leftSidebarOpen &&
					!$(e.target).closest('.doc_mobile_menu.left-column, #mobile-left-toggle').length
				) {
					closeLeftSidebar();
				}
			});

			function closeLeftSidebar() {
				$('.doc_documentation_area.fullscreen-layout').removeClass('overlay');
				$('.fullscreen-layout .doc_mobile_menu.left-column')
					.removeClass('opened')
					.animate({ left: '-300px' }, 300);
				leftSidebarOpen = false;
			}
		}
		
		/** Modal **/
		if ($('.modal-toggle').length > 0) {
			$(document).on('click', '.modal-toggle', function (e) {
				e.preventDefault();
				$('.ezd-modal-overlay').fadeIn();
				let id = $(this).data('id');
				$('.ezd-modal[data-id="modal' + id + '"]').fadeIn();
				
				// if click on esc fadeout it
				$(document).on('keyup', function (e) {
					if (e.keyCode === 27) {
						$('.ezd-modal-overlay').fadeOut();
						$('.ezd-modal').fadeOut();
					}
				});
				
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
		
		// Masonry [ Doc Archive ]
		function ezd_masonry_column() {

			var masonryCols = $(".ezd-masonry").attr("ezd-massonry-col");
			  var masonryColumns = parseInt(masonryCols);
			
			if ($(window).width() <= 1024 ) {
				  var masonryColumns = 2
			}
			
			if ($(window).width() <= 768 ) {
			  var masonryColumns = 1
			}
			
			var count = 0
			var content = $(".ezd-masonry > *");
			
			$(".ezd-masonry").before("<div class='ezd-masonry-columns'></div>");
		  
			content.each(function(index) {
				count = count + 1
				$(this).addClass("ezd-masonry-sort-" + count + "")
		
				if (count == masonryColumns) {
					count = 0
				}
			});
			
			for( var i = 0 + 1; i < masonryColumns + 1 ; i++ ) {
			  $(".ezd-masonry-columns").append("<div class='ezd-masonry-"+ i +"'></div>");
			  $(".ezd-masonry-sort-" + i).appendTo(".ezd-masonry-" + i);
			}
		  }
		  
		  ezd_masonry_column();

		$('.nav-sidebar.one-page-doc-nav-wrap .nav-item').click(function () {
			$(this).toggleClass('active');
			$(this).siblings().removeClass('active');
		});
		
    	// Prevent form submission when pressing Enter in the search input field
		$('#ezd_searchInput').on('keypress', function(event) {
			if (event.key === 'Enter') {
				event.preventDefault(); // Prevent form submission
			}
		});
		
	});
})(jQuery);
