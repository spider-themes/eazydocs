(function($){
    'use sticky'
    $(document).ready(function(){

        // NEW DOC
        function add_new_doc() {
            $(document).on('click', '#new-doc', function (e) {
                e.preventDefault();
                let href = $(this).attr('href')
                Swal.fire({
                    title: eazydocs_local_object.create_prompt_title,
                    input: 'text',
                    showCancelButton: true,
                    inputAttributes: {
                        name: 'new_doc'
                    },
                }).then((result) => {
                    if (result.value) {
                        document.location.href = href + result.value;
                    }
                })
            })
        }
        add_new_doc();

        // ADD PARENT DOC
        function add_parent_doc() {
            $(document).on('click', '#parent-doc', function (e) {
                e.preventDefault();
                let href = $(this).attr('data-url')
                Swal.fire({
                    title: eazydocs_local_object.create_prompt_title,
                    input: 'text',
                    showCancelButton: true,
                    inputAttributes: {
                        name: 'parent_title'
                    },
                }).then((result) => {
                    if (result.value) {
                        document.location.href = href + result.value;
                    }
                })
            })
        }
        add_parent_doc();

        // SECTION DOC
        function create_section_doc() {
            $(document).on('click', '#section-doc', function (e) {
                e.preventDefault();
                let href = $(this).attr('data-url')
                Swal.fire({
                    title: eazydocs_local_object.create_prompt_title,
                    input: 'text',
                    showCancelButton: true,
                    inputAttributes: {
                        name: 'section'
                    },
                }).then((result) => {
                    if (result.value) {
                        document.location.href = href + result.value;
                    }
                })
            })
        }
        create_section_doc();

        // ADD CHILD DOC
        function add_child_doc() {
            $('.child-doc').on('click', function (e) {
                e.preventDefault();
                let href = $(this).attr('href')
                Swal.fire({
                    title: eazydocs_local_object.create_prompt_title,
                    input: 'text',
                    showCancelButton: true,
                    inputAttributes: {
                        name: 'child_title'
                    },
                }).then((result) => {
                    if (result.value) {
                        document.location.href = href + result.value;
                    }
                })
            })
        }
        add_child_doc();

        // Delete parent doc
        function delete_parent_doc() {
            $(".parent-delete").on("click", function (e) {
                e.preventDefault();
                let href = $(this).attr('href')
                Swal.fire({
                    title: eazydocs_local_object.delete_prompt_title,
                    text: eazydocs_local_object.no_revert_title,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        document.location.href = href;
                    }
                })
            })
        }
        delete_parent_doc();

        // DELETE DOC SECTION
        function delete_doc_sec() {
            $('.section-delete').on('click', function (e) {
                e.preventDefault();
                const href = $(this).attr('href')
                Swal.fire({
                    title: eazydocs_local_object.delete_prompt_title,
                    text: eazydocs_local_object.no_revert_title,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        document.location.href = href;
                    }
                })
            })
        }
        delete_doc_sec();

        // DELETE CHILD DOC
        function delete_child_doc() {
            $(document).on('click', '.child-delete', function (e) {
                e.preventDefault();
                const href = $(this).attr('href')
                Swal.fire({
                    title: eazydocs_local_object.delete_prompt_title,
                    text: eazydocs_local_object.no_revert_title,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        document.location.href = href;
                    }
                })
            })
        }
        delete_child_doc();

        // Docs Search
        $("#easydocs-search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".easydocs-accordion-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            })
        });

        // If nav item is active with cookie / else
        if (!$('.easydocs-navitem').hasClass("is-active")) {
            $('.easydocs-navitem:first-child').addClass('is-active');
            $('.easydocs-tab:first-child').css('display','block');
        }

        // CREATE ONE PAGE DOC
        function one_page_doc() {
            $(document).on('click', '.one-page-doc', function (e) {
                e.preventDefault();
                let href = $(this).attr('data-url');
                Swal.fire({
                    title: 'Want to make OnePage?',
                    html:
                        '<div class="create_onepage_doc_area">' +
                        '<label for="ezd_docs_sidebar">Select Layout</label>' +
                        '<select class="widefat" id="ezd_docs_select" name="ezd_onepage_select">' +
                        '<option value="default-layout">Default Layout</option>' +
                        '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>' +
                        '</select>' +
                        '<div class="ezd_docs_content_type_wrap">' +
                        '<label for="ezd_docs_content_type">Content Type:</label>' +
                        '<input type="radio" id="shortcode" name="ezd_docs_content_type" value="shortcode">' +
                        '<label for="shortcode">Shortcode</label>' +
                        '<input type="radio" checked id="string_data" name="ezd_docs_content_type" value="string_data">' +
                        '<label for="string_data">Normal Content</label>' +
                        '</div>' +
                        '<div class="ezd_shortcode_wrap"></div>' +
                        '<label for="ezd-shortcode">Content (Optional) </label><br>' +
                        '<textarea name="ezd-shortcode-content" id="ezd-shortcode-content" rows="5" class="widefat"></textarea>' +
                        '</div>',
                    confirmButtonText: 'Publish',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        encoded = encodeURIComponent(JSON.stringify(document.getElementById('ezd-shortcode-content').value))
                        //encoded = JSON.parse(decodeURIComponent(document.getElementById('ezd-shortcode-content').value));

                        window.location.href = href + '&layout=' + document.getElementById('ezd_docs_select').value +
                            '&content_type=' + document.querySelector('input[name=ezd_docs_content_type]:checked').value +
                            '&shortcode_content=' + encoded
                    }
                });

            })
        }
        one_page_doc()

    });
})(jQuery);