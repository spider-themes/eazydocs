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
			// 'data-bs-spy': 'scroll',
			'data-bs-target': '#eazydocs-toc',
		});

		/**
		 * Make the Titles clickable with anchor js
		 * If no selector is provided, it falls back to a default selector of:
		 * 'h2, h3, h4, h5, h6'
		 */
		if ( $('.doc-scrollable h2, .doc-scrollable h3, .doc-scrollable h4').length ) {
			anchors.add(
					'.doc-scrollable h2, .doc-scrollable h3, .doc-scrollable h4'
			);
		}

		// Anchor JS scroll
		var urlHash = window.location.href.split('#')[1];
		if (urlHash && $('#' + urlHash).length) {
			$('html,body').animate({
				scrollTop: $('#' + urlHash).offset().top
			}, 30);
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
			
			if ( nameDisallowedCharacters(name) ) {
				$('.form-name-field').append('<span class="ezd-input-warning-text">Special characters not allowed</span>');
				$('.ezd-input-warning-text:not(:last)').remove();
				// remove the error message after 3 seconds
				setTimeout(function() {
					$('.ezd-input-warning-text').remove();
				}, 3000);

		 	} else if ( emailDisallowedCharacters(email) ) {
				$('.form-email-field').append('<span class="ezd-input-warning-text">Invalid email format.</span>');
				$('.ezd-input-warning-text:not(:last)').remove();
				// remove the error message after 3 seconds
				setTimeout(function() {
					$('.ezd-input-warning-text').remove();
				}, 3000);

			} else {

				$.ajax({
					url: eazydocs_local_object.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'eazydocs_feedback_email',
						name: name,
						email: email,
						subject: subject,
						doc_id: doc_id,
						message: message,
						security: eazydocs_local_object.nonce,
					},
					beforeSend: function () {
						$('.eazydocs-form-result').html(
							'<div class="spinner-border spinner-border-sm" role="status">\n' +
								'<span class="visually-hidden">Loading...</span>\n' +
								'</div>'
						);
					},
					success: function (response) {
						if (response.success) {
							$('.eazydocs-form-result').html(response.data);
						} else {
							$('.eazydocs-form-result').html('Something went wrong.');
						}
						setTimeout(function () {
							$('.eazydocs-form-result').html('');
						}, 3000);
					},

					error: function () {
						$('.eazydocs-form-result').html(
							'Oops! Something wrong, try again!'
						);
					},
				});
				$('form#edocs-contact-form')[0].reset();
			}

			// Check for disallowed characters in the name field
			function nameDisallowedCharacters(str) {
				// Regular expression to check for disallowed characters
				var pattern = /[#$%^&*()+={}\[\];:'",<>\/?@]/;
				return pattern.test(str);
			}

			// Check for disallowed characters in the email field
			function emailDisallowedCharacters(email) {
				// Regular expression to check for disallowed characters
				var mailPattern = /[#$%^&*()+={}\[\];:'",<>\/?]/;	
				// Check if there is more than one "@" symbol
				var atSymbolCount = (email.match(/@/g) || []).length;				
				return atSymbolCount !== 1 || mailPattern.test(email);
			}
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
					security: eazydocs_local_object.nonce,
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
			$('.doc-middle-content .doc-post-content, body.single-onepage-docs .documentation_info').printThis({
				loadCSS:
					eazydocs_local_object.EAZYDOCS_FRONT_CSS + '/print.css',
			});
		});

		/**
		 * ============================
		 * Navbar Show / Hide Script
		 * ============================
		 * This script handles dynamic adjustments of the navbar's top margin based on user interactions:
		 * - Clicking internal page anchor links (.doc_menu and .ezd-note-indicator)
		 * - Clicking outside the anchor links
		 * - Scrolling the page
		 * ============================
		 */

		// Function to handle navbar margin adjustments
		function ezdSetNavbarMarginTop(active) {
			let height = $('.navbar.navbar_fixed').outerHeight(); // Get navbar height
			let adminBarOffset = $('body').hasClass('admin-bar') ? 32 : 0; // Check for admin bar
			let marginTop = active ? `-${height}px` : `${adminBarOffset}px`;

			// Apply the appropriate margin and toggle active class
			$('.navbar.navbar_fixed').toggleClass('doc_menu_active', active).css('margin-top', marginTop);
		}

		/**
		 * Handle link click for internal page navigation
		 * Applies to both .doc_menu and .ezd-note-indicator mean footnote elements.
		 */
		$('.doc_menu a[href^="#"]:not([href="#"]), .ezd-note-indicator').on('click', function (event) {
			event.preventDefault();
			ezdSetNavbarMarginTop(true); // Set navbar margin on link click
			$('html, body').stop().animate({
				scrollTop: $($(this).attr('href')).offset().top
			}, 900); // Smooth scroll animation
		});

		/**
		 * Handle click outside the target links
		 * Reset navbar margin when clicking outside the links.
		 */
		$(document).on('click', function (event) {
			if (!$(event.target).closest('.doc_menu a, .ezd-note-indicator').length) {
				ezdSetNavbarMarginTop(false);
			}
		});

		/**
		 * Handle scroll events
		 * Reset navbar margin when scrolled near the top.
		 */
		$(window).on('scroll', function () {
			if ($(this).scrollTop() <= $('.navbar.navbar_fixed').outerHeight()) {
				ezdSetNavbarMarginTop(false);
			}
		});

		/**
		 * ============================
		 * End of Navbar Show / Hide Script
		 * ============================
		 */
		
		// Ensure sidebars are sticky and toggle logic works properly
		if ($('.doc_documentation_area').length > 0) {
			var leftOpen = false;
			var rightOpen = false;

			// Right sidebar toggle
			$(document).on('click', '#mobile-right-toggle, .doc_rightsidebar.opened .doc_menu a', function (e) {
				e.preventDefault();
				if (leftOpen) closeLeftSidebar();

				if (!rightOpen) {
					$('.doc_documentation_area').addClass('overlay');
					$('.doc_rightsidebar').addClass('opened').animate({ right: '0px' }, 100);
					rightOpen = true;
				} else {
					closeRightSidebar();
				}
			});

			// Left sidebar toggle
			$(document).on('click', '#mobile-left-toggle', function (e) {
				e.preventDefault();
				if (rightOpen) closeRightSidebar();

				if (!leftOpen) {
					$('.doc_documentation_area').addClass('overlay');
					$('.left-column .doc_left_sidebarlist')
						.addClass('opened')
						.animate({ left: '0px' }, 300);
					leftOpen = true;
				} else {
					closeLeftSidebar();
				}
			});

			// Close sidebars when clicking outside
			$(document).on('click', function (e) {
				if (
					!$(e.target).closest('.doc_rightsidebar, .doc_rightsidebar.opened .doc_menu a, .left-column, #mobile-right-toggle, #mobile-left-toggle').length
				) {
					if (leftOpen) closeLeftSidebar();
					if (rightOpen) closeRightSidebar();
				}
			});

			function closeLeftSidebar() {
				var screenWidth = $(window).width(); // Get current window width
				var animationDistance = (screenWidth < 360) ? '-280px' : '-330px'; // Set distance based on screen width

				$('.doc_documentation_area').removeClass('overlay');
				$('.left-column .doc_left_sidebarlist')
					.removeClass('opened')
					.animate({ left: animationDistance }, 300);

				leftOpen = false;
			}

			function closeRightSidebar() {
				$('.doc_documentation_area').removeClass('overlay');
				$('.doc_rightsidebar').removeClass('opened').animate({ right: '-290px' }, 100);
				rightOpen = false;
			}
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

		function tocSidebarScrollHeight() {
			var leftSidebarScrollElement = $('.doc_left_sidebarlist  .ezd-scroll');
			if(leftSidebarScrollElement.length){
				var leftSidebarScrollOffset = leftSidebarScrollElement.position().top;
				var maxHeightLeftSidebar = `calc(100vh - ${leftSidebarScrollOffset }px)`;
				leftSidebarScrollElement.css('max-height', maxHeightLeftSidebar );

			}

			var rightSidebarScrollElement = $('.single-docs .doc_rightsidebar .toc_right');
			if(rightSidebarScrollElement.length){
				var rightSidebarScrollOffset = rightSidebarScrollElement.position().top;
				var maxHeightRightSidebar = `calc(100vh - ${rightSidebarScrollOffset + 70}px)`;
				rightSidebarScrollElement.css('max-height', maxHeightRightSidebar);
			}

		}
		tocSidebarScrollHeight();


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
			$('body.single-docs, body.single-onepage-docs').addClass('body_dark');
			$('body.single-onepage-docs').addClass('body_dark');
			$('.light-mode').removeClass('active');
			$('.dark-mode').addClass('active');
		}

		function applyDay() {
			$('body.single-docs, body.single-onepage-docs').removeClass('body_dark');
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
		$('#ezd-contributor-search').on('input', function () {
			let value = $(this).val().toLowerCase();
		  
			if (value === '') {
			  // Show all users when input is cleared
			  $('.users_wrap_item').show();
			} else {
			  // Filter the users based on input
			  $('.users_wrap_item').filter(function () {
				$(this).toggle(
				  $(this).text().toLowerCase().indexOf(value) > -1
				);
			  });
			}
		  });			
		
		/*
		* Font size switcher
		* Check if there are buttons for font size switcher
		*/

		if ($('#rvfs-controllers button').length) {
			var $speech = $('#post p, #post ul li:not(.process_tab_shortcode ul li), #post h1, #post h2, #post h3, #post h4, #post h5, #post h6, #post ol li, #post table:not(.basic_table_info,.table-dark), #post table tr td, #post .tab-content');
			var originalSizes = {};
		
			// Function to check if cookie exists and apply font size
			function checkFontSize() {
				var cookieFontSize = readCookie("fontSize");
				if (cookieFontSize) {
					var sizeFactor = parseFloat(cookieFontSize);
					$speech.each(function () {
						var tagName = $(this).prop('tagName').toLowerCase();
						var originalSize = originalSizes[tagName];
						$(this).css('fontSize', originalSize * sizeFactor + 'px');
					});
				}
			}		
			// Store the original font sizes
			$speech.each(function () {
				var tagName = $(this).prop('tagName').toLowerCase();
				if (!originalSizes[tagName]) {
					originalSizes[tagName] = parseFloat($(this).css('fontSize'));
				}
			});
		
			// Apply font size when page loads
			checkFontSize();
		
			// Event handler for font size buttons
			$(document).on('click', '#rvfs-controllers button', function () {
				var sizeFactor;
				var currentFactor = parseFloat(readCookie("fontSize")) || 1;
		
				switch (this.id) {
					case 'switcher-large':
						sizeFactor = currentFactor * 1.1;
						break;
					case 'switcher-small':
						sizeFactor = currentFactor / 1.1;
						break;
					case 'switcher-default':
						sizeFactor = 1;
						break;
					default:
						sizeFactor = 1;
				}
		
				if (sizeFactor === 1) {
					$speech.each(function () {
						var tagName = $(this).prop('tagName').toLowerCase();
						$(this).css('fontSize', originalSizes[tagName] + 'px');
					});
					eraseCookie("fontSize");
				} else {
					$speech.each(function () {
						var tagName = $(this).prop('tagName').toLowerCase();
						var originalSize = originalSizes[tagName];
						$(this).css('fontSize', originalSize * sizeFactor + 'px');
					});
					createCookie("fontSize", sizeFactor, 30);
				}
			});
		}
		
		/**
		 * Enables sticky behavior for the sidebar when it reaches the top of the viewport.
		 * This applies to both the left sidebar and mobile right sidebar in smaller screens.
		 */
		function ezd_sidebar_enable_sticky() {
			var $stickyElements = $('.left-column .doc_left_sidebarlist, .doc_right_mobile_menu .doc_rightsidebar'); // Select sidebar elements

			if ( $stickyElements.length === 0 ) return; // Exit if no target elements found

			var stickyOffset = $stickyElements.offset().top; // Get initial top position

			$(window).on('scroll', function() {
				var scrollTop = $(window).scrollTop(); // Current scroll position

				if (scrollTop >= stickyOffset) {
					$stickyElements.addClass('sticky');
				} else {
					$stickyElements.removeClass('sticky');
				}
			});
		}

		// Initialize the sticky sidebar function 
    	ezd_sidebar_enable_sticky();

		// Google Login script
		$(".ezd-google-login-btn").on("click", function(e) {
			e.preventDefault();
			var $btn = $(this);
			var googleUrl = $btn.data("href");

			// Open popup
			var width = 600;
			var height = 600;
			var left = (screen.width / 2) - (width / 2);
			var top = (screen.height / 2) - (height / 2);
			var popup = window.open(googleUrl, "GoogleLogin", "width=" + width + ",height=" + height + ",top=" + top + ",left=" + left);

			// Loading feedback
			var originalText = $btn.find("span").text();
			$btn.find("span").text("Connecting...");
			$btn.css("opacity", "0.7");

			// Reset after 10s
			setTimeout(function() {
				$btn.find("span").text(originalText);
				$btn.css("opacity", "1");
			}, 10000);
		});
	});
})(jQuery);

// Listen for messages from the popup window
window.addEventListener("message", function(event) {
    if (event.data.type === "google_login_success" && event.data.redirect) {
        window.location.href = event.data.redirect;
    }
});
