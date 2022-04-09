<?php
namespace eazyDocs\Admin\Create_Docs;

class Section_Doc {
    /**
     * Bind Ajax Action
     */
    public function __construct() {
        // Ajax actions
        add_action( 'wp_ajax_create_section_doc', [ $this, 'create_doc' ] );
    }

    /**
     * Create the Parent Doc and Return the Post ID
     * @return int Post ID
     */
    public function post() {
        $parent_id = $_GET['parentID'];
        $section_title = $_GET['section'];

        // Create post object
        return wp_insert_post( array(
            'post_title'   => $section_title,
            'post_parent'  => $parent_id,
            'post_content' => '',
            'post_type'    => 'docs',
            'post_status'  => 'publish'
        ));
    }

    /**
     * Create parent post
     * @return void Ajax Success Content
     */
    public function create_parent_doc() {
        if ( isset ( $_GET['section'] ) && ! empty ( $_GET['section'] ) ) {

            $post = $this->post();

            if ( is_wp_error( $post ) ) {
                wp_send_json_error();
            }

            $added_doc = $this->insert_doc_on_left_sidebar($post);
            $child_docs = $this->insert_child_docs_tab($post);

            wp_send_json_success([
                'post' => [
                    'id'     => $post,
                ],
                'added_doc' => $added_doc,
                'child_docs' => $child_docs
            ]);
        }
    }
}