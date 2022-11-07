(function ($) {
  "use strict";

  $(document).ready(function () {
    $(".dd-item").hover(
      function () {
        $(this).addClass("dd-item--hover");
      },
      function () {
        $(this).removeClass("dd-item--hover");
      }
    );
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



