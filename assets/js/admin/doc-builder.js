/**
 * EazyDocs Doc Builder JavaScript
 * 
 * This file contains all JavaScript functionality specific to the Doc Builder page.
 * It handles tab management, doc CRUD operations, search, bulk options, and MixItUp configurations.
 * 
 * @package EazyDocs
 * @since 2.7.0
 */
(function ($) {
	'use strict';
	
	$(document).ready(function () {
		/*------------ Cookie functions ------------*/
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
			$('.tab-menu .easydocs-navitem').removeClass('active is-active').attr('aria-selected', 'false');
			$nav.addClass('active is-active').attr('aria-selected', 'true');

			// Tab content
			$('.easydocs-tab-content .easydocs-tab').removeClass('tab-active').hide();
			$tab.addClass('tab-active').fadeIn('slow');

			if (saveCookie) {
				createCookie('eazydocs_doc_current_tab', tabId, 999);
			}

			return true;
		}

		// Sidebar Tabs [COOKIE]
		$(document).on('click keydown', '.tab-menu .easydocs-navitem', function (e) {
			// Handle keyboard activation (Enter or Space)
			if (e.type === 'keydown' && e.which !== 13 && e.which !== 32) {
				return;
			}
			if (e.type === 'keydown') {
				// If the keydown originated from an interactive descendant
				// (e.g. Edit/View/Delete links or form controls), do not
				// intercept Enter/Space so their default behavior works.
				if (e.target !== this && $(e.target).is('a, button, [role="button"], input, select, textarea')) {
					return;
				}
				e.preventDefault();
			}

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

		// Notification icon toggle (Doc Builder specific)
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
		
		// SECTION DOC
		function create_section_doc() {
			$(document).on('click', '.section-doc', function (e) {
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
		if (!$('.easydocs-navitem').hasClass('active')) {
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
		
		// Accessibility for Filter Tabs (Child Docs)
		$('.single-item-filter li[role="button"]').on('keydown', function(e) {
			if (e.which === 13 || e.which === 32) { // Enter or Space
				e.preventDefault();
				$(this).click();
			}
		});

		$('.single-item-filter li[role="button"]').on('click', function() {
			var $parent = $(this).closest('.single-item-filter');
			$parent.find('li[role="button"]').attr('aria-pressed', 'false');
			$(this).attr('aria-pressed', 'true');
		});

		// Notifications filter buttons (Doc Builder page)
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

	// Glossary doc JS ==============
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

})(jQuery);

// Menu Toggle function (Doc Builder specific)
function menuToggle() {
	const toggleMenu = document.querySelector('.easydocs-dropdown');
	if (toggleMenu) {
		toggleMenu.classList.toggle('active');
	}
}

// MixItUp configurations for Doc Builder
(function() {
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
})();

/**
 * Enhanced Drag & Drop functionality for EazyDocs Builder
 * Modern, intuitive, and user-friendly drag-and-drop experience
 * 
 * @package EazyDocs
 */
(function ($) {
	'use strict';

	// Configuration object
	const EZD_DragDrop = {
		config: {
			animationDuration: 200,
			saveDebounceTime: 500,
			tooltipShowDelay: 300,
			tooltipHideDelay: 1500,
		},
		
		// State
		state: {
			isDragging: false,
			dragStartTime: null,
			saveTimeout: null,
		},
		
		// Elements cache
		elements: {
			tooltip: null,
			saveIndicator: null,
		},
		
		/**
		 * Initialize the enhanced drag-drop functionality
		 */
		init: function () {
			this.createUIElements();
			this.initChildDocs();
			this.initParentDocs();
			this.bindEvents();
			this.initCookieState();
		},
		
		/**
		 * Create UI elements for feedback
		 */
		createUIElements: function () {
			// Create drag tooltip
			if (!$('.ezd-drag-tooltip').length) {
				const tooltip = $(`
					<div class="ezd-drag-tooltip">
						<svg class="ezd-drag-tooltip-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
							<path d="M7 19v-2h2v2H7zm4 0v-2h2v2h-2zm4 0v-2h2v2h-2zM7 15v-2h2v2H7zm4 0v-2h2v2h-2zm4 0v-2h2v2h-2zM7 11V9h2v2H7zm4 0V9h2v2h-2zm4 0V9h2v2h-2zM7 7V5h2v2H7zm4 0V5h2v2h-2zm4 0V5h2v2h-2z"/>
						</svg>
						<span>Drag to reorder documentation</span>
					</div>
				`);
				$('body').append(tooltip);
				this.elements.tooltip = tooltip;
			}
			
			// Create save indicator
			if (!$('.ezd-save-indicator').length) {
				const saveIndicator = $(`
					<div class="ezd-save-indicator">
						<span class="dashicons dashicons-saved"></span>
						<span>Order saved successfully!</span>
					</div>
				`);
				$('body').append(saveIndicator);
				this.elements.saveIndicator = saveIndicator;
			}
		},
		
		/**
		 * Initialize child docs nestable
		 */
		initChildDocs: function () {
			const self = this;
			const $sections = $('.easydocs-navitem');
			
			if ($sections.length === 0) {
				return;
			}
			
			$sections.each(function () {
				const $this = $(this);
				const secId = $this.data('id');
				const $nestable = $('#nestable-' + secId);
				
				if (!$nestable.length) {
					return;
				}
				
				// Initialize nestable with enhanced options
				$nestable.nestable({
					maxDepth: 4,
					expandBtnHTML: '<button class="nestable--button nestable--expand" data-action="expand" type="button" aria-label="Expand" title="Expand section">Expand</button>',
					collapseBtnHTML: '<button class="nestable--button nestable--collapse" data-action="collapse" type="button" aria-label="Collapse" title="Collapse section">Collapse</button>',
					collapsedClass: 'dd-collapsed eazdocs-collapsed',
				})
				.on('change', function (e) {
					self.handleChildDocsChange(e);
				})
				.on('dragStart', function (e, item) {
					self.onDragStart(e, item, 'child');
				})
				.on('dragEnd', function (e, item) {
					self.onDragEnd(e, item, 'child');
				})
				.nestable('collapseAll');
			});
		},
		
		/**
		 * Initialize parent docs nestable
		 */
		initParentDocs: function () {
			const self = this;
			const $parentNestable = $('.parent-nestable');
			
			if ($parentNestable.length === 0) {
				return;
			}
			
			$parentNestable.nestable({
				maxDepth: 1,
			})
			.on('change', function (e) {
				self.handleParentDocsChange(e);
			})
			.on('dragStart', function (e, item) {
				self.onDragStart(e, item, 'parent');
			})
			.on('dragEnd', function (e, item) {
				self.onDragEnd(e, item, 'parent');
			});
		},
		
		/**
		 * Handle drag start event
		 */
		onDragStart: function (e, item, type) {
			this.state.isDragging = true;
			this.state.dragStartTime = Date.now();
			
			// Add dragging class to body
			$('body').addClass('ezd-is-dragging');
			
			// Show tooltip if dragging takes more than threshold
			setTimeout(() => {
				if (this.state.isDragging) {
					this.showTooltip();
				}
			}, this.config.tooltipShowDelay);
			
			// Add visual feedback
			if (item) {
				$(item).addClass('is-dragging');
			}
		},
		
		/**
		 * Handle drag end event
		 */
		onDragEnd: function (e, item, type) {
			this.state.isDragging = false;
			
			// Remove dragging class
			$('body').removeClass('ezd-is-dragging');
			
			// Hide tooltip
			this.hideTooltip();
			
			// Remove visual feedback
			if (item) {
				$(item).removeClass('is-dragging');
			}
		},
		
		/**
		 * Handle child docs order change
		 */
		handleChildDocsChange: function (e) {
			const self = this;
			const $list = e.length ? e : $(e.target);
			
			// Debounce save
			if (this.state.saveTimeout) {
				clearTimeout(this.state.saveTimeout);
			}
			
			this.state.saveTimeout = setTimeout(function () {
				self.saveChildDocsOrder($list);
			}, this.config.saveDebounceTime);
		},
		
		/**
		 * Save child docs order via AJAX
		 */
		saveChildDocsOrder: function ($list) {
			const self = this;
			const serializedData = $list.nestable('serialize');
			
			$.ajax({
				url: eazydocs_local_object.ajaxurl,
				type: 'POST',
				data: {
					action: 'eaz_nestable_docs',
					data: window.JSON.stringify(serializedData),
					security: eazydocs_local_object.nonce,
				},
				success: function (res) {
					self.showSaveIndicator();
				},
				error: function (err) {
					console.error('EazyDocs: Error saving order', err);
					self.showErrorIndicator();
				},
			});
		},
		
		/**
		 * Handle parent docs order change
		 */
		handleParentDocsChange: function (e) {
			const self = this;
			const $list = e.length ? e : $(e.target);
			
			// Debounce save
			if (this.state.saveTimeout) {
				clearTimeout(this.state.saveTimeout);
			}
			
			this.state.saveTimeout = setTimeout(function () {
				self.saveParentDocsOrder($list);
			}, this.config.saveDebounceTime);
		},
		
		/**
		 * Save parent docs order via AJAX
		 */
		saveParentDocsOrder: function ($list) {
			const self = this;
			const serializedData = $list.nestable('serialize');
			
			$.ajax({
				url: eazydocs_local_object.ajaxurl,
				type: 'POST',
				data: {
					action: 'eaz_parent_nestable_docs',
					data: window.JSON.stringify(serializedData),
					security: eazydocs_local_object.nonce,
				},
				success: function (res) {
					self.showSaveIndicator();
				},
				error: function (err) {
					console.error('EazyDocs: Error saving parent order', err);
					self.showErrorIndicator();
				},
			});
		},
		
		/**
		 * Show save success indicator
		 */
		showSaveIndicator: function () {
			const $indicator = this.elements.saveIndicator;
			
			$indicator.addClass('is-visible');
			
			setTimeout(function () {
				$indicator.removeClass('is-visible');
			}, 2000);
		},
		
		/**
		 * Show error indicator  
		 */
		showErrorIndicator: function () {
			const $indicator = this.elements.saveIndicator;
			
			$indicator
				.removeClass('is-visible')
				.css('background', '#e74c3c')
				.find('span:last')
				.text('Error saving order!');
			
			$indicator.addClass('is-visible');
			
			setTimeout(function () {
				$indicator
					.removeClass('is-visible')
					.css('background', '')
					.find('span:last')
					.text('Order saved successfully!');
			}, 3000);
		},
		
		/**
		 * Show drag tooltip
		 */
		showTooltip: function () {
			if (this.elements.tooltip) {
				this.elements.tooltip.addClass('is-visible');
			}
		},
		
		/**
		 * Hide drag tooltip
		 */
		hideTooltip: function () {
			if (this.elements.tooltip) {
				this.elements.tooltip.removeClass('is-visible');
			}
		},
		
		/**
		 * Bind additional events
		 */
		bindEvents: function () {
			const self = this;
			
			// Toggle expand/collapse on clicking dd3-content area (blank space of dd-item)
			$(document).on('click', '.nestables-child .dd3-content', function (e) {
				// Don't toggle if clicking on a link, button, actions, or drag handle
				if ($(e.target).closest('a, button, .dd-handle, .actions, ul.actions, .right-content').length) {
					return;
				}
				
				const $this = $(this);
				const $item = $this.closest('.dd-item');
				const $childList = $item.children('.dd-list');
				
				// Only toggle if item has children
				if ($childList.length === 0) {
					return;
				}
				
				// Find the collapse/expand button and trigger it
				const $collapseBtn = $item.children('button[data-action="collapse"]');
				const $expandBtn = $item.children('button[data-action="expand"]');
				
				if ($collapseBtn.length && $collapseBtn.is(':visible')) {
					// Currently expanded - collapse it
					$item.addClass('dd-collapsed');
					$childList.slideUp(self.config.animationDuration);
					$collapseBtn.hide();
					$expandBtn.show();
				} else if ($expandBtn.length) {
					// Currently collapsed - expand it
					$item.removeClass('dd-collapsed');
					$childList.slideDown(self.config.animationDuration);
					$expandBtn.hide();
					$collapseBtn.show();
				} else {
					// No buttons yet, toggle using class
					if ($item.hasClass('dd-collapsed')) {
						$item.removeClass('dd-collapsed');
						$childList.slideDown(self.config.animationDuration);
					} else {
						$item.addClass('dd-collapsed');
						$childList.slideUp(self.config.animationDuration);
					}
				}
			});
			
			// Add cursor pointer to indicate clickability for items with children
			$(document).on('mouseenter', '.nestables-child .dd-item', function () {
				const $item = $(this);
				const $content = $item.children('.dd3-content');
				const hasChildren = $item.children('.dd-list').length > 0;
				
				if (hasChildren) {
					$content.css('cursor', 'pointer');
				}
			});
			
			// Toggle children visibility on click (legacy behavior for dd3-has-children class)
			$(document).on('click', '.dd-item.dd3-has-children', function (e) {
				// Don't toggle if clicking on a link or button
				if ($(e.target).closest('a, button, .dd-handle').length) {
					return;
				}
				
				const $this = $(this);
				$('.dd-item').removeClass('show-child');
				$this.toggleClass('show-child');
			});
			
			// Prevent link clicks from toggling
			$(document).on('click', '.dd3-has-children .expand--child a', function (e) {
				e.stopPropagation();
			});
			
			// Handle expand/collapse all toggle functionality
			$(document).on('click', '.ezd-toggle-expand-btn', function (e) {
				e.preventDefault();
				const $btn = $(this);
				const $tab = $btn.closest('.easydocs-tab');
				const tabId = $tab.attr('id');
				const currentState = $btn.data('state');
				const $icon = $btn.find('.dashicons');
				const $text = $btn.find('.btn-text');
				
				if (currentState === 'collapsed') {
					// Expand all
					$('#' + tabId + ' .nestables-child').nestable('expandAll');
					$btn.data('state', 'expanded');
					$icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
					$text.text(eazydocs_local_object.i18n?.collapse_all || 'Collapse All');
					$btn.attr('title', eazydocs_local_object.i18n?.collapse_all_title || 'Collapse all sections');
					$('.nestable--collapse').show();
					$('.nestable--expand').hide();
				} else {
					// Collapse all
					$('#' + tabId + ' .nestables-child').nestable('collapseAll');
					$btn.data('state', 'collapsed');
					$icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
					$text.text(eazydocs_local_object.i18n?.expand_all || 'Expand All');
					$btn.attr('title', eazydocs_local_object.i18n?.expand_all_title || 'Expand all sections');
					$('.nestable--collapse').hide();
					$('.nestable--expand').show();
				}
			});
			
			// Keyboard accessibility for drag handles
			$(document).on('keydown', '.dd-handle', function (e) {
				const $handle = $(this);
				const $item = $handle.closest('.dd-item');
				
				if (e.key === 'ArrowUp' && e.altKey) {
					e.preventDefault();
					const $prev = $item.prev('.dd-item');
					if ($prev.length) {
						$prev.before($item);
						$item.closest('.dd').trigger('change');
					}
				} else if (e.key === 'ArrowDown' && e.altKey) {
					e.preventDefault();
					const $next = $item.next('.dd-item');
					if ($next.length) {
						$next.after($item);
						$item.closest('.dd').trigger('change');
					}
				}
			});
			
			// Make drag handles focusable
			$('.dd-handle').attr('tabindex', '0');
		},
		
		/**
		 * Initialize cookie-based state restoration
		 */
		initCookieState: function () {
			// Restore expanded state from cookies
			this.restoreChildState();
			this.restoreChildOfChildState();
			
			// Save state on change
			this.bindStateSaveEvents();
		},
		
		/**
		 * Helper: Create cookie
		 */
		createCookie: function (name, value, days) {
			let expires = '';
			if (days) {
				const date = new Date();
				date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
				expires = '; expires=' + date.toUTCString();
			}
			document.cookie = name + '=' + value + expires + '; path=/';
		},
		
		/**
		 * Helper: Read cookie
		 */
		readCookie: function (name) {
			const nameEQ = name + '=';
			const ca = document.cookie.split(';');
			for (let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) === ' ') {
					c = c.substring(1, c.length);
				}
				if (c.indexOf(nameEQ) === 0) {
					return c.substring(nameEQ.length, c.length);
				}
			}
			return null;
		},
		
		/**
		 * Bind state save events
		 */
		bindStateSaveEvents: function () {
			const self = this;
			
			$(document).on('click', '.dd3-have-children', function () {
				const itemId = $(this).attr('data-id');
				const isActiveChild = $(this).hasClass('show-child');
				
				if (isActiveChild) {
					self.createCookie('eazydocs_current_child', 'child-' + itemId, 999);
				} else {
					self.createCookie('eazydocs_current_child', '', 999);
				}
			});
			
			const currentChild = this.readCookie('eazydocs_current_child');
			if (currentChild) {
				$(document).on('click', '.' + currentChild + ' .dd3-have-sub-children', function () {
					const itemId = $(this).attr('data-id');
					const isActiveChild = $(this).hasClass('show-child');
					
					if (isActiveChild) {
						self.createCookie('eazydocs_current_child_of', 'child-of-' + itemId, 999);
					} else {
						self.createCookie('eazydocs_current_child_of', '', 999);
					}
				});
			}
		},
		
		/**
		 * Restore child state from cookie
		 */
		restoreChildState: function () {
			const currentChild = this.readCookie('eazydocs_current_child');
			
			if (currentChild && currentChild.length > 0) {
				const $child = $('.' + currentChild);
				
				if ($child.length) {
					$child.addClass('showing-expand');
					$child.find('> .nestable--collapse').css('display', 'block');
					$child.find('> .nestable--expand').css('display', 'none');
					$child.find('> .dd-list').addClass('showing').css('display', 'block');
				}
			}
		},
		
		/**
		 * Restore child-of-child state from cookie
		 */
		restoreChildOfChildState: function () {
			const currentChild = this.readCookie('eazydocs_current_child');
			
			if (!currentChild) {
				return;
			}
			
			const currentChildOf = this.readCookie('eazydocs_current_child_of');
			
			if (currentChildOf && currentChildOf.trim().length > 0) {
				const $childOf = $('.' + currentChildOf);
				
				if ($childOf.length) {
					$childOf.addClass('showing-expand');
					$childOf.find('> .nestable--collapse').css('display', 'block');
					$childOf.find('> .nestable--expand').css('display', 'none');
					$childOf.find('> .dd-list').addClass('showing').css('display', 'block');
				}
			}
		},
	};
	
	// Initialize on document ready
	$(document).ready(function () {
		EZD_DragDrop.init();
	});
	
	// Expose to window for potential external access
	window.EZD_DragDrop = EZD_DragDrop;

})(jQuery);