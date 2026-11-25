<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all registered sidebars
 *
 * @return string
 */
function sidebar_selectbox() {
	global $wp_registered_sidebars;
	$sidebars = '';
	foreach ( $wp_registered_sidebars as $wp_registered_sidebar ) {
		$sidebars .= '<option value="' . esc_attr( $wp_registered_sidebar['id'] ) . '">' . esc_html( $wp_registered_sidebar['name'] ) . '</option>';
	}

	return $sidebars;
}

/**
 * Fetch the reusable blocks (ID + title only) once per request.
 *
 * The left/right select boxes and the React options list all need the same
 * data; previously each ran its own unbounded query that loaded full post
 * objects (content, meta and term caches) for every block on the site.
 *
 * @return array<int, array{id: int, title: string}>
 */
function ezd_get_reusable_blocks_list() {
	static $blocks = null;

	if ( null !== $blocks ) {
		return $blocks;
	}

	$blocks = [];
	$ids    = get_posts(
		[
			'post_type'              => [ 'wp_block', 'wp_pattern' ],
			'post_status'            => [ 'publish', 'private' ],
			'posts_per_page'         => 500,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	// Load the post rows in one query instead of one get_post() per block.
	_prime_post_caches( $ids, false, false );

	foreach ( $ids as $id ) {
		$blocks[] = [
			'id'    => (int) $id,
			'title' => wp_strip_all_tags( get_post_field( 'post_title', $id, 'raw' ) ),
		];
	}

	return $blocks;
}

/**
 * Build the reusable-block <select> markup used by the OnePage sidebar popups.
 *
 * @param string $name Select name attribute.
 * @param string $id   Select id attribute.
 * @return string
 */
function ezd_reusable_blocks_select( $name, $id ) {
	$options = '';
	foreach ( ezd_get_reusable_blocks_list() as $block ) {
		$options .= '<option value="' . esc_attr( $block['id'] ) . '">' . esc_html( $block['title'] ) . '</option>';
	}

	if ( '' === $options ) {
		$options = '<option>' . esc_html__( 'No block found!', 'eazydocs' ) . '</option>';
	}

	return '<label for="ezd-shortcode"> ' . esc_html__( 'Select a Reusable Block (Optional)', 'eazydocs' ) . ' </label><br>'
		. '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="widefat">' . $options . '</select>';
}

/**
 * Get all registered reusable blocks
 *
 * @return string
 */
function get_reusable_blocks() {
	return ezd_reusable_blocks_select( 'ezd_sidebar_select_data', 'left_side_sidebar' );
}


/**
 * Get all registered reusable blocks for the right sidebar.
 *
 * @return string HTML option tags for the select box.
 */
function get_reusable_blocks_right() {
	return ezd_reusable_blocks_select( 'ezd_sidebar_select_data_right', 'right_side_sidebar' );
}

/**
 * Get reusable blocks as structured options for React UIs.
 *
 * @return array<int, array{id: string, title: string}>
 */
function ezd_get_reusable_blocks_options() {
	return array_map(
		static function ( $block ) {
			return [
				'id'    => (string) $block['id'],
				'title' => '' !== $block['title'] ? $block['title'] : esc_html__( '(Untitled)', 'eazydocs' ),
			];
		},
		ezd_get_reusable_blocks_list()
	);
}

/**
 * Get the link to manage reusable blocks.
 *
 * @return string HTML link to the reusable blocks admin page.
 */
function ezd_manage_reusable_blocks() {
	$admin_url = admin_url( 'edit.php?post_type=wp_block' );
	/* translators: %s: URL for managing reusable blocks */
	$message = sprintf(
		'<p class="ezd-text-support"><a href="%s" target="_blank">%s</a></p>',
		esc_url( $admin_url ),
		esc_html__( 'Manage Reusable blocks', 'eazydocs')
	);

	return $message;
}

/**
 * Get all registered sidebars
 *
 * @return string
 */
function ezd_edit_sidebar_selectbox() {
	global $wp_registered_sidebars;
	global $post;
	$edit_sidebars = '';
	foreach ( $wp_registered_sidebars as $wp_registered_sidebar ) {
		$edit_sidebars .= '<option value="' . esc_attr( $wp_registered_sidebar['id'] ) . '">' . esc_html( $wp_registered_sidebar['name'] ) . '</option>';
	}

	return $edit_sidebars;
}

//CUSTOM META BOX
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'EZD OnePage Options', 'EZD OnePage Options', 'ezd_onepage_docs', 'onepage-docs' );
} );

