<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

wp_enqueue_style( 'eazydocs-frontend-global', EAZYDOCS_ASSETS . '/css/frontend-global.css' );
 // Localize the script with new data.
$ajax_url              = admin_url( 'admin-ajax.php' );
$wpml_current_language = apply_filters( 'wpml_current_language', null );
if ( ! empty( $wpml_current_language ) ) {
	$ajax_url = add_query_arg( 'wpml_lang', $wpml_current_language, $ajax_url );
}

wp_localize_script(
	'jquery',
	'eazydocs_local_object',
	[
		'ajaxurl'            => $ajax_url,
		'EAZYDOCS_FRONT_CSS' => EAZYDOCS_FRONT_CSS,
		'nonce'              => wp_create_nonce( 'eazydocs-ajax' ),
		'is_doc_ajax'        => true,
	]
);

wp_enqueue_style( 'eazydocs-frontend', EAZYDOCS_ASSETS . '/css/frontend.css', [ 'bootstrap' ] );
wp_enqueue_script( 'eazydocs-global', EAZYDOCS_ASSETS . '/js/frontend/global.js', [ 'jquery' ] );



Block::make( __( 'Eazydocs Search', 'eazydocs' ) )
	->add_fields(
		[
			Field::make( 'text', 'placeholder', __( 'Placeholder', 'eazydocs' ) ),
			Field::make( 'checkbox', 'is_keywords', __( 'Keywords', 'eazydocs' ) )
					->set_option_value( 'yes' )
					->set_default_value( true ),
			Field::make( 'complex', 'keywords' )
			->add_fields(
				'keyword',
				[
					Field::make( 'text', 'keyword', __( 'Keyword', 'eazydocs' ) ),
				]
			),
			Field::make( 'text', 'keywords_label', __( 'Keywords Label', 'eazydocs' ) ),
		]
	)
	->set_description( __( 'EazyDocs AJAX Search', 'eazydocs' ) )
	->set_category( 'eazydocs', __( 'EazyDocs', 'eazydocs' ), 'document' )
	->set_icon( 'search' )
	->set_keywords( [ __( 'search', 'eazydocs' ), __( 'doc search', 'eazydocs' ), __( 'ajax', 'eazydocs' ) ] )
	->set_render_callback(
		function ( $fields, $attributes, $inner_blocks ) {
			$custom_class = $attributes['className'] ?? '';
			?>
				<div class="container">
					<div class="row doc_banner_content">
						<div class="col-md-12">
							<form action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" method="post" class="ezd_search_form">
								<div class="header_search_form_info">
									<div class="form-group">
										<div class="input-wrapper">
											<input type='search' id="ezd_searchInput" name="s" oninput="ezSearchResults()" placeholder='<?php echo esc_attr( $fields['placeholder'] ); ?>' autocomplete="off" value="<?php echo get_search_query(); ?>"/>
											<label for="ezd_searchInput">
												<i class="icon_search"></i>
											</label>
											<div class="spinner-border spinner" role="status">
												<span class="visually-hidden">Loading...</span>
											</div>
											<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
												<input type="hidden" name="lang" value="<?php echo( ICL_LANGUAGE_CODE ); ?>"/>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<div id="ezd-search-results" class="eazydocs-search-tree" data-noresult="<?php esc_attr_e( 'No Results Found', 'eazydocs' ); ?>"></div>
							</form>
						</div>
					</div>
				</div>
			<?php
		}
	);
?>
	<script>
;(function ($) {
    "use strict";

    $(document).ready(function() {
	$("#ezd_searchInput").focus(function() {
		$('body').addClass('ezd-search-focused');
		$('form.ezd_search_form').css('z-index','999');
	})

	$("#ezd_searchInput").focusout(function() {
		$('body').removeClass('ezd-search-focused');
		$('form.ezd_search_form').css('z-index','unset');
	})

	/**
	 * Search Keywords
	 */
	$(".header_search_keyword ul li a").on("click", function (e) {
		e.preventDefault()
		var content = $(this).text()
		$("#searchInput").val(content).focus()
		fetchResults()
	});

	/**
	 * Search Form Keywords
	 */
	jQuery(".ezd_search_keywords ul li a").on("click", function (e) {
		e.preventDefault()
		var content = jQuery(this).text()
		jQuery("#ezd_searchInput").val(content).focus()
		ezSearchResults()
	})

	function ezSearchResults() {
		let keyword = jQuery('#ezd_searchInput').val();
		let noresult = jQuery('#ezd-search-results').attr('data-noresult');

		if ( keyword == "" ) {
			jQuery('#ezd-search-results').removeClass('ajax-search').html("")
		} else {
			jQuery.ajax({
				url: eazydocs_local_object.ajaxurl,
				type: 'post',
				data: {action: 'eazydocs_search_results', keyword: keyword},
				beforeSend: function () {
					jQuery(".spinner-border").show();
				},
				success: function (data) {
					jQuery(".spinner-border").hide();
					// hide search results by pressing Escape button
					jQuery(document).keyup(function(e) {
						if (e.key === "Escape") { // escape key maps to keycode `27`
							jQuery('#ezd-search-results').removeClass('ajax-search').html("")
						}
					});
					if ( data.length > 0 ) {
						jQuery('#ezd-search-results').addClass('ajax-search').html(data);
					} else {
						var data_error = '<h5 class="error title">' + noresult + '</h5>';
						jQuery('#ezd-search-results').html(data_error);
					}
				}
			})
		}
	}
</script>
