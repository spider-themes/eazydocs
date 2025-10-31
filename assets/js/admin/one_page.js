;(function ($) {
    $(document).ready(function () {

        $("body.post-type-docs .wrap #posts-filter .search-box").append(' <a href="admin.php?page=eazydocs" class="button">Modern View</a>');
        $("body.post-type-onepage-docs .wrap .page-title-action").after(' <a href="admin.php" class="page-title-action add-onepage">Add OnePage Doc</a>');

		// CREATE ONE PAGE DOC
		function one_page_doc() {
			$(document).on('click', '.page-title-action.add-onepage, .one-page-doc', function (e) {
				e.preventDefault();

                let selectFieldHtml = '';
                if (window.location.search.includes('post_type=onepage-docs')) {
                    selectFieldHtml = `
                        <label for="ezd_docs_select">Select the doc that you want to work on</label>
                        <select class="widefat" id="ezd_docs_select" required>
                            ${eazydocs_local_object.one_page_prompt_docs}
                        </select>
                        <input type="hidden" id="ezd_onepage_nonce">`;
                }
                
				Swal.fire({
					title: 'Want to make OnePage?',
					html:
						'<div class="create_onepage_doc_area">' +
                        '<input type="hidden" id="admin_url" value="'+eazydocs_local_object.onepage_doc_admin_url+'">'+                        
                         selectFieldHtml +
						'<label for="ezd_docs_sidebar">Content Layout</label>' +
						'<select required class="widefat" id="ezd_docs_layout" name="ezd_docs_layout">' +
						'<option value="">Select Layout</option>' +
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
						'<span class="ezd-text-support">*The field will support text and html formats.</span>' +
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
						'<div class="ezd-doc-sidebar-intro">To show the doc sidebar data, you have to go to <b>appearance</b> then <b>widgets</b> and just add your content inside <b>Doc Right Sidebar</b> location. If you cant find the location in the Widgets area, go to <b>EazyDocs</b> -> <b>Settings</b>. Then go to <b>Doc Single</b> -> <b>Right Sidebar</b> and then enable the option called <b>"Widgets Area"</b>' +
						'</div>' +
						'</div>' +
						'<div class="ezd_shortcode_content_wrap_right">' +
						'<label for="ezd-shortcode">Content (Optional) </label><br>' +
						'<textarea name="ezd-shortcode-content-right" id="ezd-shortcode-content-right" rows="5" class="widefat"></textarea>' +
						'<span class="ezd-text-support">*The field will support text and html formats.</span>' +
						'</div>' +
						'<div class="ezd_widget_content_wrap_right">' +
						eazydocs_local_object.get_reusable_blocks_right +
						eazydocs_local_object.manage_reusable_blocks +
						'</div>' +
						'</div>',
					confirmButtonText: 'Publish',
					showCancelButton: true,
                    preConfirm: () => {
                        let layout          = document.getElementById('ezd_docs_layout')?.value.trim();
                        let selectedDoc     = document.getElementById('ezd_docs_select')?.value.trim();
                       
                        // Validate document selection
                        if (window.location.search.includes('post_type=onepage-docs')) {
                            if (!selectedDoc || selectedDoc === "") {
                                Swal.showValidationMessage("Please select a doc.");
                                return false;
                            }
                        }
                    
                        // Validate layout selection
                        if (!layout || layout === "") {
                            Swal.showValidationMessage("Please select a layout.");
                            return false;
                        }
                        
                        return { layout, selectedDoc };
                    }
                    
				}).then((result) => {
					if (result.isConfirmed) {
						let left_content  = document.getElementById( 'ezd-shortcode-content' ).value;
						let right_content = document.getElementById( 'ezd-shortcode-content-right' ).value;

						let get_left_content = left_content.replace(
							/<!--(.*?)-->/gm,
							''
						);
						let style_attr_update1 = get_left_content.replaceAll(
							'style=',
							'style@'
						);
						let style_attr_update2 = style_attr_update1.replaceAll(
							'#',
							';hash;'
						);
						let style_attr_update = style_attr_update2.replaceAll(
							'style&equals;',
							'style@'
						);

						let get_right_content = right_content.replace(
							/<!--(.*?)-->/gm,
							''
						);
						let right_style_attr_update1 =
							get_right_content.replaceAll('style=', 'style@');
						let right_style_attr_update2 =
							right_style_attr_update1.replaceAll('#', ';hash;');
						let right_style_attr_update =
							right_style_attr_update2.replaceAll(
								'style&equals;',
								'style@'
							);

						let encoded = encodeURIComponent(
							JSON.stringify(style_attr_update)
						);
						let encoded_right = encodeURIComponent(
							JSON.stringify(right_style_attr_update)
						);

                        let href        = $(this).attr('data-url');

                        if (window.location.search.includes('post_type=onepage-docs')) {
                            let docId           = document.getElementById('ezd_docs_select').value;
                            let admin_url       = document.getElementById('admin_url').value;
                            let selectedNonce   = document.getElementById('ezd_onepage_nonce')?.value.trim();
                            href                = admin_url+'?parentID='+docId+'&make_onepage=yes&self_doc=yes&_wpnonce='+selectedNonce+'&';
                        }

						window.location.href =
                        href +
							'&layout=' +
							document.getElementById('ezd_docs_layout').value +
							'&content_type=' +
							document.querySelector(
								'input[name=ezd_docs_content_type]:checked'
							).value +
							'&left_side_sidebar=' +
							document.getElementById('left_side_sidebar').value +
							'&shortcode_content=' +
							encoded +
							'&shortcode_right=' +
							document.querySelector(
								'input[name=ezd_docs_content_type_right]:checked'
							).value +
							'&shortcode_content_right=' +
							encoded_right +
							'&right_side_sidebar=' +
							document.getElementById('right_side_sidebar').value;
					}
				});

                // Set nonce on doc selection change
                $('#ezd_docs_select').on('change', function () {
                    // Get the selected option
                    const selectedOption = $(this).find('option:selected');
                    
                    // Get the _wpnonce attribute from that option
                    const nonce = selectedOption.attr('_wpnonce') || '';

                    // Set it into the input field
                    $('#ezd_onepage_nonce').val(nonce);
                });

				$('.ezd_content_btn_wrap .left_btn_link').addClass(
					'ezd_left_active'
				);
				$('.ezd_left_content').addClass('ezd_left_content_active');

				$('.ezd_content_btn_wrap .left_btn_link').click(function () {
					$(this).addClass('ezd_left_active');
					$('.ezd_left_content').addClass('ezd_left_content_active');
					$('.ezd_right_content').removeClass(
						'ezd_left_content_active'
					);
					$('.ezd_content_btn_wrap .right_btn_link').removeClass(
						'ezd_right_active'
					);
				});
				$('.ezd_content_btn_wrap .right_btn_link').click(function () {
					$(this).addClass('ezd_right_active');
					$('.ezd_left_content').removeClass(
						'ezd_left_content_active'
					);
					$('.ezd_right_content').addClass('ezd_left_content_active');
					$('.ezd_content_btn_wrap .left_btn_link').removeClass(
						'ezd_left_active'
					);
				});

				$('input[type=radio]#widget_data').click(function () {
					if ($(this).prop('checked')) {
						$('.ezd_shortcode_content_wrap').hide();
						$('.ezd_widget_content_wrap').show();
					}
				});

				$('input[type=radio]#shortcode').click(function () {
					if ($(this).prop('checked')) {
						$('.ezd_shortcode_content_wrap').show();
						$('.ezd_widget_content_wrap').hide();
					}
				});

				$('input[type=radio]#string_data').click(function () {
					if ($(this).prop('checked')) {
						$('.ezd_shortcode_content_wrap').show();
						$('.ezd_widget_content_wrap').hide();
					}
				});

				// RIGHT TAB
				$('.ezd_widget_content_wrap_right,.ezd-doc-sidebar-intro').hide();

				$('input[type=radio]#string_data_right').click(function () {
					if ($(this).prop('checked')) {
						$('.ezd_widget_content_wrap_right').hide();
						$('.ezd_shortcode_content_wrap_right').show();
						$('.ezd-doc-sidebar-intro').hide();
					}
				});
				$('input[type=radio]#shortcode_right').click(function () {
					if ($(this).prop('checked')) {
						$('.ezd_widget_content_wrap_right').hide();
						$('.ezd_shortcode_content_wrap_right').hide();
						$('.ezd-doc-sidebar-intro').show();
					}
				});
				$('input[type=radio]#widget_data_right').click(function () {
					if ($(this).prop('checked')) {
						$('.ezd_widget_content_wrap_right').show();
						$(
							'.ezd_shortcode_content_wrap_right,.ezd-doc-sidebar-intro'
						).hide();
					}
				});
			});
		}
		one_page_doc();

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
                let edit_doc_content = $.urlParam("content");
                let doc_layout = $.urlParam('doc_layout');
                let content_type = $.urlParam('content_type');
                let content_type_right = $.urlParam('content_type_right');
                let edit_content_right = $.urlParam('content_right');
                let _wpnonce = $.urlParam('_wpnonce');

                // Doc Layout
                let doc_layout_opt;
                if (doc_layout == 'classic-onepage-layout') {
                    doc_layout_opt = '<option value="classic-onepage-layout" selected>Classic OnePage Doc</option>';
                    doc_layout_opt += '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>';
                } else if (doc_layout == 'fullscreen-layout') {
                    doc_layout_opt = '<option value="fullscreen-onepage-layout" selected>Fullscreen OnePage Doc</option>';
                    doc_layout_opt += '<option value="classic-layout">Classic OnePage Doc</option>';
                }
                 else {
                    doc_layout_opt = '<option value="" selected>Select Layout</option>';
                    doc_layout_opt += '<option value="classic-onepage-layout">Classic OnePage Doc</option>';
                    doc_layout_opt += '<option value="fullscreen-layout">Fullscreen OnePage Doc</option>';
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
                            '<label for="ezd_docs_sidebar">Content Layout</label>' +
                            '<select class="widefat" id="ezd_docs_select" name="ezd_onepage_select">' +
                            doc_layout_opt +
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
                            '<span class="ezd-text-support">*The field will support text and html formats.</span>' +
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
                            '<div class="ezd-doc-sidebar-intro">To show the doc sidebar data, you have to go to <b>appearance</b> then <b>widgets</b> and just add your content inside <b>Doc Right Sidebar</b> location. If you cant find the location in the Widgets area, go to <b>EazyDocs</b> -> <b>Settings</b>. Then go to <b>Doc Single</b> -> <b>Right Sidebar</b> and then enable the option called <b>"Widgets Area"</b>' +
                            '</div>' +
                            '</div>' +
                            '<div class="ezd_shortcode_content_wrap_right">' +
                            '<label for="ezd-shortcode">Content (Optional) </label><br>' +
                            '<textarea name="ezd-shortcode-content-right" id="ezd-shortcode-content-right" rows="5" class="widefat"></textarea>' +
                            '<span class="ezd-text-support">*The field will support text and html formats.</span>' +
                            '</div>' +
                            '<div class="ezd_widget_content_wrap_right">' +
                            eazydocs_local_object.get_reusable_blocks_right +
                            eazydocs_local_object.manage_reusable_blocks +
                            '</div>' +
                            '</div>',

                        confirmButtonText: 'Update',
                        showCancelButton: true
                    }).then((result) => {

                        if (result.isConfirmed) {

                            let left_content = document.getElementById('ezd-shortcode-content').value;
                            let right_content = document.getElementById('ezd-shortcode-content-right').value;

                            let get_left_content = left_content.replace(/<!--(.*?)-->/gm, "");
                            let style_attr_update1 = get_left_content.replaceAll('style=', 'style@');
                            let style_attr_update2 = style_attr_update1.replaceAll('#', ';hash;');
                            let style_attr_update = style_attr_update2.replaceAll('style&equals;', 'style@');

                            let get_right_content = right_content.replace(/<!--(.*?)-->/gm, "");
                            let right_style_attr_update1 = get_right_content.replaceAll('style=', 'style@');
                            let right_style_attr_update2 = right_style_attr_update1.replaceAll('#', ';hash;');
                            let right_style_attr_update = right_style_attr_update2.replaceAll('style&equals;', 'style@');

                            encoded = encodeURIComponent(JSON.stringify(style_attr_update));
                            encoded_right = encodeURIComponent(JSON.stringify(right_style_attr_update));

                            window.location.href = href + 'doc_id=' + edit_doc_id + '&_wpnonce=' + _wpnonce + '&layout=' + document.getElementById('ezd_docs_select').value +
                                '&content_type=' + document.querySelector('input[name=ezd_docs_content_type]:checked').value + '&left_side_sidebar=' + document.querySelector('select[name=ezd_sidebar_select_data]').value + '&edit_content=' + encoded + '&get_right_sidebar=' + document.querySelector('input[name=ezd_docs_content_type_right]:checked').value + '&shortcode_right=' + document.querySelector('input[name=ezd_docs_content_type_right]:checked').value + '&shortcode_content_right=' + encoded_right + '&right_side_sidebar=' + document.getElementById('right_side_sidebar').value + '&edit_onepage=yes';
                        }

                    });
                })()

                // LEFT CONTENT [ ACTIVE ]
                
                /** Content type options start **/
                $('.ezd_shortcode_content_wrap_edit').hide();
                $('.ezd_widget_content_wrap_right,.ezd-doc-sidebar-intro').hide();

                if (content_type == 'widget_data') {
                    $('#widget_data').prop('checked', true);
                    $('.ezd_shortcode_content_wrap').hide();
                    $('.ezd_widget_content_wrap').show();
                } else if (content_type == 'string_data') {
                    $('#string_data').prop('checked', true);
                    $('.ezd_shortcode_content_wrap').show();
                    $('.ezd_widget_content_wrap').hide();
                    $('#ezd-shortcode-content').val(edit_doc_content);
                }

                $('#widget_data').click(function () {
                    $('.ezd_widget_content_wrap').show();
                    $('.ezd_shortcode_content_wrap').hide();
                });

                $('#string_data').click(function () {
                    $('.ezd_widget_content_wrap').hide();
                    $('.ezd_shortcode_content_wrap').show();
                });

                if (content_type_right == 'widget_data_right') {
                    $('#widget_data_right').prop('checked', true);
                    $('.ezd_shortcode_content_wrap_right').hide();
                    $('.ezd_widget_content_wrap_right').show();
                    $('.ezd-doc-sidebar-intro').hide();

                } else if (content_type_right == 'string_data_right') {
                    $('#string_data_right').prop('checked', true);
                    $('.ezd_shortcode_content_wrap_right').show();
                    $('.ezd_widget_content_wrap_right').hide();
                    $('#ezd-shortcode-content-right').val(edit_content_right);

                } else {
                    $('#shortcode_right').prop('checked', true);
                    $('.ezd_shortcode_content_wrap_right').hide();
                    $('.ezd-doc-sidebar-intro').show();
                }

                $('#widget_data_right').click(function () {
                    $('.ezd_shortcode_content_wrap_right').hide();
                    $('.ezd_widget_content_wrap_right').show();
                    $('.ezd-doc-sidebar-intro').hide();
                });

                $('#string_data_right').click(function () {
                    $('.ezd_shortcode_content_wrap_right').show();
                    $('.ezd_widget_content_wrap_right').hide();
                    $('.ezd-doc-sidebar-intro').hide();
                });

                $('#shortcode_right').click(function () {
                    $('.ezd_widget_content_wrap_right,.ezd_shortcode_content_wrap_right').hide();
                    $('.ezd-doc-sidebar-intro').show();
                });
                /** Content type options ended **/

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
                        $('.ezd_shortcode_content_wrap_edit').hide();
                    }
                });

                $("input[type=radio]#shortcode").click(function () {
                    if ($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').show();
                        $('.ezd_widget_content_wrap').hide();
                        $('.ezd_shortcode_content_wrap_edit').show();
                    }
                });

                $("input[type=radio]#string_data").click(function () {
                    if ($(this).prop("checked")) {
                        $('.ezd_shortcode_content_wrap').show();
                        $('.ezd_widget_content_wrap').hide();
                        $('.ezd_shortcode_content_wrap_edit').show();
                    }
                });

            })
        }
        edit_one_page_doc_doc();

    });
})(jQuery);