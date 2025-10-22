<?php
$comment_visibility      = ezd_get_opt( 'enable-comment', '1' );
$reading_time_visibility = ezd_get_opt( 'enable-reading-time', '1' );
$views_visibility        = ezd_get_opt( 'enable-views', '1' );
$sidebar_toggle          = ezd_get_opt( 'toggle_visibility', '1' );
$layout                  = ezd_get_opt( 'docs_single_layout', 'both_sidebar' );
$is_doc_title			 = ezd_get_opt( 'is_doc_title', true );
$is_doc_contribution	 = ezd_get_opt( 'is_doc_contribution', false );
$is_selected_comment 	 = ezd_get_opt( 'enable-selected-comment', false );
$selected_comment_active = $is_selected_comment == true ? 'selected-comment-active' : '';
$current_parent_id  	 = wp_get_post_parent_id( get_the_ID() );

$is_meta_visible 		 = false;
if ( $reading_time_visibility == '1' || $views_visibility == '1' || $is_doc_contribution || $is_selected_comment ) {
	$is_meta_visible = true;
}

$is_parent_doc = false;
if ( ezd_get_opt('is_parent_doc', 1) && $current_parent_id ) {
	$is_parent_doc = true;
}

if ( $sidebar_toggle == 1 ) :
	if ( ! empty( $layout == 'left_sidebar' ) || ! empty( $layout == 'both_sidebar' ) ) : ?>
        <div class="left-sidebar-toggle">
            <span class="left-arrow arrow_triangle-left" title="<?php esc_attr_e( 'Hide category', 'eazydocs' ); ?>" style="display: block;"></span>
            <span class="right-arrow arrow_triangle-right" title="<?php esc_attr_e( 'Show category', 'eazydocs' ); ?>" style="display: none;"></span>
        </div>
	<?php
	endif;
endif;
?>

