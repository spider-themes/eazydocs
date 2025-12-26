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
