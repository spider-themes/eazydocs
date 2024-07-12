<header class="easydocs-header-area">
    <div class="ezd-container-fluid">
        <div class="row alignment-center justify-content-between ml-0">
            <div class="navbar-left d-flex alignment-center">
                <div class="easydocs-logo-area">
                    <a href="javascript:void(0);">
                        <?php esc_html_e( 'Documentations', 'eazydocs' ); ?>
                    </a>
                </div>

                <?php 
                if ( current_user_can( 'edit_posts' ) ) :
                    ?>
                    <button type="button" data-url="<?php echo esc_url(admin_url('admin.php')); ?>?Create_doc=yes&_wpnonce=<?php echo esc_attr(wp_create_nonce('parent_doc_nonce')); ?>&parent_title=" id="parent-doc" class="easydocs-btn easydocs-btn-outline-blue easydocs-btn-sm easydocs-btn-round">
                        <span class="dashicons dashicons-plus-alt2"></span>
                        <?php esc_html_e( 'Add Doc', 'eazydocs' ); ?>
                    </button>
                    <?php 
                endif;
                ?>
            </div>

            <form action="#" method="POST" class="easydocs-search-form">
                <div class="search-icon">
                    <span class="dashicons dashicons-search"></span>
                </div>
                <input type="search" name="keyword" class="form-control" id="easydocs-search" placeholder="<?php esc_attr_e('Search for', 'eazydocs'); ?>" onkeyup="fetch()" />
            </form>

            <div class="navbar-right">
                <ul class="d-flex justify-content-end">

                    <?php 
                    if ( current_user_can('manage_options') || current_user_can('edit_posts')  ) : 
                    ?>
                    <li>
                        <div class="easydocs-settings">

                            <?php 
                            if ( current_user_can('edit_posts') ) : 
                                ?>
                                    <div class="header-notify-icons">
                                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=docs')); ?>" title="<?php esc_attr_e( 'Go to Classic UI', 'eazydocs' );?>">
                                            <?php esc_html_e( 'Classic UI', 'eazydocs' );?>
                                        </a>
                                    </div>
                                <?php 
                            endif;
                            
                            // get current user role
                            $user       = wp_get_current_user();
                            $user_roles = !empty($user->roles) ? $user->roles : array();
                            $user_role  = array_shift( $user_roles );
                            $settings_edit_access = ezd_get_opt('settings-edit-access');
                            if ( ! is_array( $settings_edit_access ) ) {
                                $settings_edit_access = array('administrator');
                            }

                            if ( is_array( $settings_edit_access ) && in_array( $user_role, $settings_edit_access ) ) :
                                ?>
                                <div class="header-notify-icon" title="<?php esc_attr_e( 'Central settings page', 'eazydocs' ) ?>">
                                    <a href="admin.php?page=eazydocs-settings">
                                        <img src="<?php echo esc_url(EAZYDOCS_IMG) ?>/admin/admin-settings.svg" alt="<?php esc_attr_e( 'Settings Icon', 'eazydocs' ); ?>">
                                    </a>
                                </div>
                                <?php
                            endif;

                            $trash_docs = wp_count_posts('docs');
                            ?>
                            <div class="header-notify-icon ezd-trashicon" title="<?php esc_attr_e( 'View, manage, restore the trashed docs', 'eazydocs' ) ?>">
                                <a href="edit.php?post_status=trash&post_type=docs">
                                    <span class="dashicons dashicons-trash"></span>
                                </a>
                                <span class="easydocs-badge"> <?php echo esc_html( $trash_docs->trash ); ?> </span>
                            </div>
                            <?php 
                            endif;
                            ?>
                        </div>
                    </li>
                    <?php
                    
                    if ( current_user_can('manage_options') ) : 
                        if ( ezd_is_premium() ) :
                            do_action('eazydocs_notification');
                        else :
                            ?>
                            <li class="easydocs-notification pro-notification-alert" title="<?php esc_attr_e('Notifications', 'eazydocs'); ?>">
                                <div class="header-notify-icon">
                                    <img class="notify-icon" src="<?php echo esc_url(EAZYDOCS_IMG )?>/admin/notification.svg" alt="<?php esc_html_e( 'Notify Icon', 'eazydocs' ); ?>">
                                    <img class="settings-pro-icon" src="<?php echo esc_url(EAZYDOCS_IMG) ?>/admin/pro-icon.png" alt="<?php esc_html_e( 'Pro Icon', 'eazydocs' ); ?>">
                                </div>
                            </li>
                        <?php
                        endif;
                    endif;
                    ?>
                </ul>
            </div>
        </div>
    </div>
</header>