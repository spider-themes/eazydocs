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
				this.initSmartWizard();
				this.initFinishButton();
				this.initPluginActions();
				this.initTips();
				this.updateProgress();
			},

			/**
			 * Initialize color picker
			 */
			initColorPicker: function () {
				if (typeof $.fn.wpColorPicker !== 'undefined') {
					$('.brand-color-picker').wpColorPicker({
						change: function (event, ui) {
							const color = ui.color.toString();
							$('.brand-color-picker').val(color);
							$('.ezd-color-preview').css('--preview-color', color);
						},
						clear: function () {
							$('.ezd-color-preview').css('--preview-color', '#007FFF');
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
				$('.page-layout-wrap').on('change', 'input[type="radio"]', function () {
					const name = $(this).attr('value');
					$('.page-layout-wrap .ezd-layout-option').removeClass('active');
					$('.page-layout-wrap label[for="' + name + '"]').addClass('active');
				});
			},

			/**
			 * Initialize width options
			 */
			initWidthOptions: function () {
				$('.page-width-wrap').on('change', 'input[type="radio"]', function () {
					const name = $(this).attr('value');
					$('.page-width-wrap .ezd-width-option').removeClass('active');
					$('.page-width-wrap label[for="' + name + '"]').addClass('active');
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
						keyboard: {
							keyNavigation: true,
							keyLeft: [37],
							keyRight: [39]
						},
						lang: {
							next: 'Next',
							previous: 'Previous'
						}
					});

					// Listen for Smart Wizard showStep event
					$wizard.on('showStep', function (e, anchorObject, stepNumber, stepDirection, stepPosition) {
						// stepNumber is 0-indexed, so add 1
						self.currentStep = stepNumber + 1;
						self.updateProgress();
						self.updateNavigation(stepPosition);
						self.updateTip(self.currentStep);

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

					// Handle clicks on completed progress steps
					$(document).on('click', '.ezd-progress-step.completed', function () {
						const stepNum = parseInt($(this).data('step'));
						if (stepNum && stepNum >= 1 && stepNum <= self.totalSteps) {
							// Use Smart Wizard's goToStep method (0-indexed)
							$wizard.smartWizard("goToStep", stepNum - 1);
						}
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
					} else if (stepNum === self.currentStep) {
						$step.addClass('active');
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
				const $nextBtn = $('.sw-btn-next');

				// Handle previous button
				if (position === 'first') {
					$prevBtn.addClass('disabled');
				} else {
					$prevBtn.removeClass('disabled');
				}

				// Handle next button visibility on last step
				if (position === 'last') {
					$nextBtn.hide();
				} else {
					$nextBtn.show();
				}
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

					// Get form values
					let customSlug = $('.custom-slug-field').val();
					customSlug = customSlug.replace(/[^a-zA-Z0-9-_]/g, '-').toLowerCase();

					const formData = {
						action: 'ezd_setup_wizard_save_settings',
						security: eazydocs_local_object.nonce,
						rootslug: customSlug,
						brandColor: $('.brand-color-picker').val(),
						slugType: $('.root-slug-wrap input[name="slug"]:checked').val(),
						docSingleLayout: $('.page-layout-wrap input[name="docs_single_layout"]:checked').val(),
						docsPageWidth: $('.page-width-wrap input[name="docsPageWidth"]:checked').val(),
						live_customizer: $('input[name="customizer_visibility"]:checked').val(),
						archivePage: $('.archive-page-selection-wrap select').val()
					};

					// Show loading state
					$btn.prop('disabled', true).html(
						'<span class="dashicons dashicons-update-alt spin"></span> Saving...'
					);

					$.ajax({
						url: eazydocs_local_object.ajaxurl,
						type: 'POST',
						data: formData,
						success: function (response) {
							if (response.success) {
								self.showSuccessState();
							} else {
								self.showErrorState($btn, 'Error saving settings');
							}
						},
						error: function (xhr, status, error) {
							self.showErrorState($btn, 'AJAX error: ' + error);
						}
					});
				});
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
				alert(message);
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
							alert('Error: ' + response.data);
						}
					},
					error: function (xhr, status, error) {
						if (action === 'activate') {
							// Likely already active
							self.markPluginActivated($btn);
						} else {
							self.resetPluginButton($btn, originalText, action);
							alert('Error: ' + error);
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