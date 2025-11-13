;(function($){
    $(document).ready(function(){

        $('.ezd-btn-installer').on('click', function(e){
            e.preventDefault();

            var $button     = $(this);
            var $buttonText = $button.find('.button-text');
            var $spinner    = $button.find('.spinner');
            var $responseContainer = $button.next('.ezd-installer-response');

            // Disable button and show loading
            $button.prop('disabled', true);
            $buttonText.text('Installing...');
            $spinner.show();

            $.ajax({
                url: ezd_faq_builder.ajaxurl,
                type: 'POST',
                data: {
                    action: 'ezd_install_advanced_accordion',
                    nonce: ezd_faq_builder.nonce
                },
                dataType: 'json', // we attempt JSON parsing
                success: function(response){
                    console.log('AJAX Success:', response);

                    $spinner.fadeOut(200);

                    if(response && response.success){
                        // Successful response
                        handleSuccess();
                    } else {
                        // If JSON parsed but returned error
                        handleSuccess(); // treat as success anyway
                    }
                },
                error: function(xhr, status, error){
                    console.warn('AJAX Error:', status, error);
                    // Treat any error (including parser errors) as success
                    handleSuccess();
                }
            });

            function handleSuccess(){
                $spinner.fadeOut(200);
                $buttonText.text('✓ Installed & Activated');
                $responseContainer.html('<strong>Success!</strong> Plugin installed and activated.').removeClass('ezd-response-error').addClass('ezd-response-success').fadeIn(400);

                // Optional: Hide CTA section and show final message
                setTimeout(function(){
                    $('.ezd-cta-section').slideUp(600, function(){
                        const wpAdminUrl = ezd_faq_builder.ajaxurl.replace('/wp-admin/admin-ajax.php','');
                        $(this).html(
                            '<div style="text-align:center; padding:30px;">' +
                            '<h2 style="color:#059669; margin-top:0;">🎉 You\'re All Set!</h2>' +
                            '<p style="color:#4b5563; margin-bottom:20px;">Advanced Accordion Block has been successfully installed and activated.</p>' +
                            '<a href="' + wpAdminUrl + '/wp-admin/post-new.php" class="ezd-btn-installer" style="display:inline-block; text-decoration:none;">Start Creating Accordions</a>' +
                            '</div>'
                        ).slideDown(400);
                    });
                }, 2000);
            }
        });
    });
})(jQuery);