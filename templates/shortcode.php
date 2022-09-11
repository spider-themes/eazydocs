<?php
$opt            = get_option( 'eazydocs_settings' );
$topics_count   = $opt['topics_count'] ?? '1';
$topics         = $opt['topics_text'] ?? esc_html__( 'Topics', 'eazydocs' );

// Child docs per page
$layout         = 'grid';

// Check pro plugin class exists
if ( class_exists( 'EazyDocsPro' ) ) {
	$layout     = $opt['docs-archive-layout'] ?? $layout; // id of field
}

if ( $docs ) :
    ?>

    <div class="eazydocs_shortcode">
        <div class="container">
            <div class="row" <?php do_action( 'eazydocs_masonry_wrap', $layout ); ?>>
                <?php
                $i = 1;
                foreach ( $docs as $main_doc ) :
                    $doc_counter = get_pages( [
                        'child_of'      => $main_doc['doc']->ID,
                        'post_type'     => 'docs',
                        'orderby'       => 'menu_order',
                        'order'         => 'asc',
                        'post_status'   => array( 'publish', 'private' )
                    ]);

                    global $post;

                    $private_bg = $main_doc['doc']->post_status == 'private' ? 'bg-warning' : '';
                    $private_bg_op = $main_doc['doc']->post_status == 'private' ? 'style="--bs-bg-opacity: .2;"' : '';
                    $protected_bg = !empty($main_doc['doc']->post_password) ? 'bg-dark' : '';

                    $col_wrapper = $i == 1;
                    if ( class_exists( 'EazyDocsPro' ) ) {
                        do_action( 'before_docs_column_wrapper', $col );
                    } else { ?>
                        <div class="col-lg-<?php echo esc_attr( $col ); ?>">
                    <?php } ?>

                        <div class="categories_guide_item <?php echo $private_bg.$protected_bg; ?> wow fadeInUp" <?php echo $private_bg_op; ?>>
                            <?php
                            if ( $main_doc['doc']->post_status == 'private' ) {
                                $pd_txt = esc_html__( 'Private Doc', 'eazydocs' );
                                echo "<div class='private' title='$pd_txt'><i class='icon_lock'></i></div>";
                            }
                            if ( !empty($main_doc['doc']->post_password) ) {
                                ?>
                                <div class="private" title="Password Protected Doc">
                                    <svg width="50px" height="50px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#4e5668">
                                        <g>
                                            <path fill="none" d="M0 0h24v24H0z"/>
                                            <path d="M18 8h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h2V7a6 6 0 1 1 12 0v1zm-2 0V7a4 4 0 1 0-8 0v1h8zm-5 6v2h2v-2h-2zm-4 0v2h2v-2H7zm8 0v2h2v-2h-2z"/>
                                        </g>
                                    </svg>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="doc-top d-flex align-items-start">
                                <?php echo get_the_post_thumbnail( $main_doc['doc']->ID, 'full', array( 'class' => 'featured-image' ) ); ?>
                                <a class="doc_tag_title" href="<?php echo get_permalink( $main_doc['doc']->ID ); ?>">
                                    <?php if ( !empty($main_doc['doc']->post_title) ) : ?>
                                        <h4 class="title">
                                            <?php echo $main_doc['doc']->post_title; ?>
                                        </h4>
                                    <?php endif; ?>
                                    <?php if ( $topics_count == '1' ) : ?>
                                        <span class="badge">
                                            <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                                            <?php echo esc_html($topics); ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <?php if ( $main_doc['sections'] ) : ?>
                                <ul class="list-unstyled article_list">
                                    <?php
                                    foreach ( $main_doc['sections'] as $item ) :
                                        ?>
                                        <li>
                                            <a href="<?php echo get_permalink( $item->ID ); ?>">
                                                <?php echo esc_html( $item->post_title ); ?>
                                            </a>
                                        </li>
                                        <?php
                                    endforeach;
                                    ?>
                                </ul>
                            <?php endif; ?>
                            <a href="<?php echo get_permalink( $main_doc['doc']->ID ); ?>" class="doc_border_btn">
                                <?php echo $more; ?>
                                <i class="arrow_right"></i>
                            </a>
                        </div>
                     </div>
                    <?php
                endforeach;
                ?>
            </div>
        </div>
    </div>
    <?php
endif;