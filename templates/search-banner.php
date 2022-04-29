<?php
$cz_options    = get_option( 'eazydocs_settings' );;
$search_banner = $cz_options['is_search_banner'] ?? '0';

if( $search_banner == '1' ) :
    ?>
    <section class="ezd_search_banner has_bg_dark">
        <div class="container">
            <div class="doc_banner_content">
                <form action="<?php echo esc_url(home_url('/')) ?>" role="search" method="get" class="ezd_search_form">
                    <div class="header_search_form_info">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <label for="ezd_searchInput">
                                    <i class="icon_search"></i>
                                </label>
                                <input type='search' id="ezd_searchInput" name="s" oninput="ezSearchResults()" placeholder='<?php esc_attr_e('Search here', 'eazydocs') ?>' autocomplete="off" value="<?php echo get_search_query() ?>"/>
                                <div class="spinner-border spinner" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <?php if ( defined('ICL_LANGUAGE_CODE') ) : ?>
                                    <input type="hidden" name="lang" value="<?php echo(ICL_LANGUAGE_CODE); ?>"/>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div id="ezd-search-results" data-noresult="<?php esc_attr_e('No Results Found', 'eazydocs'); ?>"></div>
                    <?php
                    include('keywords.php');
                    ?>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
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
                    })
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
