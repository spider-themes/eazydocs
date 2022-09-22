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
                (async () => {
                    const {value: formValues} = await Swal.fire({
                        title: 'Want to make OnePage?',
                        customClass: {
                            container: 'onepage_create_wrapper',
                        },
                        html:
                            '<div class="create_onepage_doc_area">' +
                            '<label for="ezd_docs_sidebar">Select Layout</label>' +
                            '<select class="widefat" id="ezd_docs_select" name="ezd_onepage_select">' +
                            '<option value="default-layout">Default Layout</option>' +
                            '<option value="others-layout">Fullscreen OnePage Doc</option>' +
                            '</select>' +
                            '<div class="other_layout_fields">' +
                            '<div class="ezd_docs_btn_one_wrap">' +
                            '<label for="ezd_docs_btn_one">Primary Button</label>' +
                            '<input type="text" id="ezd_docs_btn_one" name="ezd_docs_btn_one" class="widefat" placeholder="Insert Button Text">' +
                            '<input type="text" id="ezd_docs_btn_one_url" name="ezd_docs_btn_one_url" class="widefat" placeholder="Insert Button URL">' +
                            '</div>' +
                            '<div class="ezd_docs_btn_two_wrap">' +
                            '<label for="ezd_docs_btn_two">Secondary Button</label>' +
                            '<input type="text" id="ezd_docs_btn_two" name="ezd_docs_btn_two" class="widefat" placeholder="Insert Button Text">' +
                            '<input type="text" id="ezd_docs_btn_two_url" name="ezd_docs_btn_two_url" class="widefat" placeholder="Insert Button URL">' +
                            '</div>' +
                            '</div>' +
                            '</div>',
                        confirmButtonText: 'Publish',
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {

                            let layout = document.getElementById('ezd_docs_select').value;

                            if( layout === 'others-layout' ){
                                window.location.href = href + '&layout=' + document.getElementById('ezd_docs_select').value  +
                                    '&primary_btn_text=' + document.getElementById('ezd_docs_btn_one').value + '&primary_btn_url=' +
                                    document.getElementById('ezd_docs_btn_one_url').value + '&secondary_btn_text=' +
                                    document.getElementById('ezd_docs_btn_two').value + '&secondary_btn_url=' +
                                    document.getElementById('ezd_docs_btn_two_url').value
                            }else{
                                window.location.href = href + '&layout=' + document.getElementById('ezd_docs_select').value
                            }

                         }
                    })
                })();

                $('#ezd_docs_select').on('change', function() {
                   let layout = this.value;

                    if ( layout  === 'others-layout') {
                        $('.onepage_create_wrapper').addClass('others-layout');
                        $('.other_layout_fields').show();
                    } else {
                        $('.other_layout_fields').hide();
                        $('.onepage_create_wrapper').removeClass('others-layout');
                    }

                });

            })
        }
        one_page_doc();









    });
})(jQuery);
