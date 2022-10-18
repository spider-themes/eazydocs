<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <!-- Charset Meta -->
    <meta charset="<?php bloginfo('charset' ); ?>">
    <!-- For IE -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- For Responsive Device -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); if(function_exists('docy_has_scrollspy')){docy_has_scrollspy();} ?> >
    <?php
    if ( function_exists('wp_body_open') ) {
        wp_body_open();
    }

    wp_enqueue_script('eazydocs-onepage');

    $opt                = get_option( 'eazydocs_settings' );
    $onepage_number     = $opt['onepage_numbering'] ?? '';
    $is_number = '';
    if( $onepage_number == 1 ){
        $is_number = 'numbering_show';
    }
    global $post;
    $post_slug          = $post->post_name;
    $post_id            = get_page_by_path($post_slug, OBJECT, array( 'docs' ) );

    $children           = wp_list_pages( array(
        'title_li'      => '',
        'order'         => 'menu_order',
        'child_of'      => $post_id->ID,
        'echo'          => false,
        'post_type'     => 'docs',
        'walker'        => new Walker_Onepage_Fullscren(),
        'depth' => 3
    ) );
    ?>
    <section class="documentation_area_sticky doc_documentation_area onepage_doc_area page_wrapper fullscreen-layout" id="sticky_doc">
            <div class="overlay_bg"></div>
            <div class="container-fluid p-lg-5">
                <div class="row doc-container">
                    <div class="col-xl-3 col-lg-3 doc_mobile_menu doc-sidebar sticky-top sticky-lg-top left-column">
                        <aside class=" one-page-docs-sidebar-wrap">
                            <div class="open_icon" id="left">
                                <i class="arrow_carrot-right"></i>
                                <i class="arrow_carrot-left"></i>
                            </div>

                            <?php
                            echo get_the_post_thumbnail($post_id->ID, 'full');
                            ?>
                            <h3 class="doc-title">
                                <?php echo get_post_field( 'post_title', $post_id->ID, 'display' ); ?>
                            </h3>
                            <?php

                            if ( $children ) :
                                ?>
                                <nav class="scroll op-docs-sidebar">
                                    <ul class="<?php echo esc_attr($is_number); ?> list-unstyled nav-sidebar fullscreen-layout-onepage-sidebar doc-nav one-page-doc-nav-wrap" id="eazydocs-toc">
                                        <?php
                                        echo wp_list_pages(array(
                                            'title_li' => '',
                                            'order' => 'menu_order',
                                            'child_of' => $post_id->ID,
                                            'echo' => false,
                                            'post_type' => 'docs',
                                            'walker' => new Walker_Onepage_Fullscren(),
                                            'depth' => 3
                                        ));
                                        ?>
                                    </ul>
                                </nav>
                            <?php
                            endif;

                            $ezd_shortcode = get_the_content( $post_id->ID );
                            $content_type = get_post_meta( get_the_ID(), 'ezd_doc_content_type', true );
                            if( $content_type == 'string_data' ){
                                echo html_entity_decode( $ezd_shortcode ) ?? '';
                            } elseif( $content_type == 'shortcode' ){
                                echo do_shortcode( html_entity_decode($ezd_shortcode) );
                            } else {
                                dynamic_sidebar( html_entity_decode($ezd_shortcode) );
                            }

                            ?>
                        </aside>
                    </div>
                    <div class="col-xl-7 col-lg-6 col-md-9 middle-content">
                        <div class="documentation_info" id="post">
                            <?php
                            $sections = get_children( array(
                                'post_parent'    => $post_id->ID,
                                'post_type'      => 'docs',
                                'post_status'    => 'publish',
                                'orderby'        => 'menu_order',
                                'order'          => 'ASC',
                                'posts_per_page' =>  -1,
                            ));

                            $i = 0;
                            $sec_serial = 0;
                            foreach ( $sections as $doc_item ) {
                                $child_sections = get_children( array(
                                    'post_parent'    => $doc_item->ID,
                                    'post_type'      => 'docs',
                                    'post_status'    => 'publish',
                                    'orderby'        => 'menu_order',
                                    'order'          => 'ASC',
                                    'posts_per_page' => -1,
                                ));
                                $sec_serial++;
                                ?>
                                <article class="documentation_body doc-section onepage-doc-sec" id="<?php echo sanitize_title($doc_item->post_title) ?>" itemscope itemtype="http://schema.org/Article">
                                    <?php if ( !empty($doc_item->post_title) ) : ?>
                                        <div class="shortcode_title doc-sec-title">
                                            <h2> <?php
                                                echo $sec_serial.'. ';
                                                echo esc_html($doc_item->post_title) ?> </h2>
                                        </div>
                                    <?php endif; ?>
                                    <div class="doc-content">
                                        <?php
                                        if ( did_action( 'elementor/loaded' ) ) {
                                            $parent_content = \Elementor\Plugin::instance()->frontend->get_builder_content($doc_item->ID);
                                            echo !empty($parent_content) ? $parent_content : apply_filters('the_content', $doc_item->post_content);
                                        } else {
                                            echo apply_filters('the_content', $doc_item->post_content);
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    $child_serial = 0;
                                    foreach ( $child_sections as $child_section ) :
                                        $child_serial++;
                                        ?>
                                        <div class="child-doc onepage-doc-sec" id="<?php echo sanitize_title($child_section->post_title) ?>">
                                            <div class="shortcode_title depth-one ">
                                                <h2> <?php
                                                    echo $sec_serial.'.'.$child_serial.'. ';
                                                    echo $child_section->post_title ?> </h2>
                                            </div>
                                            <div class="doc-content">
                                                <?php
                                                if ( did_action( 'elementor/loaded' ) ) {
                                                    $child_content = \Elementor\Plugin::instance()->frontend->get_builder_content($child_section->ID);
                                                    echo !empty($child_content) ? $child_content : apply_filters('the_content', $child_section->post_content);
                                                } else {
                                                    echo apply_filters('the_content', $child_section->post_content);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php

                                    $last_depth = get_children( array(
                                        'post_parent'    => $child_section->ID,
                                        'post_type'      => 'docs',
                                        'post_status'    => 'publish',
                                        'orderby'        => 'menu_order',
                                        'order'          => 'ASC',
                                        'posts_per_page' => -1,
                                    ));
                                    $last_depth_serial = 0;
                                    foreach( $last_depth as $last_depth_doc ) :
                                        $last_depth_serial++;
                                        ?>
                                        <div class="child-doc onepage-doc-sec" id="<?php echo sanitize_title($last_depth_doc->post_title) ?>">
                                            <div class="shortcode_title depth-one ">
                                                <h2> <?php
                                                    echo $sec_serial.'.'.$child_serial.'.'.$last_depth_serial.'. ';
                                                    echo $last_depth_doc->post_title ?> </h2>
                                            </div>
                                            <div class="doc-content">
                                                <?php
                                                if ( did_action( 'elementor/loaded' ) ) {
                                                    $child_content = \Elementor\Plugin::instance()->frontend->get_builder_content($last_depth_doc->ID);
                                                    echo !empty($child_content) ? $child_content : apply_filters('the_content', $last_depth_doc->post_content);
                                                } else {
                                                    echo apply_filters('the_content', $last_depth_doc->post_content);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    endforeach;


                                    endforeach;


                                    ?>
                                </article>
                                <?php
                                ++$i;
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-3 doc_right_mobile_menu">
                        <div class="open_icon" id="right">
                            <i class="arrow_carrot-left"></i>
                            <i class="arrow_carrot-right"></i>
                        </div>
                        <div class="doc_rightsidebar scroll one-page-docs-right-sidebar">
                            <div class="pageSideSection">
                                <?php
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
                                ?>

                                <div class="onepage-sidebar doc_sidebar">
                                    <?php
                                    $content_type  = get_post_meta( get_the_ID(), 'ezd_doc_content_type_right', true );
                                    $ezd_shortcode  = get_post_meta( get_the_ID(), 'ezd_doc_content_box_right', true );
                                    if ( $content_type == 'string_data_right' ) {
                                        echo html_entity_decode( $ezd_shortcode ) ?? '';
                                    } elseif ( $content_type == 'shortcode_right' ) {
                                        echo do_shortcode( html_entity_decode( $ezd_shortcode ) );
                                    } else {
                                        dynamic_sidebar( html_entity_decode( $ezd_shortcode ) );
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <?php wp_footer(); ?>
</body>
</html>