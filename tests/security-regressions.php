<?php
/**
 * Isolated security regression tests. Run: php tests/security-regressions.php
 *
 * Uses WordPress test doubles; no database, site content, or mail is touched.
 * Pro checks run when the sibling eazydocs-pro checkout is available.
 */

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'DAY_IN_SECONDS', 86400 );
$_SERVER['REQUEST_URI'] = '/wp-admin/admin-ajax.php';

class Security_Response extends Exception {
	public $success;
	public $data;
	public $status;
	public function __construct( $success, $data, $status = 200 ) {
		parent::__construct( 'Response' );
		$this->success = $success;
		$this->data = $data;
		$this->status = $status;
	}
}
class WP_Error {
	public $code;
	public $data;
	public function __construct( $code, $message, $data = [] ) {
		$this->code = $code;
		$this->data = $data;
	}
}
class WP_REST_Response {
	public $data;
	public function __construct( $data, $status = 200 ) { $this->data = $data; }
}
class WP_REST_Request {
	private $params;
	public function __construct( $params = [] ) { $this->params = $params; }
	public function get_param( $key ) { return $this->params[ $key ] ?? null; }
}
class WP_Query {
	public $posts;
	public $found_posts;
	public $post_count;
	private $index = 0;
	public function __construct( $args ) {
		$this->posts = get_posts( $args );
		$this->found_posts = count( $this->posts );
		$this->post_count = count( $this->posts );
	}
	public function have_posts() { return isset( $this->posts[ $this->index ] ); }
	public function the_post() { $GLOBALS['post'] = $this->posts[ $this->index++ ]; }
}
class Security_Database {
	public $posts = 'wp_posts';
	public $prefix = 'wp_';
	public $updates = [];
	public function update( $table, $data, $where, ...$formats ) { $this->updates[] = $where['ID']; }
	public function prepare( $sql, ...$args ) { return $sql; }
	public function esc_like( $text ) { return $text; }
	public function get_results( $sql ) { return array_values( $GLOBALS['security_posts'] ); }
}

