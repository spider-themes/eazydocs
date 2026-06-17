;(function ($) {
    'use strict'

    $(document).ready(function () {

        /**
         * Load Doc single page via ajax
         */
        if ( eazydocs_local_object.is_doc_ajax == '1' ) {
            const elementorDocs = eazydocs_local_object.elementor_docs || [];
            const $content      = $('.doc-middle-content');

            // Track the in-flight request so a rapid second click can cancel the
            // first one instead of racing it (last click wins).
            let activeRequest = null;

            /**
             * Toggle the loading state on the content area.
             *
             * Adds an overlay + spinner and marks the region busy for assistive
             * tech while the next doc is fetched.
             *
             * @param {boolean} isLoading Whether a request is in progress.
             */
            function setLoading( isLoading ) {
                if ( isLoading ) {
                    if ( ! $content.children('.ezd-ajax-loader').length ) {
                        $content.append(
                            '<div class="ezd-ajax-loader" role="status" aria-live="polite">' +
                                '<span class="ezd-ajax-spinner" aria-hidden="true"></span>' +
                                '<span class="screen-reader-text">' +
                                    ( eazydocs_local_object.i18n_loading || 'Loading…' ) +
                                '</span>' +
                            '</div>'
                        );
                    }
                    $content.addClass('ezd-is-loading').attr('aria-busy', 'true');
                } else {
                    $content.removeClass('ezd-is-loading').attr('aria-busy', 'false');
                    $content.children('.ezd-ajax-loader').remove();
                }
            }

            $('.single-docs .nav-sidebar .nav-item .nav-link, .single-docs .nav-sidebar .nav-item .dropdown_nav li a').on('click', function (e) {
                let self    = $(this);
                const postid = parseInt( self.attr('data-postid'), 10 );
                if ( isNaN( postid ) || elementorDocs.indexOf( postid ) !== -1 ) {
                    return;
                }

                e.preventDefault();

                // Already on this doc (or its request is mid-flight) — do nothing.
                if ( self.hasClass('active') && ! activeRequest ) {
                    return;
                }

                let title = self.text();

                function changeurl(page_title) {
                    let new_url = self.attr('href');
                    window.history.pushState('data', 'Title', new_url);
                    document.title = page_title;
                }

                // Cancel any request still in progress before starting a new one.
                if ( activeRequest ) {
                    activeRequest.abort();
                }

                activeRequest = $.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    method: 'post',
                    data: {
                        action: 'docs_single_content',
                        postid: postid,
                        security: eazydocs_local_object.nonce,
                    },
                    beforeSend: function () {
                        setLoading( true );
                        $('#reading-progress-fill').css({
                            width: '100%',
                            display: 'block',
                        });
                    },
                    success: function (response) {
                        if ( ! response || ! response.success || ! response.data ) {
                            const message = ( response && response.data && response.data.message )
                                ? response.data.message
                                : ( eazydocs_local_object.i18n_error || 'Something went wrong. Please try again.' );
                            $content.html( '<div class="ezd-ajax-error" role="alert">' + message + '</div>' );
                            return;
                        }

                        $content.html(response.data.content);
                        $('.ezd-breadcrumb time span').text(response.data.modified_date);
                        $('nav .breadcrumb .breadcrumb-item:last-child').text(title);
                        changeurl(title);

                        if (typeof window.ezd_heading_anchors === 'function') {
                            window.ezd_heading_anchors();
                        }

                        // Remove 'active' classes from all links and items
                        $('.nav-sidebar .nav-item').removeClass('current_page_item active');
                        $('.nav-sidebar .menu-link').removeClass('active');

                        // Activate the clicked link
                        self.addClass('active');

                        // Activate all parent .nav-item elements
                        self.parents('.nav-item').addClass('current_page_item active');

                        // Hide all dropdown_nav elements first
                        $('.nav-sidebar .dropdown_nav').css('display', 'none');

                        // Show only the dropdowns in the active trail
                        self.parents('.dropdown_nav').css('display', 'block');

                        // If this link has children, show its dropdown_nav too
                        const nextDropdown = self.closest('.nav-item').children('.dropdown_nav');
                        if (nextDropdown.length > 0) {
                            nextDropdown.css('display', 'block');
                        }

                        // TOC Update
                        $('#eazydocs-toc').empty();
                        Toc.init({
                            $nav: $('#eazydocs-toc'),
                            $scope: $('.doc-scrollable'),
                        });

                        if (typeof window.ezd_refresh_scrollspy === 'function') {
                            window.ezd_refresh_scrollspy();
                        }

                        // Bring the reader back to the top of the freshly loaded doc.
                        if ( $content.length ) {
                            $('html, body').animate(
                                { scrollTop: $content.offset().top - 120 },
                                300
                            );
                        }
                    },
                    error: function (jqXHR, textStatus) {
                        // Ignore aborts triggered by a newer click.
                        if ( 'abort' === textStatus ) {
                            return;
                        }
                        $content.html(
                            '<div class="ezd-ajax-error" role="alert">' +
                                ( eazydocs_local_object.i18n_error || 'Something went wrong. Please try again.' ) +
                            '</div>'
                        );
                    },
                    complete: function (jqXHR, textStatus) {
                        // Only clear the loading UI for the request that actually
                        // finished — an aborted request is superseded by a newer one
                        // that owns the spinner now.
                        if ( 'abort' === textStatus ) {
                            return;
                        }
                        activeRequest = null;
                        setLoading( false );
                        $('#reading-progress-fill').css({ display: 'none', width: '0%' });
                    },
                });
            });
        }
    });
})(jQuery);
