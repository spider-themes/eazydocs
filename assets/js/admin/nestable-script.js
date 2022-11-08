(function ($) {
  "use strict";

  $(document).ready(function () {
    var eaz_show_parent_child = function (clickable_class) {
      $(clickable_class).click(function (e) {
        var $this = $(this);
        if ($this.next().hasClass('show')) {
          $this.next().removeClass('show');
          $this.removeClass('rotate-arrow');
          $this.parent().removeClass('show-child');
          $this.next().slideUp(350);
        } else {
          $this.toggleClass('rotate-arrow');
          $this.parent().toggleClass('show-child');
          $this.parent().parent().find('li .dd-list').removeClass('show');
          $this.parent().parent().find('li .dd-list').slideUp(350);
          $this.next().toggleClass('show');
          $this.next().slideToggle(350);
        }
      });
    }
    eaz_show_parent_child('.dd3-have-children .accordion-title');
    var eaz_create_cookie = function (name, value, days) {
      var expires = "";
      if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + value + expires + "; path=/";
    }
    var eaz_read_cookie = function (name) {
      var nameEQ = name + "=";
      var ca = document.cookie.split(";");
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
      }
      return null;
    }
    var eaz_child_cookie_set = function (e) {
      let have_children = $('.dd3-have-children');
      if (have_children.length > 0) {
        $('.dd3-have-children > .accordion-title').click(function (e) {
          let target = $(this).parent().attr('data-id');
          let is_active_child = $(this).parent().hasClass('show-child');
          console.log(is_active_child);
          if (is_active_child) {
            eaz_create_cookie("eazydocs_current_child", "child-" + target, 999);
          } else {
            eaz_create_cookie("eazydocs_current_child", "", 999);
          }

          return true;
        });
      }
    }
    eaz_child_cookie_set();
    var eaz_child_of_child_cookie_set = function (e) {
      let have_sub_children = $('.dd3-have-sub-children');
      if (have_sub_children.length > 0) {
        let doc_last_current_child = eaz_read_cookie('eazydocs_current_child');
        if (doc_last_current_child) {
          $('.' + doc_last_current_child + ' .accordion-title').click(function (e) {
            let target = $(this).parent().attr('data-id');
            let is_active_child = $(this).parent().hasClass('show-child');
            if (is_active_child) {
              eaz_create_cookie("eazydocs_current_child_of", "child-of-" + target, 999);
            } else {
              eaz_create_cookie("eazydocs_current_child_of", " ", 999);
            }

            return true;
          });
        }
      }
    }
    eaz_child_of_child_cookie_set();
    var eaz_get_cookies = function () {
      let doc_last_current_child = eaz_read_cookie('eazydocs_current_child')
      if (doc_last_current_child) {
        $('.' + doc_last_current_child + '>' + '.accordion-title').each(function (e) {
          var $this = $(this);
          if ($this.next().hasClass('show')) {
            $this.next().removeClass('show');
            $this.removeClass('rotate-arrow');
            $this.next().slideUp(350);
          } else {
            $this.toggleClass('rotate-arrow');
            $this.parent().parent().find('li .dd-list').removeClass('show');
            $this.parent().parent().find('li .dd-list').slideUp(350);
            $this.next().toggleClass('show');
            $this.next().slideToggle(350);
          }
        });
      }
    }
    eaz_get_cookies();
    var eaz_get_child_of_cookies = function () {
      let doc_last_current_child = eaz_read_cookie('eazydocs_current_child');
      if (doc_last_current_child) {
        let doc_last_current_child_of = eaz_read_cookie('eazydocs_current_child_of')
        if (doc_last_current_child_of) {
          $('.' + doc_last_current_child_of + ' ' + '.accordion-title').each(function (e) {
            var $this = $(this);
            if ($this.next().hasClass('show')) {
              $this.next().removeClass('show');
              $this.removeClass('rotate-arrow');
              $this.next().slideUp(350);
            } else {
              $this.toggleClass('rotate-arrow');
              $this.parent().parent().find('li .dd-list').removeClass('show');
              $this.parent().parent().find('li .dd-list').slideUp(350);
              $this.next().toggleClass('show');
              $this.next().slideToggle(350);
            }
          });
        }
      }
    }
    eaz_get_child_of_cookies();
    var eaz_nestable_docs = function (e) {
      var list = e.length ? e : $(e.target), output = list.data('output');
      var dataString = {
        action: 'eaz_nestable_docs',
        data: window.JSON.stringify(list.nestable('serialize')),
      };
      $.ajax({
        url: eazydocs_local_object.ajaxurl,
        type: "POST",
        data: dataString,
        async: true,
        cache: false,
        dataType: 'json',
        success: function (res) {

        },
        error: function (err) {
          console.log(err);
        }
      });
    };

    $('.dd').nestable({
      group: 1,
      maxDepth: 3
    }).on('change', eaz_nestable_docs);
  });
}(jQuery));



