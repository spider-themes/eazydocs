<div class="ezd-card ezd-grid-col-lg-2">
    <?php
    /** 
     * Combined Recent Activity for 'docs' post type.
     * - Includes latest comment per doc (no duplicate comment entries per doc).
     * - Includes post activities (publish, update, trash).
     * - Merges, sorts by date desc and limits result.
     */

    $limit = 5; // number of activity items to show

    // 1) Get recent comments for docs (fetch more than $limit to allow dedupe)
    $recent_comments = get_comments([
        'number'      => 50,
        'status'      => 'approve',
        'post_type'   => 'docs',
        'orderby'     => 'comment_date_gmt',
        'order'       => 'DESC',
    ]);

    // Build comment activities but only keep the latest comment per post_id
    $seen_post_comments = [];
    $activities = [];

    if ( ! empty( $recent_comments ) ) {
        foreach ( $recent_comments as $comment ) {
            $post_id = (int) $comment->comment_post_ID;
            if ( isset( $seen_post_comments[ $post_id ] ) ) {
                // already have a comment activity for this post (we only want the latest)
                continue;
            }

            $seen_post_comments[ $post_id ] = true;

            $activities[] = [
                'type'      => 'comment',
                'date'      => strtotime( $comment->comment_date_gmt ),
                'author'    => $comment->comment_author,
                'comment'   => wp_trim_words( $comment->comment_content, 20, '...' ),
                'post_id'   => $post_id,
                'post_title'=> get_the_title( $post_id ),
                'post_link' => get_permalink( $post_id ),
            ];
        }
    }

    // 2) Get recent docs (published/updated/trashed)
    // Fetch more than limit so merged list can be limited afterwards
    $recent_docs = get_posts([
        'post_type'      => 'docs',
        'post_status'    => ['publish','draft','trash'],
        'numberposts'    => 50,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ]);

    if ( ! empty( $recent_docs ) ) {
        foreach ( $recent_docs as $post ) {
            $status = get_post_status( $post );
            $activities[] = [
                'type'       => 'post',
                'date'       => (int) get_post_modified_time( 'U', false, $post ),
                'post_id'    => $post->ID,
                'post_title' => get_the_title( $post ),
                'edit_link'  => get_edit_post_link( $post->ID ),
                'status'     => $status,
            ];
        }
    }

    // 3) Sort activities by 'date' descending
    usort( $activities, function( $a, $b ) {
        if ( $a['date'] == $b['date'] ) return 0;
        return ( $a['date'] > $b['date'] ) ? -1 : 1;
    });

    // 4) Limit results
    $activities = array_slice( $activities, 0, $limit );
    ?>

    <h2 class="ezd-card-title"><?php esc_html_e( 'Recent Activity', 'eazydocs' ); ?></h2>
    <ul class="ezd-activity-list">
        <?php 
        if ( empty( $activities ) ) : 
            ?>
            <li class="ezd-activity-item"> <?php esc_html_e( 'No recent activity found.', 'eazydocs' ); ?> </li>
            <?php 
        else :
            foreach ( $activities as $act ) : 
                $time_ago = human_time_diff( $act['date'], current_time('timestamp') ) . ' ago';
                if ( $act['type'] === 'comment' ) :
                    // comment entry
                    $icon = 'dashicons-admin-comments';
                    $bg   = 'ezd-icon-bg-green';
                    ?>
                    <li class="ezd-activity-item">
                        <div class="ezd-activity-icon-wrapper <?php echo esc_attr( $bg ); ?>">
                            <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
                        </div>

                        <div>
                            <p class="ezd-activity-text">
                                <strong><?php echo esc_html( $act['author'] ); ?></strong>
                                <?php esc_html_e( 'commented on', 'eazydocs' ); ?>
                                <a href="<?php echo esc_url( $act['post_link'] ); ?>"><?php echo esc_html( $act['post_title'] ); ?></a>.
                            </p>
                            <p class="ezd-activity-time"><?php echo esc_html( $time_ago ); ?></p>
                        </div>
                    </li>
                    <?php 
                else :
                    // post entry (publish/update/trash)
                    switch ( $act['status'] ) {
                        case 'publish':
                            $icon = 'dashicons-plus-alt';
                            $bg   = 'ezd-icon-bg-blue';
                            $text = 'New article';
                            break;
                        case 'draft':
                            $icon = 'dashicons-edit';
                            $bg   = 'ezd-icon-bg-yellow';
                            $text = 'Draft updated';
                            break;
                        case 'trash':
                            $icon = 'dashicons-trash';
                            $bg   = 'ezd-icon-bg-red';
                            $text = 'Article deleted';
                            break;
                        default:
                            $icon = 'dashicons-admin-site-alt3';
                            $bg   = 'ezd-icon-bg-gray';
                            $text = 'Activity';
                            break;
                    }
                    ?>
                    <li class="ezd-activity-item">
                        <div class="ezd-activity-icon-wrapper <?php echo esc_attr( $bg ); ?>">
                            <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
                        </div>
                        <div>
                            <p class="ezd-activity-text">
                                <?php echo esc_html( $text ); ?> 
                                <a href="<?php echo esc_url( get_permalink( $act['post_id'] ) ); ?>" target="_blank"><?php echo esc_html( $act['post_title'] ); ?></a>
                            </p>
                            <p class="ezd-activity-time"><?php echo esc_html( $time_ago ); ?></p>
                        </div>
                    </li>
                    <?php 
                endif;
            endforeach;
        endif; 
        ?>
    </ul>
</div>