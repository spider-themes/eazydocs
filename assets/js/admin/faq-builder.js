(function ($) {
    'use strict';

    $(document).ready(function () {
        const $installButtons = $('.ezd-btn-installer');
        const $responseContainers = $('.ezd-cta-section .ezd-installer-response');
        
        // Check if nonce exists
        if ( typeof ezd_faq_builder === 'undefined' || ! ezd_faq_builder.nonce ) {
            console.error('FAQ Builder nonce not found');
            $installButtons.prop('disabled', true).find('.button-text').text('Error: Configuration missing');
            return;
        }
        
        // Use localized ajaxurl or fall back to global
        const ajaxRequestUrl = ezd_faq_builder.ajaxurl || ajaxurl;
        
        // Handle install button click for all buttons
        $installButtons.on('click', function (e) {
            e.preventDefault();
            
            const $button = $(this);
            const $buttonText = $button.find('.button-text');
            const $spinner = $button.find('.spinner');
            const $responseContainer = $button.closest('.ezd-cta-section').find('.ezd-installer-response');

            // Disable button and show loading state
            $button.prop('disabled', true);
            $buttonText.text('Installing...');
            $spinner.fadeIn(200);
            $responseContainer.hide().empty();

            console.log('Starting installation...', {
                action: 'ezd_install_advanced_accordion',
                nonce: ezd_faq_builder.nonce,
                ajaxurl: ajaxRequestUrl
            });

            // AJAX request to install plugin
            $.ajax({
                url: ajaxRequestUrl,
                type: 'POST',
                data: {
                    action: 'install_plugin',
                    plugin_slug: 'advanced-accordion-block',
                    plugin_file: 'advanced-accordion-block.php',
                    nonce: ezd_faq_builder.nonce
                },
                timeout: 300000, // 5 minutes timeout
                success: function (response) {
                    console.log('AJAX Success:', response);
                    $spinner.fadeOut(200);
                    
                    if (response.success) {
                        // Success response
                        $buttonText.text('✓ Installed & Activated');
                        $responseContainer
                            .html('<strong>Success!</strong> ' + response.data.message)
                            .removeClass('ezd-response-error')
                            .addClass('ezd-response-success')
                            .fadeIn(400);
                        
                        // Optional: Hide the CTA section after successful installation
                        setTimeout(function() {
                            $('.ezd-cta-section').slideUp(600, function() {
                                const wpAdminUrl = ajaxRequestUrl.replace('/wp-admin/admin-ajax.php', '').replace('/admin-ajax.php', '');
                                $(this).html(
                                    '<div style="text-align: center; padding: 30px;">' +
                                    '<h2 style="color: #059669; margin-top: 0;">🎉 You\'re All Set!</h2>' +
                                    '<p style="color: #4b5563; margin-bottom: 20px;">Advanced Accordion Block has been successfully installed and activated.</p>' +
                                    '<a href="' + wpAdminUrl + '/wp-admin/post-new.php" class="ezd-btn-installer" style="display: inline-block; text-decoration: none;">Start Creating Accordions</a>' +
                                    '</div>'
                                ).slideDown(400);
                            });
                        }, 2000);

                    } else {
                        // Error response
                        $button.prop('disabled', false);
                        $buttonText.text('Try Again');
                        const errorMsg = response.data && response.data.message ? response.data.message : 'Unknown error occurred';
                        console.error('Installation error:', errorMsg);
                        $responseContainer
                            .html('<strong>Error:</strong> ' + errorMsg)
                            .removeClass('ezd-response-success')
                            .addClass('ezd-response-error')
                            .fadeIn(400);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // AJAX error
                    console.error('AJAX Error:', {
                        textStatus: textStatus,
                        errorThrown: errorThrown,
                        responseText: jqXHR.responseText,
                        status: jqXHR.status,
                        statusText: jqXHR.statusText,
                        readyState: jqXHR.readyState
                    });
                    
                    $spinner.fadeOut(200);
                    $button.prop('disabled', false);
                    $buttonText.text('Try Again');
                    
                    let errorMessage = 'An unexpected error occurred. Please try again.';
                    if (textStatus === 'timeout') {
                        errorMessage = 'Request timed out. The server may be busy. Please try again.';
                    } else if (textStatus === 'error') {
                        if (jqXHR.status === 500) {
                            errorMessage = 'Server error (500). There may be a configuration issue. Please check the server logs.';
                        } else if (jqXHR.status === 403) {
                            errorMessage = 'Access denied (403). You may not have permission to install plugins.';
                        } else if (jqXHR.status === 404) {
                            errorMessage = 'Endpoint not found (404). The AJAX handler may not be registered.';
                        } else {
                            errorMessage = 'Server error (' + jqXHR.status + '): ' + errorThrown;
                        }
                    } else if (textStatus === 'parsererror') {
                        errorMessage = 'Invalid response from server. Please try again.';
                    } else if (textStatus) {
                        errorMessage = 'Error: ' + textStatus;
                    }
                    
                    $responseContainer
                        .html('<strong>Error:</strong> ' + errorMessage)
                        .removeClass('ezd-response-success')
                        .addClass('ezd-response-error')
                        .fadeIn(400);
                }
            });
        });
    });
})(jQuery);
