;(function ($) {
    'use strict'

    $(document).ready(function () {

        /**
         * Load Doc single page via ajax
         */
        if ( eazydocs_local_object.is_doc_ajax == '1' ) {
            $('.single-docs .nav-sidebar .nav-item .nav-link, .single-docs .nav-sidebar .nav-item .dropdown_nav li a').on('click', function (e) {
                e.preventDefault();
                let self    = $(this);
                let title   = self.text();
                let postid  = $(this).attr('data-postid');

                function changeurl(page_title) {
                    let new_url = self.attr('href');
                    window.history.pushState('data', 'Title', new_url);
                    document.title = page_title;
                }

                $.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    method: 'post',
                    data: {
                        action: 'docs_single_content',
                        postid: postid,
                        security: eazydocs_local_object.nonce,
                    },
                    beforeSend: function () {
                        $('#reading-progress-fill').css({
                            width: '100%',
                            display: 'block',
                        });
                    },
                    success: function (response) {
                        $('#reading-progress-fill').css({ display: 'none' });
                        $('.doc-middle-content').html(response.data.content);
                        $('.ezd-breadcrumb time span').text(response.data.modified_date);
                        changeurl(title);

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
                    },
                    error: function () {
                        console.log('Oops! Something wrong, try again!');
                    },
                });
            });
        }
    });
})(jQuery);