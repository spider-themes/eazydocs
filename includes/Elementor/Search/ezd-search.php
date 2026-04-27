<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="focus_overlay"></div>

<form action="<?php echo esc_url(home_url('/')) ?>" role="search" method="get" class="ezd_search_form" >
    <div class="header_search_form_info search_form_wrap">
        <?php
        $filter_enabled = ( $settings['show_post_type_filter'] ?? '' ) === 'yes' && ! empty( $settings['filter_post_types'] );
        $has_filter_cls = $filter_enabled ? ' has-type-filter' : '';
        $type_labels    = [
            'docs' => __( 'Docs', 'eazydocs' ),
            'page' => __( 'Page', 'eazydocs' ),
            'post' => __( 'Post', 'eazydocs' ),
        ];
        ?>
        <div class="form-group ezd-<?php echo esc_attr( $settings['btn-position'] ?? '' ); ?><?php echo esc_attr( $has_filter_cls ); ?>">
            <div class="input-wrapper">
                <input type='search' class="search_field_wrap" id="ezd_searchInput" autocomplete="off" name="s" placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>">
                <!-- Ajax Search Loading Spinner -->
                <span class="spinner-border spinner"> </span>
                <button type="submit" class="search_submit_btn">
                    <?php \Elementor\Icons_Manager::render_icon( $settings['submit_btn_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                </button>
                <?php if ( $filter_enabled ) :
                    $filter_types = $settings['filter_post_types'];
                ?>
                <div class="ezd-type-filter">
                    <button type="button" class="ezd-type-filter-btn">
                        <span class="ezd-filter-label"><?php esc_html_e( 'All', 'eazydocs' ); ?></span>
                        <svg viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5" width="10" height="10" aria-hidden="true"><path d="M1 1l4 4 4-4"/></svg>
                    </button>
                    <ul class="ezd-type-filter-dropdown">
                        <li class="active" data-type="all"><?php esc_html_e( 'All', 'eazydocs' ); ?></li>
                        <?php foreach ( $filter_types as $type ) : ?>
                            <li data-type="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type_labels[ $type ] ?? ucfirst( $type ) ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php 
    include('ajax-sarch-results.php');
    include('keywords.php');
    ?>
</form>

<?php
add_action('wp_footer', function() {
    ?>
    <script>
        jQuery(document).ready(function() {
            jQuery("#ezd_searchInput").focus(function() {
                jQuery('body').addClass('ezd-search-focused');
                jQuery('form.ezd_search_form').css('z-index', '999');
            });

            jQuery(document).on('click', '.ezd-tab', function() {
                var tab = jQuery(this).data('tab');
                jQuery('.ezd-tab').removeClass('active');
                jQuery(this).addClass('active');
                if ( tab === 'all' ) {
                    jQuery('#ezd-search-results .ezd-result-group').show();
                } else {
                    jQuery('#ezd-search-results .ezd-result-group').hide();
                    jQuery('#ezd-search-results .ezd-result-group[data-type="' + tab + '"]').show();
                }
            });

            var selectedPostType = 'all';

            jQuery(document).on('click', '.ezd-type-filter-btn', function(e) {
                e.stopPropagation();
                var $dropdown = jQuery(this).next('.ezd-type-filter-dropdown');
                $dropdown.toggleClass('open');
                jQuery(this).toggleClass('open');
            });

            jQuery(document).on('click', '.ezd-type-filter-dropdown li', function() {
                var type  = jQuery(this).data('type');
                var label = jQuery(this).text();
                selectedPostType = type;
                jQuery('.ezd-type-filter-dropdown li').removeClass('active');
                jQuery(this).addClass('active');
                jQuery('.ezd-filter-label').text(label);
                jQuery(this).closest('.ezd-type-filter-dropdown').removeClass('open');
                jQuery('.ezd-type-filter-btn').removeClass('open');
                if ( jQuery('#ezd_searchInput').val().trim() !== '' ) {
                    ezSearchResults();
                }
            });

            jQuery(document).on('click', function(e) {
                if ( ! jQuery(e.target).closest('.ezd-type-filter').length ) {
                    jQuery('.ezd-type-filter-dropdown').removeClass('open');
                    jQuery('.ezd-type-filter-btn').removeClass('open');
                }
            });

            jQuery(".focus_overlay").click(function() {
                jQuery('body').removeClass('ezd-search-focused');
                jQuery('form.ezd_search_form').css('z-index', 'unset');
            });

            /**
             * Search Form Keywords
             */
            jQuery(".header_search_keyword ul li a").on("click", function(e) {
                e.preventDefault()
                var content = jQuery(this).text()
                jQuery("#ezd_searchInput").val(content).focus()
                ezSearchResults()
            })

            function ezdBuildNoResult() {
                var $r    = jQuery('#ezd-search-results');
                var img   = $r.data('noresult-img');
                var title = $r.data('noresult-title') || $r.attr('data-noresult') || 'No Results Found';
                var sub   = $r.data('noresult-sub');
                var imgHtml = img ? '<div class="ezd-no-results-img"><img src="' + img + '" alt=""></div>' : '';
                var subHtml = sub ? '<p class="ezd-no-results-sub">' + sub + '</p>' : '';
                $r.addClass('ajax-search').html(
                    '<div class="ezd-no-results">' + imgHtml +
                    '<h4 class="ezd-no-results-title">' + title + '</h4>' +
                    subHtml + '</div>'
                );
            }

            function ezSearchResults() {
                let keyword = jQuery('#ezd_searchInput').val();

                if (keyword.trim() === "") {
                    jQuery('#ezd-search-results').removeClass('ajax-search').html("")
                } else {
                    jQuery.ajax({
                        url: eazydocs_local_object.ajaxurl,
                        type: 'post',
                        data: {
                            action: 'eazydocs_search_results',
                            keyword: keyword,
                            post_type: selectedPostType,
                            security: eazydocs_local_object.nonce
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
                            if (data.trim().length > 0) {
                                jQuery('#ezd-search-results').addClass('ajax-search').html(data);
                            } else {
                                ezdBuildNoResult();
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

                    if (keyword.trim() === "") {
                        jQuery('#ezd-search-results').removeClass('ajax-search').html("")
                    } else {
                        jQuery.ajax({
                            url: eazydocs_local_object.ajaxurl,
                            type: 'post',
                            data: {
                                action: 'eazydocs_search_results',
                                keyword: keyword,
                                post_type: selectedPostType,
                                security: eazydocs_local_object.nonce
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
                                if (data.trim().length > 0) {
                                    jQuery('#ezd-search-results').addClass('ajax-search').html(data);
                                } else {
                                    ezdBuildNoResult();
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
            if (searchInput) {
                searchInput.addEventListener("keypress", function (event) {
                    if (eazydocs_local_object.ezd_search_submit != 1) {
                        if (event.key === "Enter") {
                            event.preventDefault(); // Prevent form submission
                        }
                    }
                });
            }
        });

        jQuery("button.search_submit_btn").on("click", function(e) {
            if (eazydocs_local_object.ezd_search_submit != 1) {
                e.preventDefault(); // stop the form from submitting
                return false;
            }
            jQuery(".ezd_search_form").submit();
        });

        // Prevent empty search submit
        jQuery('.ezd_search_form').on('submit', function(e) {
            let keyword = jQuery('#ezd_searchInput').val().trim();

            // If empty or only sp  aces, stop search submit
            if (keyword === "") {
                e.preventDefault(); // Stop submit
                return false;
            }
        });

    </script>
    <?php
});
