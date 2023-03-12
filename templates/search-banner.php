<?php
$cz_options     = get_option( 'eazydocs_settings' );

$custom_banner  = $cz_options['doc_banner_bg'] ?? '';
$cs_banner_wrap = empty( $custom_banner['background-color'] ) && empty( $custom_banner['background-image']['url'] ) ? 'no_cs_bg' : 'has_cs_bg';

$search_banner = $cz_options['is_search_banner'] ?? '1';

if ( $search_banner == '1' ) :
	?>
    <div class="focus_overlay"></div>
    <section class="ezd_search_banner has_bg_dark <?php echo esc_attr( $cs_banner_wrap ); ?>">
		<div class="container">
			<div class="row doc_banner_content">
				<div class="col-md-12">
					<form action="<?php echo esc_url( home_url('/') ); ?>" role="search" method="post" class="ezd_search_form">
						<div class="header_search_form_info">
							<div class="form-group">
								<div class="input-wrapper">
									<input type='search' id="ezd_searchInput" name="s" placeholder='<?php esc_attr_e( 'Search here', 'eazydocs' ); ?>' autocomplete="off" value="<?php echo get_search_query(); ?>"/>
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
                        <?php
                        if ( ezd_is_premium() ) {
                            eazydocs_get_template_part('keywords');
                        }
                        ?>
					</form>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<script>
    jQuery("#ezd_searchInput").focus(function() {
        jQuery('body').addClass('ezd-search-focused');
        jQuery('form.ezd_search_form').css('z-index','999');
    })

    jQuery(".focus_overlay").click(function() {
        jQuery('body').removeClass('ezd-search-focused');
        jQuery('form.ezd_search_form').css('z-index','unset');
    })

	/**
	 * Search Form Keywords
	 */
	jQuery(".ezd_search_keywords ul li a").on("click", function (e) {
		e.preventDefault()
		var content = jQuery(this).text()
		jQuery("#ezd_searchInput").val(content).focus()
		ezSearchResults()
	})

	function ezSearchResults(){
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

	function ezdFetchDelay(callback, ms) {
		var timer = 0;
		return function () {
			var context = this,
			args = arguments;
			clearTimeout(timer);
			timer = setTimeout(function () {
			callback.apply(context, args);
			}, ms || 0);
		};
	}

    jQuery('#ezd_searchInput').keyup(
        ezdFetchDelay(function (e) {
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
	}, 500 )
);
 
</script>