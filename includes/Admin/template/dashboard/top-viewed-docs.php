<div class="ezd-card">
    <h2 class="ezd-card-title"><?php esc_html_e( 'Top Viewed Docs', 'eazydocs' ); ?></h2>
    <?php
    $args = array(
        'post_type'      => 'docs',
        'posts_per_page' => 10,
        'meta_key'       => 'post_views_count',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    );
    $top_products = new WP_Query( $args );
    ?>
    <ul class="ezd-activity-list">
        <?php
        if ( $top_products->have_posts() ) :
            while ( $top_products->have_posts() ) : $top_products->the_post();
                ?>            
                <li class="ezd-activity-item">                
                    <div class="ezd-activity-icon-wrapper ezd-icon-bg-blue" bis_skin_checked="1">
                        <a target="_blank" href="<?php the_permalink(); ?>" class="ezd-quick-links-link">
                            <img src="<?php echo EAZYDOCS_IMG . '/icon/external-white.svg'; ?>" />
                        </a>
                    </div>
                    <div>
                        <p class="ezd-activity-text">
                            <a target="_blank" href="<?php the_permalink(); ?>" ><?php the_title(); ?></a>
                        </p>
                        <p class="ezd-activity-time"> 
                            <span class="dashicons dashicons-visibility ezd-state-view-icon"></span>
                            <?php 
                            echo get_post_meta( get_the_ID(), 'post_views_count', true );
                            esc_html_e( ' Views', 'eazydocs' ); 
                            ?>
                        </p>
                    </div>
                </li>
                <?php
            endwhile;
            wp_reset_postdata(); 
        else :
            ?>
            <li class="ezd-activity-item"><?php esc_html_e( 'No viewed docs found.', 'eazydocs' ); ?></li>
            <?php
        endif;
        ?>
    </ul>
</div>