function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function apply_filters( $name, $value ) { return $value; }
function __( $text, $domain = '' ) { return $text; }
function _n( $single, $plural, $count, $domain = '' ) { return 1 === $count ? $single : $plural; }
function number_format_i18n( $number ) { return (string) $number; }
function esc_html__( $text, $domain = '' ) { return $text; }
function esc_html_e( $text, $domain = '' ) { echo esc_html( $text ); }
function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES ); }
function esc_url( $text ) { return $text; }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES ); }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_text_field( $text ) { return trim( strip_tags( (string) $text ) ); }
function sanitize_key( $text ) { return strtolower( (string) $text ); }
function sanitize_title( $text ) { return strtolower( str_replace( ' ', '-', $text ) ); }
function wp_unslash( $value ) { return $value; }
function wp_strip_all_tags( $text ) { return strip_tags( $text ); }
function strip_shortcodes( $text ) { return $text; }
function wp_kses_post( $text ) { return $text; }
function check_ajax_referer( ...$args ) { return true; }
function wp_verify_nonce( ...$args ) { return true; }
function current_user_can( $cap, $id = null ) {
	return ! empty( $GLOBALS['security_caps'][ null === $id ? $cap : "$cap:$id" ] );
}
function wp_get_current_user() { return (object) [ 'roles' => [ 'contributor' ] ]; }
function get_current_user_id() { return 7; }
function is_user_logged_in() { return true; }
function ezd_get_doc_author_roles() { return [ 'contributor' ]; }
function ezd_is_premium() { return true; }
function ezd_get_opt( $key, $default = '' ) { return $GLOBALS['security_options'][ $key ] ?? $default; }
function ezd_user_can_view_doc( $id ) { return ! in_array( $id, $GLOBALS['security_restricted'], true ); }
function ezd_perform_edit_delete_actions( $action, $id ) { return current_user_can( 'delete_post', $id ); }
function ezd_get_doc_tree_ids( $id ) { return $GLOBALS['security_tree']; }
function ezd_flush_docs_tree_cache( ...$args ) {}
function clean_post_cache( ...$args ) {}
function get_post( $id = null ) {
	if ( null === $id ) { return $GLOBALS['post'] ?? null; }
	return is_object( $id ) ? $id : ( $GLOBALS['security_posts'][ $id ] ?? null );
}
function get_post_type( $id ) { $post = get_post( $id ); return $post ? $post->post_type : false; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function get_post_status( $id ) { $post = get_post( $id ); return $post ? $post->post_status : false; }
function get_posts( $args ) {
	$posts = array_filter( $GLOBALS['security_posts'], function ( $post ) use ( $args ) {
		return $post->post_type === $args['post_type']
			&& ( ! isset( $args['p'] ) || $post->ID === $args['p'] )
			&& ( ! isset( $args['post__in'] ) || in_array( $post->ID, $args['post__in'], true ) )
			&& ( ! isset( $args['post_parent'] ) || $post->post_parent === $args['post_parent'] );
	} );
	return array_values( $posts );
}
function get_post_field( $field, $id ) { return get_post( $id )->$field; }
function get_post_meta( ...$args ) { return ''; }
function wp_list_pluck( $items, $field ) { return array_map( function ( $item ) use ( $field ) { return $item->$field; }, $items ); }
function _prime_post_caches( ...$args ) {}
function get_option( $key ) { return []; }
function get_the_ID() { return $GLOBALS['post']->ID; }
function get_permalink( $id ) { return 'https://example.test/docs/' . $id; }
function get_the_permalink( $id ) { return get_permalink( $id ); }
function get_the_title( $id = null ) { return get_post( $id )->post_title; }
function the_title() { echo esc_html( get_the_title() ); }
function the_permalink() { echo esc_url( get_permalink( get_the_ID() ) ); }
function post_password_required( $post ) { return ! empty( get_post( $post )->post_password ) && ! $GLOBALS['security_password_unlocked']; }
function wp_trash_post( $id ) { $GLOBALS['security_writes'][] = $id; return get_post( $id ); }
function wp_insert_post( $args ) { $GLOBALS['security_writes'][] = $args; throw new Security_Response( true, 'inserted' ); }
function update_post_meta( ...$args ) { $GLOBALS['security_writes'][] = $args; }
function wp_reset_postdata() {}
function eazydocs_breadcrumbs() {}
function setup_postdata( $post ) {}
function eazydocs_get_template_part( $name ) { echo 'DOCUMENT_BODY'; }
function get_the_modified_date( ...$args ) { return ''; }
function get_the_password_form( $id ) { return 'PASSWORD_FORM'; }
function do_shortcode( $text ) { return $text; }
function get_the_author_meta( ...$args ) { return ''; }
function get_the_date( ...$args ) { return ''; }
function get_the_modified_time( ...$args ) { return ''; }
function admin_url( $path ) { return 'https://example.test/wp-admin/' . $path; }
function wp_safe_redirect( $url ) { throw new Security_Response( true, 'redirect' ); }
function wp_die( $message = '', $title = '', $args = [] ) { throw new Security_Response( false, $message, $args['response'] ?? 200 ); }
function wp_send_json_error( $data, $status = 200 ) { throw new Security_Response( false, $data, $status ); }
function wp_send_json_success( $data, $status = 200 ) { throw new Security_Response( true, $data, $status ); }
function wp_cache_get( ...$args ) { return [ 'docs' => array_keys( $GLOBALS['security_posts'] ) ]; }
function get_transient( ...$args ) { return 0; }
function get_term_by( ...$args ) { return false; }
function post_type_exists( $type ) { return 'api_docs' !== $type; }

$core = $argv[1] ?? dirname( __DIR__ );
$pro = $argv[2] ?? dirname( $core ) . '/eazydocs-pro';
require $core . '/includes/Admin/Api/DocsBuilderController.php';
require $core . '/includes/Admin/Delete_Post.php';
require $core . '/includes/One_Page.php';
require $core . '/includes/Frontend/Ajax.php';
class Security_Frontend {}
class_alias( Security_Frontend::class, 'EazyDocs\Frontend\Frontend' );
// Avoid constructors: only exercise the handlers, without booting WordPress.
$controller = new EazyDocs\Admin\Api\Docs_Builder_Controller();
$frontend = ( new ReflectionClass( EazyDocs\Frontend\Ajax::class ) )->newInstanceWithoutConstructor();
$legacy_delete = ( new ReflectionClass( EazyDocs\Admin\Delete_Post::class ) )->newInstanceWithoutConstructor();
$onepage = ( new ReflectionClass( EazyDocs\One_Page::class ) )->newInstanceWithoutConstructor();

function security_reset() {
	$GLOBALS['security_caps'] = [];
	$GLOBALS['security_posts'] = [];
	$GLOBALS['security_restricted'] = [];
	$GLOBALS['security_options'] = [];
	$GLOBALS['security_tree'] = [];
	$GLOBALS['security_writes'] = [];
	$GLOBALS['security_password_unlocked'] = false;
	$GLOBALS['wpdb'] = new Security_Database();
	$_POST = $_GET = [];
}
function security_doc( $id, $type = 'docs', $parent = 0, $password = '' ) {
	$GLOBALS['security_posts'][ $id ] = (object) [
		'ID' => $id, 'post_type' => $type, 'post_parent' => $parent,
		'post_status' => 'publish', 'post_title' => 'Document ' . $id,
		'post_password' => $password, 'post_content' => 'SECRET_CONTENT_' . $id,
		'post_excerpt' => '', 'post_name' => 'doc-' . $id, 'post_author' => 7, 'menu_order' => 0,
	];
}
function security_assert( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}
function security_capture( $callback ) {
	$level = ob_get_level();
	ob_start();
	try {
		$result = $callback();
	} catch ( Security_Response $response ) {
		$result = $response;
	} finally {
		$output = '';
		while ( ob_get_level() > $level ) { $output = ob_get_clean() . $output; }
	}
	return [ $result, $output ];
}
$failures = 0;
$checks = 0;
function security_test( $name, $callback ) {
	global $checks, $failures;
	++$checks;
	security_reset();
	try { $callback(); echo "PASS $name\n"; }
	catch ( Throwable $error ) { ++$failures; echo "FAIL $name: {$error->getMessage()}\n"; }
}

security_test( 'editing posts does not authorize publishing docs', function () use ( $controller ) {
	$GLOBALS['security_caps'] = [ 'edit_posts' => true ];
	security_assert( false === $controller->check_publish_permission(), 'Publishing was authorized' );
} );
security_test( 'doc editors can create drafts', function () use ( $controller ) {
	$GLOBALS['security_caps'] = [ 'edit_docs' => true ];
	security_assert( true === $controller->check_publish_permission( new WP_REST_Request( [ 'post_status' => 'draft' ] ) ), 'Draft denied' );
} );
security_test( 'doc publishers can publish', function () use ( $controller ) {
	$GLOBALS['security_caps'] = [ 'publish_docs' => true ];
	security_assert( true === $controller->check_publish_permission(), 'Publisher denied' );
} );
security_test( 'anonymous draft creation is denied', function () use ( $controller ) {
	security_assert( false === $controller->check_publish_permission( new WP_REST_Request( [ 'post_status' => 'draft' ] ) ), 'Anonymous draft authorized' );
} );
security_test( 'REST deletion checks every descendant before writing', function () use ( $controller ) {
	security_doc( 10 ); security_doc( 11, 'docs', 10 );
	$GLOBALS['security_caps'] = [ 'delete_post:10' => true ];
	$GLOBALS['security_tree'] = [ 11 ];
	$result = $controller->delete_doc( new WP_REST_Request( [ 'doc_id' => 10 ] ) );
	security_assert( $result instanceof WP_Error && 403 === $result->data['status'] && [] === $GLOBALS['security_writes'], 'Unauthorized or partial deletion' );
} );
security_test( 'REST deletion rejects non-doc posts', function () use ( $controller ) {
	security_doc( 10, 'page' );
	$GLOBALS['security_caps'] = [ 'delete_post:10' => true ];
	$result = $controller->delete_doc( new WP_REST_Request( [ 'doc_id' => 10 ] ) );
	security_assert( $result instanceof WP_Error && [] === $GLOBALS['security_writes'], 'Non-doc deleted' );
} );
security_test( 'authorized tree deletion still works', function () use ( $controller ) {
	security_doc( 10 ); security_doc( 11, 'docs', 10 );
	$GLOBALS['security_caps'] = [ 'delete_post:10' => true, 'delete_post:11' => true ];
	$GLOBALS['security_tree'] = [ 11 ];
	$result = $controller->delete_doc( new WP_REST_Request( [ 'doc_id' => 10 ] ) );
	security_assert( $result instanceof WP_REST_Response && [ 10, 11 ] === $GLOBALS['security_writes'], 'Tree deletion denied' );
} );
security_test( 'legacy deletion checks descendants before writing', function () use ( $legacy_delete ) {
	security_doc( 10 ); security_doc( 11, 'docs', 10 );
	$GLOBALS['security_caps'] = [ 'delete_post:10' => true ];
	$GLOBALS['security_tree'] = [ 11 ];
	$_GET = [ 'Doc_Delete' => 'yes', 'DeleteID' => '10', '_wpnonce' => 'valid' ];
	[ $result ] = security_capture( [ $legacy_delete, 'delete_doc' ] );
	security_assert( $result instanceof Security_Response && ! $result->success && [] === $GLOBALS['security_writes'], 'Unauthorized or partial legacy deletion' );
} );
foreach ( [ 'non-publisher' => [ 'edit_docs' => true, 'edit_post:10' => true ], 'other author' => [ 'edit_docs' => true, 'publish_docs' => true ] ] as $name => $caps ) {
	security_test( 'OnePage rejects ' . $name, function () use ( $onepage, $caps ) {
		security_doc( 10 );
		$GLOBALS['security_caps'] = $caps;
		$_GET = [ 'make_onepage' => 'yes', '_wpnonce' => 'valid', 'parentID' => '10' ];
		[ $result ] = security_capture( [ $onepage, 'doc_one_page' ] );
		security_assert( $result instanceof Security_Response && ! $result->success && [] === $GLOBALS['security_writes'], 'OnePage published without authorization' );
	} );
}
security_test( 'authorized OnePage publishing still works', function () use ( $onepage ) {
	security_doc( 10 );
	$GLOBALS['security_caps'] = [ 'edit_docs' => true, 'edit_post:10' => true, 'publish_docs' => true ];
	$_GET = [ 'make_onepage' => 'yes', '_wpnonce' => 'valid', 'parentID' => '10' ];
	[ $result ] = security_capture( [ $onepage, 'doc_one_page' ] );
	security_assert( $result instanceof Security_Response && $result->success && 1 === count( $GLOBALS['security_writes'] ), 'OnePage creation denied' );
} );
security_test( 'single-doc AJAX rejects role-restricted docs before rendering', function () use ( $frontend ) {
	security_doc( 10 ); $GLOBALS['security_restricted'] = [ 10 ]; $_POST['postid'] = 10;
	[ $result, $output ] = security_capture( [ $frontend, 'docs_single_content' ] );
	security_assert( $result instanceof Security_Response && ! $result->success && 404 === $result->status && '' === $output, 'Restricted doc rendered' );
} );
security_test( 'single-doc AJAX still renders authorized docs', function () use ( $frontend ) {
	security_doc( 10 ); $_POST['postid'] = 10;
	[ $result ] = security_capture( [ $frontend, 'docs_single_content' ] );
	security_assert( $result instanceof Security_Response && $result->success && false !== strpos( $result->data['content'], 'DOCUMENT_BODY' ), 'Authorized doc unavailable' );
} );
security_test( 'browse AJAX hides role-restricted child titles', function () use ( $frontend ) {
	security_doc( 10, 'docs', 1 ); security_doc( 11, 'docs', 1 );
	$GLOBALS['security_restricted'] = [ 10 ]; $_POST['parent_id'] = 1;
	[ $result ] = security_capture( [ $frontend, 'child_docs' ] );
	security_assert( 1 === $result->data['count'] && false === strpos( $result->data['html'], 'Document 10' ) && false !== strpos( $result->data['html'], 'Document 11' ), 'Browse visibility mismatch' );
} );
security_test( 'shared search candidates are filtered for each visitor', function () use ( $frontend ) {
	security_doc( 10 ); security_doc( 11 );
	$GLOBALS['security_restricted'] = [ 10 ]; $_POST = [ 'keyword' => 'Document', 'post_type' => 'docs' ];
	[ , $output ] = security_capture( [ $frontend, 'eazydocs_search_results' ] );
	security_assert( false === strpos( $output, 'Document 10' ) && false !== strpos( $output, 'Document 11' ), 'Search disclosed restricted title' );
	$GLOBALS['security_restricted'] = [];
	[ , $output ] = security_capture( [ $frontend, 'eazydocs_search_results' ] );
	security_assert( false !== strpos( $output, 'Document 10' ), 'Authorized visitor lost cached result' );
} );

if ( is_file( $pro . '/includes/Admin/Admin_Actions.php' ) ) {
	require $pro . '/includes/Admin/Admin_Actions.php';
	require $pro . '/includes/Frontend/Search.php';
	require $pro . '/includes/Frontend/Assistant.php';
	require $pro . '/includes/Duplicator/EazyDocs_Duplicate.php';
	$admin = ( new ReflectionClass( eazyDocsPro\Admin\Admin_Actions::class ) )->newInstanceWithoutConstructor();
	$assistant = ( new ReflectionClass( eazyDocsPro\Frontend\Assistant::class ) )->newInstanceWithoutConstructor();
	$search = ( new ReflectionClass( eazyDocsPro\Frontend\Search::class ) )->newInstanceWithoutConstructor();
	foreach ( [ 'other author' => 'docs', 'non-doc source' => 'page' ] as $name => $type ) {
		security_test( 'Pro duplication rejects ' . $name, function () use ( $type ) {
			security_doc( 10, $type );
			$GLOBALS['security_caps'] = [ 'edit_post:10' => 'page' === $type ];
			$result = eazyDocsPro\Duplicator\ezd_duplicate_single_doc( get_post( 10 ), 0, 123 );
			security_assert( $result instanceof WP_Error && [] === $GLOBALS['security_writes'], 'Unauthorized content copied' );
		} );
	}
	security_test( 'authorized Pro duplication still creates a draft', function () {
		security_doc( 10 ); $GLOBALS['security_caps'] = [ 'edit_post:10' => true ];
		[ $result ] = security_capture( function () { return eazyDocsPro\Duplicator\ezd_duplicate_single_doc( get_post( 10 ), 0, 123 ); } );
		security_assert( $result->success && 'draft' === $GLOBALS['security_writes'][0]['post_status'], 'Authorized draft copy denied' );
	} );
	// Search scopes are unrelated to the permission boundary under test.
	class_alias( Security_Scope::class, 'eazyDocsPro\Frontend\Assistant_Scope' );
	foreach ( [ 'other author' => 'docs', 'unrelated post type' => 'page' ] as $name => $type ) {
		security_test( 'Pro sort rejects ' . $name . ' atomically', function () use ( $admin, $type ) {
			security_doc( 10 ); security_doc( 11, $type );
			$GLOBALS['security_caps'] = [ 'edit_posts' => true, 'edit_docs' => true, 'edit_post:10' => true, 'edit_post:11' => 'page' === $type ];
			$_POST['page_id_array'] = [ 10, 11 ];
			[ $result ] = security_capture( [ $admin, 'sortable_docs' ] );
			security_assert( ! $result->success && [] === $GLOBALS['wpdb']->updates, 'Unauthorized or partial reordering' );
		} );
	}
	security_test( 'authorized Pro sorting still works', function () use ( $admin ) {
		security_doc( 10 ); security_doc( 11 );
		$GLOBALS['security_caps'] = [ 'edit_docs' => true, 'edit_post:10' => true, 'edit_post:11' => true ];
		$_POST['page_id_array'] = [ 11, 10 ];
		[ $result ] = security_capture( [ $admin, 'sortable_docs' ] );
		security_assert( $result->success && [ 11, 10 ] === $GLOBALS['wpdb']->updates, 'Authorized reordering denied' );
	} );
	security_test( 'Pro instant answers reject role-restricted docs', function () use ( $assistant ) {
		security_doc( 10 ); $GLOBALS['security_restricted'] = [ 10 ]; $_POST['post_id'] = 10;
		[ $result, $output ] = security_capture( [ $assistant, 'eazydocs_kbase_instant' ] );
		security_assert( $result instanceof Security_Response && ! $result->success && 404 === $result->status && '' === $output, 'Restricted answer disclosed' );
	} );
	security_test( 'authorized Pro instant answers retain password protection', function () use ( $assistant ) {
		security_doc( 10, 'docs', 0, 'password' ); $_POST['post_id'] = 10;
		[ $result ] = security_capture( [ $assistant, 'eazydocs_kbase_instant' ] );
		security_assert( $result->success && false !== strpos( $result->data['html'], 'PASSWORD_FORM' ) && false === strpos( $result->data['html'], 'SECRET_CONTENT_10' ), 'Password protection bypassed' );
		$GLOBALS['security_password_unlocked'] = true;
		[ $result ] = security_capture( [ $assistant, 'eazydocs_kbase_instant' ] );
		security_assert( $result->success && false !== strpos( $result->data['html'], 'SECRET_CONTENT_10' ), 'Unlocked answer unavailable' );
	} );
	security_test( 'Pro search hides restricted titles and password-protected excerpts', function () use ( $search ) {
		security_doc( 10 ); security_doc( 11, 'docs', 0, 'password' ); security_doc( 12 );
		$GLOBALS['security_restricted'] = [ 10 ]; $_POST['keyword'] = 'Document';
		[ , $output ] = security_capture( [ $search, 'fetch_posts' ] );
		security_assert( false === strpos( $output, 'Document 10' ) && false === strpos( $output, 'SECRET_CONTENT_11' ) && false !== strpos( $output, 'SECRET_CONTENT_12' ), 'Protected search content disclosed' );
		$GLOBALS['security_password_unlocked'] = true;
		[ , $output ] = security_capture( [ $search, 'fetch_posts' ] );
		security_assert( false !== strpos( $output, 'SECRET_CONTENT_11' ), 'Unlocked excerpt unavailable' );
	} );
} else {
	echo "SKIP Pro checks (sibling checkout unavailable)\n";
}
class Security_Scope {
	public static function validate_root( $id ) { return 0; }
}
echo "$checks checks, $failures failures\n";
exit( $failures ? 1 : 0 );
