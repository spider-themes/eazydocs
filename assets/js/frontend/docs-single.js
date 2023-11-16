(function ($) {
	'use strict';

	$(document).ready(function () {
		/**
		 * Match Fade gradient Shadow color
		 * Used it for the fade gradient shadow on the Read More button
		 * @type {string}
		 */
		let bgColor = window
				.getComputedStyle(document.body, null)
				.getPropertyValue('background-color'),
			bgColorRGBA = bgColor.replace(')', ', 0)').replace('rgb', 'rgba');

		if (bgColor) {
			$('.fadeGradient').css(
				'background',
				'-webkit-linear-gradient(bottom, ' +
					bgColor +
					' 15%, ' +
					bgColorRGBA +
					' 100%)'
			);
		}

		/**
		 * Social Share options
		 * Copy the current page link to clipboard
		 */
		if ($('.share-this-doc').length) {
			$('.share-this-doc').on('click', function (e) {
				e.preventDefault();
				let success_message = $(this).data('success-message');
				let $temp = $('<input>');
				$('body').append($temp);
				$temp.val($(location).attr('href')).select();
				document.execCommand('copy');
				$temp.remove();

				setTimeout(function () {
					$('.ezd-link-copied-wrap')
						.text(success_message)
						.addClass('copied');
				}, 500);

				setTimeout(function () {
					$('.ezd-link-copied-wrap').removeClass('copied');
				}, 3500);
			});
		}

		$('.ezd-link-copied-wrap').click(function () {
			$(this).removeClass('copied');
		});

		$.fn.ezd_social_popup = function (
			e,
			intWidth,
			intHeight,
			strResize,
			blnResize
		) {
			// Prevent default anchor event
			e.preventDefault();

			// Set values for window
			intWidth = intWidth || '500';
			intHeight = intHeight || '400';
			strResize = blnResize ? 'yes' : 'no';

			// Set title and open popup with focus on it
			var strTitle =
					typeof this.attr('title') !== 'undefined'
						? this.attr('title')
						: 'Social Share',
				strParam =
					'width=' +
					intWidth +
					',height=' +
					intHeight +
					',resizable=' +
					strResize,
				objWindow = window
					.open(this.attr('href'), strTitle, strParam)
					.focus();
		};
		$('.social-links a:not(:first)').on('click', function (e) {
			$(this).ezd_social_popup(e);
		});

		// Add scroll spy attributes to body
		$('body').attr({
			'data-bs-spy': 'scroll',
			'data-bs-target': '#eazydocs-toc',
		});

		/**
		 * Make the Titles clickable with anchor js
		 * If no selector is provided, it falls back to a default selector of:
		 * 'h2, h3, h4, h5, h6'
		 */
		anchors.add(
			'.doc-scrollable h2, .doc-scrollable h3, .doc-scrollable h4'
		);
		// Anchor JS scroll
		var urlHash = window.location.href.split('#')[1];
		if (urlHash) {
			$('html,body').animate(
				{
					scrollTop: $('#' + urlHash).offset().top,
				},
				30
			);
		}

		/**
		 * Feedback Contact Form Ajax Handler
		 */
		$('form#edocs-contact-form').on('submit', function (e) {
			e.preventDefault();
			let that = $(this),
				url = that.attr('action'),
				type = that.attr('method');
			let name = $('#name').val();
			let email = $('#email').val();
			let subject = $('#subject').val();
			let doc_id = $('#doc_id').val();
			let message = $('#massage').val();
			$.ajax({
				url: eazydocs_local_object.ajaxurl,
				type: 'post',
				dataType: 'text',
				data: {
					action: 'eazydocs_feedback_email',
					name: name,
					email: email,
					subject: subject,
					doc_id: doc_id,
					message: message,
				},
				beforeSend: function () {
					$('.eazydocs-form-result').html(
						'<div class="spinner-border spinner-border-sm" role="status">\n' +
							'<span class="visually-hidden">Loading...</span>\n' +
							'</div>'
					);
				},
				success: function (response) {
					$('.eazydocs-form-result').html(
						'Your message has been sent successfully.'
					);
				},
				error: function () {
					$('.eazydocs-form-result').html(
						'Oops! Something wrong, try again!'
					);
				},
			});
			$('form#edocs-contact-form')[0].reset();
		});

		/**
		 * Feedback voting Handler
		 */
		$(document).on('click', '.vote-link-wrap a.h_btn', function (e) {
			e.preventDefault();
			let self = $(this);
			$.ajax({
				url: eazydocs_local_object.ajaxurl,
				method: 'post',
				data: {
					action: 'eazydocs_handle_feedback',
					post_id: self.data('id'),
					type: self.data('type'),
					_wpnonce: eazydocs_local_object.nonce,
				},
				beforeSend: function () {
					$('.eazydocs-feedback-wrap .vote-link-wrap').html(
						'<div class="spinner-border spinner-border-sm" role="status">\n' +
							'  <span class="visually-hidden">Loading...</span>\n' +
							'</div>'
					);
				},
				success: function (response) {
					$('.eazydocs-feedback-wrap').html(response.data);
				},
				error: function () {
					console.log('Oops! Something wrong, try again!');
				},
			});
		});

		/**
		 * Expand and collapse the sidebar menu on clicking on the arrow icon
		 */
		if ($('.nav-sidebar > li').hasClass('active')) {
			$('.nav-sidebar > li.active').find('ul').slideDown(700);
		}

		// Handle click event for all .icon elements inside .nav-sidebar
		$('.nav-sidebar').on('click', '.doc-link .icon', function(e) {
			// Prevent default action
			e.preventDefault();

			// Find the closest parent li element and toggle its active class
			let parentLi = $(this).closest('li.nav-item');
			parentLi.toggleClass('active');

			// Toggle the display of the immediate child ul element
			parentLi.children('ul.dropdown_nav').toggle(300);

			// Hide sibling submenus
			parentLi.siblings().find('ul.dropdown_nav').hide(300);
			parentLi.siblings().removeClass('active');
		});


		/**
		 * Print doc
		 */
		$('.pageSideSection .print').on('click', function (e) {
			e.preventDefault();
			$('.doc-middle-content .doc-post-content').printThis({
				loadCSS:
					eazydocs_local_object.EAZYDOCS_FRONT_CSS + '/print.css',
			});
		});

		/**
		 * TOC Menu
		 */
		$('.doc_menu a[href^="#"]:not([href="#"]').on(
			'click',
			function (event) {
				var $anchor = $(this);
				$('html, body')
					.stop()
					.animate(
						{
							scrollTop: $($anchor.attr('href')).offset().top,
						},
						900
					);
				event.preventDefault();
			}
		);

		/**
		 * Left Sidebar Toggle icon
		 */
		if ($('.doc_documentation_area').length > 0) {
			//switcher
			var switchs = true;
			$(document).on('click', '#mobile-right-toggle', function (e) {
				e.preventDefault();
				if (switchs) {
					$('.doc_documentation_area').addClass('overlay');
					$('.doc_rightsidebar').addClass('opened').animate(
						{
							right: '0px',
						},
						100
					);
					switchs = false;
				} else {
					$('.doc_documentation_area').removeClass('overlay');
					$('.doc_rightsidebar').removeClass('opened').animate(
						{
							right: '-290px',
						},
						100
					);
					switchs = true;
				}
			});

			$(document).on('click', '#mobile-left-toggle', function (e) {
				e.preventDefault();
				if (switchs) {
					$('.doc_documentation_area').addClass('overlay');
					$('.left-column .doc_left_sidebarlist')
						.addClass('opened')
						.animate(
							{
								left: '0px',
							},
							300
						);
					switchs = false;
				} else {
					$('.doc_documentation_area').removeClass('overlay');
					$('.left-column .doc_left_sidebarlist')
						.removeClass('opened')
						.animate(
							{
								left: '-330px',
							},
							300
						);
					switchs = true;
				}
			});
		}

		// Mobile menu on the Doc single page
		$(document).on('click', '.single-docs .mobile_menu_btn', function () {
			$('body').removeClass('menu-is-closed').addClass('menu-is-opened');
		});

		$(document).on('click', '.single-docs .close_nav', function (e) {
			if ($('.side_menu').hasClass('menu-opened')) {
				$('.side_menu').removeClass('menu-opened');
				$('body').removeClass('menu-is-opened');
			} else {
				$('.side_menu').addClass('menu-opened');
			}
		});

		/**
		 * Filter doc menu items on the left sidebar
		 **/
		if ($('#doc_filter').length) {
			$('#doc_filter').keyup(function () {
				var value = $(this).val().toLowerCase();
				$('.nav-sidebar .page_item').each(function () {
					var lcval = $(this).text().toLowerCase();
					if (lcval.indexOf(value) > -1) {
						$(this).show(500);
					} else {
						$(this).hide(500);
					}
					if (value.length > 0) {
						$('.left-sidebar-results')
							.find('li')
							.addClass('active');
						$('.left-sidebar-results').find('li > ul').show(500);
					} else {
						$('.left-sidebar-results')
							.find('li')
							.removeClass('active');
						$('.left-sidebar-results').find('li > ul').hide(500);
					}
				});
			});

			document
				.getElementById('doc_filter')
				.addEventListener('search', function (event) {
					$('.nav-sidebar .page_item').show(300);
					$('.left-sidebar-results').find('li').removeClass('active');
					$('.left-sidebar-results').find('li > ul').hide(500);
				});
		}

		/**
		 * Collapse left sidebar
		 **/
		function docLeftSidebarToggle() {
			let left_column = $('.doc_mobile_menu');
			let middle_column = $('.doc-middle-content');
			$(document).on(
				'click',
				'.left-sidebar-toggle .left-arrow',
				function () {
					$('.doc_mobile_menu').hide(500);

					if (middle_column.hasClass('ezd-xl-col-7')) {
						$('.doc-middle-content')
							.removeClass('ezd-xl-col-7')
							.addClass('ezd-xl-col-10 ezd-col-extended');
					} else if (middle_column.hasClass('ezd-xl-col-8')) {
						$('.doc-middle-content')
							.removeClass('ezd-xl-col-8')
							.addClass('ezd-xl-col-10');
					}

					$('.left-sidebar-toggle .left-arrow').hide(500);
					$('.left-sidebar-toggle .right-arrow').show(500);
				}
			);

			$(document).on(
				'click',
				'.left-sidebar-toggle .right-arrow',
				function () {
					$('.doc_mobile_menu').show(500);

					if (middle_column.hasClass('ezd-xl-col-10')) {
						$('.doc-middle-content')
							.removeClass('ezd-xl-col-10 ezd-col-extended')
							.addClass('ezd-xl-col-7');
					} else if (middle_column.hasClass('ezd-xl-col-8')) {
						$('.doc-middle-content')
							.removeClass('ezd-xl-col-10 ezd-col-extended')
							.addClass('ezd-xl-col-8');
					}

					$('.left-sidebar-toggle .left-arrow').show(500);
					$('.left-sidebar-toggle .right-arrow').hide(500);
				}
			);
		}

		docLeftSidebarToggle();

		//  page scroll
		function bodyFixed() {
			let windowWidth = $(window).width();
			let middle_column = $('.doc-middle-content');
			
			if ($('#sticky_doc').length) {
				if (windowWidth > 576) {
					let tops = $('#sticky_doc');
					let leftOffset = tops.offset().top;

					$(window).on('scroll', function () {
						let scroll = $(window).scrollTop();
						if (scroll >= leftOffset) {
							tops.addClass('body_fixed');
						} else {
							tops.removeClass('body_fixed');
						}
					});
				}
			}
		}

		bodyFixed();

		// TOC area
		function bodyFixed2() {
			var windowWidth = $(window).width();

			if ($('#sticky_doc2').length) {
				if (windowWidth > 576) {
					var tops = $('#sticky_doc2');
					var topOffset = tops.offset().top;
					var blogForm = $('.blog_comment_box');
					var blogFormTop = blogForm.offset().top - 300;

					$(window).on('scroll', function () {
						var scrolls = $(window).scrollTop();
						if (scrolls >= topOffset) {
							tops.addClass('stick');
						} else {
							tops.removeClass('stick');
						}
					});

					$('a[href="#hackers"]').click(function () {
						$('#hackers').css('padding-top', '100px');

						$(window).on('scroll', function () {
							var hackersOffset = $('#hackers').offset().top;
							var scrolls = $(window).scrollTop();
							if (scrolls < hackersOffset) {
								$('#hackers').css('padding-top', '0px');
							}
						});
					});
				}
			}
		}

		bodyFixed2();

		/*  Menu Click js  */
		if ($('.submenu').length) {
			$('.submenu > .dropdown-toggle').click(function () {
				var location = $(this).attr('href');
				window.location.href = location;
				return false;
			});
		}

		/*------------ Cookie functions and color js ------------*/
		function createCookie(name, value, days) {
			var expires = '';
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
				expires = '; expires=' + date.toUTCString();
			}
			document.cookie = name + '=' + value + expires + '; path=/';
		}

		function readCookie(name) {
			var nameEQ = name + '=';
			var ca = document.cookie.split(';');
			for (var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) == 0)
					return c.substring(nameEQ.length, c.length);
			}
			return null;
		}

		function eraseCookie(name) {
			createCookie(name, '', -1);
		}

		/**
		 * Dark mode switcher
		 * @type {boolean}
		 */
		let prefersDark =
			window.matchMedia &&
			window.matchMedia('(prefers-color-scheme: dark)').matches;
		let selectedNightTheme = readCookie('body_dark');

		if (
			selectedNightTheme == 'true' ||
			(selectedNightTheme === null && prefersDark)
		) {
			applyNight();
			$('#ezd_dark_switch').prop('checked', true);
		} else {
			applyDay();
			$('#ezd_dark_switch').prop('checked', false);
		}

		function applyNight() {
			$('body').addClass('body_dark');
			$('.light-mode').removeClass('active');
			$('.dark-mode').addClass('active');
		}

		function applyDay() {
			$('body').removeClass('body_dark');
			$('.dark-mode').removeClass('active');
			$('.light-mode').addClass('active');
		}

		$('#ezd_dark_switch').change(function () {
			if ($(this).is(':checked')) {
				applyNight();
				$('.tab-btns').removeClass('active');
				createCookie('body_dark', true, 999);
			} else {
				applyDay();
				$('.tab-btns').addClass('active');
				createCookie('body_dark', false, 999);
			}
		});

		// CONTRIBUTOR SEARCH
		$('#ezd-contributor-search').on('keyup', function () {
			let value = $(this).val().toLowerCase();
			$('.users_wrap_item').filter(function () {
				$(this).toggle(
					$(this).text().toLowerCase().indexOf(value) > -1
				);
			});
		});

		// Font size switcher
		if ($('#rvfs-controllers button').length) {
			var $speech = $(
				'#post p, #post ul li:not(.process_tab_shortcode ul li), #post ol li, #post table:not(.basic_table_info,.table-dark), #post table tr td, #post .tab-content'
			);
			var $defaultSize = $speech.css('fontSize');
			$(document).on('click', '#rvfs-controllers button', function () {
				var num = parseFloat($speech.css('fontSize'));
				switch (this.id) {
					case 'switcher-large':
						num *= 1.1;
						break;
					case 'switcher-small':
						num /= 1.1;
						break;
					default:
						num = parseFloat($defaultSize);
				}
				$speech.animate({ fontSize: num + 'px' });
			});
		}
	});
})(jQuery);
