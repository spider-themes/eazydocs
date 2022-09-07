<?php
eazydocs_set_post_view();
$options                    = get_option( 'eazydocs_settings' );
$comment_visibility         = $options['enable-comment'] ?? '1';
$reading_time_visibility    = $options['enable-reading-time'] ?? '1';
$views_visibility           = $options['enable-views'] ?? '1';
$docs_feedback              = $options['docs-feedback'] ?? '1';
$sidebar_toggle             = $options['toggle_visibility'] ?? '1';
$layout                     = $options['docs_single_layout'] ?? 'both_sidebar';

$contributor_meta_title         = ! empty( $options['contributor_meta_title'] ) ? $options['contributor_meta_title'] : __( 'Contributors', 'eazydocs' );
$meta_dropdown_title            = ! empty( $options['contributor_meta_dropdown_title'] ) ? $options['contributor_meta_dropdown_title'] : __( 'Manage Contributors', 'eazydocs' );
$contributor_meta_search        = ! empty( $options['contributor_meta_search'] ) ? $options['contributor_meta_search'] : '';
$contributor_meta_visibility    = $options['contributor_meta_visibility'] ?? '';

if( $sidebar_toggle         == 1 ) :
    if ( ! empty( $layout   == 'left_sidebar' ) || ! empty( $layout == 'both_sidebar' ) ) : ?>
        <div class="left-sidebar-toggle">
            <span class="left-arrow arrow_triangle-left" title="<?php esc_attr_e( 'Hide category', 'eazydocs' ); ?>" style="display: block;"></span>
            <span class="right-arrow arrow_triangle-right" title="<?php esc_attr_e( 'Show category', 'eazydocs' ); ?>" style="display: none;"></span>
        </div>
        <?php
    endif;
endif;
?>

