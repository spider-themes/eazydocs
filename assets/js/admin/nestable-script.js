(function ($) {
	'use strict';

	$(document).ready(function () {
		var eaz_show_parent_child = function () {
			$('.dd-item.dd3-has-children').on('click', function (e) {
				var $this = $(this);
				$('.dd-item').removeClass('show-child');
				$this.toggleClass('show-child');
			});
			$('.dd3-has-children .expand--child a').click(function (e) {
				e.stopPropagation();
			});
		};

		eaz_show_parent_child();
		var eaz_create_cookie = function (name, value, days) {
			var expires = '';
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
				expires = '; expires=' + date.toUTCString();
			}
			document.cookie = name + '=' + value + expires + '; path=/';
		};
		var eaz_read_cookie = function (name) {
			var nameEQ = name + '=';
			var ca = document.cookie.split(';');
			for (var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) == 0)
					return c.substring(nameEQ.length, c.length);
			}
			return null;
		};
		var eaz_child_cookie_set = function (e) {
			let have_children = $('.dd3-have-children');
			if (have_children.length > 0) {
				$('.dd3-have-children').click(function (e) {
					let item_id = $(this).attr('data-id');
					let is_active_child = $(this).hasClass('show-child');
					if (is_active_child) {
						eaz_create_cookie(
							'eazydocs_current_child',
							'child-' + item_id,
							999
						);
					} else {
						eaz_create_cookie('eazydocs_current_child', '', 999);
					}

					return true;
				});
			}
		};
		eaz_child_cookie_set();
		var eaz_child_of_child_cookie_set = function (e) {
			let have_sub_children = $('.dd3-have-sub-children');
			if (have_sub_children.length > 0) {
				let doc_last_current_child = eaz_read_cookie(
					'eazydocs_current_child'
				);
				if (doc_last_current_child) {
					$(
						'.' + doc_last_current_child + ' .dd3-have-sub-children'
					).click(function (e) {
						let item_id = $(this).attr('data-id');
						let is_active_child = $(this).hasClass('show-child');
						if (is_active_child) {
							eaz_create_cookie(
								'eazydocs_current_child_of',
								'child-of-' + item_id,
								999
							);
						} else {
							eaz_create_cookie(
								'eazydocs_current_child_of',
								' ',
								999
							);
						}

						return true;
					});
				}
			}
		};
		eaz_child_of_child_cookie_set();
		var eaz_nestable_docs = function (e) {
			var list = e.length ? e : $(e.target),
				output = list.data('output');
			var dataString = {
				action: 'eaz_nestable_docs',
				data: window.JSON.stringify(list.nestable('serialize')),
                security: eazydocs_local_object.nonce
			};
			$.ajax({
				url: eazydocs_local_object.ajaxurl,
				type: 'POST',
				data: dataString,
				async: true,
				cache: false,
				dataType: 'json',
				success: function (res) {
					console.log(window.JSON.stringify(list.nestable('serialize')));
				},
				error: function (err) {
					console.log(err);
				},
			});
		};
		var eaz_nestable_callback = function () {
			var section_tab = $('.easydocs-navitem');
			if (section_tab.length > 0) {
				$('.easydocs-navitem').each(function (e) {
					var $this = $(this),
						$secId = $this.data('id');
					$('#nestable-' + $secId)
						.nestable({
							maxDepth: 4,
							expandBtnHTML:
								'<button class="nestable--button nestable--expand" data-action="expand" type="button">Expand</button>',
							collapseBtnHTML:
								'<button class="nestable--button nestable--collapse" data-action="collapse" type="button">Collapse</button>',
							collapsedClass: 'dd-collapsed eazdocs-collapsed',
						})
						.on('change', eaz_nestable_docs)
						.nestable('collapseAll');
				});
			}
		};
		eaz_nestable_callback();
		var eaz_parent_nestable_docs = function (e) {
			var list = e.length ? e : $(e.target),
				output = list.data('output');
			var dataString = {
				action: 'eaz_parent_nestable_docs',
				data: window.JSON.stringify(list.nestable('serialize')),
			};
			console.log(dataString);
			$.ajax({
				url: eazydocs_local_object.ajaxurl,
				type: 'POST',
				data: dataString,
				async: true,
				cache: false,
				dataType: 'json',
				success: function (res) {
					console.log(res);
				},
				error: function (err) {
					console.log(err);
				},
			});
		};
		var eaz_parent_nestable_callback = function () {
			var parent_section_tab = $('.parent-nestable');
			if (parent_section_tab.length > 0) {
				$('.parent-nestable')
					.nestable({
						maxDepth: 1,
					})
					.on('change', eaz_parent_nestable_docs);
			}
		};
		eaz_parent_nestable_callback();
		var eaz_get_cookies = function () {
			let doc_last_current_child = eaz_read_cookie(
				'eazydocs_current_child'
			);
			if (doc_last_current_child) {
				$('.' + doc_last_current_child).each(function (e) {
					var $this = $(this);
					$('.' + doc_last_current_child).addClass('showing-expand');
					$(
						'.' + doc_last_current_child + '>.nestable--collapse'
					).attr('style', 'display: block');
					$('.' + doc_last_current_child + '>.nestable--expand').attr(
						'style',
						'display: none'
					);
					$('.' + doc_last_current_child + '>.dd-list').addClass(
						'showing'
					);
					$('.' + doc_last_current_child + '>.dd-list').attr(
						'style',
						'display: block'
					);
				});
			}
		};
		eaz_get_cookies();
		var eaz_get_child_of_cookies = function () {
			let doc_last_current_child = eaz_read_cookie(
				'eazydocs_current_child'
			);
			if (doc_last_current_child) {
				let doc_last_current_child_of = eaz_read_cookie(
					'eazydocs_current_child_of'
				);
				if (doc_last_current_child_of) {
					$('.' + doc_last_current_child_of).each(function (e) {
						var $this = $(this);
						$('.' + doc_last_current_child_of).addClass(
							'showing-expand'
						);
						$(
							'.' +
								doc_last_current_child_of +
								'>.nestable--collapse'
						).attr('style', 'display: block');
						$(
							'.' +
								doc_last_current_child_of +
								'>.nestable--expand'
						).attr('style', 'display: none');
						$(
							'.' + doc_last_current_child_of + '>.dd-list'
						).addClass('showing');
						$('.' + doc_last_current_child_of + '>.dd-list').attr(
							'style',
							'display: block'
						);
					});
				}
			}
		};
		eaz_get_child_of_cookies();
	});
})(jQuery);
