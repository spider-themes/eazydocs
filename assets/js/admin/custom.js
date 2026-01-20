(function ($) {
	'use strict';
	$(document).ready(function () {
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
		 * Activate a Docs Builder left sidebar item + its tab panel.
		 *
		 * @param {string} tabId Example: "tab-123"
		 * @param {boolean} saveCookie Whether to persist as the last active tab
		 */
		function ezd_activate_doc_tab(tabId, saveCookie) {
			if (!tabId) {
				return false;
			}

			var $nav = $('.tab-menu .easydocs-navitem[data-rel="' + tabId + '"]');
			var $tab = $('#' + tabId);

			// If the target tab doesn't exist (e.g. doc deleted), bail.
			if (!$nav.length || !$tab.length) {
				return false;
			}

			// Menu item
			$('.tab-menu .easydocs-navitem').removeClass('is-active').attr('aria-selected', 'false');
			$nav.addClass('is-active').attr('aria-selected', 'true');

			// Tab content
			$('.easydocs-tab-content .easydocs-tab').removeClass('tab-active').hide();
			$tab.addClass('tab-active').fadeIn('slow');

			if (saveCookie) {
				createCookie('eazydocs_doc_current_tab', tabId, 999);
			}

			return true;
		}

		// Filter Select
		if ($('select').length > 0) {
			$('select').niceSelect();
		}

		// Sidebar Tabs [COOKIE]
		$(document).on('click keydown', '.tab-menu .easydocs-navitem', function (e) {

			// Ignore if clicking/typing on interactive children like "Edit" or "Delete" links
			if ($(e.target).closest('a, button, input, .ezd-admin-bulk-options').length) {
				return;
			}

			if (e.type === 'keydown') {
				if (e.which !== 13 && e.which !== 32) {
					return; // Ignore other keys
				}
				e.preventDefault(); // Prevent scrolling for Space
			}

			// REMOVE ?tab=something ONLY AFTER CLICK
			const url = new URL(window.location.href);
			if (url.searchParams.has('more_state')) {
				url.searchParams.delete('more_state');
				window.history.replaceState({}, document.title, url.toString());
			}

			let target = $(this).attr('data-rel');
			ezd_activate_doc_tab(target, true);
		});
		
		// Remain the last active doc tab
		function keep_last_active_doc_tab() {
			let doc_last_current_tab = readCookie('eazydocs_doc_current_tab');
			if (doc_last_current_tab && ezd_activate_doc_tab(doc_last_current_tab, false)) {
				return;
			}

			// Fallback: activate first available tab
			var $first = $('.tab-menu .easydocs-navitem:first');
			var firstTabId = $first.attr('data-rel');
			ezd_activate_doc_tab(firstTabId, true);
		}

		// Check URL parameter tab
		function ezd_check_url_more_state() {
			const urlParams  = new URLSearchParams(window.location.search);
			const more_state = urlParams.get('more_state');

			if (more_state) {
				// Activate target tab + save
				ezd_activate_doc_tab(more_state, true);

				return true;
			}

			return false;
		}

		// First check URL parameter tab
		if ( ! ezd_check_url_more_state() ) {
			// If no tab in URL, use cookie
			keep_last_active_doc_tab();
		}

		$('.tab-menu .easydocs-navitem .parent-delete').on( 'click', function () {
			return false;
		} );

		$(document).ready(function (e) {
			function t(t) {
				e(t).bind('click', function (t) {
					t.preventDefault();
					e(this).parent().fadeOut();
				});
			}

			e('.header-notify-icon').click(function () {
				var t = e(this)
					.parents('.easydocs-notification')
					.children('.easydocs-dropdown')
					.is(':hidden');
				e('.easydocs-notification .easydocs-dropdown').hide();
				e('.easydocs-notification .header-notify-icon').removeClass(
					'active'
				);
				if (t) {
					e(this)
						.parents('.easydocs-notification')
						.children('.easydocs-dropdown')
						.toggle()
						.parents('.easydocs-notification')
						.children('.header-notify-icon')
						.addClass('active');
				}
			});
			e(document).bind('click', function (t) {
				var n = e(t.target);
				if (!n.parents().hasClass('easydocs-notification'))
					e('.easydocs-notification .easydocs-dropdown').hide();
			});
			e(document).bind('click', function (t) {
				var n = e(t.target);
				if (!n.parents().hasClass('easydocs-notification'))
					e('.easydocs-notification .header-notify-icon').removeClass(
						'active'
					);
			});

			// Active new created doc tab				
			const urlParams  = new URLSearchParams(window.location.search);
			const newDocId 	 = urlParams.get('new_doc_id');

			if (newDocId) {
				const tabId = 'tab-' + newDocId;
				ezd_activate_doc_tab(tabId, true);

				// Clean URL
				const url = new URL(window.location.href);
				url.searchParams.delete('new_doc_id');
				window.history.replaceState({}, document.title, url.toString());
			} else {
				// Fallback to previously active tab
				keep_last_active_doc_tab();
			}
		});

		// NEW DOC
		function add_new_doc() {
			$(document).on('click', '#new-doc', function (e) {
				e.preventDefault();
				let href = $(this).attr('href');
				Swal.fire({
					title: eazydocs_local_object.create_prompt_title,
					input: 'text',
					showCancelButton: true,
					inputAttributes: {
						name: 'new_doc',
					},
				}).then((result) => {
					if (result.value) {
						let results = result.value.replaceAll(
							'&',
							'ezd_ampersand'
						);
						results = results.replaceAll('#', 'ezd_hash');
						results = results.replaceAll('+', 'ezd_plus');

						document.location.href = href + results;
					}
				});
			});
		}

		add_new_doc();

		// ADD PARENT DOC
		function add_parent_doc() {
			$(document).on('click', '#parent-doc', function (e) {
				e.preventDefault();
				let href = $(this).attr('data-url');
				Swal.fire({
					title: eazydocs_local_object.create_prompt_title,
					input: 'text',
					showCancelButton: true,
					inputAttributes: {
						name: 'parent_title',
					},
				}).then((result) => {
					if (result.value) {
						let results = result.value.replaceAll(
							'&',
							'ezd_ampersand'
						);
						results = results.replaceAll('#', 'ezd_hash');
						results = results.replaceAll('+', 'ezd_plus');

						document.location.href = href + results;
					}
				});
			});
		}

		add_parent_doc();

		// SECTION DOC
		function create_section_doc() {
			$(document).on('click', '#section-doc', function (e) {
				e.preventDefault();
				let href = $(this).attr('data-url');
				Swal.fire({
					title: eazydocs_local_object.create_prompt_title,
					input: 'text',
					showCancelButton: true,
					inputAttributes: {
						name: 'section',
					},
				}).then((result) => {
					if (result.value) {
						let results = result.value.replaceAll(
							'&',
							'ezd_ampersand'
						);
						results = results.replaceAll('#', 'ezd_hash');
						results = results.replaceAll('+', 'ezd_plus');

						document.location.href = href + results;
					}
				});
			});
		}

		create_section_doc();

		// ADD CHILD DOC
		function add_child_doc() {
			$('.child-doc').on('click', function (e) {
				e.preventDefault();
				let href = $(this).attr('href');
				Swal.fire({
					title: eazydocs_local_object.create_prompt_title,
					input: 'text',
					showCancelButton: true,
					inputAttributes: {
						name: 'child_title',
					},
				}).then((result) => {
					if (result.value) {
						let results = result.value.replaceAll(
							'&',
							'ezd_ampersand'
						);
						results = results.replaceAll('#', 'ezd_hash');
						results = results.replaceAll('+', 'ezd_plus');

						document.location.href = href + results;
					}
				});
			});
		}

		add_child_doc();

		// Delete parent doc
		function delete_parent_doc() {
			$('.parent-delete').on('click', function (e) {
				e.preventDefault();
				let href = $(this).attr('href');
				Swal.fire({
					title: eazydocs_local_object.delete_prompt_title,
					text: eazydocs_local_object.no_revert_title,
					icon: 'question',
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: 'Yes, delete it!',
				}).then((result) => {
					if (result.value) {
						document.location.href = href;
					}
				});
			});
		}

		delete_parent_doc();

		// DELETE DOC SECTION
		function delete_doc_sec() {
			$('.section-delete').on('click', function (e) {
				e.preventDefault();
				const href = $(this).attr('href');
				Swal.fire({
					title: eazydocs_local_object.delete_prompt_title,
					text: eazydocs_local_object.no_revert_title,
					icon: 'question',
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: 'Yes, delete it!',
				}).then((result) => {
					if (result.value) {
						document.location.href = href;
					}
				});
			});
		}

		delete_doc_sec();

		// DELETE CHILD DOC
		function delete_child_doc() {
			$('.child-delete').on('click', function (e) {
				e.preventDefault();
				const href = $(this).attr('href');
				Swal.fire({
					title: eazydocs_local_object.delete_prompt_title,
					text: eazydocs_local_object.no_revert_title,
					icon: 'question',
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: 'Yes, delete it!',
				}).then((result) => {
					if (result.value) {
						document.location.href = href;
					}
				});
			});
		}

		delete_child_doc();

		// Docs Search on the Docs Builder UI page
		if ($('#easydocs-search').length > 0) {
			$('#easydocs-search').on('keyup', function () {
				var value = $(this).val().toLowerCase();
				$('.easydocs-accordion-item').filter(function () {
					$(this).toggle(
						$(this).text().toLowerCase().indexOf(value) > -1
					);
					if (value.length > 0) {
						$('.easydocs-accordion')
							.find('.dd-list')
							.css('display', 'block');
						$('.nestable--collapse').show();
						$('.nestable--expand').hide();
					} else {
						$('.easydocs-accordion')
							.find('ol li ol.dd-list')
							.css('display', 'none');
						$('.nestable--collapse').hide();
						$('.nestable--expand').show();
					}
				});
			});

			document
				.getElementById('easydocs-search')
				.addEventListener('search', function (event) {
					$('.easydocs-accordion')
						.find('ol li ol.dd-list')
						.css('display', 'none');
					$('.nestable--collapse').hide();
					$('.nestable--expand').show();
				});
		}

		// Ensure the left sidebar has an active item on first load.
		// (This also fixes cases where cookie points to a deleted/non-existent doc.)
		if (!$('.easydocs-navitem').hasClass('is-active')) {
			keep_last_active_doc_tab();
		}

		// BULK OPTIONS
		$('.ezd-admin-bulk-options').on('click keydown', function (e) {
			if ($(e.target).closest('.ezd-admin-bulk-actions').length) {
				return;
			}
			if (e.type === 'keydown' && e.which !== 13 && e.which !== 32) {
				return;
			}
			if (e.type === 'keydown') {
				e.preventDefault();
			}

			var $trigger = $(this);
			$trigger.toggleClass('active');
			var isExpanded = $trigger.hasClass('active');
			$trigger.attr('aria-expanded', isExpanded);
			$trigger
				.find('> .dashicons')
				.toggleClass('arrow-active', isExpanded);
		});
		
		// Notifications filter buttons
		$('.easydocs-filters button').on('click', function(e){
			e.preventDefault();

			var filter = $(this).data('filter'); 
			var $items = $('.ezd-mix-wrapper .mix');

			// Highlight active button
			$('.easydocs-filters button').removeClass('mixitup-control-active');
			$(this).addClass('mixitup-control-active');

			$items.each(function(){
				var $item = $(this);

				if(filter === '*' || $item.is(filter)){
					// Match → fade in + add class
					$item.stop(true, true).fadeIn({
						duration: 300,
						start: function(){
							// Force display flex when fading in
							$item.css('display', 'flex');
						},
						complete: function(){
							$item.addClass('show_mix');
						}
					});
				} else {
					// Not match → fade out + remove class
					$item.stop(true, true).fadeOut(300, function(){
						$item.removeClass('show_mix');
					});
				}
			});
		});
		
	});

	// glossary doc js ==============
	if ($('.spe-list-wrapper').length) {
		$('.spe-list-wrapper').each(function () {
			var $elem = $(this);

			var $active_filter = $elem
				.find('.spe-list-filter .filter.active')
				.data('filter');
			if ($active_filter == '' || typeof $active_filter == 'undefined') {
				$active_filter = 'all';
			}

			var mixer = mixitup($elem, {
				load: {
					filter: $active_filter,
				},
				controls: {
					scope: 'local',
				},
				callbacks: {
					onMixEnd: function (state) {
						$('#' + state.container.id)
							.find('.spe-list-block.spe-removed')
							.hide();
					},
				},
			});

			if ($('.spe-list-search-form').length) {
				var $searchInput = $('.spe-list-search-form input');

				$searchInput.on('input', function (e) {
					var $keyword = $(this).val().toLowerCase();

					$elem.find('.spe-list-block').each(function () {
						var $elem_list_block = $(this);
						var $block_visible_items = 0;

						$elem_list_block
							.find('.spe-list-item')
							.each(function () {
								if (
									$(this)
										.text()
										.toLowerCase()
										.includes($keyword)
								) {
									$(this).show();
									$block_visible_items++;
								} else {
									$(this).hide();
								}
							});

						var $filter_base = $elem_list_block.data('filter-base');
						var $filter_source = $elem.find(
							'.spe-list-filter a[data-filter=".spe-filter-' +
								$filter_base +
								'"]'
						);
						var $active_block = $elem
							.find('.spe-list-filter a.mixitup-control-active')
							.data('filter');

						if ($block_visible_items > 0) {
							$elem_list_block.removeClass('spe-removed');

							if ($active_block != 'all') {
								if (
									$elem_list_block.is(
										$elem.find($active_block)
									)
								) {
									$elem.find($active_block).show();
								}
							} else {
								$elem_list_block.show();
							}

							$filter_source
								.removeClass('filter-disable')
								.addClass('filter');
						} else {
							$elem_list_block.addClass('spe-removed');

							if ($active_block != 'all') {
								if (
									$elem_list_block.is(
										$elem.find($active_block)
									)
								) {
									$elem.find($active_block).hide();
								}
							} else {
								$elem_list_block.hide();
							}

							$filter_source
								.removeClass('filter')
								.addClass('filter-disable');
						}
					});

					if ($keyword == '') {
						mixer.filter('all'); // Reset the filter to show all items
					}
				});

				$searchInput.val('');
			}
		});
	}

	// Analytics Stats Filter Active Class Toggle
	$(".ezd-stat-filter-container ul li").on("click", function() {
        // Remove active class from all
        $(".ezd-stat-filter-container ul li").removeClass("is-active");
		
        // Add active class to the clicked one
        $(this).addClass("is-active");
    });

})(jQuery);

function menuToggle() {
	const toggleMenu = document.querySelector('.easydocs-dropdown');
	toggleMenu.classList.toggle('is-active');
}

let docContainer = document.querySelectorAll('.easydocs-tab');

var config = {
	controls: {
		scope: 'local',
	},
	animation: {
		enable: false,
	},
};

if ( docContainer.length > 0 ) {
	for (let i = 0; i < docContainer.length; i++) {
		var mixer1 = mixitup(docContainer[i], config);
	}
}

/**
 * Mixitup config
 * Used in the Notification Filter
 * Located on the Doc Builder UI page
 */
var containerEl1 = document.querySelector('[data-ref="container-1"]');
if (containerEl1) {
	var config = {
		controls: {
			scope: 'local',
		},
	};
	mixitup(containerEl1, config);
}
