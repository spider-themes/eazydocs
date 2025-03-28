<div class="focus_overlay"></div>
<form action="<?php echo esc_url(home_url('/')) ?>" role="search" method="get" class="ezd_search_form" >
    <div class="header_search_form_info search_form_wrap">
        <div class="form-group ezd-<?php echo esc_attr($settings['btn-position'] ?? ''); ?>">
            <div class="input-wrapper">
                <input type='search' class="search_field_wrap" id="ezd_searchInput" autocomplete="off" name="s"  placeholder="<?php echo esc_attr($settings['placeholder']) ?>">
                <!-- Ajax Search Loading Spinner -->
                <span class="spinner-border spinner"> </span>
                <button type="submit" class="search_submit_btn">
                    <?php \Elementor\Icons_Manager::render_icon( $settings['submit_btn_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                </button>
            </div>
        </div>
    </div>
    
    <?php 
    include('ajax-sarch-results.php');
    include('keywords.php');
    ?>
</form>

<script>
    jQuery(document).ready(function() {
        jQuery("#ezd_searchInput").focus(function() {
            jQuery('body').addClass('ezd-search-focused');
            jQuery('form.ezd_search_form').css('z-index', '999');
        })

        jQuery(".focus_overlay").click(function() {
            jQuery('body').removeClass('ezd-search-focused');
            jQuery('form.ezd_search_form').css('z-index', 'unset');
        })

        /**
         * Search Form Keywords
         */
        jQuery(".header_search_keyword ul li a").on("click", function(e) {
            e.preventDefault()
            var content = jQuery(this).text()
            jQuery("#ezd_searchInput").val(content).focus()
            ezSearchResults()
        })

        function ezSearchResults() {
            let keyword = jQuery('#ezd_searchInput').val();
            let noresult = jQuery('#ezd-search-results').attr('data-noresult');

            if (keyword == "") {
                jQuery('#ezd-search-results').removeClass('ajax-search').html("")
            } else {
                jQuery.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'eazydocs_search_results',
                        keyword: keyword
                    },
                    beforeSend: function() {
                        jQuery(".spinner-border").show();
                    },
                    success: function(data) {
                        jQuery(".spinner-border").hide();
                        // hide search results by pressing Escape button
                        jQuery(document).keyup(function(e) {
                            if (e.key === "Escape") { // escape key maps to keycode `27`
                                jQuery('#ezd-search-results').removeClass('ajax-search').html("")
                            }
                        });
                        if (data.length > 0) {
                            jQuery('#ezd-search-results').addClass('ajax-search').html(data);
                        } else {
                            var data_error = '<h5 class="error title">' + noresult + '</h5>';
                            jQuery('#ezd-search-results').html(data_error);
                        }
                    }
                })
            }
        }

        function ezdFetchDelay(callback, ms) {
            var timer = 0;
            return function() {
                var context = this,
                    args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function() {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }

        jQuery('#ezd_searchInput').keyup(
            ezdFetchDelay(function(e) {
                let keyword = jQuery('#ezd_searchInput').val();
                let noresult = jQuery('#ezd-search-results').attr('data-noresult');

                if (keyword == "") {
                    jQuery('#ezd-search-results').removeClass('ajax-search').html("")
                } else {
                    jQuery.ajax({
                        url: eazydocs_local_object.ajaxurl,
                        type: 'post',
                        data: {
                            action: 'eazydocs_search_results',
                            keyword: keyword
                        },
                        beforeSend: function() {
                            jQuery(".spinner-border").show();
                        },
                        success: function(data) {
                            jQuery(".spinner-border").hide();
                            // hide search results by pressing Escape button
                            jQuery(document).keyup(function(e) {
                                if (e.key === "Escape") { // escape key maps to keycode `27`
                                    jQuery('#ezd-search-results').removeClass('ajax-search').html(
                                        "")
                                }
                            });
                            if (data.length > 0) {
                                jQuery('#ezd-search-results').addClass('ajax-search').html(data);
                            } else {
                                var data_error = '<h5 class="error title">' + noresult + '</h5>';
                                jQuery('#ezd-search-results').html(data_error);
                            }
                        }
                    })
                }
            }, 500)
        );
    });

    // Search results should close on clearing the input field
    if ( document.getElementById('ezd_searchInput') ) {
        document.getElementById('ezd_searchInput').addEventListener('search', function (event) {
            jQuery('#ezd-search-results').empty().removeClass('ajax-search');
        });
    }

    // Prevent form submission when pressing Enter in the search input field
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("ezd_searchInput");
        searchInput.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Prevent form submission
            }
        });
    });
</script>