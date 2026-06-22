/**
 * EazyDocs Setup Wizard JavaScript
 * Enhanced functionality with progress tracking, animations, and improved UX
 *
 * @package EazyDocs
 */

(function ($) {
	'use strict';

	$(document).ready(function () {
		// Setup Wizard initialization
		const setupWizard = {
			currentStep: 1,
			totalSteps: 5,

			init: function () {
				this.initColorPicker();
				this.initSlugOptions();
				this.initLayoutOptions();
				this.initWidthOptions();
				this.initPreviewControls();
				this.initSmartWizard();
				this.initFinishButton();
				this.initPluginActions();
				this.initTips();
				this.updateProgress();
				this.updateLayoutPreview();
			},

			/**
			 * Initialize color picker
			 */
			initColorPicker: function () {
				const self = this;
				if (typeof $.fn.wpColorPicker !== 'undefined') {
					$('.brand-color-picker').wpColorPicker({
						change: function (event, ui) {
							const color = ui.color.toString();
							$('.brand-color-picker').val(color);
							$('.ezd-color-preview').css('--preview-color', color);
							self.updateLayoutPreview();
						},
						clear: function () {
							$('.ezd-color-preview').css('--preview-color', '#007FFF');
							self.updateLayoutPreview();
						}
					});
				}
			},

			/**
			 * Initialize slug options
			 */
			initSlugOptions: function () {
				const $slugWrap = $('.root-slug-wrap');
				const $customInput = $('.ezd-custom-slug-input');

				$slugWrap.on('change', 'input[type="radio"]', function () {
					const value = $(this).val();

					// Update active state
					$slugWrap.find('.ezd-slug-option').removeClass('active');
					$(this).closest('.ezd-slug-option').addClass('active');

					// Toggle custom slug input
					if (value === 'custom-slug') {
						$customInput.addClass('active').find('input').focus();
					} else {
						$customInput.removeClass('active');
					}
				});
			},

			/**
			 * Initialize layout options
			 */
			initLayoutOptions: function () {
				const self = this;
				$('.page-layout-wrap').on('change', 'input[type="radio"]', function () {
					$('.page-layout-wrap .ezd-layout-option').removeClass('active');
					$(this).closest('.ezd-layout-option').addClass('active');
					self.updateLayoutPreview();
				});
			},

			/**
			 * Initialize width options
			 */
			initWidthOptions: function () {
				const self = this;
				$('.page-width-wrap').on('change', 'input[type="radio"]', function () {
					$('.page-width-wrap .ezd-width-option').removeClass('active');
					$(this).closest('.ezd-width-option').addClass('active');
					self.updateLayoutPreview();
				});
			},

			/**
			 * Initialize the live-preview controls: the in-step brand colour
			 * swatch, the light/dark appearance toggle, and "Restore defaults".
			 */
			initPreviewControls: function () {
				const self = this;

				// In-step brand colour: keep the wpColorPicker field as the single
				// source of truth so the Step 2 picker and the save payload stay
				// in sync, then refresh the preview.
				$(document).on('input change', '.ezd-preview-brand-input', function () {
					const color = $(this).val();
					const $picker = $('.brand-color-picker');

					if ($picker.length && typeof $.fn.wpColorPicker !== 'undefined' && $picker.hasClass('wp-color-picker')) {
						$picker.wpColorPicker('color', color);
					} else {
						$picker.val(color);
					}

					self.updateLayoutPreview();
				});

				// Light / dark appearance toggle (preview only).
				$(document).on('click', '.ezd-mode-btn', function () {
					const mode = $(this).data('mode');
					$('.ezd-mode-btn').removeClass('active').attr('aria-pressed', 'false');
					$(this).addClass('active').attr('aria-pressed', 'true');
					$('.ezd-layout-live-preview').attr('data-theme', mode);
				});

				// Restore recommended defaults.
				$(document).on('click', '.ezd-restore-defaults', function () {
					$('#both_sidebar').prop('checked', true).trigger('change');
					$('#boxed').prop('checked', true).trigger('change');
					$('.ezd-mode-btn[data-mode="light"]').trigger('click');
					self.updateLayoutPreview();
				});
			},

			/**
			 * Initialize Smart Wizard
			 */
			initSmartWizard: function () {
				const self = this;
				const $wizard = $('#ezd-setup-wizard-wrap');


				if (typeof $.fn.smartWizard !== 'undefined' && $wizard.length) {

					// Initialize Smart Wizard
					$wizard.smartWizard({
						autoAdjustHeight: false,
						// Keyboard arrow navigation is disabled: SmartWizard binds it
						// to the document with no focus check, so arrow keys used to
						// move the caret inside the slug / colour inputs would jump steps.
						keyboard: {
							keyNavigation: false
						},
						lang: {
							next: 'Next',
							previous: 'Previous'
						}
					});

					// Validate and persist before leaving a step. Returning false
					// cancels the navigation (SmartWizard honours a false result).
					$wizard.on('leaveStep', function (e, anchorObject, fromStep, toStep, stepDirection) {
						// fromStep is 0-indexed.
						if (stepDirection === 'forward' && !self.validateStep(fromStep + 1)) {
							return false;
						}

						// Persist progress on every navigation so nothing is lost if
						// the user skips, closes the tab, or clicks a Finish-screen card.
						self.saveSettings();
					});

					// Listen for Smart Wizard showStep event
					$wizard.on('showStep', function (e, anchorObject, stepNumber, stepDirection, stepPosition) {
						// stepNumber is 0-indexed, so add 1
						self.currentStep = stepNumber + 1;
						self.updateProgress();
						self.updateNavigation(stepPosition);
						self.updateTip(self.currentStep);
						self.clearNotice();

						// Refresh the live layout preview when entering the Layout step.
						if (self.currentStep === 3) {
							self.updateLayoutPreview();
						}

						// Show confetti on finish step
						if (self.currentStep === 5) {
							self.showConfetti();
						}
					});

					// Listen for URL hash changes as a backup
					$(window).on('hashchange', function () {
						self.syncStepFromHash();
					});

					// Bind to step anchor clicks
					$wizard.on('click', '.nav-link', function () {
						setTimeout(function () {
							self.syncStepFromHash();
						}, 100);
					});

					// Handle clicks on completed progress steps (mouse + keyboard).
					const goToProgressStep = function ($step) {
						const stepNum = parseInt($step.data('step'));
						if (stepNum && stepNum >= 1 && stepNum <= self.totalSteps) {
							// Use Smart Wizard's goToStep method (0-indexed)
							$wizard.smartWizard("goToStep", stepNum - 1);
						}
					};

					$(document).on('click', '.ezd-progress-step.completed', function () {
						goToProgressStep($(this));
					});

					$(document).on('keydown', '.ezd-progress-step.completed', function (e) {
						if (e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32) {
							e.preventDefault();
							goToProgressStep($(this));
						}
					});

					// Dismiss inline wizard notices.
					$(document).on('click', '.ezd-notice-dismiss', function () {
						$(this).closest('.ezd-wizard-notice').slideUp(150, function () {
							$(this).remove();
						});
					});

					// Sync on initial load if there's a hash
					self.syncStepFromHash();
				}
			},

			/**
			 * Sync current step from URL hash
			 */
			syncStepFromHash: function () {
				const hash = window.location.hash;

				if (hash) {
					const match = hash.match(/step-(\d+)/);
					if (match) {
						const stepNum = parseInt(match[1]);

						if (stepNum !== this.currentStep && stepNum >= 1 && stepNum <= this.totalSteps) {
							this.currentStep = stepNum;
							this.updateProgress();
							this.updateTip(stepNum);

							// Show confetti on finish step
							if (stepNum === 5) {
								this.showConfetti();
							}
						}
					}
				}
			},

			/**
			 * Update progress bar and step indicators
			 */
			updateProgress: function () {
				const self = this;
				const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;

				// Update progress bar
				$('.ezd-progress-fill').css('width', progress + '%');

				// Update step indicators - use self to maintain context
				$('.ezd-progress-step').each(function () {
					const $step = $(this);
					const stepNum = parseInt($step.data('step'));

					$step.removeClass('active completed');

					if (stepNum < self.currentStep) {
						$step.addClass('completed');
						// Completed steps are navigable, so expose them to keyboard / AT.
						$step.attr({ role: 'button', tabindex: '0' });
					} else if (stepNum === self.currentStep) {
						$step.addClass('active').attr('role', 'button').removeAttr('tabindex');
					} else {
						$step.removeAttr('tabindex').removeAttr('role');
					}
				});

				// Update footer counter
				$('.ezd-step-counter .current-step').text(this.currentStep);
			},

			/**
			 * Update navigation buttons
			 */
			updateNavigation: function (position) {
				const $prevBtn = $('.sw-btn-prev');

				// Handle previous button
				if (position === 'first') {
					$prevBtn.addClass('disabled');
				} else {
					$prevBtn.removeClass('disabled');
				}

				// On the last step, swap the Next button for the Finish button.
				// (Visibility is handled in CSS via this class so it overrides the
				// !important display rules on the SmartWizard buttons.)
				$('.ezd-wizard-footer').toggleClass('is-last-step', position === 'last');
			},

				/**
			 * Update tips based on current step and handle toggle functionality
			 */
			initTips: function () {
				const self = this;
				const $tipsPanel = $('.ezd-setup-tips');
				const $toggleBtn = $('.ezd-tips-toggle');
				const $tipsHeader = $('.ezd-tips-header');
				const storageKey = 'ezd_quick_tips_collapsed';

				// Check localStorage for collapsed state
				const isCollapsed = localStorage.getItem(storageKey) === 'true';
				if (isCollapsed) {
					$tipsPanel.addClass('is-collapsed');
					$toggleBtn.attr('aria-expanded', 'false');
				}

				// Toggle button click handler
				$toggleBtn.on('click', function (e) {
					e.stopPropagation();
					self.toggleTipsPanel($tipsPanel, $toggleBtn, storageKey);
				});

				// Also allow clicking on header to toggle
				$tipsHeader.on('click', function (e) {
					// Only toggle if click was on header, not on the button itself
					if (!$(e.target).closest('.ezd-tips-toggle').length) {
						self.toggleTipsPanel($tipsPanel, $toggleBtn, storageKey);
					}
				});

				// Show first tip on load
				this.updateTip(1);
			},

			/**
			 * Toggle Quick Tips panel visibility
			 */
			toggleTipsPanel: function ($panel, $btn, storageKey) {
				const isCollapsed = $panel.hasClass('is-collapsed');

				if (isCollapsed) {
					// Expand
					$panel.removeClass('is-collapsed');
					$btn.attr('aria-expanded', 'true');
					localStorage.setItem(storageKey, 'false');
				} else {
					// Collapse
					$panel.addClass('is-collapsed');
					$btn.attr('aria-expanded', 'false');
					localStorage.setItem(storageKey, 'true');
				}
			},

			updateTip: function (step) {
				$('.ezd-tips-list li').removeClass('active');
				$('.ezd-tips-list li[data-step="' + step + '"]').addClass('active');
			},

			/**
			 * Initialize finish button
			 */
			initFinishButton: function () {
				const self = this;

				$('#finish-btn').on('click', function () {
					const $btn = $(this);

					// Show loading state
					$btn.prop('disabled', true).html(
						'<span class="dashicons dashicons-update-alt spin"></span> Saving...'
					);

					self.saveSettings({
						success: function (response) {
							if (response && response.success) {
								self.showSuccessState();
							} else {
								self.showErrorState($btn, 'Error saving settings');
							}
						},
						error: function (error) {
							self.showErrorState($btn, 'AJAX error: ' + error);
						}
					});
				});
			},

			/**
			 * Collect all wizard field values into the save payload.
			 */
			collectFormData: function () {
				const slugType = $('.root-slug-wrap input[name="slug"]:checked').val() || 'post-name';

				// Only send a custom slug when the custom option is selected, so the
				// stored value never leaks from the hidden field on the default option.
				let customSlug = '';
				if (slugType === 'custom-slug') {
					customSlug = ($('.custom-slug-field').val() || '')
						.replace(/[^a-zA-Z0-9-_]/g, '-')
						.toLowerCase();
				}

				return {
					action: 'ezd_setup_wizard_save_settings',
					security: eazydocs_local_object.nonce,
					rootslug: customSlug,
					brandColor: $('.brand-color-picker').val() || '',
					slugType: slugType,
					docSingleLayout: $('.page-layout-wrap input[name="docs_single_layout"]:checked').val() || '',
					docsPageWidth: $('.page-width-wrap input[name="docsPageWidth"]:checked').val() || '',
					// Always send an explicit 0/1 so the toggle can be turned off.
					live_customizer: $('#live-customizer').is(':checked') ? '1' : '0',
					is_dark_switcher: $('#dark-mode-switcher').is(':checked') ? '1' : '0',
					archivePage: $('.archive-page-selection-wrap select').val() || ''
				};
			},

			/**
			 * Persist the current wizard state. Silent by default; pass
			 * success/error callbacks for the explicit Finish action.
			 */
			saveSettings: function (options) {
				options = options || {};

				return $.ajax({
					url: eazydocs_local_object.ajaxurl,
					type: 'POST',
					data: this.collectFormData(),
					success: function (response) {
						if (typeof options.success === 'function') {
							options.success(response);
						}
					},
					error: function (xhr, status, error) {
						if (typeof options.error === 'function') {
							options.error(error);
						}
					}
				});
			},

			/**
			 * Validate a step before advancing. Returns false to block navigation.
			 *
			 * @param {number} step 1-indexed step number being left.
			 */
			validateStep: function (step) {
				this.clearNotice();

				// Step 2 — Basic Setup.
				if (step === 2) {
					const slugType = $('.root-slug-wrap input[name="slug"]:checked').val();
					if (slugType === 'custom-slug' && !($('.custom-slug-field').val() || '').trim()) {
						this.showNotice(2, 'Please enter a custom URL slug, or choose the Default Slug option.', 'error');
						$('.custom-slug-field').focus();
						return false;
					}

					const color = ($('.brand-color-picker').val() || '').trim();
					if (color && !/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(color)) {
						this.showNotice(2, 'Please enter a valid hex brand colour, e.g. #007FFF.', 'error');
						return false;
					}
				}

				// Step 4 — required plugins must be active before continuing.
				if (step === 4) {
					const $pending = $('.ezd-plugin-card[data-status="required"]').not('.is-active');
					if ($pending.length) {
						this.showNotice(4, 'Please install the required plugin(s) before continuing.', 'warning');
						return false;
					}
				}

				return true;
			},

			/**
			 * Show a dismissible inline notice within a step.
			 *
			 * @param {number} step    1-indexed step to attach the notice to.
			 * @param {string} message Notice text.
			 * @param {string} type    info | warning | error | success.
			 */
			showNotice: function (step, message, type) {
				this.clearNotice();

				type = type || 'info';
				const icons = {
					error: 'dashicons-warning',
					warning: 'dashicons-info',
					success: 'dashicons-yes',
					info: 'dashicons-info'
				};

				const $notice = $(
					'<div class="ezd-wizard-notice ezd-notice-' + type + '" role="alert">' +
						'<span class="dashicons ' + (icons[type] || icons.info) + '"></span>' +
						'<span class="ezd-notice-text"></span>' +
						'<button type="button" class="ezd-notice-dismiss" aria-label="Dismiss notice">' +
							'<span class="dashicons dashicons-no-alt"></span>' +
						'</button>' +
					'</div>'
				);

				$notice.find('.ezd-notice-text').text(message);
				$('#step-' + step + ' .ezd-step-header').after($notice);
				$notice.hide().slideDown(150);
			},

			/**
			 * Remove any visible inline notice.
			 */
			clearNotice: function () {
				$('.ezd-wizard-notice').stop(true, true).remove();
			},

			/**
			 * Sync the live layout preview with the current selections.
			 */
			updateLayoutPreview: function () {
				const $preview = $('.ezd-layout-live-preview');
				if (!$preview.length) {
					return;
				}

				const layout = $('.page-layout-wrap input[name="docs_single_layout"]:checked').val() || 'both_sidebar';
				const width = $('.page-width-wrap input[name="docsPageWidth"]:checked').val() || 'boxed';
				const color = ($('.brand-color-picker').val() || '').trim() || '#007FFF';

				$preview.attr('data-layout', layout).attr('data-width', width);
				$preview[0].style.setProperty('--ezd-preview-brand', color);

				// Keep the in-step brand swatch in sync with the saved colour.
				const $brandInput = $('.ezd-preview-brand-input');
				if ($brandInput.length && $brandInput.val().toLowerCase() !== color.toLowerCase()) {
					$brandInput.val(color);
				}
			},

			/**
			 * Show success state after saving
			 */
			showSuccessState: function () {
				$('.ezd-success-circle').html(
					'<span class="dashicons dashicons-yes"></span>'
				);
				$('.ezd-finish-content h2').text('All Done!');
				$('.ezd-finish-subtitle').text('Your documentation site is ready to use.');

				// Show confetti
				this.showConfetti();

				// Redirect after delay
				setTimeout(function () {
					window.location.href = 'admin.php?page=eazydocs';
				}, 1500);
			},

			/**
			 * Show error state
			 */
			showErrorState: function ($btn, message) {
				this.showNotice(5, message, 'error');
				$btn.prop('disabled', false).html(
					'<span class="dashicons dashicons-yes"></span> Finish & Go to Dashboard'
				);
			},

			/**
			 * Show confetti animation
			 */
			showConfetti: function () {
				const container = document.getElementById('confetti-container');
				if (!container) return;

				const colors = ['#007FFF', '#00B16E', '#F5A623', '#9B59B6', '#E74C3C'];

				for (let i = 0; i < 50; i++) {
					const confetti = document.createElement('div');
					confetti.className = 'confetti-piece';
					confetti.style.cssText = `
						position: absolute;
						width: ${Math.random() * 10 + 5}px;
						height: ${Math.random() * 10 + 5}px;
						background: ${colors[Math.floor(Math.random() * colors.length)]};
						left: ${Math.random() * 100 - 50}px;
						opacity: 0;
						border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
						animation: confettiFall ${Math.random() * 2 + 1.5}s ease-out forwards;
						animation-delay: ${Math.random() * 0.5}s;
					`;
					container.appendChild(confetti);
				}

				// Clean up confetti after animation
				setTimeout(function () {
					container.innerHTML = '';
				}, 3000);
			},

			/**
			 * Initialize plugin actions
			 */
			initPluginActions: function () {
				const self = this;

				$(document).on('click', '.button-action', function () {
					const $btn = $(this);
					const plugin = $btn.data('plugin');
					const action = $btn.data('action');

					if (plugin && action) {
						self.handlePluginAction($btn, plugin, action);
					}
				});
			},

			/**
			 * Handle plugin install/activate
			 */
			handlePluginAction: function ($btn, plugin, action) {
				const self = this;
				const originalText = $btn.find('.button-text').text();

				// Update button state
				$btn.prop('disabled', true)
					.find('.dashicons').removeClass('dashicons-download dashicons-update').addClass('dashicons-update-alt spin');
				$btn.find('.button-text').text(action === 'install' ? 'Installing...' : 'Activating...');

				$.ajax({
					url: eazydocs_local_object.ajaxurl,
					type: 'POST',
					data: {
						action: 'ezd_plugin_action',
						plugin: plugin,
						task: action,
						security: eazydocs_local_object.nonce
					},
					dataType: 'json',
					success: function (response) {
						if (response.success) {
							if (action === 'install') {
								// Trigger activation after install
								$btn.data('action', 'activate');
								self.handlePluginAction($btn, plugin, 'activate');
							} else {
								// Mark as activated
								self.markPluginActivated($btn);
							}
						} else {
							self.resetPluginButton($btn, originalText, action);
							self.showNotice(4, 'Error: ' + response.data, 'error');
						}
					},
					error: function (xhr, status, error) {
						if (action === 'activate') {
							// Likely already active
							self.markPluginActivated($btn);
						} else {
							self.resetPluginButton($btn, originalText, action);
							self.showNotice(4, 'Error: ' + error, 'error');
						}
					}
				});
			},

			/**
			 * Mark plugin as activated
			 */
			markPluginActivated: function ($btn) {
				$btn.removeClass('button-action')
					.addClass('button-disabled')
					.prop('disabled', true)
					.find('.dashicons').removeClass('dashicons-update-alt spin').addClass('dashicons-yes');
				$btn.find('.button-text').text('Activated');

				// Update card state
				$btn.closest('.ezd-plugin-card').addClass('is-active');

				// Clear any "required plugin" gate notice now that it is active.
				this.clearNotice();
			},

			/**
			 * Reset plugin button on error
			 */
			resetPluginButton: function ($btn, text, action) {
				$btn.prop('disabled', false)
					.find('.dashicons').removeClass('dashicons-update-alt spin')
					.addClass(action === 'install' ? 'dashicons-download' : 'dashicons-update');
				$btn.find('.button-text').text(text);
			}
		};

		// Initialize setup wizard
		setupWizard.init();

		// Add CSS for spinning animation
		$('<style>')
			.prop('type', 'text/css')
			.html(`
				.dashicons.spin {
					animation: dashiconsSpin 1s linear infinite;
				}
				@keyframes dashiconsSpin {
					0% { transform: rotate(0deg); }
					100% { transform: rotate(360deg); }
				}
				@keyframes confettiFall {
					0% {
						opacity: 1;
						transform: translateY(0) rotate(0deg);
					}
					100% {
						opacity: 0;
						transform: translateY(200px) rotate(720deg);
					}
				}
			`)
			.appendTo('head');
	});
})(jQuery);