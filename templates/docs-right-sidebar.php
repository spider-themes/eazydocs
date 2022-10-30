<?php
$opt                    = get_option( 'eazydocs_settings' );
$widget_sidebar         = $opt['is_widget_sidebar'] ?? '';
$toc_switcher           = $opt['toc_switcher'] ?? '';
$toc_heading            = $opt['toc_heading'] ??  __( 'CONTENTS', 'eazydocs' );
?>
<div class="col-xl-2 col-lg-3 doc_right_mobile_menu sticky-lg-top">
    <div class="doc_rightsidebar scroll">
        <div class="open_icon" id="right">
            <i class="arrow_carrot-left"></i>
            <i class="arrow_carrot-right"></i>
        </div>
        
        <div class="pageSideSection">
            <?php
            /**
             * Contribution Buttons
             */
            eazydocs_get_template_part('tools/edit-add-doc');

            /**
             * Share Buttons
             */
            eazydocs_get_template_part('tools/share-btns');

            /**
             * Conditional Dropdown
             */
            eazydocs_get_template_part('tools/conditional-dropdown');

            /**
             * Font Size Switcher & Print Icon
             */
            eazydocs_get_template_part('tools/font-switcher');

            /**
             * Dark Mode switcher
             */
            eazydocs_get_template_part('tools/dark-mode-switcher');

            if( ! empty ( $toc_switcher ) ) :
                ?>
                <div class="table-of-content">
                    <h6><i class="icon_ul"></i> <?php echo esc_html( $toc_heading ); ?></h6>
                    <nav class="list-unstyled doc_menu toc_right" data-toggle="toc" id="eazydocs-toc"></nav>
                </div>
                <?php
            endif;
            ?>
            <div class="ezd-widgets">
                <?php
                // Widgets area
                $parent_doc_id       = get_root_parent_id( get_queried_object_id() );
                $content_type        = get_post_meta( $parent_doc_id, 'ezd_doc_right_sidebar_type', true );
                $ezd_shortcode       = get_post_meta( $parent_doc_id, 'ezd_doc_right_sidebar', true );

                if( ! empty ( $ezd_shortcode ) ){                     
                    if (  $content_type  == 'string_data_right' ) {
                        echo html_entity_decode( $ezd_shortcode ) ?? '';
                    } elseif ( $content_type == 'shortcode_right' ) {
                        echo do_shortcode( html_entity_decode( $ezd_shortcode ) );                
                    } else {
                        $wp_blocks = new WP_Query([
                            'post_type' 	=> 'wp_block',
                            'p'				=> $ezd_shortcode
                        ]);
                        while( $wp_blocks->have_posts() ) : $wp_blocks->the_post();
                        the_content();
                        endwhile;
                        wp_reset_postdata();                  
                    }
                } else {
                    if ( is_active_sidebar('doc_sidebar') && $widget_sidebar == 1 ) {
                        dynamic_sidebar('doc_sidebar');
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>