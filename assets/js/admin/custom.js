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

                        let results = result.value.replaceAll('&', 'ezd_ampersand');
                        results     = results.replaceAll('#', 'ezd_hash'); 
                        results     = results.replaceAll('+', 'ezd_plus');

                       document.location.href = href + results;
                       
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

                        let results = result.value.replaceAll('&', 'ezd_ampersand');
                        results     = results.replaceAll('#', 'ezd_hash'); 
                        results     = results.replaceAll('+', 'ezd_plus');
                
                        document.location.href = href + results;
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
                        
                        let results = result.value.replaceAll('&', 'ezd_ampersand');
                        results     = results.replaceAll('#', 'ezd_hash'); 
                        results     = results.replaceAll('+', 'ezd_plus');

                       document.location.href = href + results;
                       
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
                        
                        let results = result.value.replaceAll('&', 'ezd_ampersand');
                        results     = results.replaceAll('#', 'ezd_hash'); 
                        results     = results.replaceAll('+', 'ezd_plus');

                       document.location.href = href + results;
                       
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
            $('.child-delete').on('click', function (e) {
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
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                if (value.length > 0) {
                    $('.easydocs-accordion').find('.dd-list').css("display", "block");
                    $('.nestable--collapse').show();
                    $('.nestable--expand').hide();
                } else {
                    $('.easydocs-accordion').find('ol li ol.dd-list').css("display", "none");
                    $('.nestable--collapse').hide();
                    $('.nestable--expand').show();
                }

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
                                '<option value="classic-onepage-layout">Classic OnePage Doc</option>' +
                                '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>' +
                            '</select>' +
                            '<div class="ezd_content_btn_wrap">' +
                                '<div class="left_btn_link ezd_left_active">Left Sidebar</div>' +
                                '<div class="right_btn_link">Right Sidebar</div>' +
                            '</div>' +
                            
                            '<div class="ezd_left_content">' +
                                '<div class="ezd_docs_content_type_wrap">' +                                    
                                    '<label for="ezd_docs_content_type">Content Type:</label>' +

                                    '<input type="radio" id="widget_data" name="ezd_docs_content_type" value="widget_data">' +                                    
                                    '<label for="widget_data">Reusable Blocks</label>' +

                                    '<input type="radio" checked id="string_data" name="ezd_docs_content_type" value="string_data">' +
                                    '<label for="string_data">Normal Content</label>' +
                                '</div>' +

                                '<div class="ezd_shortcode_content_wrap">' +
                                    '<label for="ezd-shortcode">Content (Optional) </label><br>' +
                                    '<textarea name="ezd-shortcode-content" id="ezd-shortcode-content" rows="5" class="widefat"></textarea>' +
                                    '<span class="ezd-text-support">*The field will support text and html formats.</span>'+
                                '</div>' +
                                '<div class="ezd_widget_content_wrap">' +
                                    eazydocs_local_object.get_reusable_block +
                                    eazydocs_local_object.manage_reusable_blocks +
                                '</div>' +
                            '</div>' +

                            '<div class="ezd_right_content">' +
                                '<div class="ezd_docs_content_type_wrap">' +
                                    '<label for="ezd_docs_content_type">Content Type:</label>' +
                                    '<input type="radio" id="widget_data_right" name="ezd_docs_content_type_right" value="widget_data_right">' +
                            
                                    '<label for="widget_data_right">Reusable Blocks</label>' +
                                    '<input type="radio" checked id="string_data_right" name="ezd_docs_content_type_right" value="string_data_right">' +
                                    '<label for="string_data_right">Normal Content</label>' +

                                    '<input type="radio" id="shortcode_right" name="ezd_docs_content_type_right" value="shortcode_right">' +
                                    '<label for="shortcode_right">Doc Sidebar</label>' +
                                    '<div class="ezd-doc-sidebar-intro">To show the doc sidebar data, you have to go to <b>appearance</b> then <b>widgets</b> and just add your content inside <b>Doc Right Sidebar</b> location. If you cant find the location in the Widgets area, go to <b>EazyDocs</b> -> <b>Settings</b>. Then go to <b>Doc Single</b> -> <b>Right Sidebar</b> and then enable the option called <b>"Widgets Area"</b>'+                        
                                '</div>' +
                            '</div>' +
                            '<div class="ezd_shortcode_content_wrap_right">' +
                                '<label for="ezd-shortcode">Content (Optional) </label><br>' +
                                '<textarea name="ezd-shortcode-content-right" id="ezd-shortcode-content-right" rows="5" class="widefat"></textarea>' +
                                '<span class="ezd-text-support">*The field will support text and html formats.</span>'+
                            '</div>' +
                            '<div class="ezd_widget_content_wrap_right">' +
                                eazydocs_local_object.get_reusable_blocks_right +
                                eazydocs_local_object.manage_reusable_blocks +
                            '</div>' +
                        '</div>',
                    confirmButtonText: 'Publish',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {

                        let left_content        = document.getElementById('ezd-shortcode-content').value;
                        let right_content       = document.getElementById('ezd-shortcode-content-right').value;
                         
                        let get_left_content    = left_content.replace(/<!--(.*?)-->/gm, "");
                        let style_attr_update1   = get_left_content.replaceAll('style=', 'style@');
                        let style_attr_update2 = style_attr_update1.replaceAll('#', ';hash;');
                        let style_attr_update = style_attr_update2.replaceAll('style&equals;', 'style@');
                        
                        let get_right_content       = right_content.replace(/<!--(.*?)-->/gm, "");
                        let right_style_attr_update1 = get_right_content.replaceAll('style=', 'style@');
                        let right_style_attr_update2 = right_style_attr_update1.replaceAll('#', ';hash;');
                        let right_style_attr_update = right_style_attr_update2.replaceAll('style&equals;', 'style@');
                        
                        encoded = encodeURIComponent(JSON.stringify(style_attr_update));						 
                        encoded_right = encodeURIComponent(JSON.stringify(right_style_attr_update)); 
                         
                        window.location.href = href + '&layout=' + document.getElementById('ezd_docs_select').value + '&content_type=' + document.querySelector('input[name=ezd_docs_content_type]:checked').value + '&left_side_sidebar=' + document.getElementById('left_side_sidebar').value + '&shortcode_content=' + encoded
					
                        + '&shortcode_right=' + document.querySelector('input[name=ezd_docs_content_type_right]:checked').value + '&shortcode_content_right=' + encoded_right +
						
                            '&right_side_sidebar=' + document.getElementById('right_side_sidebar').value
                    }
                });

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
                    }
                });

                $("input[type=radio]#shortcode").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').show();
                        $('.ezd_widget_content_wrap').hide();
                    }
                });

                $("input[type=radio]#string_data").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').show();
                        $('.ezd_widget_content_wrap').hide();
                    }
                });

                // RIGHT TAB
                $('.ezd_widget_content_wrap_right,.ezd-doc-sidebar-intro').hide();

                $("input[type=radio]#string_data_right").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_widget_content_wrap_right').hide();
                        $('.ezd_shortcode_content_wrap_right').show();
                        $('.ezd-doc-sidebar-intro').hide();
                    }
                });
                $("input[type=radio]#shortcode_right").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_widget_content_wrap_right').hide();
                        $('.ezd_shortcode_content_wrap_right').hide();
                        $('.ezd-doc-sidebar-intro').show();
                    }
                });
                $("input[type=radio]#widget_data_right").click(function () {
                    if($(this).prop("checked")) {
                        $('.ezd_widget_content_wrap_right').show();
                        $('.ezd_shortcode_content_wrap_right,.ezd-doc-sidebar-intro').hide();
                    }
                });

            })
        }
        one_page_doc()

        // BULK OPTIONS
        $('.ezd-admin-bulk-options').click(function(){
            $(this).toggleClass('active');
            $('.ezd-admin-bulk-options.active > .dashicons').addClass('arrow-active')
        });
        
    });

})(jQuery);