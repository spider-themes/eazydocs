<?php
$depth_one_parents = [];
$depth_two_parents = [];
$posts             = get_posts( [ 'post_type' => 'docs' ] );
$docs_num          = count( $posts );
?>
<div class="wrap">
    <div class="body-dark">
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
                <img src="<?php echo EAZYDOCS_IMG ?>/icon/folder-open.png" alt="">
				<span> <?php esc_html_e( 'No docs has been found. Perhaps', 'eazydocs' ); ?> </span>
                <a href="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?new_doc=" id="new-doc">
					<?php esc_html_e( 'Create one?', 'eazydocs' ); ?>
                </a>
            </div>
		    <?php
		endif;
		?>
    </div>
</div>