<div class="ezd-card">
    <h2 class="ezd-card-title">
        <?php esc_html_e( 'Top Ranked Docs', 'eazydocs' ); ?>
    </h2>

    <?php
    // Fetch all docs
    $posts = get_posts([
        'post_type'      => 'docs',
        'posts_per_page' => 10,
    ]);

    // Build ranking data
    $post_data = [];
    foreach ( $posts as $post ) {
        $positive_meta = get_post_meta( $post->ID, 'positive', false );
        $negative_meta = get_post_meta( $post->ID, 'negative', false );
        $positive = array_sum( is_array( $positive_meta ) ? $positive_meta : [] );
        $negative = array_sum( is_array( $negative_meta ) ? $negative_meta : [] );
        $total_votes = $positive + $negative;

        // Skip docs with NO ranking at all
        if ( $total_votes === 0 ) {
            continue;
        }
        
        // Collect post data
        $post_data[] = [
            'post_id'        => $post->ID,
            'post_title'     => $post->post_title,
            'post_permalink' => get_permalink( $post->ID ),
            'post_edit_link' => get_edit_post_link( $post->ID ),
            'positive_time'  => $positive,
            'negative_time'  => $negative,
            'created_at'     => get_the_time( 'U', $post->ID ),
        ];
    }

    // Sort by total positive votes (DESC)
    usort( $post_data, function( $a, $b ) {
        return $b['positive_time'] <=> $a['positive_time'];
    });
    ?>
    <ul class="ezd-activity-list">
        <?php 
        if ( ! empty( $post_data ) ) :
            foreach ( $post_data as $post ) :
                $positive = (int) $post['positive_time'];
                $negative = (int) $post['negative_time'];
                $total_votes = $positive + $negative;
                ?>
                <li class="ezd-activity-item">

                    <!-- Icon wrapper -->
                    <div class="ezd-activity-icon-wrapper ezd-icon-bg-blue">
                        <a target="_blank" href="<?php echo esc_url( $post['post_permalink'] ); ?>" class="ezd-quick-links-link">
                            <img src="<?php echo EAZYDOCS_IMG . '/icon/external-white.svg'; ?>" />
                        </a>
                    </div>

                    <!-- Content -->
                    <div>
                        <!-- Rank number + title -->
                        <p class="ezd-activity-text">
                            <a target="_blank" href="<?php echo esc_url( $post['post_permalink'] ); ?>">
                                <?php echo esc_html( $post['post_title'] ); ?>
                            </a>
                        </p>
                        
                        <!-- Votes -->
                        <div class="ezd-activity-meta">
                            <div class="ezd-votes">
                                <span class="like">
                                    <span class="dashicons dashicons-thumbs-up t-success"></span>
                                    <?php echo esc_html( $positive ); ?>
                                </span>

                                <span class="dislike">
                                    <span class="dashicons dashicons-thumbs-down t-danger"></span>
                                    <?php echo esc_html( $negative ); ?>
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="ezd-progress">
                                <?php 
                                if ( $total_votes > 0 ) : 
                                    ?>
                                    <progress value="<?php echo esc_attr( $positive ); ?>" max="<?php echo esc_attr( $total_votes ); ?>"></progress>
                                    <?php 
                                else : 
                                    ?>
                                    <span> <?php esc_html_e( 'No rates', 'eazydocs' ); ?> </span>
                                    <?php 
                                endif; 
                                ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php
            endforeach;
        else : 
            ?>
            <li class="ezd-activity-item">
                <?php esc_html_e( 'No ranked docs found.', 'eazydocs' ); ?>
            </li>
            <?php 
        endif; 
        ?>
    </ul>
</div>