<article class="shortcode_info" itemscope itemtype="http://schema.org/Article">
	<div class="doc-post-content <?php echo esc_attr( $selected_comment_active ); ?>" id="post">

		<?php 
		if ( $is_parent_doc || $is_meta_visible || $is_doc_title ) :
			?>
			<div class="shortcode_title">
				<?php
				if ( $is_parent_doc ) : ?>
					<a class="ezd-doc-badge" href="<?php the_permalink($current_parent_id) ?>">
						<?php echo esc_html(get_the_title($current_parent_id)) ?>
					</a>
					<?php
				endif;

				if ( $is_doc_title ) {
					the_title( '<h1>', '</h1>' );
				}

				if ( $is_meta_visible ) : ?>
					<div class="ezd-meta dot-sep">
						<?php
						if ( $reading_time_visibility == '1' ) : ?>
							<span class="read-time">
								<?php esc_html_e( 'Estimated reading: ', 'eazydocs' );
								ezd_reading_time(); ?>
							</span>
							<?php
						endif;

						if ( $views_visibility == '1' ) : ?>
							<span class="views sep">
								<?php echo esc_html(eazydocs_get_post_view()); ?>
							</span>
							<?php
						endif;

						if( ! empty( $is_doc_contribution ) ) {
							do_action( 'eazydocs_docs_contributor', get_the_ID() );
						}

						if ( $is_selected_comment ) {
							do_action( 'ezd_selected_comment_switcher_meta' );
						}
						?>
					</div>
					<?php
				endif;
				?>
			</div>
			<?php 
		endif;
		?>

		<div class="doc-scrollable editor-content">
			<?php
			if ( has_post_thumbnail() && ezd_get_opt( 'is_featured_image' ) == '1' ) {
				the_post_thumbnail( 'full', array( 'class' => 'mb-3' ) );
			}
			?>

			<div class="doc-content-wrap">
				<?php
				if ( ezd_get_opt( 'is_excerpt' ) == '1' && has_excerpt() ) {
					?>
					<p class="doc-excerpt ezd-alert ezd-alert-info">
						<strong><?php echo esc_html(ezd_get_opt( 'excerpt_label', 'Summary' ));; ?></strong>
						<?php echo wp_kses_post( get_the_excerpt() ); ?>
					</p>
					<?php
				}
				the_content();
				?>	
			</div>

            <div class="ezd-doc-attached-files-accordion">
                <?php
                $attached_files = get_post_meta( get_the_ID(), 'ezd_doc_attached_files', true );
                $allowed_types = ['pdf', 'zip', 'docx', 'txt'];
                $item_count = 0;
                if ( !empty($attached_files) && is_array($attached_files) ) {
                    foreach ( $attached_files as $file ) {
                        $file_url = $file['ezd_upload_doc_attachment'];
                        $file_ext = strtolower(pathinfo(basename($file_url), PATHINFO_EXTENSION));
                        if ( in_array($file_ext, $allowed_types) ) {
                            $item_count++;
                        }
                    }
                }
                ?>
                <div class="accordion__header" role="button" aria-expanded="true" aria-controls="accordion-content" tabindex="0">
                    <div class="accordion__title">
                        <?php echo esc_html__('Attached Files', 'eazydocs'); ?>
                        <span class="accordion__count">
                            (<?php echo esc_html($item_count); ?>)
                        </span>
                    </div>
                    <button class="accordion__toggle" aria-label="<?php echo esc_attr__('Toggle accordion', 'eazydocs'); ?>">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="accordion__content" id="accordion-content">
                    <ul class="file-list">
                        <?php
                        if ( !empty($attached_files) && is_array($attached_files) ) :
                            foreach ( $attached_files as $file ) :
                                $file_url = $file['ezd_upload_doc_attachment'];
                                $file_name = basename($file_url);
                                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                                if ( !in_array($file_ext, $allowed_types) ) continue;
                                // Try to get file size (if local)
                                $file_path = ABSPATH . str_replace(site_url() . '/', '', $file_url);
                                $file_size = '';
                                if (file_exists($file_path)) {
                                    $size_bytes = filesize($file_path);
                                    if ($size_bytes >= 1048576) {
                                        $file_size = round($size_bytes / 1048576, 2) . ' MB';
                                    } elseif ($size_bytes >= 1024) {
                                        $file_size = round($size_bytes / 1024, 2) . ' KB';
                                    } else {
                                        $file_size = $size_bytes . ' bytes';
                                    }
                                }
                                // Get upload date from URL (year/month) or fallback to current date
                                preg_match('/uploads\/(\d{4})\/(\d{2})\//', $file_url, $matches);
                                $upload_date = '';
                                if ( !empty($matches) ) {
                                    $year = $matches[1];
                                    $month = $matches[2];
                                    $date_str = $year . '-' . $month . '-01';
                                    $upload_date = 'Uploaded ' . date( 'M d, Y', strtotime( $date_str ) );
                                } else {
                                    $upload_date = 'Uploaded ' . date( 'M d, Y', strtotime( 'now' ) );
                                }
                                // Icon and color logic
                                $icon_class = 'far fa-file';
                                $color_class = '';
                                $badge = '';
                                if ($file_ext === 'pdf') {
                                    $icon_class = 'far fa-file-pdf';
                                    $color_class = 'file-list__icon--pdf';
                                } elseif ($file_ext === 'docx') {
                                    $icon_class = 'far fa-file-word';
                                    $color_class = 'file-list__icon--docx';
                                } elseif ($file_ext === 'txt') {
                                    $icon_class = 'far fa-file-alt';
                                    $color_class = 'file-list__icon--txt';
                                } elseif ($file_ext === 'zip') {
                                    $icon_class = 'fa fa-file-archive'; // fallback to fa-file if not available
                                    $color_class = 'file-list__icon--zip';
                                }
                                ?>
                                <li class="file-list__item">
                                    <div class="file-list__icon <?php echo esc_attr($color_class); ?> file-list__icon--<?php echo esc_attr($file_ext); ?>">
                                        <i class="<?php echo esc_attr($icon_class); ?>"></i>
                                    </div>
                                    <div class="file-list__info">
                                        <h4 class="file-list__name"><?php echo esc_html($file_name); ?></h4>
                                        <div class="file-list__meta">
                                            <span class="file-list__size"><?php echo esc_html($file_size); ?></span>
                                            <span class="file-list__separator">&bull;</span>
                                            <span class="file-list__date"><?php echo esc_html($upload_date); ?></span>
                                        </div>
                                    </div>
                                    <div class="file-list__actions">
                                        <a class="file-list__action file-list__action--download" href="<?php echo esc_url($file_url); ?>" download aria-label="Download file">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </li>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
            </div>

			<?php
			// Footnote
			do_action( 'eazydocs_footnote', get_the_ID() );

			eazydocs_get_template_part( 'single-doc-home' );

			global $post;
			$children = ezd_list_pages( "title_li=&order=menu_order&child_of=" . absint($post->ID) . "&echo=0&post_type=" . esc_attr($post->post_type) );

			if ( ezd_get_opt('is_articles', 1 ) && $children && $post->post_parent != 0 ) {
				echo '<div class="details_cont ent recently_added" id="content_elements">';
				echo '<h4 class="c_head">' . esc_html( ezd_get_opt('articles_title', esc_html__( 'Articles', 'eazydocs' )) ) . '</h4>';
				echo '<ul class="article_list">';
				echo wp_kses_post(ezd_list_pages( "title_li=&order=menu_order&child_of=" . absint($post->ID) . "&echo=0&post_type=" . esc_attr($post->post_type) ));
				echo '</ul>';
				echo '</div>';
			}
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Docs:', 'eazydocs' ),
				'after'  => '</div>',
			) );
			?>
		</div>

		<?php do_action( 'ezd_selected_comment_lists', get_the_ID() ); ?>

	</div>
	<?php eazydocs_get_template_part( 'content-feedback' ); ?>
</article>

<?php
eazydocs_get_template_part( 'content-related' );

if ( $comment_visibility == '1' )  :
	if ( comments_open() || get_comments_number() )  :
		?>
        <div class="eazydocs-comments-wrap">
			<?php comments_template(); ?>
        </div>
	<?php
	endif;
endif;

