<?php
$depth_one_parents = [];
$depth_two_parents = [];
$posts             = get_posts( [ 'post_type' => 'docs',  'post_status' => ['all'] ] );
$docs_num          = count( $posts );
?>
<div class="ezd_doc_builder">
	<?php
		if ( $docs_num > 0 ) :
			require_once __DIR__ . '/template/header.php';
			?>
            <main>
                <div class="easydocs-sidebar-menu">
                    <div class="tab-container">
						<?php require_once __DIR__ . '/template/parent-docs.php'; ?>
                        <div class="easydocs-tab-content">
							<?php require_once __DIR__ . '/template/child-docs.php'; ?>
                        </div>
                    </div>
                </div>
            </main>
		    <?php
		else :
		    ?>
            <div class="eazydocs-no-content">
                <img src="<?php echo esc_url( EAZYDOCS_IMG . '/icon/folder-open.png' ); ?>" alt="<?php esc_attr_e('Folder Open', 'eazydocs' ); ?>">
                <p class="big-p"> <?php esc_html_e( 'No docs has been found. Perhaps', 'eazydocs' ); ?> </p>
                <p> <br>
                <a class="button button-primary ezd-btn btn-lg" href="<?php echo admin_url( 'admin.php' ); ?>?Create_doc=yes&_wpnonce=<?php echo wp_create_nonce('create_new_doc_nonce'); ?>&new_doc=" id="new-doc">
                    <?php esc_html_e( 'Create a Doc', 'eazydocs' ); ?>
                </a>
                </p>
            </div>
		    <?php
		endif;
	?>
</div>