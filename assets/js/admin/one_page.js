(function ($) {
    $(document).ready(function () {

        $("body.post-type-docs .wrap #posts-filter .search-box").append(' <a href="admin.php?page=eazydocs" class="button">Modern View</a>');
        $("body.post-type-onepage-docs .wrap .page-title-action").after(' <a href="admin.php/One_Page.php?single_doc_title=" class="page-title-action add-onepage">Add OnePage Doc</a>');

        // CREATE ONE PAGE DOC
        function create_one_page_doc_doc() {
            $(document).on('click', '.page-title-action.add-onepage', function (e) {
                e.preventDefault();
                let href = $(this).attr('href');
                (async () => {
                    const {value: formValues} = await Swal.fire({
                        title: 'Create OnePage Doc',
                        html:
                            '<div class="create_onepage_doc_area">' +
                            '<label for="ezd_docs_select">Select the doc that want to work on</label>' +
                            '<select class="widefat" id="ezd_docs_select" required>' +
                            eazydocs_local_object.one_page_prompt_docs +
                            '</select>' +

                            '<label for="ezd_docs_sidebar">Select Layout</label>' +
                            '<select class="widefat" id="ezd_docs_layout_select" name="ezd_onepage_select">' +
                            '<option value="default-layout">Default Layout</option>' +
                            '<option value="classic-onepage-layout">Classic OnePage Doc</option>' +
                            '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>' +
                            '</select>' +
                            '<div class="ezd_content_btn_wrap">' +
                            '<div class="left_btn_link ezd_left_active">Left Content</div>' +
                            '<div class="right_btn_link">Right Content</div>' +
                            '</div>' +

                            '<div class="ezd_left_content">' +
                            '<div class="ezd_docs_content_type_wrap">' +
                            '<label for="ezd_docs_content_type">Content Type:</label>' +
                            '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode">' +
                            '<label for="shortcode">Shortcode</label>' +
                            '<input type="radio" id="widget_data" name="ezd_docs_content_type" value="widget_data">' +
                            '<label for="widget_data">Widget Content</label>' +
                            '<input type="radio" checked id="string_data" name="ezd_docs_content_type" value="string_data">' +
                            '<label for="string_data">Normal Content</label>' +
                            '</div>' +
                            '<div class="ezd_shortcode_content_wrap">' +
                            '<label for="ezd-shortcode">Content (Optional) </label>' +
                            '<textarea name="ezd-shortcode-content" id="ezd-shortcode-content" rows="3" class="widefat"></textarea>' +
                            '</div>' +
                            '<div class="ezd_widget_content_wrap">' +
                            '<label for="ezd-shortcode">Select a sidebar (Optional) </label>' +
                            '<select name="ezd_sidebar_select_data" id="left_side_sidebar" class="widefat">' +
                            eazydocs_local_object.one_page_prompt_sidebar +
                            '</select>' +
                            '</div>' +
                            '</div>' +
                            '<div class="ezd_right_content">' +
                            '<div class="ezd_docs_content_type_wrap">' +
                            '<label for="ezd_docs_content_type">Content Type:</label>' +
                            '<input type="radio" id="shortcode_right" name="ezd_docs_content_type_right" value="shortcode_right">' +
                            '<label for="shortcode_right">Shortcode</label>' +
                            '<input type="radio" id="widget_data_right" name="ezd_docs_content_type_right" value="widget_data_right">' +
                            '<label for="widget_data_right">Widget Content</label>' +
                            '<input type="radio" checked id="string_data_right" name="ezd_docs_content_type_right" value="string_data_right">' +
                            '<label for="string_data_right">Normal Content</label>' +
                            '</div>' +
                            '<div class="ezd_shortcode_content_wrap_right">' +
                            '<label for="ezd-shortcode">Content (Optional) </label>' +
                            '<textarea name="ezd-shortcode-content-right" id="ezd-shortcode-content-right" rows="3" class="widefat"></textarea>' +
                            '</div>' +
                            '<div class="ezd_widget_content_wrap_right">' +
                            '<label for="ezd-shortcode">Select a sidebar (Optional) </label>' +
                            '<select name="ezd_sidebar_select_data_right" id="right_side_sidebar" class="widefat">' +
                            eazydocs_local_object.one_page_prompt_sidebar +
                            '</select>' +
                            '</div>' +
                            '</div>' +
                            '</div>',
                        confirmButtonText: 'Publish',
                        showCancelButton: true,
                        customClass: {
                            container: 'ezd-onepage-doc-container',
                        }
                    }).then((result) => {

                        if (result.isConfirmed) {
                            encoded = encodeURIComponent(JSON.stringify(document.getElementById('ezd-shortcode-content').value))

                            window.location.href = href + document.getElementById('ezd_docs_select').value + '&layout=' + document.getElementById('ezd_docs_layout_select').value + '&content_type=' + document.querySelector('input[name=ezd_docs_content_type]:checked').value + '&left_side_sidebar=' + document.getElementById('left_side_sidebar').value + '&shortcode_content=' + encoded
                                + '&shortcode_right=' + document.querySelector('input[name=ezd_docs_content_type_right]:checked').value + '&shortcode_content_right=' + document.getElementById('ezd-shortcode-content-right').value +
                                '&right_side_sidebar=' + document.getElementById('right_side_sidebar').value + '&self_doc=ezd-one-page'

                        }

                    })
                })();

                $('.ezd_content_btn_wrap .left_btn_link').addClass('ezd_left_active');
                $('.ezd_left_content').addClass('ezd_left_content_active');

                $('.ezd_content_btn_wrap .left_btn_link').click(function () {
                    $(this).addClass('ezd_left_active');
                    $('.ezd_left_content').addClass('ezd_left_content_active');
                    $('.ezd_right_content').removeClass('ezd_left_content_active');
                    $('.ezd_content_btn_wrap .right_btn_link').removeClass('ezd_right_active');
                });
                $('.ezd_content_btn_wrap .right_btn_link').click(function () {
                    $(this).addClass('ezd_right_active');
                    $('.ezd_left_content').removeClass('ezd_left_content_active');
                    $('.ezd_right_content').addClass('ezd_left_content_active');
                    $('.ezd_content_btn_wrap .left_btn_link').removeClass('ezd_left_active');
                });

                $("input[type=radio]#widget_data").click(function () {
                    if ($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').hide();
                        $('.ezd_widget_content_wrap').show();
                    }
                });

                $("input[type=radio]#shortcode").click(function () {
                    if ($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').show();
                        $('.ezd_widget_content_wrap').hide();
                    }
                });

                $("input[type=radio]#string_data").click(function () {
                    if ($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').show();
                        $('.ezd_widget_content_wrap').hide();
                    }
                });

                if ($("#no-more-doc-available").val() === 'no-more-doc-available') {
                    $('.ezd-onepage-doc-container .swal2-html-container').hide();
                    $('.ezd-onepage-doc-container .swal2-title').hide();
                    $('.ezd-onepage-doc-container .swal2-confirm').hide();
                    $('.ezd-onepage-doc-container .swal2-actions').prepend('<h2 class="ezd-not-found-doc-heading">No doc was found to make it OnePage<h2>').css({
                        'display': 'block',
                        'width': '100%',
                        'text-align': 'center'
                    });
                }

                // RIGHT TAB
                $('.ezd_widget_content_wrap_right').hide();
                $("input[type=radio]#string_data_right").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_widget_content_wrap_right').hide();
                    }
                });
                $("input[type=radio]#widget_data_right").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_widget_content_wrap_right').show();
                        $('.ezd_shortcode_content_wrap_right').hide();
                    }
                });
                $("input[type=radio]#shortcode_right").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_widget_content_wrap_right').hide();
                        $('.ezd_shortcode_content_wrap_right').show();
                    }
                });
            })
        }

        create_one_page_doc_doc();

        // EDIT ONE PAGE DOC
        function edit_one_page_doc_doc() {
            $(document).on('click', 'body.post-type-onepage-docs .type-onepage-docs .row-actions span.edit, body.post-type-onepage-docs .type-onepage-docs .page-title > strong', function (e) {
                e.preventDefault();

                let edit_url = $('a', this).filter("[href]").attr('href');

                // function created to get parameter from edit url
                $.urlParam = function (name) {
                    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(edit_url);
                    if (results == null) {
                        return '';
                    }
                    return decodeURI(results[1]) || 0;
                }

                // parameter of edit url - post & content
                let edit_doc_id = $.urlParam('post');
                let edit_doc_content = $.urlParam('content');
                let doc_layout = $.urlParam('doc_layout');
                let content_type = $.urlParam('content_type');
                let content_type_right = $.urlParam('content_type_right');
                let edit_content_right = $.urlParam('content_right');

                // Doc Layout
                let doc_layout_opt;
                if (doc_layout == 'default-layout') {
                    doc_layout_opt = '<option value="default-layout" selected>Default Layout </option>';
                    doc_layout_opt += '<option value="classic-onepage-layout">Classic OnePage Doc</option>';
                    doc_layout_opt += '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>';
                } else if (doc_layout == 'classic-onepage-layout') {
                    doc_layout_opt = '<option value="classic-onepage-layout" selected>Classic OnePage Doc</option>';
                    doc_layout_opt += '<option value="default-layout">Default Layout</option>';
                    doc_layout_opt += '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>';
                }else{
                    doc_layout_opt = '<option value="classic-onepage-layout">Classic OnePage Doc</option>';
                    doc_layout_opt += '<option value="default-layout">Default Layout</option>';
                    doc_layout_opt += '<option value="fullscreen-layout" selected>Fullscreen OnePage Doc</option>';
                }

                // LEFT TAB CONTNET
                //Content Type
                let content_type_opt;
                if (content_type == 'string_data') {
                    content_type_opt = '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode"><label for="shortcode">Shortcode</label>'
                    content_type_opt += '<input type="radio" checked id="string_data" name="ezd_docs_content_type" value="string_data" checked><label for="string_data">Normal Content</label>'

                    content_type_opt = '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode"><label for="shortcode">Shortcode</label>'
                    content_type_opt +=  '<input type="radio" id="widget_data" name="ezd_docs_content_type" value="widget_data"><label for="widget_data">Widget Content</label>'
                    content_type_opt += '<input type="radio" id="string_data" name="ezd_docs_content_type" value="string_data" checked><label for="string_data">Normal Content</label>'
                } else if(content_type == 'shortcode') {
                    content_type_opt = '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode" checked><label for="shortcode">Shortcode</label>'
                    content_type_opt +=  '<input type="radio" id="widget_data" name="ezd_docs_content_type" value="widget_data"><label for="widget_data">Widget Content</label>'
                    content_type_opt += '<input type="radio" id="string_data" name="ezd_docs_content_type" value="string_data"><label for="string_data">Normal Content</label>'
                }else{
                    content_type_opt = '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode"><label for="shortcode">Shortcode</label>'
                    content_type_opt +=  '<input type="radio" checked id="widget_data" name="ezd_docs_content_type" value="widget_data"><label for="widget_data">Widget Content</label>'
                    content_type_opt += '<input type="radio" id="string_data" name="ezd_docs_content_type" value="string_data"><label for="string_data">Normal Content</label>'
                }

                let widget_enable = '';
              if( content_type == 'widget_data' ){
                  widget_enable = '<div class="ezd_widget_content_wrap_edit left"><label for="ezd-shortcode-edit">Select a sidebar (Optional) </label><select name="ezd_sidebar_select_datas" id="left_side_sidebar_edit" class="widefat">' + eazydocs_local_object.one_page_doc_sidebar_edit + '</select></div>'
                  widget_enable += '<div class="ezd_shortcode_content_wrap_edits left"><label for="ezd-shortcode">Content (Optional) </label><textarea name="ezd-shortcode-content" id="ezd-shortcode-content-left" rows="5" class="widefat">' + edit_doc_content + '</textarea></div>'
                }else{
                  widget_enable = '<div class="ezd_shortcode_content_wrap_edits left"><label for="ezd-shortcode">Content (Optional) </label><textarea name="ezd-shortcode-content" id="ezd-shortcode-content-left" rows="5" class="widefat">' + edit_doc_content + '</textarea></div>'
                  widget_enable += '<div class="ezd_widget_content_wrap_edit left"><label for="ezd-shortcode">Select a sidebar (Optional) </label><select name="ezd_sidebar_select_data" id="left_side_sidebar_edit" class="widefat">' + eazydocs_local_object.one_page_doc_sidebar_edit + '</select></div>'
                }

                // RIGHT TAB CONTNET
                //Content Type
                let content_type_right_opt;
                if (content_type_right == 'string_data_right') {
                    content_type_right_opt = '<input type="radio" id="shortcode_right" name="ezd_docs_content_type_right" value="shortcode_right"><label for="shortcode_right">Shortcode</label>'
                    content_type_right_opt +=  '<input type="radio" id="widget_data_right" name="ezd_docs_content_type_right" value="widget_data_right"><label for="widget_data_right">Widget Content</label>'
                    content_type_right_opt += '<input type="radio" checked id="string_data_right" name="ezd_docs_content_type_right" value="string_data_right" checked><label for="string_data_right">Normal Content</label>'

                } else if(content_type_right == 'shortcode_right') {
                    content_type_right_opt = '<input type="radio" id="shortcode_right" name="ezd_docs_content_type_right" value="shortcode_right" checked><label for="shortcode_right">Shortcode</label>'
                    content_type_right_opt +=  '<input type="radio" id="widget_data_right" name="ezd_docs_content_type_right" value="widget_data_right"><label for="widget_data_right">Widget Content</label>'
                    content_type_right_opt += '<input type="radio" id="string_data_right" name="ezd_docs_content_type_right" value="string_data_right"><label for="string_data_right">Normal Content</label>'

                }else{
                    content_type_right_opt = '<input type="radio" id="shortcode_right" name="ezd_docs_content_type_right" value="shortcode_right"><label for="shortcode_right">Shortcode</label>'
                    content_type_right_opt += '<input type="radio" id="widget_data_right" name="ezd_docs_content_type_right" value="widget_data_right" checked><label for="widget_data_right">Widget Content</label>'
                    content_type_right_opt += '<input type="radio" id="string_data_right" name="ezd_docs_content_type_right" value="string_data_right"><label for="string_data_right">Normal Content</label>'
                }

                let widget_enable_right = '';
              if( content_type_right == 'widget_data_right' ){
                  widget_enable_right = '<div class="ezd_widget_content_wrap_edit right"><label for="right_side_sidebar">Select a sidebar (Optional) </label><select name="ezd_sidebar_select_data_right" id="right_side_sidebar" class="widefat">' + eazydocs_local_object.one_page_doc_sidebar_edit + '</select></div>'
                  widget_enable_right += '<div class="ezd_shortcode_content_wrap_edits right"><label for="ezd-shortcode">Content (Optional) </label><textarea name="ezd-shortcode-content-right" id="ezd-shortcode-content-right" rows="5" class="widefat">' + edit_content_right + '</textarea></div>'

                }else{
                  widget_enable_right  = '<div class="ezd_shortcode_content_wrap_edits right"><label for="ezd-shortcode">Content (Optional) </label><textarea name="ezd-shortcode-content-right" id="ezd-shortcode-content-right" rows="5" class="widefat">' + edit_content_right + '</textarea></div>'
                  widget_enable_right += '<div class="ezd_widget_content_wrap_edit right"><label for="right_side_sidebar">Select a sidebar (Optional) </label><select name="ezd_sidebar_select_data_right" id="right_side_sidebar" class="widefat">' + eazydocs_local_object.one_page_doc_sidebar_edit + '</select></div>'
                }

                // redirect url when editing
                let href = eazydocs_local_object.edit_one_page_url;

                (async () => {
                    const {value: formValues} = await Swal.fire({

                        title: 'Want to edit this doc?',
                        customClass: {
                            container: 'onepage_create_wrapper',
                        },
                        html:
                            '<div class="create_onepage_doc_area">' +
                            '<label for="ezd_docs_sidebar">Select Layout</label>' +
                            '<select class="widefat" id="ezd_docs_select" name="ezd_onepage_select">' +
                             doc_layout_opt +
                            '</select>' +
                            '<div class="ezd_content_btn_wrap">' +
                            '<div class="left_btn_link ezd_left_active">Left Content</div>' +
                            '<div class="right_btn_link">Right Content</div>' +
                            '</div>' +

                            '<div class="ezd_left_content">' +
                            '<div class="ezd_docs_content_type_wrap">' +
                            '<label for="ezd_docs_content_type">Content Type:</label>' +
                            content_type_opt +
                            '</div>' +
                            widget_enable
                             +
                            '</div>' +

                            '<div class="ezd_right_content">' +
                            '<div class="ezd_docs_content_type_wrap">' +
                            '<label for="ezd_docs_content_type">Content Type:</label>' +
                            content_type_right_opt +
                            '</div>' +
                            widget_enable_right
                            +
                            '</div>' +
                            '</div>',

                        confirmButtonText: 'Update',
                        showCancelButton: true
                    }).then((result) => {

                        if (result.isConfirmed) {
                            encoded = encodeURIComponent(JSON.stringify(document.getElementById('ezd-shortcode-content-left').value))

                            window.location.href = href + '&doc_id=' + edit_doc_id + '&layout=' + document.getElementById('ezd_docs_select').value +
                                '&content_type=' + document.querySelector('input[name=ezd_docs_content_type]:checked').value +
                                '&edit_content=' + encoded + '&get_left_sidebar=' + document.getElementById('left_side_sidebar_edit').value +
                                '&shortcode_right=' + document.querySelector('input[name=ezd_docs_content_type_right]:checked').value + '&shortcode_content_right=' + document.getElementById('ezd-shortcode-content-right').value +
                                '&right_side_sidebar=' + document.getElementById('right_side_sidebar').value
                        }

                    })
                })()

                // LEFT CONTENT [ ACTIVE ]
                if($('#shortcode').is(':checked')){
                    $('.ezd_widget_content_wrap_edit.left').hide();
                    $('.ezd_shortcode_content_wrap_edits.left').show();

                    $('#widget_data').click(function(){
                        $('.ezd_widget_content_wrap_edit.left').show();
                        $('.ezd_shortcode_content_wrap_edits.left').hide();
                    });

                    $('#string_data, #shortcode').click(function(){
                        $('.ezd_widget_content_wrap_edit.left').hide();
                        $('.ezd_shortcode_content_wrap_edits.left').show();
                    });
                }
                if($('#widget_data').is(':checked')){
                    $('.ezd_widget_content_wrap_edit.left').show();
                    $('.ezd_shortcode_content_wrap_edits.left').hide();

                    $('#widget_data').click(function(){
                        $('.ezd_widget_content_wrap_edit.left').show();
                        $('.ezd_shortcode_content_wrap_edits.left').hide();
                    });

                    $('#string_data, #shortcode').click(function(){
                        $('.ezd_widget_content_wrap_edit.left').hide();
                        $('.ezd_shortcode_content_wrap_edits.left').show();
                    });
                }
                if($('#string_data').is(':checked')){
                    $('.ezd_widget_content_wrap_edit.left').hide();
                    $('.ezd_shortcode_content_wrap_edits.left').show();

                    $('#widget_data').click(function(){
                        $('.ezd_widget_content_wrap_edit.left').show();
                        $('.ezd_shortcode_content_wrap_edits.left').hide();
                    });

                    $('#string_data, #shortcode').click(function(){
                        $('.ezd_widget_content_wrap_edit.left').hide();
                        $('.ezd_shortcode_content_wrap_edits.left').show();
                    });
                }

                // RIGHT CONTENT [ ACTIVE ]
                if($('#shortcode_right').is(':checked')){
                    $('.ezd_widget_content_wrap_edit.right').hide();
                    $('.ezd_shortcode_content_wrap_edits.right').show();

                    $('#widget_data_right').click(function(){
                        $('.ezd_widget_content_wrap_edit.right').show();
                        $('.ezd_shortcode_content_wrap_edits.right').hide();
                    });

                    $('#string_data_right, #shortcode_right').click(function(){
                        $('.ezd_widget_content_wrap_edit.right').hide();
                        $('.ezd_shortcode_content_wrap_edits.right').show();
                    });
                }
                if($('#widget_data_right').is(':checked')){
                    $('.ezd_widget_content_wrap_edit.right').show();
                    $('.ezd_shortcode_content_wrap_edits.right').hide();

                    $('#widget_data_right').click(function(){
                        $('.ezd_widget_content_wrap_edit.right').show();
                        $('.ezd_shortcode_content_wrap_edits.right').hide();
                    });

                    $('#string_data_right, #shortcode_right').click(function(){
                        $('.ezd_widget_content_wrap_edit.right').hide();
                        $('.ezd_shortcode_content_wrap_edits.right').show();
                    });
                }
                if($('#string_data_right').is(':checked')){
                    $('.ezd_widget_content_wrap_edit.right').hide();
                    $('.ezd_shortcode_content_wrap_edits.right').show();

                    $('#widget_data_right').click(function(){
                        $('.ezd_widget_content_wrap_edit.right').show();
                        $('.ezd_shortcode_content_wrap_edits.right').hide();
                    });

                    $('#string_data_right, #shortcode_right').click(function(){
                        $('.ezd_widget_content_wrap_edit.right').hide();
                        $('.ezd_shortcode_content_wrap_edits.right').show();
                    });
                }

                $('.ezd_shortcode_content_wrap_edit').hide();

                $('.ezd_content_btn_wrap .left_btn_link').addClass('ezd_left_active');
                $('.ezd_left_content').addClass('ezd_left_content_active');

                $('.ezd_content_btn_wrap .left_btn_link').click(function (){
                    $(this).addClass('ezd_left_active');
                    $('.ezd_left_content').addClass('ezd_left_content_active');
                    $('.ezd_right_content').removeClass('ezd_left_content_active');
                    $('.ezd_content_btn_wrap .right_btn_link').removeClass('ezd_right_active');
                });
                $('.ezd_content_btn_wrap .right_btn_link').click(function (){
                    $(this).addClass('ezd_right_active');
                    $('.ezd_left_content').removeClass('ezd_left_content_active');
                    $('.ezd_right_content').addClass('ezd_left_content_active');
                    $('.ezd_content_btn_wrap .left_btn_link').removeClass('ezd_left_active');
                });

                $("input[type=radio]#widget_data").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').hide();
                        $('.ezd_widget_content_wrap').show();
                        $('.ezd_shortcode_content_wrap_edit').hide();
                    }
                });

                $("input[type=radio]#shortcode").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').show();
                        $('.ezd_widget_content_wrap').hide();
                        $('.ezd_shortcode_content_wrap_edit').show();
                    }
                });

                $("input[type=radio]#string_data").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').show();
                        $('.ezd_widget_content_wrap').hide();
                        $('.ezd_shortcode_content_wrap_edit').show();
                    }
                });

            })
        }

        edit_one_page_doc_doc();

    })
})(jQuery);