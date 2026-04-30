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
        $filter_enabled = ( $settings['show_post_type_filter'] ?? '' ) === 'yes';
        $raw_type       = $settings['filter_post_types'] ?? 'all';
        // handle legacy array format (old multi-select) → treat as 'all'
        $selected_type  = $filter_enabled && ! is_array( $raw_type ) ? sanitize_key( $raw_type ) : 'all';
        $allowed_types  = [ 'all', 'docs', 'page', 'post' ];
        if ( ! in_array( $selected_type, $allowed_types, true ) ) {
            $selected_type = 'all';
        }
        $type_labels = [
            'all'  => __( 'All', 'eazydocs' ),
            'docs' => __( 'Docs', 'eazydocs' ),
            'page' => __( 'Page', 'eazydocs' ),
            'post' => __( 'Post', 'eazydocs' ),
        ];
        $has_filter_cls = $filter_enabled ? ' has-type-filter' : '';
        ?>
        <div class="form-group ezd-<?php echo esc_attr( $settings['btn-position'] ?? '' ); ?><?php echo esc_attr( $has_filter_cls ); ?>">
            <div class="input-wrapper">
                <input type='search' class="search_field_wrap" id="ezd_searchInput" autocomplete="off" name="s"
                    placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>"
                    data-post-type="<?php echo esc_attr( $selected_type ); ?>">
                <!-- Ajax Search Loading Spinner -->
                <span class="spinner-border spinner"> </span>
                <button type="submit" class="search_submit_btn">
                    <?php \Elementor\Icons_Manager::render_icon( $settings['submit_btn_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                </button>

                <?php if ( $filter_enabled ) :
                    if ( $selected_type === 'all' ) {
                        // Type switcher only — no post data fetched
                        $has_dropdown = true;
                    } elseif ( $selected_type === 'docs' ) {
                        // Only top-level (parent) docs in the dropdown
                        $browse_posts = get_posts( [
                            'post_type'      => 'docs',
                            'post_parent'    => 0,
                            'posts_per_page' => -1,
                            'orderby'        => 'menu_order',
                            'order'          => 'ASC',
                            'post_status'    => 'publish',
                        ] );
                        $has_dropdown = ! empty( $browse_posts );
                    } else {
                        $browse_posts = get_posts( [
                            'post_type'      => $selected_type,
                            'posts_per_page' => 15,
                            'orderby'        => 'title',
                            'order'          => 'ASC',
                            'post_status'    => 'publish',
                        ] );
                        $has_dropdown = ! empty( $browse_posts );
                    }
                ?>
                <div class="ezd-type-filter">
                    <button type="button" class="ezd-type-filter-btn">
                        <span class="ezd-filter-label"><?php echo esc_html( $type_labels[ $selected_type ] ); ?></span>
                        <svg viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5" width="10" height="10" aria-hidden="true"><path d="M1 1l4 4 4-4"/></svg>
                    </button>
                    <?php if ( $has_dropdown ) : ?>
                    <ul class="ezd-type-filter-dropdown ezd-title-dropdown">
                        <?php if ( $selected_type === 'all' ) : ?>
                            <?php foreach ( [ 'all', 'docs', 'page', 'post' ] as $_pt ) : ?>
                            <li><a href="#" class="ezd-type-option" data-type="<?php echo esc_attr( $_pt ); ?>"><?php echo esc_html( $type_labels[ $_pt ] ); ?></a></li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <?php foreach ( $browse_posts as $p ) : ?>
                            <li><a href="<?php echo esc_url( get_permalink( $p->ID ) ); ?>" data-id="<?php echo esc_attr( $p->ID ); ?>"><?php echo esc_html( $p->post_title ); ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
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

            // Read configured post type from input data attribute
            var selectedPostType = jQuery('#ezd_searchInput').data('post-type') || 'all';

            // Dropdown toggle — only rendered when configured type is 'all'
            jQuery(document).on('click', '.ezd-type-filter-btn', function(e) {
                e.stopPropagation();
                var $dropdown = jQuery(this).next('.ezd-type-filter-dropdown');
                $dropdown.toggleClass('open');
                jQuery(this).toggleClass('open');
            });

            // Type option click (when "All" is configured) — updates the active filter and re-searches
            jQuery(document).on('click', '.ezd-type-option', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var newType  = jQuery(this).data('type');
                var newLabel = jQuery(this).text().trim();
                selectedPostType = newType;
                jQuery('#ezd_searchInput').attr('data-post-type', newType);
                jQuery('.ezd-filter-label').text(newLabel);
                jQuery('.ezd-type-filter-dropdown').removeClass('open');
                jQuery('.ezd-type-filter-btn').removeClass('open');
                // Re-run search with updated type if there is already a query
                var kw = jQuery('#ezd_searchInput').val().trim();
                if ( kw.length > 0 ) {
                    ezSearchResults();
                }
            });

            // Dropdown title click: for docs → load child docs in results; others → navigate
            jQuery(document).on('click', '.ezd-title-dropdown li a:not(.ezd-type-option)', function(e) {
                e.preventDefault();
                var postId      = jQuery(this).data('id');
                var postTitle   = jQuery(this).text().trim();
                var fallbackUrl = jQuery(this).attr('href');

                jQuery('.ezd-type-filter-dropdown').removeClass('open');
                jQuery('.ezd-type-filter-btn').removeClass('open');

                if ( selectedPostType !== 'docs' ) {
                    window.location.href = fallbackUrl;
                    return;
                }

                jQuery('#ezd_searchInput').val('');
                jQuery('#ezd-search-results').addClass('ajax-search').html(
                    '<div class="ezd-panel-loading"><span class="spinner-border spinner-border-sm" role="status"></span></div>'
                );

                jQuery.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    type: 'post',
                    data: {
                        action:    'eazydocs_child_docs',
                        parent_id: postId,
                        security:  eazydocs_local_object.nonce
                    },
                    success: function(response) {
                        if ( response.success && response.data.html.trim().length > 0 ) {
                            jQuery('#ezd-search-results').addClass('ajax-search').html(
                                '<div class="ezd-result-group-label">' + postTitle + '</div>' +
                                response.data.html
                            );
                        } else {
                            ezdBuildNoResult();
                        }
                    },
                    error: function() {
                        ezdBuildNoResult();
                    }
                });
            });

            // Seamless input↔results join: toggle class on form when results appear/disappear
            var _resultsEl = document.getElementById('ezd-search-results');
            if (_resultsEl) {
                var _$form = jQuery(_resultsEl).closest('form.ezd_search_form');
                new MutationObserver(function(mutations) {
                    mutations.forEach(function(m) {
                        if (m.attributeName === 'class') {
                            _$form.toggleClass('ezd-results-open', _resultsEl.classList.contains('ajax-search'));
                        }
                    });
                }).observe(_resultsEl, { attributes: true });
            }

            jQuery(document).on('click', function(e) {
                if ( !jQuery(e.target).closest('.ezd-type-filter').length ) {
                    jQuery('.ezd-type-filter-dropdown').removeClass('open');
                    jQuery('.ezd-type-filter-btn').removeClass('open');
                }
            });

            jQuery(".focus_overlay").click(function() {
                jQuery('#ezd-search-results').removeClass('ajax-search').html('');
                jQuery('body').removeClass('ezd-search-focused');
                jQuery('form.ezd_search_form').css('z-index', 'unset');
            });

            // Close results when clicking outside the search form
            jQuery(document).on('mousedown', function(e) {
                var $t = jQuery(e.target);
                if (
                    !$t.closest('#ezd-search-results').length &&
                    !$t.closest('.header_search_form_info').length &&
                    !$t.closest('.focus_overlay').length
                ) {
                    jQuery('#ezd-search-results').removeClass('ajax-search').html('');
                    jQuery('body').removeClass('ezd-search-focused');
                    jQuery('form.ezd_search_form').css('z-index', 'unset');
                }
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

            function ezdApplyTabVisibility() {
                if ( jQuery('#ezd-search-results').data('show-tabs') == '0' ) {
                    jQuery('#ezd-search-results .ezd-result-tabs').hide();
                }
            }

            function ezdActivateTab() {
                if (selectedPostType !== 'all') {
                    jQuery('#ezd-search-results .ezd-tab[data-tab="' + selectedPostType + '"]').trigger('click');
                }
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
                                ezdApplyTabVisibility();
                                ezdActivateTab();
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
                                    ezdApplyTabVisibility();
                                    ezdActivateTab();
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
