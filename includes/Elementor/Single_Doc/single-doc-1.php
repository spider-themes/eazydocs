<div class="ezd-grid ezd-column-<?php echo esc_attr( $ppp_column ); ?>">
    <?php
        foreach ( $sections as $section ) :
            $doc_items = get_children( array(
                'post_parent'    => $section->ID,
                'post_type'      => 'docs',
                'post_status'    => 'publish',
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
                'posts_per_page' => !empty($settings['ppp_doc_items']) ? $settings['ppp_doc_items'] : -1,
            ));
            $doc_counter    = get_pages( [
                'child_of'  => $section->ID,
                'post_type' => 'docs',
            ]);
            ?>
    <div class="categories_guide_item box-item wow fadeInUp single-doc-layout-one">
        <div class="doc-top ezd-d-flex ezd-align-items-start">
            <?php echo wp_get_attachment_image( get_post_thumbnail_id( $section->ID ) ); ?>
            <a class="doc_tag_title" href="<?php echo get_the_permalink($section->ID); ?>">
                <h4 class="title"> <?php echo get_the_title($section->ID); ?> </h4>
                <span class="badge">
                    <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                    <?php esc_html_e( 'Topics', 'eazydocs' ) ?>
                </span>
            </a>
        </div>
        <ul class="list-unstyled tag_list">
            <?php
                        foreach ( $doc_items as $doc_item ) : ?>
            <li>
                <a class="ct-content-text" href="<?php echo get_permalink($doc_item->ID) ?>">
                    <?php echo wp_kses_post($doc_item->post_title) ?>
                </a>
            </li>
            <?php
                        endforeach;
                        ?>
        </ul>
        <?php
                    if ( !empty($settings['read_more']) ) : ?>
        <a href="<?php echo get_permalink($section->ID); ?>" class="doc_border_btn">
            <?php echo wp_kses_post($settings['read_more']) ?>
            <i class="<?php ezd_arrow() ?>"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php
        endforeach;
        ?>
</div>

<?php
	if ( $settings['section_btn'] == 'yes' && !empty( $settings['section_btn_txt'] ) ) : ?>
<div class="text-center">
    <a href="<?php echo esc_url($settings['section_btn_url']); ?>" class="action_btn all_doc_btn wow fadeinUp">
        <?php echo esc_html($settings['section_btn_txt']) ?><i class="<?php ezd_arrow() ?>"></i>
    </a>
</div>
<?php endif; ?>