<article class="shortcode_info" id="post" itemscope itemtype="http://schema.org/Article">
        <div class="doc-post-content">
            <div class="shortcode_title">
				<?php the_title( '<h1>', '</h1>' ); ?>
                <?php
                if( $reading_time_visibility == '1' ||  $views_visibility == '1' ) : ?>
                    <div class="meta dot-sep">
                        <?php
                        if( $reading_time_visibility == '1') : ?>
                            <span class="read-time">
                                <?php esc_html_e( 'Estimated reading: ', 'eazydocs' );
                                eazydocs_reading_time();
                                ?>
                            </span>
                            <?php
                        endif;
                        if( $views_visibility == '1') : ?>
                            <span class="views sep">
                                <?php echo eazydocs_get_post_view(); ?>
                            </span>
                            <?php
                        endif;   
                        if ( eaz_fs()->is_plan__premium_only('promax') ) :
                            if( ! empty( $contributor_meta_visibility ) ) :
                                ?>
                                <span class="views sep contributed_users">
                                    <span class="ezdoc_contributed_user_avatar"> 
                                    <?php
                                    echo esc_html( $contributor_meta_title );
                                    do_action('ezd_doc_contributor', get_the_ID());
                                    $current_doc_author         = get_the_author_meta( 'ID' );  
                                    $ezd_doc_contributor_list   = get_post_meta(get_the_ID(), 'ezd_doc_contributors', true);
                                    $ezd_doc_contributors       = rtrim($ezd_doc_contributor_list, ',');
                                    $ezd_doc_contributors       = explode(',', $ezd_doc_contributors);
                                    $ezd_doc_contributors       = array_unique($ezd_doc_contributors);
                                    ?>
                                    <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" title="<?php echo $available_user->display_name ?? ''; ?>" data-bs-toggle="tooltip" data-bs-placement="bottom">
                                        <?php echo get_avatar( get_the_author_meta('ID'), '20' ); ?>
                                    </a>
                                    <?php 
                                    foreach ( $ezd_doc_contributors as $ezd_doc_contributor ) {
                                        $available_user         = get_user_by('id', $ezd_doc_contributor);
                                        if ( ! empty($available_user->user_login) && empty($available_user->ID == $current_doc_author) ) {
                                            ?>
                                            <a href="<?php echo get_author_posts_url($ezd_doc_contributor); ?>" title="<?php echo $available_user->display_name ?? ''; ?>" data-bs-toggle="tooltip" data-bs-placement="bottom">
                                                <?php echo get_avatar($available_user, '20'); ?>
                                            </a>
                                            <?php
                                        }
                                    }
                                    
                                    if ( current_user_can('administrator') ) :
                                        ?>                               
                                        <div class="ezdoc_contributed_users">
                                            <i class="arrow_carrot-down"></i>
                                            <div class="doc_users_dropdown">
                                                <h5 class="title"> <?php echo esc_html( $meta_dropdown_title ); ?> </h5>
                                                <?php
                                                if ( $contributor_meta_search == 1 ) :
                                                    ?>
                                                    <form action="#" method="POST"> 
                                                        <input type="text" name="ezd_contributor_search" id="ezd-contributor-search" placeholder="<?php esc_attr_e( 'Search By Email', 'eazydocs' ); ?>">
                                                    </form>
                                                    <?php 
                                                endif;
                                                ?>
                                                
                                                <div class="doc_dropdown_users_list" id="added_contributors">  
                                                    <?php                     
                                                        $available_user = get_user_by('id', $current_doc_author);
                                                        ?>
                                                        <ul class="users_wrap_item <?php echo esc_attr('user-'.$current_doc_author); ?>" id="<?php echo esc_attr('user-'.$current_doc_author); ?>">
                                                            <li>
                                                                <a href='<?php echo get_author_posts_url($current_doc_author); ?>'>
                                                                <?php echo get_avatar($available_user, '35'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href='<?php echo get_author_posts_url($current_doc_author); ?>'>
                                                                    <?php echo $available_user->display_name ?? ''; ?>
                                                                </a>
                                                                <span> <?php echo $available_user->user_email ?? ''; ?> </span>
                                                            </li>
                                                            <li></li>
                                                        </ul>
                                                    <?php
                                                    foreach ( $ezd_doc_contributors as $ezd_doc_contributor ) {                                    
                                                        $available_user = get_user_by('id', $ezd_doc_contributor);
                                                        if( ! empty( $available_user->user_login) && empty($available_user->ID == $current_doc_author ) ){
                                                            ?>
                                                            <ul class="users_wrap_item <?php echo esc_attr('user-'.$ezd_doc_contributor); ?>" id="<?php echo esc_attr('user-'.$ezd_doc_contributor); ?>">
                                                                <li>
                                                                    <a href='<?php echo get_author_posts_url($ezd_doc_contributor); ?>'>
                                                                    <?php echo get_avatar($ezd_doc_contributor, '35'); ?>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href='<?php echo get_author_posts_url($ezd_doc_contributor); ?>'>
                                                                        <?php echo $available_user->display_name ?? ''; ?>
                                                                    </a>
                                                                    <span> <?php echo $available_user->user_email ?? ''; ?> </span>
                                                                </li>
                                                                <li>
                                                                    <a class="circle-btn ezd_contribute_delete" data-contributor-delete="<?php echo esc_attr($ezd_doc_contributor); ?>" data-doc-id="<?php echo esc_attr(get_the_ID()); ?>">
                                                                    &times;
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                            <?php 
                                                            }
                                                        }
                                                    ?>
                                                </div>
                                                <div class="doc_dropdown_users_list" id="to_add_contributors"> 
                                                    <?php                                            
                                                    $current_doc_author     = get_the_author_meta( 'ID' );
                                                    $ezd_exclude_users      = get_post_meta(get_the_ID(), 'ezd_doc_contributors', true);
                                                    $ezd_exclude_users     = rtrim($ezd_exclude_users, ',');
                                                    $ezd_exclude_users     = $current_doc_author.','.$ezd_exclude_users;
                                                    $all_users              = get_users(['exclude'  => $ezd_exclude_users]);

                                                    foreach( $all_users as $add_contributor ){                                                                                                     
                                                        $available_user = get_user_by('id', $add_contributor);
                                                        ?>
                                                        <ul class="users_wrap_item <?php echo esc_attr('to-add-user-'.$add_contributor->ID); ?>" id="<?php echo esc_attr('to-add-user-'.$add_contributor->ID); ?>">
                                                            <li>
                                                                <a href='<?php echo get_author_posts_url($add_contributor->ID); ?>'>
                                                                    <?php echo get_avatar($add_contributor, '35'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href='<?php echo get_author_posts_url($add_contributor->ID); ?>'>
                                                                    <?php echo get_the_author_meta( 'display_name', $add_contributor->ID ); ?>
                                                                </a>
                                                                <span> 
                                                                    <?php echo get_the_author_meta( 'user_email', $add_contributor->ID ); ?>
                                                                </span>
                                                            </li>
                                                            <li>
                                                                <a class="circle-btn ezd_contribute_add" data-contributor-add="<?php echo esc_attr($add_contributor->ID); ?>" data-doc-id="<?php echo esc_attr(get_the_ID()); ?>">
                                                                    &plus; 
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    <?php }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                    endif;
                                    ?>
                                </span>
                            </span>
                                <?php 
                            endif;
                        endif;
                        ?>                                
                    </div>
                    <?php
                endif;
                ?>
            </div>
            <div class="doc-scrollable">
				<?php
				the_content();
				eazydocs_get_template_part( 'single-doc-home' );
				$children = eazydocs_list_pages( "title_li=&order=menu_order&child_of=" . $post->ID . "&echo=0&post_type=" . $post->post_type );
				if ( $children && $post->post_parent != 0 ) {
					echo '<div class="details_cont ent recently_added" id="content_elements">';
					echo '<h4 class="c_head">' . esc_html__( 'Articles', 'eazydocs' ) . '</h4>';
					echo '<ul class="article_list">';
					echo eazydocs_list_pages( "title_li=&order=menu_order&child_of=" . $post->ID . "&echo=0&post_type=" . $post->post_type );
					echo '</ul>';
					echo '</div>';
				}
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Docs:', 'eazydocs' ),
					'after'  => '</div>',
				));
				?>
            </div>
        </div>
		<?php
        if( $docs_feedback == '1' ) {
	        eazydocs_get_template_part( 'content-feedback' );
        }
        ?>
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