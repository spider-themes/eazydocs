;(function ($) {
  'use strict'

  $(document).ready(function () {
    
      function active_dropdown(is_ajax = false) {
        if (is_ajax == true) {
            $(document).on(
                'click',
                '.nav-sidebar .nav-item .nav-link',
                function (e) {
                    $('.nav-sidebar .nav-item').removeClass('active');
                    $(this).parent().addClass('active');
                    $(this).parent().find('ul').first().show(300);
                    $(this).parent().siblings().find('ul').hide(300);
                }
            );
        } else {
            $('.nav-sidebar > li .doc-link .icon').on(
                'click',
                function (e) {
                    $(this)
                        .parent()
                        .parent()
                        .find('ul')
                        .first()
                        .toggle(300);
                    $(this)
                        .parent()
                        .parent()
                        .siblings()
                        .find('ul')
                        .hide(300);
                }
            );
        }
    }
    active_dropdown();
    
    // Collapse left sidebar
    function docLeftSidebarToggle() {
      let left_column = $('.doc_mobile_menu');
      let middle_column = $('.doc-middle-content');
      $('.left-sidebar-toggle .left-arrow').on('click', function () {
          $('.doc_mobile_menu').hide(500);

          if (middle_column.hasClass('col-xl-7')) {
              $('.doc-middle-content')
                  .removeClass('col-xl-7')
                  .addClass('col-xl-10');
          } else if (middle_column.hasClass('col-xl-8')) {
              $('.doc-middle-content')
                  .removeClass('col-xl-8')
                  .addClass('col-xl-10');
          }

          $('.left-sidebar-toggle .left-arrow').hide(500);
          $('.left-sidebar-toggle .right-arrow').show(500);
      });

      $('.left-sidebar-toggle .right-arrow').on('click', function () {
          $('.doc_mobile_menu').show(500);

          if (middle_column.hasClass('col-xl-10')) {
              $('.doc-middle-content')
                  .removeClass('col-xl-10')
                  .addClass('col-xl-7');
          } else if (middle_column.hasClass('col-xl-8')) {
              $('.doc-middle-content')
                  .removeClass('col-xl-10')
                  .addClass('col-xl-8');
          }

          $('.left-sidebar-toggle .left-arrow').show(500);
          $('.left-sidebar-toggle .right-arrow').hide(500);
      });
    }
    docLeftSidebarToggle();
    

    // Contributor [ Delete ] 
    function ezd_contribute_delete(){
      $('.ezd_contribute_delete').click(function(e){ 
        e.preventDefault();
        
        let contributor_id      = $(this).attr('data-contributor-delete');
        let data_doc_id         = $(this).attr('data-doc-id');
        let user_name           = $(this).attr('data_name');

          $.ajax({
            url: eazydocs_ajax_search.ajax_url,
            method: 'POST',
            data: {
              action: 'ezd_doc_contributor',
              contributor_delete: contributor_id,
              data_doc_id: data_doc_id
            },
            beforeSend: function () {
              $('.ezd_contribute_delete[data-contributor-delete='+contributor_id+']').html( '<span class="spinner-border ezd-contributor-loader"><span class="visually-hidden">Loading...</span></span>' )
            },
            success: function (response) {
              $('#to_add_contributors').append(response)
              $('#user-'+contributor_id).remove();
              $('.to-add-user-'+contributor_id).not(':last').remove();

              $('.ezdoc_contributed_user_avatar a[title="'+user_name+'"]').remove();
              $('.ezdoc_contributed_user_avatar a[data-bs-original-title="'+user_name+'"]').remove();
              ezd_contributor_add();

            },
            error: function () {
              console.log('Oops! Something wrong, try again!')
            }
          });
      });
    }
    ezd_contribute_delete();
    
    // Contributor [ Add ] 
    function ezd_contributor_add(){
      $('.ezd_contribute_add').click(function(e){   
        e.preventDefault();

        let contributor_add   = $(this).attr('data-contributor-add');
        let data_doc_id       = $(this).attr('data-doc-id');
        
        let user_img          = $(this).parent().parent().find('img').attr('src');
        let user_name         = $(this).attr('data_name');
        let user_url          = $(this).parent().parent().find('a').attr('href');

        $.ajax({
          url: eazydocs_ajax_search.ajax_url,
          method: 'POST',
          data: {
            action: 'ezd_doc_contributor',
            contributor_add: contributor_add,
            data_doc_id: data_doc_id
          },
          beforeSend: function () {
            $('.ezd_contribute_add[data-contributor-add='+contributor_add+']').html( '<span class="spinner-border ezd-contributor-loader"><span class="visually-hidden">Loading...</span></span>' )
          },
          success: function (response) {
            $('#added_contributors').append(response);
            $('#to-add-user-'+contributor_add).remove();

            $('.user-'+contributor_add).not(':last').remove();

            $('.contributed_user_list').append('<a title="'+user_name+'" href="'+user_url+'" data-bs-toggle="tooltip" data-bs-placement="bottom"><img width="24px" src="'+user_img+'"></a>');
            $('.contributed_user_list a[data-bs-original-title="'+user_name+'"]').remove();
            
            $('[data-bs-toggle="tooltip"]').tooltip();

            ezd_contribute_delete();
            
          },
          error: function () {
            console.log('Oops! Something wrong, try again!')
          }
        });
      });
    }
    ezd_contributor_add();
    

    /**
     * Feedback Handler
     */
    function ezd_feadback(){
      $('.vote-link-wrap a.h_btn').on('click', function (e) {
        e.preventDefault();
        let self = $(this);
        $.ajax({
            url: eazydocs_local_object.ajaxurl,
            method: 'post',
            data: {
                action: 'eazydocs_handle_feedback',
                post_id: self.data('id'),
                type: self.data('type'),
                _wpnonce: eazydocs_local_object.nonce,
            },
            beforeSend: function () {
                $('.eazydocs-feedback-wrap .vote-link-wrap').html(
                    '<div class="spinner-border spinner-border-sm" role="status">\n' +
                    '  <span class="visually-hidden">Loading...</span>\n' +
                    '</div>'
                );
            },
            success: function (response) {
                $('.eazydocs-feedback-wrap').html(response.data);
            },
            error: function () {
                console.log('Oops! Something wrong, try again!');
            },
        });
    });
    }


    /**
     * Font size switcher
    **/
    function fontSize_switcher(){
      if ($('#rvfs-controllers button').length) {
        var $speech = $(
            '#post p, #post ul li:not(.process_tab_shortcode ul li), #post ol li, #post table:not(.basic_table_info,.table-dark), #post table tr td, #post .tab-content'
        );
        var $defaultSize = $speech.css('fontSize');
        $('#rvfs-controllers button').click(function () {
            var num = parseFloat($speech.css('fontSize'));
            switch (this.id) {
                case 'switcher-large':
                    num *= 1.1;
                    break;
                case 'switcher-small':
                    num /= 1.1;
                    break;
                default:
                    num = parseFloat($defaultSize);
            }
            $speech.animate({fontSize: num + 'px'});
        });
      }
    }
    

    /**
     * Load Doc single page via ajax
     */
    if (eazydocs_local_object.is_doc_ajax == '1') {

        $(document).on('click', '.single-docs .nav-sidebar .nav-item .dropdown_nav li a', function (e) {
            e.preventDefault();
            let self    = $(this);
            let title   = self.text();
            let postid  = $(this).attr('data-postid');

            function changeurl(page_title) {
                let new_url     = self.attr('href');
                window.history.pushState('data', 'Title', new_url);
                document.title  = page_title;
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

                    $('.nav-sidebar .nav-item').removeClass(
                        'current_page_item'
                    );

                    $('.nav-sidebar .nav-item .dropdown_nav li a').removeClass('active');
                    if (!self.parent().parent().hasClass('has_child')) {
                        self.addClass('active');
                        self.parent().addClass('current_page_item');
                    } else if (self.parent().parent().hasClass('has_child')) {
                        self.parent().parent().addClass('current_page_item');
                    }
                    
                    
                    // Toc
                    $('#eazydocs-toc').empty();
                    Toc.init({
                        $nav: $('#eazydocs-toc'),
                        $scope: $('.doc-scrollable'),
                    });
                    docLeftSidebarToggle();
                },
                error: function () {
                    console.log('Oops! Something wrong, try again!');
                },
            });
        });

        $(document).on('click', '.single-docs .nav-sidebar .nav-item .nav-link', function (e) {
              e.preventDefault();
              let self    = $(this);
              let title   = self.text();
              let postid  = $(this).attr('data-postid');

              function changeurl(page_title) {
                  let new_url     = self.attr('href');
                  window.history.pushState('data', 'Title', new_url);
                  document.title  = page_title;
              }
              
              $.ajax({
                  url: eazydocs_local_object.ajaxurl,
                  method: 'post',
                  data: {
                      action: 'docs_single_content',
                      postid: postid,
                  },
                  beforeSend: function () {
                      $('#reading-progress-fill').css({width: '100%',display: 'block'});
                  },
                  success: function (response) {
                      active_dropdown(true);
                      $('#reading-progress-fill').css({display: 'none'});
                      $('.doc-middle-content').html(response);
                      changeurl(title);
                      $('.nav-sidebar .nav-item').removeClass('current_page_item');
                      self.addClass('active');
                      self.parent().parent().addClass('current_page_item');
                      docLeftSidebarToggle();

                      /*** Contributors Dropdown ***/
                      $(".ezdoc_contributed_user_avatar .ezdoc_contributed_users .arrow_carrot-down").click(function (e) {
                        e.stopPropagation();
                        $(this).toggleClass("active");
                        $(".ezdoc_contributed_user_avatar .ezdoc_contributed_users .doc_users_dropdown").toggleClass("active");
                        
                      });

                      $(document).click(function () {
                        $(".ezdoc_contributed_user_avatar .ezdoc_contributed_users .doc_users_dropdown").removeClass("active");
                        $(".ezdoc_contributed_user_avatar .ezdoc_contributed_users .arrow_carrot-down").removeClass("active");
                      });   
                  
                      $(".ezdoc_contributed_user_avatar .ezdoc_contributed_users .doc_users_dropdown").click(function (event) {
                        event.stopPropagation();
                      });
                                     
                      /*** Font size switcher ***/
                      fontSize_switcher();

                      /** === Contributor Handler === **/
                      ezd_contributor_add();
                      ezd_contribute_delete();

                      /** === Feedback Handler === **/
                      ezd_feadback();
                      
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