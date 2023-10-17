<div class=" docs4">
    <div id="bookchapter" class="doc4-nav-bar">
        <div class="container">
            <ul id="bcNav" class="book-chapter-nav list-unstyled">
                <?php
			$slug_type = $settings['docs_slug_format'] ?? '';
			$widget_id = $this->get_id();
			$part_no                 = 1;
			if ( $parent_docs ) :
				$sn_n = 1;
				foreach ( $parent_docs as $i => $doc ) :
					$active = ( $i == 0 ) ? ' active' : '';
					$post_title_slug = $doc->post_name;
					$doc_name        = explode( ' ', $doc->post_title );

					if( $slug_type == 1 ) {
						$atts = "href='#doc-4{$post_title_slug}'";
					}else{
						$atts = "href='#doc-4{$widget_id}-{$doc->ID}'";
					}

					?>
                <li class="nav-item<?php echo esc_attr( $active ) ?>">
                    <a <?php echo $atts; ?> class="nav-link">
                        <?php
							if ( ! empty( $settings['book_chapter_prefix'] ) ):
								?>
                        <span class="chapter-part">
                            <?php echo esc_html( $settings['book_chapter_prefix'] . " " . $part_no ++ ); ?></span>
                        <?php
							endif;
							echo wp_kses_post( $doc->post_title );
							?>
                    </a>
                </li>
                <?php
				endforeach;
			endif;
			?>
            </ul>
        </div>
    </div>

    <div class="copic-contentn ezd-container">
        <?php
        $sc_n = 1;
        if ( ! empty( $docs ) ) :
            foreach ( $docs as $i => $main_doc ) :
                // Active Doc
                if ( $slug_type == 1 ) {
                    $doc_id       = $main_doc['doc']->post_name;
                } else {
                    $doc_id       = "{$widget_id}-{$main_doc['doc']->ID}";
                }
                ?>
        <div id="doc-4<?php echo $doc_id ?>" class="doc_section_wrap ">
            <div class="ezd-grid ezd-grid-cols-12">
                <div class="ezd-lg-col-12 ezd-md-col-12 ezd-grid-column-full">
                    <div class="docs4-heading">
                        <h3>
                            <?php echo wp_kses_post( $main_doc['doc']->post_title ); ?>
                        </h3>
                        <?php
                                if ( strlen( trim( $main_doc['doc']->post_excerpt ) ) != 0 ) {
                                    echo wpautop( wp_trim_words( $main_doc['doc']->post_excerpt, $settings['main_doc_excerpt'], '' ) );
                                } else {
                                    echo wpautop( wp_trim_words( $main_doc['doc']->post_content, $settings['main_doc_excerpt'], '' ) );
                                }
                                ?>
                    </div>
                </div>
            </div>
            <div class="ezd-grid ezd-grid-cols-12">
                <?php
                        $sections = 1;
                        if ( ! empty( $main_doc['sections'] ) ) :
                            foreach ( $main_doc['sections'] as $section ) :
                                $section_count = $sections ++;
                                ?>
                <div class="ezd-lg-col-4 ezd-md-col-6 ezd-grid-column-full">
                    <div class="topic_list_item">
                        <?php if ( ! empty( $section->post_title ) ) : ?>
                        <a class="doc4-section-title" href="<?php echo get_permalink( $section->ID ); ?>">
                            <h4>
                                <?php echo wp_kses_post( $section->post_title ); ?>
                            </h4>
                        </a>
                        <?php endif; ?>
                        <ul class="navbar-nav">
                            <?php
                                            $doc_items = get_children( array(
                                                'post_parent'    => $section->ID,
                                                'post_type'      => 'docs',
                                                'post_status'    => 'publish',
                                                'orderby'        => 'menu_order',
                                                'order'          => 'ASC',
                                                'posts_per_page' => ! empty( $settings['ppp_doc_items'] ) ? $settings['ppp_doc_items'] : - 1,
                                            ) );
                                            $child     = 1;
                                            foreach ( $doc_items as $doc_item ) :
                                                $child_count = $child ++
                                                ?>
                            <li>
                                <a href="<?php echo get_permalink( $doc_item->ID ) ?>">
                                    <span class="chapter_counter">
                                        <?php echo esc_html( $section_count . "." . $child_count . " " ); ?>
                                    </span>
                                    <?php echo wp_kses_post( $doc_item->post_title ) ?>
                                </a>
                            </li>
                            <?php
                                            endforeach;
                                            ?>
                        </ul>
                    </div>
                </div>
                <?php
                            endforeach;
                        endif;
                        ?>
            </div>
        </div>
        <?php
            endforeach;
        endif;
        ?>
    </div>
</div>

<script>
;
(function($) {
    "use strict";

    $(document).ready(function() {

        function navFixed() {

            var windowWidth = $(window).width();
            if ($(".doc4-nav-bar").length) {
                if (windowWidth > 330) {
                    var tops = $(".doc4-nav-bar");
                    var tabs = $(".doc4-nav-bar").height();
                    var leftOffset = tops.offset().top + tabs;

                    $(window).on("scroll", function() {
                        var scroll = $(window).scrollTop();
                        if (scroll >= leftOffset) {
                            tops.addClass("dock4-nav-sticky");
                        } else {
                            tops.removeClass("dock4-nav-sticky")
                        }
                    })
                }
            }
        }

        navFixed();
    });
})(jQuery);

;
(function($, window, document) {

    $('[data-bs-toggle]').on('click', function(event) {
        event.preventDefault();
        var target = $(this.hash);
        target.toggle();
    });

    // Cache selectors
    // var lastId,
    //     topMenu = $("#bcNav"),
    //     topMenuHeight = topMenu.outerHeight() + 15,
    //     // All list items
    //     menuItems = topMenu.find("a"),
    //     // Anchors corresponding to menu items
    //     scrollItems = menuItems.map(function() {
    //         var item = $(this).attr("href");
    //         if (item != '#') {
    //             return $(item)
    //         }
    //     });

    // Bind to scroll
    // $(window).scroll(function() {
    //     // Get container scroll position
    //     var fromTop = $(this).scrollTop() + topMenuHeight;

    //     // Get id of current scroll item
    //     var cur = scrollItems.map(function() {
    //         if ($(this).offset().top < fromTop)
    //             // console.log(this)
    //             return this;
    //     });
    //     // Get the id of the current element
    //     cur = cur[cur.length - 1];
    //     var id = cur && cur.length ? cur[0].id : "";

    //     if (lastId !== id) {
    //         lastId = id;
    //         // Set/remove active class
    //         menuItems
    //             .parent().removeClass("active")
    //             .end().filter("[href='#" + id + "']").parent().addClass("active");
    //         let is_active_added = jQuery('.book-chapter-nav li').hasClass('active')
    //         if (is_active_added != true) {
    //             jQuery('.book-chapter-nav li:first-child').addClass('active');
    //         }
    //     }
    // });
})(jQuery, window, document);
</script>