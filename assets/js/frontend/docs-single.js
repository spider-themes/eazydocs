;(function ($) {
    "use strict";

    $(document).ready(function() {

        // Match Fade gradient Shadow color
        let bgColor = window.getComputedStyle(document.body, null).getPropertyValue('background-color'),
            bgColorRGBA = bgColor.replace(')', ', 0)').replace('rgb', 'rgba')
        if ( bgColor ) {
            $('.fadeGradient').css( 'background', '-webkit-linear-gradient(bottom, '+bgColor+' 15%, '+bgColorRGBA+' 100%)' )
        }

        // Add scroll spy attributes to body
        $('body').attr({'data-bs-spy': 'scroll', 'data-bs-target': "#eazydocs-toc"})

        /**
         * Make the Titles clickable
         * If no selector is provided, it falls back to a default selector of:
         * 'h2, h3, h4, h5, h6'
         */
        anchors.add('.doc-scrollable h2, .doc-scrollable h3, .doc-scrollable h4');

        /** === Contact Form Ajax === **/
        $(document).on('submit', '#edocs-contact-form', function (e) {
            e.preventDefault();
            var thisForm = this;
            $.ajax({
                url: eazydocs_local_object.ajaxurl,
                data: $(thisForm).serialize(),
                action: 'eazydocs_feedback_email',
                type: "POST",
                success:function(data){
                    $(thisForm).append('<p>'+data.message+'</p>');
                },
                error:function (){
                    $(thisForm).append('<p> Ohh no! Something went wrong!! </p>');
                }
            })
        })

        /** === Feedback Handler === **/
        $('.vote-link-wrap a.h_btn').on('click', function (e) {
            e.preventDefault()
            let self = $(this)
            $.ajax({
                url: eazydocs_local_object.ajaxurl,
                method: "post",
                data: {
                    action: 'eazydocs_handle_feedback',
                    post_id: self.data('id'),
                    type: self.data('type'),
                    _wpnonce: eazydocs_local_object.nonce,
                },
                beforeSend: function () {
                    $(".eazydocs-feedback-wrap .vote-link-wrap").html('<div class="spinner-border spinner-border-sm" role="status">\n' +
                        '  <span class="visually-hidden">Loading...</span>\n' +
                        '</div>')
                },
                success: function (response) {
                    $(".eazydocs-feedback-wrap").html(response.data)
                    console.log(response.data)
                },
                error: function () {
                    console.log("Oops! Something wrong, try again!")
                }
            })
        })

        /*--------------- nav-sidebar js--------*/
        if ($('.nav-sidebar > li').hasClass('active')) {
            $(".nav-sidebar > li.active").find('ul').slideDown(700);
        }

        function active_dropdown( is_ajax = false ) {
            if ( is_ajax == true) {
                $(document).on('click', '.nav-sidebar .nav-item .nav-link', function (e) {
                    $('.nav-sidebar .nav-item').removeClass('active')
                    $(this).parent().addClass('active')
                    $(this).parent().find('ul').first().show(300)
                    $(this).parent().siblings().find('ul').hide(300)
                })
            } else {
                $('.nav-sidebar > li .icon').on('click', function (e) {
                    $(this).parent().find('ul').first().toggle(300)
                    $(this).parent().siblings().find('ul').hide(300)
                })
            }
        }
        active_dropdown()

        $('.nav-sidebar > li .icon').each(function () {
            let $this = $(this)
            $this.on('click', function (e) {
                let has = $this.parent().hasClass('active')
                $('.nav-sidebar li').removeClass('active')
                if (has) {
                    $this.parent().removeClass('active')
                } else {
                    $this.parent().addClass('active')
                }
            })
        })

        /**
         * Print doc
         */
        $('.pageSideSection .print').on('click', function (e) {
            e.preventDefault()
            $(".doc-middle-content .doc-post-content").printThis({
                'loadCSS': eazydocs_local_object.EAZYDOCS_FRONT_CSS + '/print.css',
            })
        })

        /**
         * Doc Menu
         */
        $('.doc_menu a[href^="#"]:not([href="#"]').on('click', function (event) {
            var $anchor = $(this)
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top
            }, 900)
            event.preventDefault()
        });

        /**
         * Left Sidebar Toggle icon
         */
        if ($(".doc_documentation_area").length > 0) {
            //switcher
            var switchs = true;
            $("#right").on("click", function (e) {
                e.preventDefault();
                if (switchs) {
                    $(".doc_documentation_area").addClass("overlay");
                    $(".doc_rightsidebar").animate({
                        "right": "0px"
                    }, 100);
                    switchs = false;
                } else {
                    $(".doc_documentation_area").removeClass("overlay");
                    $(".doc_rightsidebar").animate({
                        "right": "-250px"
                    }, 100);
                    switchs = true;
                }
            })

            $("#left").on("click", function (e) {
                e.preventDefault()
                if (switchs) {
                    $(".doc_documentation_area").addClass("overlay");
                    $(".left-column .doc_left_sidebarlist").animate({
                        "left": "0px"
                    }, 300);
                    switchs = false;
                } else {
                    $(".doc_documentation_area").removeClass("overlay");
                    $(".left-column .doc_left_sidebarlist").animate({
                        "left": "-300px"
                    }, 300);
                    switchs = true
                }
            });
        }

        // Mobile menu on the Doc single page
        $('.single-docs .mobile_menu_btn').on('click', function () {
            $('body').removeClass('menu-is-closed').addClass('menu-is-opened');
        });

        $('.single-docs .close_nav').on("click", function (e) {
            if ($('.side_menu').hasClass('menu-opened')) {
                $('.side_menu').removeClass('menu-opened')
                $('body').removeClass('menu-is-opened')
            } else {
                $('.side_menu').addClass('menu-opened')
            }
        });

        // Filter doc chapters
        if ( $('#doc_filter').length ) {
            $('#doc_filter').keyup(function () {
                var value = $(this).val().toLowerCase();
                $('.nav-sidebar .nav-item').each(function () {
                    var lcval = $(this).text().toLowerCase();
                    if (lcval.indexOf(value) > -1) {
                        $(this).show(500);
                    } else {
                        $(this).hide(500);
                    }
                });
            });

            document.getElementById("doc_filter").addEventListener("search", function (event) {
                $(".nav-sidebar .nav-item").show(300);
            });
        }

        // Collapse left sidebar
        function docLeftSidebarToggle() {
            let left_column = $('.doc_mobile_menu');
            let middle_column = $('.doc-middle-content');
            $('.left-sidebar-toggle .left-arrow').on('click', function () {
                $('.doc_mobile_menu').hide(500)

                if (middle_column.hasClass('col-xl-7')) {
                    $('.doc-middle-content').removeClass('col-xl-7').addClass('col-xl-10')
                } else if (middle_column.hasClass('col-xl-8')) {
                    $('.doc-middle-content').removeClass('col-xl-8').addClass('col-xl-10')
                }

                $('.left-sidebar-toggle .left-arrow').hide(500)
                $('.left-sidebar-toggle .right-arrow').show(500)
            })

            $('.left-sidebar-toggle .right-arrow').on('click', function () {
                $('.doc_mobile_menu').show(500)

                if ( middle_column.hasClass('col-xl-10') ) {
                    $('.doc-middle-content').removeClass('col-xl-10').addClass('col-xl-7')
                } else if (middle_column.hasClass('col-xl-8')) {
                    $('.doc-middle-content').removeClass('col-xl-10').addClass('col-xl-8')
                }

                $('.left-sidebar-toggle .left-arrow').show(500)
                $('.left-sidebar-toggle .right-arrow').hide(500)
            })
        }
        docLeftSidebarToggle();



        //*=============menu sticky js =============*//
        var $window = $(window);
        var didScroll,
            lastScrollTop = 0,
            delta = 5,
            $mainNav = $("#sticky"),
            $mainNavHeight = $mainNav.outerHeight(),
            scrollTop;

        $window.on("scroll", function () {
            didScroll = true;
            scrollTop = $(this).scrollTop();
        });

        setInterval(function () {
            if (didScroll && $('.navbar button.navbar-toggler.collapsed').length) {
                hasScrolled();
                didScroll = false;
            }
        }, 200)

        function hasScrolled() {
            if (Math.abs(lastScrollTop - scrollTop) <= delta) {
                return;
            }
            if (scrollTop > lastScrollTop && scrollTop > $mainNavHeight) {
                $mainNav.removeClass("fadeInDown").addClass("fadeInUp").css('top', -$mainNavHeight);
                $('body').removeClass('navbar-shown').addClass('navbar-hidden')
            } else {
                if (scrollTop + $(window).height() < $(document).height()) {
                    $mainNav.removeClass("fadeInUp").addClass("fadeInDown").css('top', 0);
                    $('body').removeClass('navbar-hidden').addClass('navbar-shown')
                }
            }
            lastScrollTop = scrollTop;
        }

        function navbarFixed() {
            if ($('#sticky').length) {
                $(window).scroll(function () {
                    var scroll = $(window).scrollTop();
                    if (scroll) {
                        $("#sticky").addClass("navbar_fixed");
                        $(".sticky-nav-doc .body_fixed").addClass("body_navbar_fixed");
                    } else {
                        $("#sticky").removeClass("navbar_fixed");
                        $(".sticky-nav-doc .body_fixed").removeClass("body_navbar_fixed");
                    }
                });
            }
        }
        navbarFixed();

        function navbarFixedTwo() {
            if ($('#stickyTwo').length) {
                $(window).scroll(function () {
                    var scroll = $(window).scrollTop();
                    if (scroll) {
                        $("#stickyTwo").addClass("navbar_fixed");
                    } else {
                        $("#stickyTwo").removeClass("navbar_fixed");
                    }
                });
            }
        }
        navbarFixedTwo()

        function mobileNavbarFixed() {
            if ($('#mobile-sticky').length) {
                $(window).scroll(function () {
                    var scroll = $(window).scrollTop();
                    if (scroll) {
                        $("#mobile-sticky").addClass("navbar_fixed");
                    } else {
                        $("#mobile-sticky").removeClass("navbar_fixed");
                    }
                })
            }
        }
        mobileNavbarFixed();

        function mobileNavbarFixedTwo() {
            if ($('#mobile-stickyTwo').length) {
                $(window).scroll(function () {
                    var scroll = $(window).scrollTop();
                    if (scroll) {
                        $("#mobile-stickyTwo").addClass("navbar_fixed");
                    } else {
                        $("#mobile-stickyTwo").removeClass("navbar_fixed");
                    }
                })
            }
        }
        mobileNavbarFixedTwo()

        //*=============menu sticky js =============*//

        //  page scroll
        function bodyFixed() {
            var windowWidth = $(window).width();
            if ($('#sticky_doc').length) {
                if (windowWidth > 576) {
                    var tops = $('#sticky_doc');
                    var leftOffset = tops.offset().top;

                    $(window).on('scroll', function () {
                        var scroll = $(window).scrollTop();
                        if (scroll >= leftOffset) {
                            tops.addClass("body_fixed");
                        } else {
                            tops.removeClass("body_fixed");
                        }
                    });
                }
            }
        }

        bodyFixed();

        // TOC area
        function bodyFixed2() {
            var windowWidth = $(window).width();

            if ($("#sticky_doc2").length) {
                if (windowWidth > 576) {
                    var tops = $("#sticky_doc2");
                    var topOffset = tops.offset().top;
                    var blogForm = $('.blog_comment_box');
                    var blogFormTop = blogForm.offset().top - 300;

                    $(window).on("scroll", function () {
                        var scrolls = $(window).scrollTop();
                        if (scrolls >= topOffset) {
                            tops.addClass("stick");
                        } else {
                            tops.removeClass("stick");
                        }
                    });


                    $('a[href="#hackers"]').click(function () {
                        $("#hackers").css("padding-top", "100px");

                        $(window).on("scroll", function () {
                            var hackersOffset = $("#hackers").offset().top;
                            var scrolls = $(window).scrollTop();
                            if (scrolls < hackersOffset) {
                                $("#hackers").css("padding-top", "0px");
                            }
                        })
                    });
                }
            }
        }

        bodyFixed2();


        /*  Menu Click js  */
        function Menu_js() {
            if ($('.submenu').length) {
                $('.submenu > .dropdown-toggle').click(function () {
                    var location = $(this).attr('href');
                    window.location.href = location;
                    return false;
                })
            }
        }

        Menu_js();

        /**
         * Load Doc single page via ajax
         */
        if ( eazydocs_local_object.is_doc_ajax == '1' ) {
            $(document).on('click', '.nav-sidebar .nav-item .dropdown_nav li a', function (e) {
                e.preventDefault()
                let self = $(this)
                let title = self.text()
                let postid = $(this).attr('data-postid')

                function changeurl(page_title) {
                    let new_url = self.attr('href');
                    window.history.pushState("data", "Title", new_url);
                    document.title = page_title;
                }

                $.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    method: "post",
                    data: {
                        action: 'docs_single_content',
                        postid: postid
                    },
                    beforeSend: function () {
                        $("#reading-progress-fill").css({'width': '100%', 'display': 'block'});
                    },
                    success: function (response) {
                        $("#reading-progress-fill").css({'display': 'none'});
                        $(".doc-middle-content").html(response);
                        changeurl(title)
                        $('.nav-sidebar .nav-item .dropdown_nav li a').removeClass('active')
                        self.addClass('active')
                        // Toc
                        $('#eazydocs-toc').empty();
                        Toc.init({
                            $nav: $("#eazydocs-toc"),
                            $scope: $('.doc-scrollable')
                        });
                        docLeftSidebarToggle()
                    },
                    error: function () {
                        console.log("Oops! Something wrong, try again!");
                    }
                })
            })

            $(document).on('click', '.nav-sidebar .nav-item .nav-link', function (e) {
                e.preventDefault();
                let self = $(this)
                let title = self.text()
                let postid = $(this).attr('data-postid')

                function changeurl(page_title) {
                    let new_url = self.attr('href');
                    window.history.pushState("data", "Title", new_url);
                    document.title = page_title;
                }

                $.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    method: "post",
                    data: {
                        action: 'docs_single_content',
                        postid: postid
                    },
                    beforeSend: function () {
                        $("#reading-progress-fill").css({'width': '100%', 'display': 'block'});
                    },
                    success: function (response) {
                        active_dropdown(true)
                        $("#reading-progress-fill").css({'display': 'none'});
                        $(".doc-middle-content").html(response);
                        changeurl(title)
                        docLeftSidebarToggle()
                        // Toc
                        $('#eazydocs-toc').empty();
                        Toc.init({
                            $nav: $("#eazydocs-toc"),
                            $scope: $('.doc-scrollable')
                        });
                    },
                    error: function () {
                        console.log("Oops! Something wrong, try again!");
                    }
                })
            })
        }

        /*------------ Cookie functions and color js ------------*/
        function createCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        }

        function readCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        function eraseCookie(name) {
            createCookie(name, "", -1);
        }

        let prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        let selectedNightTheme = readCookie("body_dark");

        if (selectedNightTheme == "true" || (selectedNightTheme === null && prefersDark)) {
            applyNight();
            $('#ezd_dark_switch').prop('checked', true);
        } else {
            applyDay();
            $('#ezd_dark_switch').prop('checked', false);
        }

        function applyNight() {
            $("body").addClass("body_dark");
            $(".light-mode").removeClass("active");
            $(".dark-mode").addClass("active");
        }

        function applyDay() {
            $("body").removeClass("body_dark");
            $(".dark-mode").removeClass("active");
            $(".light-mode").addClass("active");
        }

        $('#ezd_dark_switch').change(function () {
            if ($(this).is(':checked')) {
                applyNight();
                $(".tab-btns").removeClass("active");
                createCookie("body_dark", true, 999)
            } else {
                applyDay();
                $(".tab-btns").addClass("active");
                createCookie("body_dark", false, 999);
            }
        });

    })
})(jQuery);