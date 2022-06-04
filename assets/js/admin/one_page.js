(function ($) {
    $(document).ready(function () {

        $("body.post-type-docs .wrap #posts-filter .search-box").append(' <a href="admin.php?page=eazydocs" class="button">Grid View</a>');
        $("body.post-type-onepage-docs .wrap .page-title-action").after(' <a href="admin.php/One_Page.php?single_doc_title=" class="page-title-action add-onepage">Add One Page</a>');

        // CREATE ONE PAGE DOC
        function create_one_page_doc_doc() {
            $(document).on('click', '.page-title-action.add-onepage', function (e) {
                e.preventDefault();
                let href = $(this).attr('href');
                (async () => {
                    const {value: formValues} = await Swal.fire({
                        title: 'Want to make One Page?',
                        html:
                            '<label for="ezd_docs_select">Select the doc that want to work on</label><select class="widefat" id="ezd_docs_select">' + eazydocs_local_object.one_page_prompt_docs + '</select>' +
                            '<label for="ezd_docs_sidebar">Sidebar Content</label><textarea id="ezd_docs_sidebar" class="widefat"></textarea>',
                        confirmButtonText: 'Submit',
                    }).then((result) => {
                        if (document.getElementById('ezd_docs_select').value && result.isConfirmed) {
                            window.location.href = href + document.getElementById('ezd_docs_select').value + '&content=' + document.getElementById('ezd_docs_sidebar').value + '&self_doc=ezd-one-page'
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
                let edit_doc_id = $.urlParam('post');
                let edit_doc_content = $.urlParam('content');

                // redirect url when editing
                let href = eazydocs_local_object.edit_one_page_url;

                (async () => {
                    const {value: formValues} = await Swal.fire({
                        title: 'Want to edit this doc?',
                        html:
                            '<label for="ezd_edit_docs_sidebar">Sidebar Content</label>' +
                            '<textarea id="ezd_edit_docs_sidebar" class="widefat">'+ edit_doc_content +'</textarea>',
                        confirmButtonText: 'Update',
                    }).then((result) => {
                        if (document.getElementById('ezd_edit_docs_sidebar').value && result.isConfirmed) {
                            window.location.href = href + '&doc_id=' + edit_doc_id + '&edit_content=' + document.getElementById('ezd_edit_docs_sidebar').value
                        }
                    })
                })()
            })
        }
        edit_one_page_doc_doc();

    })
})(jQuery);