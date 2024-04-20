
;(function ($) {
	'use strict';

	$(document).ready(function () {	

		// Block theme container
		let doc_layout = eazydocs_local_object.ezd_layout_container;
		$('.alignwide.is-layout-flex, .container').addClass(doc_layout);

		// Block theme humburger menu toggler
		$('.wp-block-navigation__responsive-container-open').on('click', function(){
			$('.wp-block-navigation__responsive-container').addClass('has-modal-open is-menu-open');
			
			$('.wp-block-navigation__responsive-container-close').on('click', function(){
				$('.wp-block-navigation__responsive-container').removeClass('has-modal-open is-menu-open');
			});
		});

	});
})(jQuery);