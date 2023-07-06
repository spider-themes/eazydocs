(function ($) {
    $(document).ready(function () {

        /**
         * Search results
         */
        function fetchDelay(callback, ms) {
            var timer = 0;
            return function () {
                var context = this,
                    args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }

        $('#ezd_searchInput').keyup(
            fetchDelay(function (e) {
                let keyword = $('#ezd_searchInput').val();
                let noresult = $('#docy-search-result').attr('data-noresult');
                if ( keyword === '' ) {
                    $('#docy-search-result').removeClass('ajax-search').html('');
                } else {
                    $.ajax({
                        url: eazydocs_local_object.ajaxurl,
                        type: 'post',
                        data: {action: 'eazydocs_search_results', keyword: keyword},
                        beforeSend: function () {
                            $('.spinner').css('display', 'block');
                        },
                        success: function (data) {
                            if (data.length > 0) {
                                $('#docy-search-result').addClass('ajax-search').html(data);
                                $('.spinner').hide();
                            } else {
                                var data_error = '<h5>' + noresult + '</h5>';
                                $('#docy-search-result')
                                    .removeClass('ajax-search')
                                    .html(data_error);
                            }
                        },
                    });
                }
            }, 500));

        $('.header_search_keyword ul li a').on('click', function (e) {
            e.preventDefault();
            var content = $(this).text();
            $('#ezd_searchInput').val(content).focus();
            $('#ezd_searchInput').keyup();
            $('#docy-search-result').css({"z-index": "9999"});
        });

        $('.header_search_form_info input[type=search]').focus(function () {
            let ezd_current_theme = $('body').hasClass('ezd-theme-docy');

            if (ezd_current_theme === true) {
                $('body').addClass('search-focused');
                $('.header_search_form_info').css({"z-index": "9999"});
            } else {
                $('body').addClass('search-focused');
                $('body.search-focused').prepend('<div class="ezd_click_capture"></div>');
                $('.ezd_click_capture').css({"visibility": "visible", "opacity": "1", "z-index": "9999"});
                $('.header_search_form_info, #docy-search-result').css({"z-index": "9999"});
            }
        });

        $('.header_search_form_info input[type=search]').focusout(function () {
            $('body').removeClass('search-focused');
            $('.ezd_click_capture').css({"visibility": "hidden", "opacity": "0", "z-index": ""});
            $('.header_search_form_info, #docy-search-result').css({"z-index": ""});
            $('.ezd_click_capture').remove();
        });

        $('#ezd_searchInput').on('input', function (e) {
            if ('' == this.value) {
                $('#docy-search-result').removeClass('ajax-search');
            }
        });

    });



    // glossary doc js
    if ($(".spe-list-wrapper").length) {
        $(".spe-list-wrapper").each(function () {
          var $elem = $(this);
    
          var $active_filter = $elem
            .find(".spe-list-filter .filter.active")
            .data("filter");
          if ($active_filter == "" || typeof $active_filter == "undefined") {
            $active_filter = "all";
          }
    
          var mixer = mixitup($elem, {
            load: {
              filter: $active_filter,
            },
            controls: {
              scope: "local",
            },
            callbacks: {
              onMixEnd: function (state) {
                $("#" + state.container.id)
                  .find(".spe-list-block.spe-removed")
                  .hide();
              },
            },
          });
    
          if ($(".spe-list-search-form").length) {
            var $searchInput = $(".spe-list-search-form input");
    
            $searchInput.on("input", function (e) {
              var $keyword = $(this).val().toLowerCase();
    
              $elem.find(".spe-list-block").each(function () {
                var $elem_list_block = $(this);
                var $block_visible_items = 0;
    
                $elem_list_block.find(".spe-list-item").each(function () {
                  if ($(this).text().toLowerCase().includes($keyword)) {
                    $(this).show();
                    $block_visible_items++;
                  } else {
                    $(this).hide();
                  }
                });
    
                var $filter_base = $elem_list_block.data("filter-base");
                var $filter_source = $elem.find(
                  '.spe-list-filter a[data-filter=".spe-filter-' +
                    $filter_base +
                    '"]'
                );
                var $active_block = $elem
                  .find(".spe-list-filter a.mixitup-control-active")
                  .data("filter");
    
                if ($block_visible_items > 0) {
                  $elem_list_block.removeClass("spe-removed");
    
                  if ($active_block != "all") {
                    if ($elem_list_block.is($elem.find($active_block))) {
                      $elem.find($active_block).show();
                    }
                  } else {
                    $elem_list_block.show();
                  }
    
                  $filter_source.removeClass("filter_disable").addClass("filter");
                } else {
                  $elem_list_block.addClass("spe-removed");
    
                  if ($active_block != "all") {
                    if ($elem_list_block.is($elem.find($active_block))) {
                      $elem.find($active_block).hide();
                    }
                  } else {
                    $elem_list_block.hide();
                  }
    
                  $filter_source.removeClass("filter").addClass("filter_disable");
                }
              });
    
              if ($keyword == "") {
                mixer.filter("all"); // Reset the filter to show all items
              }
            });
    
            $searchInput.val("");
          }
        });
    }


})(jQuery);