function ezd_onepage_docs( $post ) {
	wp_nonce_field( 'ezd_onepage_save_' . $post->ID, 'ezd_onepage_nonce' );
	?>
    <p>
        <label for="ezd_doc_layout"><?php esc_html_e( 'Doc Layout', 'eazydocs' ); ?></label><br/>
        <input type="text" disabled name="ezd_doc_layout" id="ezd_doc_layout" value="<?php echo esc_attr( get_post_meta( $post->ID, 'ezd_doc_layout', true ) ); ?>" class="widefat"/>
    </p> <br>

    <p class="ezd_left_content_heading"> <?php esc_html_e( 'Left Side Content', 'eazydocs' ); ?></p>

    <p>
        <label for="ezd_doc_content_type"><?php esc_html_e( 'Content Type', 'eazydocs' ); ?></label><br/>
        <input type="text" disabled name="ezd_doc_content_type" id="ezd_doc_content_type" value="<?php echo esc_attr( get_post_meta( $post->ID, 'ezd_doc_content_type', true ) ); ?>" class="widefat"/>
    </p>

    <p>
        <label for="ezd_doc_left_sidebar"><?php esc_html_e( 'Content Box', 'eazydocs' ); ?></label><br/>
        <textarea name="ezd_doc_left_sidebar" id="ezd_doc_left_sidebar" disabled cols="30" rows="3" class="widefat">
            <?php echo esc_attr( get_post_meta( $post->ID, "ezd_doc_left_sidebar", true ) ); ?>
        </textarea>
    </p>

    <p class="ezd_left_content_heading"> <?php esc_html_e( 'Right Side Content', 'eazydocs' ); ?></p>

    <p>
        <label for="ezd_doc_content_type_right"><?php esc_html_e( 'Content Type', 'eazydocs' ); ?></label><br/>
        <input type="text" disabled name="ezd_doc_content_type_right" id="ezd_doc_content_type_right" value="<?php echo esc_attr( get_post_meta( $post->ID, 'ezd_doc_content_type_right', true ) ); ?>" class="widefat"/>
    </p>
    <p>
        <label for="ezd_doc_content_box_right"><?php esc_html_e( 'Content Box', 'eazydocs' ); ?></label><br/>
        <textarea disabled name="ezd_doc_content_box_right" id="ezd_doc_content_box_right" cols="30" rows="3" class="widefat">
            <?php echo esc_attr( get_post_meta( $post->ID, 'ezd_doc_content_box_right', true ) ); ?>
        </textarea>
    </p>
    <?php
}

add_action( 'save_post', function ( $post_id ) {
	// Doc Options
	if ( 'onepage-docs' !== get_post_type( $post_id ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['ezd_onepage_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ezd_onepage_nonce'] ) ), 'ezd_onepage_save_' . $post_id ) ) {
		return;
	}

	if ( isset( $_POST['ezd_doc_layout'] ) ) {
		update_post_meta( $post_id, 'ezd_doc_layout', sanitize_text_field( wp_unslash( $_POST['ezd_doc_layout'] ) ) );
	}

	if ( isset( $_POST['ezd_doc_content_type'] ) ) {
		update_post_meta( $post_id, 'ezd_doc_content_type', sanitize_text_field( wp_unslash( $_POST['ezd_doc_content_type'] ) ) );
	}

	if ( isset( $_POST['ezd_doc_content_type_right'] ) ) {
		update_post_meta( $post_id, 'ezd_doc_content_type_right', sanitize_text_field( wp_unslash( $_POST['ezd_doc_content_type_right'] ) ) );
	}

	if ( isset( $_POST['ezd_doc_content_box_right'] ) ) {
		update_post_meta( $post_id, 'ezd_doc_content_box_right', wp_kses_post( wp_unslash( $_POST['ezd_doc_content_box_right'] ) ) );
	}	

} );

add_image_size( 'ezd_searrch_thumb16x16', '16', '16', true );
add_image_size( 'ezd_searrch_thumb50x50', '50', '50', true );

/**
 * Doc password form
 *
 * @param string      $output The password form HTML.
 * @param int|WP_Post $post   Optional. Post ID or WP_Post object. Default 0.
 *
 * @return string
 */
function ezd_password_form( $output, $post = 0 ) {

    // Check if post is set and is the desired custom post type
    if ( is_null( $post ) || ( 'docs' !== get_post_type( $post ) ) ) {
        // If it's not the correct post type, return the original output
        return $output;
    }

	$protected_form_switcher = ezd_is_premium() ? ezd_get_opt( 'protected_doc_form' ) : 'default';
	$protected_form_title    = ezd_is_premium() ? ezd_get_opt( 'protected_form_title', esc_html__( 'Enter Password & Read this Doc', 'eazydocs' ) ) : esc_html__( 'Enter Password & Read this Doc', 'eazydocs' );
	$protected_form_subtitle = ezd_is_premium() ? ezd_get_opt( 'protected_form_subtitle', esc_html__( 'This content is password protected. To view it please enter your password below:', 'eazydocs' ) ) : esc_html__( 'This content is password protected. To view it please enter your password below:', 'eazydocs' );

	if ( 'eazydocs-form' === $protected_form_switcher ) :
		ob_start();
		?>
		<div class="card ezd-password-wrap">
			<div class="card-body p-0 ezd-password-head">
				<div class="text-center p-4">
					<?php
					if ( has_post_thumbnail() ) :
						?>
                        <?php the_post_thumbnail( 'ezd_searrch_thumb50x50', [ 'class' => 'pw-logo' ] ); ?>
						<?php
					endif;
					?>
					<p class="mb-1 ezd-password-title">
						<?php echo esc_html( $protected_form_title ); ?>
					</p>
					<p class="mb-0 ezd-password-subtitle">
						<?php echo esc_html( $protected_form_subtitle ); ?>
					</p>
				</div>
			</div>
			<div class="card-body ezd-password-body p-4">
				<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post" class="form-horizontal auth-form ezd-password-form">
					<div class="form-group mb-2">
						<label class="form-label" for="ezd_password"> <?php esc_html_e( 'Password', 'eazydocs' ); ?> </label>
						<div class="input-group mb-3">
							<input name="post_password" required id="ezd_password" class="form-control" type="password" placeholder="Enter password" />
						</div>
					</div>
					<div class="form-group mb-0 row">
						<div class="col-12">
							<button class="btn btn-primary w-100 waves-effect waves-light" type="submit"> <?php esc_html_e( 'Unlock', 'eazydocs' ); ?> <i class="fas fa-sign-in-alt ms-1"></i> </button>
						</div>
					</div>
				</form></div>
			</div>
		<?php
		return ob_get_clean();
	endif;
	return $output;
}

add_filter( 'the_password_form', 'ezd_password_form', 9999 );
