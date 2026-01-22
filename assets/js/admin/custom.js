/**
 * EazyDocs Dashboard JavaScript
 * 
 * This file contains JavaScript functionality for the EazyDocs Dashboard page.
 * It handles NiceSelect initialization and stats filter interactions.
 * 
 * Note: Doc Builder-specific code is now in doc-builder.js
 * Note: Analytics page has its own tab handling in Analytics.php
 * 
 * @package EazyDocs
 * @since 2.7.0
 */
(function ($) {
	'use strict';
	
	$(document).ready(function () {
		// Filter Select (NiceSelect initialization)
		if ($('select').length > 0) {
			$('select').niceSelect();
		}

		// Sidebar Tabs [COOKIE]
		$(document).on('click', '.tab-menu .easydocs-navitem', function () {

			// REMOVE ?tab=something ONLY AFTER CLICK
			const url = new URL(window.location.href);
			if (url.searchParams.has('more_state')) {
				url.searchParams.delete('more_state');
				window.history.replaceState({}, document.title, url.toString());
			}

			let target = $(this).attr('data-rel');
			ezd_activate_doc_tab(target, true);

			return true;
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
			// Trigger on click or Enter/Space keys
			if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
				return;
			}
			if (e.type === 'keydown') {
				e.preventDefault(); // Prevent scrolling for Space
			}

			$(this).toggleClass('active');
			$('.ezd-admin-bulk-options.active > .dashicons').addClass(
				'arrow-active'
			);
		});

		// Keyboard accessibility for bulk options button
		$('.ezd-admin-bulk-options').keydown(function (e) {
			if (e.key === 'Enter' || e.key === ' ' || e.key === 'Space') {
				e.preventDefault();
				$(this).click();
			}
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

})(jQuery);