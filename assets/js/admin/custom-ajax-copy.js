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
        add_new_doc()

        // ADD PARENT DOC
        $(document).on('click', '#parent-doc', function (e) {
            e.preventDefault();
            Swal.fire({
                title: eazydocs_local_object.create_prompt_title,
                input: 'text',
                showCancelButton: true,
                inputAttributes: {
                    name: 'parent_title'
                },
            }).then((result) => {
                if (result.isDismissed) {
                    return;
                }
                //document.location.href =  href+result.value;
                $.ajax({
                    url: eazydocs_local_object.ajaxurl,
                    method: "post",
                    data: {
                        action: 'create_parent_doc',
                        parent_title: result.value.trim(),
                    },
                    success: function (response) {
                        $(".easydocs-navbar").prepend(response.data.added_doc)
                        delete_parent_doc(); add_doc_section(); add_child_doc(); delete_child_doc(); delete_doc_sec()
                        $('.easydocs-navitem').removeClass('is-active')
                        $('.tab-'+response.data.post.id).addClass('is-active')
                        // child docs
                        $(".easydocs-tab-content").prepend(response.data.child_docs)
                        $('.easydocs-tab').removeClass('tab-active').hide()
                        $('#tab-'+response.data.post.id).addClass('tab-active').show()
                    },
                    error: function () {
                        console.log("Oops! Something wrong, try again!");
                    }
                })
            })
        })

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
        delete_parent_doc()

        // SECTION DOC
        function add_doc_section() {
            $(document).on('click', '.section-doc', function (e) {
                e.preventDefault();
                let href = $(this).attr('data-url')
                Swal.fire({
                    title: eazydocs_local_object.create_prompt_title,
                    input: 'text',
                    showCancelButton: true,
                    inputAttributes: {
                        name: 'section_title'
                    },
                }).then((result) => {
                    if (result.value) {
                        document.location.href = href + result.value;
                    }
                })
            })
        }
        add_doc_section()

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
        add_child_doc()

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
        delete_doc_sec()

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
        delete_child_doc()

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

    });
})(jQuery);