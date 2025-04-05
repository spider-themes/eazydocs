;(function ($) {
    'use strict'

    $(document).ready(function () {

        /**
         * Load Doc single page via ajax
         */
        if ( eazydocs_local_object.is_doc_ajax == '1' ) {

            $('.single-docs .nav-sidebar .nav-item .dropdown_nav li a').on('click', function (e) {
                e.preventDefault();
                let self = $(this);
                let title = self.text();
                let postid = $(this).attr('data-postid');

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
                    },
                    beforeSend: function () {
                        $('#reading-progress-fill').css({
                            width: '100%',
                            display: 'block',
                        });
                    },
                    success: function (response) {
                        $('#reading-progress-fill').css({
                            display: 'none',
                        });
                        $('.doc-middle-content').html(response);
                        changeurl(title);
                        
                        $('.nav-sidebar .nav-item').removeClass('current_page_item active');
                        $('.nav-sidebar .nav-item .dropdown_nav li a').removeClass('active');

                        if (!self.parent().parent().hasClass('has_child')) {
                            self.addClass('active');
                            self.parent().addClass('current_page_item active');
                        } else if (self.parent().parent().hasClass('has_child')) {
                            self.parent().parent().addClass('current_page_item active');
                        }
                        
                        $('.nav-sidebar .nav-item.current_page_item.active .dropdown_nav').css('display', 'block').closest('.nav-item').siblings().find('.dropdown_nav').css('display', 'none');
                        
                        // Toc
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

            $('.single-docs .nav-sidebar .nav-item .nav-link').on('click', function (e) {
                    e.preventDefault();
                    let self = $(this);
                    let title = self.text();
                    let postid = $(this).attr('data-postid');

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
                        },
                        beforeSend: function () {
                            $('#reading-progress-fill').css({width: '100%', display: 'block'});
                        },
                        success: function (response) {
                            $('#reading-progress-fill').css({display: 'none'});
                            $('.doc-middle-content').html(response);
                            changeurl(title);
                            $('.nav-sidebar .nav-item').removeClass('current_page_item active');
                            self.addClass('active');
                            self.parent().parent().addClass('current_page_item active');
                            
                            $('.nav-sidebar .nav-item.current_page_item.active .dropdown_nav').css('display', 'block').closest('.nav-item').siblings().find('.dropdown_nav').css('display', 'none');
                            
                            // Toc
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
                }
            );
        }
    });
})(jQuery);