<?php
namespace eazyDocs\Admin;

/**
 * Class Create_Post
 * @package eazyDocs\Admin
 */
class Create_Post {
	/**
	 * Create_Post constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'create_parent_doc' ] );
		add_action( 'admin_init', [ $this, 'create_new_doc' ] );
		add_action( 'admin_init', [ $this, 'create_section_doc' ] );
		add_action( 'admin_init', [ $this, 'create_child_doc' ] );
	}

    /**
     * Create parent Doc post
     */
    public function create_parent_doc() {
	        if ( isset ( $_GET['parent_title'] ) && ! empty ( $_GET['parent_title'] ) ) {


			$title 				= ! empty ( $_GET['parent_title'] ) ? htmlspecialchars( $_GET['parent_title'] ) : '';
			$title_text      	= substr( chrEncode( $title ), 6 );
			$title_text 		= substr_replace( $title_text, "", -6 );

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
                'post_title'   => $title_text,
                'post_parent'  => 0,
                'post_content' => '',
                'post_type'    => 'docs',
                'post_status'  => 'publish',
                'post_author'  => get_current_user_id(),
                'menu_order'   => $order,
            ) );
            wp_insert_post( $post, $wp_error = '' );            
            wp_safe_redirect( admin_url('admin.php?page=eazydocs') );
        }
    }

	/**
	 * Create new Doc post
	 */
	public function create_new_doc() {
		if ( isset ( $_GET['new_doc'] ) && ! empty ( $_GET['new_doc'] ) ) {

			$doc_title      		= ! empty ( $_GET['new_doc'] ) ? htmlspecialchars( $_GET['new_doc'] ) : 0;
			$doc_title_text      	= substr( chrEncode( $doc_title ), 6 );
			$doc_title_text 		= substr_replace( $doc_title_text, "", -6 );

			// Create post object
			$post = array(
				'post_title'   => $doc_title_text,
				'post_parent'  => 0,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => 'publish',
				'menu_order'   => 1
			);
			wp_insert_post( $post, $wp_error = '' );
			wp_safe_redirect( admin_url('admin.php?page=eazydocs') );
		}
	}

	/**
	 * Create section doc post
	 */
	public function create_section_doc() {

		if ( isset ( $_GET['is_section'] ) && ! empty ( $_GET['is_section'] ) ) {

			$parentID      			= ! empty ( $_GET['parentID'] ) ? absint( $_GET['parentID'] ) : 0;
			$section_title 			= ! empty ( $_GET['is_section'] ) ? htmlspecialchars( $_GET['is_section'] ) : '';			
			$section_title_text     = substr( chrEncode( $section_title ), 6 );
			$section_title_text 	= substr_replace( $section_title_text, "", -6 );



			$parent_item   = get_children( array(
				'post_parent' => $parentID,
				'post_type'   => 'docs'
			) );

			$sec_post_status ='publish';
			if( ezd_is_premium() ){
				$sec_post_status = get_post_status($parentID);
			}

			$add   = 2;
			$order = count( $parent_item );
			$order = $order + $add;

			// Create post object
			$post = array(
				'post_title'   => $section_title_text,
				'post_parent'  => $parentID,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => $sec_post_status,
				'menu_order'   => $order
			);
			wp_insert_post( $post, $wp_error = '' );
			wp_safe_redirect( admin_url('admin.php?page=eazydocs') );
		}
	}

	/**
	 *  Create child doc post
	 */
	public function create_child_doc() {

		if ( isset ( $_GET['child'] ) && ! empty ( $_GET['child'] ) ) {

			$child_id    		= ! empty ( $_GET['childID'] ) ? absint( $_GET['childID'] ) : 0;
			$child_title 		= ! empty ( $_GET['child'] ) ? htmlspecialchars( $_GET['child'] ) : '';					
			$child_title_text   = substr( chrEncode( $child_title ), 6 );
			$child_title_text 	= substr_replace( $child_title_text, "", -6 );

			$child_item = get_children( array(
				'post_parent' => $child_id,
				'post_type'   => 'docs'
			) );

			$add = 2;
			$order = count( $child_item );
			$order = $order + $add;

			$child_post_status ='publish';
			if( ezd_is_premium() ){
				$child_post_status = get_post_status($child_id);
			}

			// Create post object
			$post = array(
				'post_title'   => $child_title_text,
				'post_parent'  => $child_id,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => $child_post_status,
				'menu_order'   => $order
			);
			wp_insert_post( $post, $wp_error = '' );
            wp_safe_redirect( admin_url('admin.php?page=eazydocs') );
		}
	}
}