<header class="easydocs-header-area">
    <div class="container-fluid">
        <div class="row alignment-center justify-content-between">
            <div class="col-lg-4">
                <div class="navbar-left d-flex alignment-center">
                    <div class="easydocs-logo-area">
                        <a href="javascript:void(0);">
							<?php esc_html_e( 'Documentations', 'eazydocs' ); ?>
                        </a>
                    </div>

                    <button type="button" data-url="<?php echo admin_url('admin.php'); ?>/Create_Post.php?parent_title=" id="parent-doc" class="easydocs-btn easydocs-btn-outline-blue easydocs-btn-sm easydocs-btn-round">
                        <span class="dashicons dashicons-plus-alt2"></span>
						<?php esc_html_e( 'Add Doc', 'eazydocs' ); ?>
                    </button>

                </div>
            </div>

            <div class="col-lg-5">
                <form action="#" method="POST" class="easydocs-search-form">
                    <input type="search" name="keyword" class="form-control" id="easydocs-search" placeholder="<?php esc_attr_e('Search for', 'eazydocs') ?>" onkeyup="fetch()" />
                    <div class="search-icon">
                        <span class="dashicons dashicons-search"></span>
                    </div>
                </form>
            </div>
            <div class="col-lg-3">
                <div class="navbar-right">
                    <ul class="d-flex justify-content-end">
                        <li>
                            <div class="easydocs-settings">

                                <?php if( current_user_can('edit_posts') ) : ?>
                                    <div class="header-notify-icons">
                                        <a href="<?php echo admin_url('edit.php?post_type=docs'); ?>" title="<?php esc_html_e( 'Go to Classic UI', 'eazydocs' );?>">
                                            <?php esc_html_e( 'Classic UI', 'eazydocs' );?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="header-notify-icon">
                                    <a href="admin.php?page=eazydocs-settings">
                                        <img src="<?php echo EAZYDOCS_IMG ?>/admin/admin-settings.svg" alt="<?php esc_html_e( 'Settings Icon', 'eazydocs' ); ?>">
                                    </a>
                                </div>
                            </div>
                        </li>

	                    <?php
	                    if ( class_exists('EazyDocsPro') && eaz_fs()->can_use_premium_code() ) :
		                    do_action('eazydocs_notification');
	                    else :
		                    ?>
                            <li class="easydocs-notification pro-notification-alert" title="<?php esc_attr_e('Notifications', 'eazydocs'); ?>">
                                <div class="header-notify-icon">
                                    <img class="notify-icon" src="<?php echo EAZYDOCS_IMG ?>/admin/notification.svg" alt="<?php esc_html_e( 'Notify Icon', 'eazydocs' ); ?>">
                                    <img class="settings-pro-icon" src="<?php echo EAZYDOCS_IMG ?>/admin/pro-icon.png" alt="<?php esc_html_e( 'Pro Icon', 'eazydocs' ); ?>">
                                </div>
                            </li>
	                        <?php
	                    endif;
	                    ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>