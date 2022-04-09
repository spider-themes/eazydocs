<?php

namespace eazyDocs\Admin\Create_Docs;

class Parent_Doc {
	/**
	 * Bind Ajax Action
	 */
	public function __construct() {
		// Ajax actions
		add_action( 'wp_ajax_create_parent_doc', [ $this, 'create_doc' ] );
	}

	/**
	 * Create parent post
	 * @return void Ajax Success Content
	 */
	public function create_doc() {
		if ( isset ( $_POST['parent_title'] ) && ! empty ( $_POST['parent_title'] ) ) {

			$post = $this->post();

			if ( is_wp_error( $post ) ) {
				wp_send_json_error();
			}

			$added_doc  = $this->insert_doc_on_left_sidebar( $post );
			$child_docs = $this->insert_child_docs_tab( $post );

			wp_send_json_success( [
				'post'       => [
					'id' => $post,
				],
				'added_doc'  => $added_doc,
				'child_docs' => $child_docs
			] );
		}
	}

	/**
	 * Create the Parent Doc and Return the Post ID
	 * @return int Post ID
	 */
	public function post() {
		$title = $_POST['parent_title'] ?? '';

		$args = [
			'post_type'   => 'docs',
			'post_parent' => 0
		];

		$query = new \WP_Query( $args );
		$total = $query->found_posts;
		$add   = 2;
		$order = $total + $add;

		// Create post object
		$post = wp_insert_post( array(
			'post_title'   => $title,
			'post_parent'  => 0,
			'post_content' => '',
			'post_type'    => 'docs',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'menu_order'   => $order,
		) );

		return $post;
	}

	/**
	 * Place the created doc on the left sidebar
	 *
	 * @param int $post Post ID
	 *
	 * @return false|string
	 */
	public function insert_doc_on_left_sidebar( $post ) {
		/**
		 * Doc Left Sidebar Item
		 */
		$doc_counter = get_pages( [
			'child_of'  => $post,
			'post_type' => 'docs'
		] );

		if ( is_array( $doc_counter ) ) {
			foreach ( $doc_counter as $docs ) {
				$child_counter[] = $docs->ID;
			}
		}
		$child_docs = implode( ",", $child_counter );
		ob_start();
		?>
        <li class="easydocs-navitem tab-<?php echo $post; ?>" data-rel="tab-<?php echo $post; ?>">
            <div class="title">
                <img src="<?php echo EAZYDOCS_IMG ?>/icon/globe.svg" alt="<?php esc_attr_e( 'Globe icon', 'eazydocs' ) ?>">
				<?php echo get_the_title( $post ); ?>
            </div>
            <div class="total-page">
                <span>
                    <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                </span>
            </div>
            <div class="link">
                <a href="<?php echo get_edit_post_link( $post ); ?>" class="link edit" target="_blank">
                    <img src="<?php echo EAZYDOCS_IMG ?>/admin/edit.svg" alt="<?php esc_attr_e( 'Edit Icon', 'eazydocs' ); ?>" class="edit-img">
                </a>
                <a href="<?php echo get_the_permalink( $post ); ?>" class="link external-link" target="_blank" data-id="tab1">
                    <img src="<?php echo EAZYDOCS_IMG ?>/icon/external.svg" alt="<?php esc_attr_e( 'External icon', 'eazydocs' ) ?>">
                </a>
                <a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo $post . ',' . $child_docs; ?>" class="link delete parent-delete">
                    <img src="<?php echo EAZYDOCS_IMG ?>/admin/delete2.svg" alt="<?php esc_attr_e( 'Delete Icon', 'eazydocs' ); ?>">
                </a>
            </div>
        </li>
		<?php
		return ob_get_clean();
	}

	/**
	 * Create and Insert the Child Docs Tab
	 *
	 * @param int $post Post ID
	 *
	 * @return false|string
	 */
	public function insert_child_docs_tab( $post ) {
		ob_start();
		?>
        <div class="easydocs-tab" id="tab-<?php echo esc_attr( $post ); ?>">
            <div class="easydocs-filter-container">
                <ul class="single-item-filter">
                    <li class="easydocs-btn easydocs-btn-black-light easydocs-btn-rounded easydocs-btn-sm is-active" data-filter="all">
                        <span class="dashicons dashicons-media-document"></span>
			            <?php esc_html_e( 'All articles', 'eazydocs' ); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-green-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".publish">
                        <span class="dashicons dashicons-admin-site-alt3"></span>
			            <?php esc_html_e( 'Public', 'eazydocs' ); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".private">
                        <span class="dashicons dashicons-privacy"></span>
			            <?php esc_html_e( 'Private', 'eazydocs' ); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-orange-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".protected">
                        <span class="dashicons dashicons-lock"></span>
			            <?php esc_html_e( 'Protected', 'eazydocs' ); ?>
                    </li>
                    <li class="easydocs-btn easydocs-btn-gray-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".draft">
                        <span class="dashicons dashicons-edit-page"></span>
			            <?php esc_html_e( 'Draft', 'eazydocs' ); ?>
                    </li>
                </ul>
            </div>

            <ul class="easydocs-accordion sortable accordionjs"></ul>
            <button class="button button-info section-doc" name="submit" data-url="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?parentID=<?php echo $post; ?>&section=">
				<?php esc_html_e( 'Add Section', 'eazydocs' ); ?>
            </button>
        </div>
		<?php
		return ob_get_clean();
	}
}