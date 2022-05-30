(function ($){
    $(document).ready(function (){
        $("body.post-type-docs .wrap #posts-filter .search-box").append(' <a href="admin.php?page=eazydocs" class="button">Grid View</a>');
        $("body.post-type-one-page-docs .wrap .page-title-action").after(' <a href="admin.php/One_Page.php?single_doc_title=" class="page-title-action add-one-page">Add One Page</a>');

        // SECTION DOC
        function create_one_page_doc_doc() {
            $(document).on('click', '.page-title-action.add-one-page', function (e) {
                e.preventDefault();
                let href = $(this).attr('href');
                (async () => {
                    const { value: formValues } = await Swal.fire({
                        title: 'Want to make One Page?',
                        html:
                            '<label for="ezd_docs_select">Select the doc that want to work on</label><select class="widefat" id="ezd_docs_select">' + eazydocs_local_object.one_page_prompt_docs + '</select>' +
                            '<label for="ezd_docs_sidebar">Sidebar Content</label><textarea id="ezd_docs_sidebar" class="widefat"></textarea>',
                        confirmButtonText: 'Submit',
                    }).then((result) => {
                        if (document.getElementById('ezd_docs_select').value && result.isConfirmed) {
                            window.location.href = href + document.getElementById('ezd_docs_select').value + '&content=' + document.getElementById('ezd_docs_sidebar').value+'&single=ezd-one-page'
                        }
                    })
                })()
            })
        }
        create_one_page_doc_doc();
    })
})(jQuery);