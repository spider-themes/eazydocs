(function ($) {
    'use sticky'
    $(document).ready(function () {

        // Setup wizard scripts start
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.brand-color-picker').wpColorPicker({
                change: function(event, ui) {
                    var color = ui.color.toString();
                    $('.brand-color-picker').val(color);
                }
            });
        }

        $('.root-slug-wrap input[type="radio"]').on('change', function() {
            var name = $(this).attr('value');
            $('.root-slug-wrap label[for="'+name+'"]').addClass('active').siblings().removeClass('active');

            var root_slug = $(this).val();
            if (root_slug == 'custom-slug') {
                $('.custom-slug-field').show();
            } else {
                $('.custom-slug-field').hide();
            }
        });

        $('.page-width-wrap input[type="radio"]').on('change', function() {
            var name = $(this).attr('value');
            $('.page-width-wrap label[for="'+name+'"]').addClass('active').siblings().removeClass('active');
        });
        $('.page-layout-wrap input[type="radio"]').on('change', function() {
            var name = $(this).attr('value');
            $('.page-layout-wrap label[for="'+name+'"]').addClass('active').siblings().removeClass('active');
        });

        // Smart Wizard
        if (typeof $.fn.smartWizard !== 'undefined') {

            $('#ezd-setup-wizard-wrap').smartWizard({
                keyboard: {
                    keyNavigation: true, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
                    keyLeft: [37], // Left key code
                    keyRight: [39] // Right key code
                },
                lang: { // Language variables for button
                    next: ' Next →',
                    previous: '← Previous',
                }
            });
        }

        $('#finish-btn').on('click', function () {
            var customSlug = $('.custom-slug-field').val();
            var customSlug = customSlug.replace(/[^a-zA-Z0-9-_]/g, '-');
            customSlug = customSlug.toLowerCase();

            var brandColor = $('.brand-color-picker').val();
            var slugType = $('.root-slug-wrap input[name="slug"]:checked').val();
            var live_customizer = $('input[name="customizer_visibility"]:checked').val();

            // select field .archive-page-selection-wrap > select
            var archivePage = $('.archive-page-selection-wrap select').val();

            // doc single layout
            var docSingleLayout = $('.page-layout-wrap input[name="docs_single_layout"]:checked').val();
            var docsPageWidth = $('.page-width-wrap input[name="docsPageWidth"]:checked').val();

            // make hypen to underscore
            $.ajax({
                url: eazydocs_local_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'ezd_setup_wizard_save_settings',
                    rootslug: customSlug,
                    brandColor: brandColor,
                    slugType: slugType,
                    docSingleLayout: docSingleLayout,
                    docsPageWidth: docsPageWidth,
                    live_customizer: live_customizer,
                    archivePage: archivePage,
                },
                beforeSend: function () {
                    $('div#ezd-setup-wizard-wrap .tab-content #step-4 h2').html('Submitting...');
                },
                success: function (response) {
                    if (response.success) {
                        $('.swal2-icon .swal2-icon-content').html('<img src="' + eazydocs_local_object.EAZYDOCS_ASSETS + '/images/wizard-success.png"  />');
                        $('div#ezd-setup-wizard-wrap .tab-content #step-4 h2').text('Thank you');
                        $('div#ezd-setup-wizard-wrap .tab-content #step-4 p').text('You have setup everything successfully');
                        // redirect
                        setTimeout(function () {
                            window.location.href = 'admin.php?page=eazydocs';
                        }, 100);

                    } else {
                        alert('Error saving settings');
                    }
                },
                error: function (xhr, status, error) {
                    alert('AJAX error: ' + status + ' - ' + error);
                }
            });
        });

        // Plugin activation in setup wizard
        function ezdHandlePluginAction(button, plugin, action) {
            button.text(action === "install" ? "Installing.." : "Activating..").prop("disabled", true);

            $.ajax({
                url: eazydocs_local_object.ajaxurl,
                type: "POST",
                data: {
                    action: "ezd_plugin_action",
                    plugin: plugin,
                    task: action,
                    security: eazydocs_local_object.nonce
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        if (action === "install") {
                            // Automatically trigger activation after install
                            button.text("Activating...").attr("data-action", "activate").removeClass("button-action").addClass("button-activate");
                            ezdHandlePluginAction(button, plugin, "activate"); // Call activation immediately
                        } else {
                            button.text("Activated").removeClass("button-activate button-action").addClass("button-disabled").prop("disabled", true);
                        }
                    } else {
                        alert("Error: " + response.data);
                        button.text(action.charAt(0).toUpperCase() + action.slice(1)).prop("disabled", false);
                    }
                },
                error: function (xhr, status, error) {
                    // For activate action, silently mark as activated (likely already active)
                    if (action === "activate") {
                        button.text("Activated").removeClass("button-activate button-action").addClass("button-disabled").prop("disabled", true);
                    } else {
                        alert("Error: " + error);
                        button.text(action.charAt(0).toUpperCase() + action.slice(1)).prop("disabled", false);
                    }
                }
            });
        }

        $(document).on("click", ".button-action, .button-activate", function () {
            let button = $(this);
            let plugin = button.data("plugin");
            let action = button.data("action");

            if (plugin && action) {
                ezdHandlePluginAction(button, plugin, action);
            }
        });
        // Setup wizard scripts end
    });
})(jQuery);