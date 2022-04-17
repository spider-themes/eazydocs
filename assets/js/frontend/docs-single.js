;(function ($) {
    "use strict";

    $(document).ready(function() {

        $('body').attr({'data-bs-spy': 'scroll', 'data-bs-target': "#eazydocs-toc"})

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

        function active_dropdown(is_ajax = false) {
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
         * Doc : On this page
         * @param str
         * @returns {string}
         */
        var slug = function(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
            var to   = "aaaaaeeeeeiiiiooooouuuunc------";
            for (var i=0, l=from.length ; i<l ; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            return str;
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function convertToTitle(Text) {
            let title = Text.replaceAll('-', ' ');
            return capitalizeFirstLetter(title)
        }

        function isNumeric(value) {
            let firstStr = value.charAt(0);
            return /^-?\d+$/.test(firstStr);
        }

        function onThisPageTitles( divs, changelogs = false ) {
            let ids = [];
            let titles = [];
            jQuery(divs).each(function () {
                let idText = $(this).attr("id")

                // Add title attribute
                if ( changelogs === false ) {
                    let titleText = $(this).text()
                    $(this).attr("title", titleText)
                }

                // Modify the ID
                let isFirstCharNumber = isNumeric(idText)
                if ( isFirstCharNumber === true ) {
                    $(this).attr("id", 'docy-'+idText)
                }
                // ID and Title pushing into the arrays
                ids.push( $(this).attr("id") );
                titles.push( $(this).attr("title") )
            });
            ids.forEach(onThisPageID)
            titles.forEach(onThisPageTitle)


            function onThisPageID(item, index) {
                document.getElementById("eazydocs-toc").innerHTML += "<a class='nav-link link-"+index+"' href='#" + item + "'>" + item + " </a>"
            }

            function onThisPageTitle(item, index) {
                $('#eazydocs-toc .link-'+index).text(item)
            }

            // table of contents on top
            ids.forEach(onThisPageIDTop);
            function onThisPageIDTop(item, index) {
                if ( $('#docy-top-toc').length ) {
                    let selector = "#"+item +' \+ p';
                    let content = document.querySelector(selector).innerHTML;
                    if(content.length > 80) content = content.substring(0, 80);

                    // header table of contents
                    document.getElementById("docy-top-toc").innerHTML += " <div class='col-lg-4 col-md-6'>" +
                        "<a class='tip_item link-"+index+"' href='#" + item + "'>" +
                        "<div class='tip_box'>" +
                        "<h4 class='tip_title title-"+index+"'></h4>" +
                        "<p class='tip_para'>"+ content +"...</p>"+
                        "</div></a></div>"
                }
            }

            titles.forEach(onThisPageTitleTop);
            function onThisPageTitleTop(item, index) {
                $('#docy-top-toc .title-'+index).text(item)
            }
        }

        // Doc on this page nav
        function doc_toc_enable(ajax = false) {
            if ( ajax == true ) {
                $("#eazydocs-toc").html('');
            }
            if ( $(".doc-middle-content #post h2").length ) {
                anchors.options = {
                    icon: '#'
                };
                anchors.add('.doc-middle-content #post h2');
                onThisPageTitles($(".doc-middle-content #post h2").toArray())
            }

            // Anchor enabled titles
            if ( $(".anchor-enabled h2").length ) {
                anchors.options = {
                    icon: '#'
                };
                anchors.add('.anchor-enabled h2');
                onThisPageTitles($(".anchor-enabled h2").toArray())
            }

            // Changelog on this page nav
            if ( $(".changelog_inner .changelog_info").length ) {
                onThisPageTitles( $(".changelog_inner .changelog_info").toArray(), true );
            }
        }

        doc_toc_enable()

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
                    $(".doc_documentation_area,#right").addClass("overlay");
                    $(".doc_right_mobile_menu").animate({
                        "right": "0px"
                    }, 100);
                    switchs = false;
                } else {
                    $(".doc_documentation_area,#right").removeClass("overlay");
                    $(".doc_right_mobile_menu").animate({
                        "right": "-250px"
                    }, 100);
                    switchs = true;
                }
            })

            $("#left").on("click", function (e) {
                e.preventDefault()
                if (switchs) {
                    $(".doc_documentation_area,#left").addClass("overlay");
                    $(".doc_mobile_menu").animate({
                        "left": "0px"
                    }, 300);
                    switchs = false;
                } else {
                    $(".doc_documentation_area,#left").removeClass("overlay");
                    $(".doc_mobile_menu").animate({
                        "left": "-245px"
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

                if (middle_column.hasClass('col-lg-7')) {
                    $('.doc-middle-content').removeClass('col-lg-7').addClass('col-lg-10')
                } else if (middle_column.hasClass('col-lg-8')) {
                    $('.doc-middle-content').removeClass('col-lg-8').addClass('col-lg-10')
                }

                $('.left-sidebar-toggle .left-arrow').hide(500)
                $('.left-sidebar-toggle .right-arrow').show(500)
            })

            $('.left-sidebar-toggle .right-arrow').on('click', function () {
                $('.doc_mobile_menu').show(500)

                if (middle_column.hasClass('col-lg-10')) {
                    $('.doc-middle-content').removeClass('col-lg-10').addClass('col-lg-7')
                } else if (middle_column.hasClass('col-lg-8')) {
                    $('.doc-middle-content').removeClass('col-lg-10').addClass('col-lg-8')
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
                });
            }
        }

        Menu_js();


        $(window).on('scroll', function() {
            var $elem = $('.eazydocx-credit-text');
            var $window = $(window);

            var docViewTop = $window.scrollTop();
            var docViewBottom = docViewTop + $window.height();
            var elemTop = $elem.offset().top;
            var elemBottom = elemTop + $elem.height();
            if (elemBottom < docViewBottom) {
                $('.doc_documentation_area').removeClass('body_fixed');
                $('.left-sidebar-toggle').hide();
            }else{
                $('.left-sidebar-toggle').show();
            }
        });





    })
})(jQuery);