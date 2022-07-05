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
                            '<select class="widefat" id="ezd_docs_select">' + eazydocs_local_object.one_page_prompt_docs + '</select>' +
                            '<label for="ezd_docs_sidebar">Select Layout</label>' +
                            '<select class="widefat" id="ezd_docs_layout" name="ezd_docs_layout">' +
                            '<option value="default-layout">Default Layout</option>' +
                            '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>' +
                            '</select>' +
                            '<div class="ezd_docs_content_type_wrap">' +
                            '<label for="ezd_docs_content_type">Content Type</label>' +
                            '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode">' +
                            '<label for="shortcode">Shortcode</label>' +
                            '<input type="radio" checked id="string_data" name="ezd_docs_content_type" value="string_data">' +
                            '<label for="string_data">Normal Content</label>' +
                            '</div>' +
                            '<div class="ezd_shortcode_wrap"></div>' +
                            '<label for="ezd-shortcode">Content (Optional) </label><br>' +
                            '<textarea name="ezd_docs_sidebar" id="ezd_docs_sidebar" rows="5" class="widefat"></textarea>' +
                            '</div>',


                        confirmButtonText: 'Publish',
                        showCancelButton: true,
                    }).then((result) => {

                        if (result.isConfirmed) {
                            encoded = encodeURIComponent(JSON.stringify(document.getElementById('ezd_docs_sidebar').value))
                            //encoded = JSON.parse(decodeURIComponent(document.getElementById('ezd-shortcode-content').value));

                            window.location.href = href + document.getElementById('ezd_docs_select').value + '&layout=' + document.getElementById('ezd_docs_layout').value +
                                '&content_type=' + document.querySelector('input[name=ezd_docs_content_type]:checked').value +
                                '&shortcode_content=' + encoded + '&self_doc=ezd-one-page'
                        }

                    })
                })()
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
                let edit_doc_id         = $.urlParam('post');
                let edit_doc_content    = $.urlParam('content');
                let doc_layout          = $.urlParam('doc_layout');
                let content_type        = $.urlParam('content_type');

                // Doc Layout
                let doc_layout_opt;
                if ( doc_layout         == 'default-layout' ){
                    doc_layout_opt      = '<option value="default-layout" selected>Default Layout </option>';
                    doc_layout_opt      += '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>';
                } else {
                    doc_layout_opt      = '<option value="default-layout">Default Layout</option>';
                    doc_layout_opt      += '<option value="fullscreen-layout" selected>Fullscreen OnePage Doc</option>';
                }

                //Content Type
                let content_type_opt;
                if ( content_type       == 'string_data' ){
                    content_type_opt    = '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode"><label for="shortcode">Shortcode</label>'
                    content_type_opt    += '<input type="radio" checked id="string_data" name="ezd_docs_content_type" value="string_data" checked><label for="string_data">Normal Content</label>'
                } else {
                    content_type_opt    = '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode" checked><label for="shortcode">Shortcode</label>'
                    content_type_opt    += '<input type="radio" id="string_data" name="ezd_docs_content_type" value="string_data"><label for="string_data">Normal Content</label>'
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
                            '<select class="widefat" id="ezd_docs_select" name="ezd_onepage_select">'
                            + doc_layout_opt +
                            '</select>' +
                            '<div class="ezd_docs_content_type_wrap">' +
                            '<label for="ezd_docs_content_type">Content Type:</label>'
                            + content_type_opt +
                            '</div>' +
                            '<div class="ezd_shortcode_wrap"></div>' +
                            '<label for="ezd-shortcode">Content (Optional) </label><br>' +
                            '<textarea name="ezd-shortcode-content" class="widefat" id="ezd-shortcode-content" rows="5">'+ edit_doc_content +'</textarea>' +
                            '</div>',
                        confirmButtonText: 'Update',
                        showCancelButton: true
                    }).then((result) => {

                        if (result.isConfirmed) {
                            encoded = encodeURIComponent(JSON.stringify(document.getElementById('ezd-shortcode-content').value))
                            //encoded = JSON.parse(decodeURIComponent(document.getElementById('ezd-shortcode-content').value));

                            window.location.href = href + '&doc_id=' + edit_doc_id + '&layout=' + document.getElementById('ezd_docs_select').value +
                                '&content_type=' + document.querySelector('input[name=ezd_docs_content_type]:checked').value +
                                '&edit_content=' + encoded
                        }

                        /*if (document.getElementById('ezd_edit_docs_sidebar').value && result.isConfirmed) {
                            window.location.href = href + '&doc_id=' + edit_doc_id + '&edit_content=' + document.getElementById('ezd_edit_docs_sidebar').value
                        }*/
                    })
                })()
            })
        }
        edit_one_page_doc_doc();

    })
})(jQuery);