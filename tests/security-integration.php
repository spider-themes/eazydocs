<?php
/**
 * Integration checks against a disposable WordPress installation.
 * Usage: php tests/security-integration.php /path/to/disposable/wp-load.php [core] [pro]
 * Requires DB_NAME=ezd_audit and site URL http://ezd-audit.invalid.
 * Creates fixtures only in that database. Mail and external HTTP are blocked.
 */
if ( 'cli' !== PHP_SAPI || empty( $argv[1] ) ) {
	exit( 1 );
}
ob_start(); // Keep AJAX cookie/header behavior available throughout the suite.
// Check the target configuration before bootstrapping any plugin sources.
define( 'DOING_AJAX', true );
define( 'WP_ADMIN', true );
require $argv[1];
if ( 'ezd_audit' !== DB_NAME || 'http://ezd-audit.invalid' !== get_option( 'siteurl' ) ) {
	fwrite( STDERR, "Refusing to run outside the disposable audit database.\n" );
	exit( 1 );
}
$core = $argv[2] ?? dirname( __DIR__ );
$pro = $argv[3] ?? dirname( $core ) . '/eazydocs-pro';
define( 'EZD_PATH', $core );
define( 'EZD_URL', 'http://ezd-audit.invalid/core' );
define( 'EZD_ASSETS', EZD_URL . '/assets/' );
define( 'EZD_IMG', EZD_ASSETS . 'images/' );
define( 'EZD_VERSION', 'audit' );
define( 'EAZYDOCSPRO_PATH', $pro );
define( 'EAZYDOCSPRO_URL', 'http://ezd-audit.invalid/pro' );
define( 'EAZYDOCSPRO_VERSION', 'audit' );
class EZD_EazyDocsPro {}
class Audit_License {
	public function can_use_premium_code() { return true; }
	public function is_plan( ...$args ) { return true; }
}
function eaz_fs() { static $license; return $license ?? ( $license = new Audit_License() ); }
require dirname( __DIR__ ) . '/vendor/autoload.php';
require $core . '/includes/functions.php';
require $pro . '/includes/functions.php';
require $core . '/includes/Post_Types.php';
( new Docs() )->register_post_type();
( new Docs() )->register_taxonomy();
register_post_type( 'ezd-text-feedback', [ 'public' => false ] );
register_post_type( 'ezd_feedback', [ 'public' => false ] );
require $pro . '/includes/features/role-visibility/functions.php';
require $pro . '/includes/features/role-visibility/Role_Visibility.php';
require $pro . '/includes/features/subscription/subscribe.php';
require $pro . '/includes/features/selected_comment/selected_comment.php';
require $core . '/includes/Frontend/Mailer.php';
require $core . '/includes/Frontend/Ajax.php';
require $core . '/includes/Admin/Admin.php';
require $core . '/includes/Admin/Create_Post.php';
require $core . '/includes/One_Page.php';
require $core . '/includes/One_Page_Docs.php';
( new EazyDocs\One_Page_Docs() )->register_post_type();
require $core . '/includes/Admin/Import_Export.php';
require $core . '/includes/Admin/Api/DocsBuilderController.php';
require $pro . '/includes/Admin/Doc_Visibility.php';
require $pro . '/includes/Admin/Doc_Section_Visibility.php';
require $pro . '/includes/Frontend/Assistant.php';
require $pro . '/includes/Frontend/Search.php';
require $pro . '/includes/Frontend/Assistant_Scope.php';
require $core . '/includes/IO/Csv.php';
require $core . '/includes/Markdown/Converter.php';
require $core . '/includes/Markdown/Importer.php';
require $core . '/includes/Markdown/Exporter.php';
require $core . '/includes/Google_Login.php';
require_once ABSPATH . 'wp-admin/includes/user.php';
require_once ABSPATH . 'wp-admin/includes/file.php';

class Audit_Stop extends Exception {}
add_filter( 'wp_die_ajax_handler', function () { return function () { throw new Audit_Stop(); }; } );
add_filter( 'wp_die_handler', function () { return function () { throw new Audit_Stop(); }; } );
add_filter( 'wp_redirect', function () { throw new Audit_Stop(); } );
$mails = [];
add_filter( 'pre_wp_mail', function ( $return, $args ) use ( &$mails ) { $mails[] = $args; return true; }, 10, 2 );
add_filter( 'pre_http_request', function () { return new WP_Error( 'audit_no_network' ); } );
// A nonce created in this CLI process must also be visible to AJAX verification.
function audit_nonce( $action, $field = 'nonce' ) {
	$_POST[ $field ] = wp_create_nonce( $action );
	$_REQUEST = array_merge( $_GET, $_POST );
}
function audit_capture( $callback ) {
	$level = ob_get_level();
	ob_start();
	$result = null;
	try { $result = $callback(); } catch ( Audit_Stop $stop ) {}
	finally {
		$output = '';
		while ( ob_get_level() > $level ) { $output = ob_get_clean() . $output; }
	}
	return [ $result, json_decode( $output, true ), $output ];
}
function audit_assert( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}
function audit_instance( $class ) { return ( new ReflectionClass( $class ) )->newInstanceWithoutConstructor(); }
$options = [
	'docs-write-access' => [ 'administrator', 'editor', 'author', 'contributor' ],
	'role_visibility_enable' => true, 'subscriptions' => true,
	'enable-selected-comment' => true,
];
update_option( 'eazydocs_settings', $options );
$suffix = bin2hex( random_bytes( 4 ) );
$users = [];
foreach ( [ 'administrator', 'author', 'editor', 'contributor', 'subscriber' ] as $role ) {
	$users[ $role ] = wp_insert_user( [ 'user_login' => $role . $suffix, 'user_pass' => wp_generate_password(), 'role' => $role, 'user_email' => $role . $suffix . '@example.test' ] );
}
$author = $users['author'];
$admin = $users['administrator'];
$editor = $users['editor'];
$contributor = $users['contributor'];
function audit_doc( $args = [] ) {
	global $author;
	$id = wp_insert_post( array_merge( [ 'post_type' => 'docs', 'post_status' => 'publish', 'post_author' => $author, 'post_title' => 'Audit ' . bin2hex( random_bytes( 3 ) ), 'post_content' => 'CONFIDENTIAL_AUDIT_BODY' ], $args ), true );
	if ( is_wp_error( $id ) ) { throw new RuntimeException( $id->get_error_message() ); }
	return $id;
}
$checks = 0;
$failures = 0;
function audit_test( $name, $callback ) {
	global $checks, $failures, $options, $mails;
	++$checks;
	if ( getenv( 'EZD_AUDIT_VERBOSE' ) ) { fwrite( STDERR, "RUN $name\n" ); }
	wp_set_current_user( 0 );
	$_POST = $_GET = $_REQUEST = $_COOKIE = [];
	$_SERVER['REMOTE_ADDR'] = '192.0.2.' . $checks;
	update_option( 'eazydocs_settings', $options );
	$mails = [];
	try { $callback(); echo "PASS $name\n"; }
	catch ( Throwable $error ) { ++$failures; echo "FAIL $name: {$error->getMessage()}\n"; }
}
$visibility = new eazyDocsPro\Features\RoleVisibility\Role_Visibility();
$rest = rest_get_server();
$builder = new EazyDocs\Admin\Api\Docs_Builder_Controller();
$builder->register_routes();

audit_test( 'configured Documentation Authors retain publishing rights', function () use ( $contributor, $builder ) {
	ezd_sync_docs_capabilities( true ); wp_set_current_user( $contributor );
	audit_assert( current_user_can( 'edit_docs' ) && current_user_can( 'publish_docs' ) && $builder->check_publish_permission(), 'Explicitly delegated author permissions changed' );
	$request = new WP_REST_Request(); $request->set_param( 'post_status', 'draft' );
	audit_assert( $builder->check_publish_permission( $request ), 'Contributor cannot draft docs' );
} );
audit_test( 'a role with only edit_docs can draft but cannot publish', function () use ( $builder ) {
	add_role( 'audit_drafter', 'Audit drafter', [ 'read' => true, 'edit_docs' => true ] );
	$user = wp_insert_user( [ 'user_login' => 'drafter' . bin2hex( random_bytes( 4 ) ), 'user_pass' => wp_generate_password(), 'role' => 'audit_drafter' ] );
	wp_set_current_user( $user );
	$request = new WP_REST_Request(); $request->set_param( 'post_status', 'draft' );
	audit_assert( $builder->check_publish_permission( $request ) && ! $builder->check_publish_permission(), 'Draft-only capability bypassed or denied' );
	remove_role( 'audit_drafter' );
} );
audit_test( 'settings save checks authorization independently of the nonce', function () use ( $contributor ) {
	wp_set_current_user( $contributor ); $denied = false;
	try { apply_filters( 'csf_eazydocs_settings_save', [ 'google_client_secret' => 'attacker' ] ); }
	catch ( Audit_Stop $stop ) { $denied = true; }
	audit_assert( $denied, 'Unprivileged settings save accepted' );
} );
audit_test( 'administrative settings save remains authorized', function () use ( $admin ) {
	wp_set_current_user( $admin ); $settings = [ 'example' => 'value' ];
	audit_assert( $settings === apply_filters( 'csf_eazydocs_settings_save', $settings ), 'Admin settings save denied' );
} );
audit_test( 'explicitly delegated settings access remains authorized', function () use ( $author, $options ) {
	update_option( 'eazydocs_settings', array_merge( $options, [ 'settings-edit-access' => 'publish_posts' ] ) );
	wp_set_current_user( $author ); $settings = [ 'example' => 'delegated' ];
	audit_assert( $settings === apply_filters( 'csf_eazydocs_settings_save', $settings ), 'Delegated settings save denied' );
} );
audit_test( 'Google OAuth rejects forged state and unverified addresses', function () use ( $admin ) {
	$google = audit_instance( EazyDocs\Google_Login::class );
	$encode = new ReflectionMethod( $google, 'encode_state' ); $decode = new ReflectionMethod( $google, 'decode_state' );
	$state = $encode->invoke( $google, [ 'redirect' => home_url() ] ); $_COOKIE['ezd_g_csrf'] = str_repeat( 'a', 32 );
	audit_assert( is_array( $decode->invoke( $google, $state . '.' . $_COOKIE['ezd_g_csrf'] ) ), 'Valid browser-bound state rejected' );
	audit_assert( null === $decode->invoke( $google, 'forged.' . $state . '.' . $_COOKIE['ezd_g_csrf'] ), 'Forged state accepted' );
	audit_assert( null === $decode->invoke( $google, $state . '.' . str_repeat( 'b', 32 ) ), 'Mismatched browser token accepted' );
	$login = new ReflectionMethod( $google, 'login_or_register_user' );
	audit_assert( false === $login->invoke( $google, [ 'email' => get_userdata( $admin )->user_email, 'verified_email' => false ] ) && 0 === get_current_user_id(), 'Unverified address authenticated' );
} );

audit_test( 'legacy visibility rejects author using a valid builder nonce', function () use ( $author, $admin ) {
	$id = audit_doc( [ 'post_author' => $admin ] );
	wp_set_current_user( $author );
	$_GET = [ 'doc_visibility' => $id, 'doc_visibility_type' => 'private', '_wpnonce' => wp_create_nonce( (string) $id ) ];
	audit_capture( [ audit_instance( eazyDocsPro\Admin\Doc_Visibility::class ), 'ezd_doc_visibility' ] );
	audit_assert( 'publish' === get_post_status( $id ), 'Another author changed visibility' );
} );
audit_test( 'legacy visibility rejects invalid statuses', function () use ( $admin ) {
	$id = audit_doc(); wp_set_current_user( $admin );
	$_GET = [ 'doc_visibility' => $id, 'doc_visibility_type' => 'trash', '_wpnonce' => wp_create_nonce( (string) $id ) ];
	audit_capture( [ audit_instance( eazyDocsPro\Admin\Doc_Visibility::class ), 'ezd_doc_visibility' ] );
	audit_assert( 'publish' === get_post_status( $id ), 'Invalid visibility accepted' );
} );
audit_test( 'section visibility preserves attached media and updates docs', function () use ( $admin ) {
	$root = audit_doc(); $child = audit_doc( [ 'post_parent' => $root ] );
	$media = audit_doc( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_parent' => $child ] );
	wp_set_current_user( $admin );
	$_GET = [ 'section_doc_visibility' => $root, 'doc_visibility_type' => 'private', 'doc_depth_one' => 'yes', '_wpnonce' => wp_create_nonce( (string) $root ) ];
	audit_capture( [ audit_instance( eazyDocsPro\Admin\Doc_Section_Visibility::class ), 'ezd_doc_section_visibility' ] );
	audit_assert( 'attachment' === get_post_type( $media ) && 'private' === get_post_status( $child ), 'Media converted or child not updated' );
} );
audit_test( 'role propagation does not overwrite another authors child', function () use ( $visibility, $author, $admin ) {
	$root = audit_doc(); $child = audit_doc( [ 'post_parent' => $root, 'post_author' => $admin ] );
	update_post_meta( $child, 'ezd_role_visibility', [ 'administrator' ] ); wp_set_current_user( $author );
	$_POST = [ 'ezd_role_visibility_nonce' => wp_create_nonce( 'ezd_role_visibility_save' ), 'ezd_role_visibility' => [ 'editor' ], 'ezd_apply_to_children' => '1' ];
	$visibility->save_meta_box( $root, get_post( $root ) );
	audit_assert( [ 'administrator' ] === get_post_meta( $child, 'ezd_role_visibility', true ), 'Child access settings overwritten' );
} );
audit_test( 'role propagation still updates owned descendants', function () use ( $visibility, $author ) {
	$root = audit_doc(); $child = audit_doc( [ 'post_parent' => $root ] ); wp_set_current_user( $author );
	$_POST = [ 'ezd_role_visibility_nonce' => wp_create_nonce( 'ezd_role_visibility_save' ), 'ezd_role_visibility' => [ 'editor' ], 'ezd_apply_to_children' => '1' ];
	$visibility->save_meta_box( $root, get_post( $root ) );
	audit_assert( [ 'editor' ] === get_post_meta( $child, 'ezd_role_visibility', true ), 'Owned descendant did not receive the visibility setting' );
} );
audit_test( 'core REST single read blocks role-restricted published content', function () {
	$id = audit_doc(); update_post_meta( $id, 'ezd_role_visibility', [ 'editor' ] );
	$response = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/docs/' . $id ) );
	audit_assert( 404 === $response->get_status() && false === strpos( wp_json_encode( $response->get_data() ), 'CONFIDENTIAL_AUDIT_BODY' ), 'REST disclosed content' );
} );
audit_test( 'REST include lists and totals exclude inherited restrictions', function () {
	$root = audit_doc(); $child = audit_doc( [ 'post_parent' => $root ] ); $open = audit_doc();
	update_post_meta( $root, 'ezd_role_visibility', [ 'editor' ] );
	$request = new WP_REST_Request( 'GET', '/wp/v2/docs' );
	$request->set_param( 'include', [ $root, $child, $open ] );
	$response = rest_do_request( $request );
	$data = $response->get_data();
	audit_assert( 200 === $response->get_status() && [ $open ] === array_column( $data, 'id' ) && 1 === (int) $response->get_headers()['X-WP-Total'], 'Restricted REST entries or counts leaked' );
} );
audit_test( 'REST visibility preserves role overrides and inheritance opt-outs', function () {
	$root = audit_doc( [ 'meta_input' => [ 'ezd_role_visibility' => [ 'editor' ] ] ] );
	$opt_out = audit_doc( [ 'post_parent' => $root, 'meta_input' => [ 'ezd_role_visibility_inherit' => '0' ] ] );
	$grandchild = audit_doc( [ 'post_parent' => $opt_out ] );
	$guest = audit_doc( [ 'post_parent' => $root, 'meta_input' => [ 'ezd_role_visibility' => [ 'guest' ] ] ] );
	$subscriber = audit_doc( [ 'post_parent' => $root, 'meta_input' => [ 'ezd_role_visibility' => [ 'subscriber' ] ] ] );
	$ids = [ $root, $opt_out, $grandchild, $guest, $subscriber ];
	$expected = array_values( array_filter( $ids, 'ezd_user_can_view_doc' ) );
	$request = new WP_REST_Request( 'GET', '/wp/v2/docs' ); $request->set_param( 'include', $ids );
	$response = rest_do_request( $request ); $actual = array_column( $response->get_data(), 'id' );
	sort( $expected ); sort( $actual );
	audit_assert( 200 === $response->get_status() && $expected === $actual, 'REST and frontend role decisions differ' );
} );
audit_test( 'authorized REST editor retains access', function () use ( $editor ) {
	$id = audit_doc(); update_post_meta( $id, 'ezd_role_visibility', [ 'editor' ] ); wp_set_current_user( $editor );
	$request = new WP_REST_Request( 'GET', '/wp/v2/docs/' . $id ); $request->set_param( 'context', 'edit' );
	audit_assert( 200 === rest_do_request( $request )->get_status(), 'Editor access denied' );
} );
audit_test( 'builder rejects reading another authors private tree', function () use ( $contributor, $admin, $builder ) {
	$id = audit_doc( [ 'post_status' => 'private', 'post_author' => $admin ] ); wp_set_current_user( $contributor );
	$request = new WP_REST_Request(); $request->set_param( 'parent_id', $id );
	$result = $builder->get_child_docs_data( $request );
	audit_assert( is_wp_error( $result ) && 403 === $result->get_error_data()['status'], 'Private tree readable by contributor' );
} );
audit_test( 'CSV export blocks protected roots and descendants', function () use ( $contributor ) {
	$root = audit_doc(); $protected = audit_doc( [ 'post_password' => 'secret' ] );
	$child = audit_doc( [ 'post_parent' => $root, 'post_password' => 'secret' ] );
	wp_set_current_user( $contributor );
	audit_assert( is_wp_error( EazyDocs\IO\Csv::export_to_string( $protected ) ), 'Protected root exported' );
	$csv = EazyDocs\IO\Csv::export_to_string( $root );
	audit_assert( false === strpos( $csv, get_the_title( $child ) ) && false !== strpos( $csv, get_the_title( $root ) ), 'Protected child exported or public root unavailable' );
} );
audit_test( 'an authorized editor can export protected documentation', function () use ( $editor ) {
	$id = audit_doc( [ 'post_password' => 'secret' ] ); wp_set_current_user( $editor );
	$csv = EazyDocs\IO\Csv::export_to_string( $id );
	audit_assert( is_string( $csv ) && false !== strpos( $csv, 'CONFIDENTIAL_AUDIT_BODY' ), 'Editor export denied' );
} );
audit_test( 'Markdown export blocks protected roots', function () use ( $contributor ) {
	$id = audit_doc( [ 'post_password' => 'secret' ] ); wp_set_current_user( $contributor );
	$result = EazyDocs\Markdown\Exporter::export_to_zip( $id );
	audit_assert( is_wp_error( $result ) && 'ezd_md_forbidden' === $result->get_error_code(), 'Protected Markdown root exported' );
} );
audit_test( 'vote endpoint rejects unrelated posts without metadata writes', function () {
	$id = audit_doc( [ 'post_type' => 'page' ] ); $_POST = [ 'post_id' => $id, 'type' => 'positive' ]; audit_nonce( 'eazydocs-ajax', 'security' );
	[ , $json ] = audit_capture( [ audit_instance( EazyDocs\Frontend\Ajax::class ), 'handle_feedback' ] );
	audit_assert( false === $json['success'] && '' === get_post_meta( $id, 'positive', true ), 'Unrelated post metadata changed' );
} );
audit_test( 'public voting on readable documentation remains available', function () {
	$id = audit_doc(); $_POST = [ 'post_id' => $id, 'type' => 'positive' ]; audit_nonce( 'eazydocs-ajax', 'security' );
	[ , $json ] = audit_capture( [ audit_instance( EazyDocs\Frontend\Ajax::class ), 'handle_feedback' ] );
	audit_assert( true === $json['success'] && 1 === (int) get_post_meta( $id, 'positive', true ), 'Public vote denied' );
} );
audit_test( 'feedback mail rejects unreadable private docs', function () use ( $admin, &$mails ) {
	$id = audit_doc( [ 'post_author' => $admin, 'post_status' => 'private' ] );
	$_POST = [ 'doc_id' => $id, 'email' => 'guest@example.test', 'subject' => 'Hello', 'message' => 'Feedback' ]; audit_nonce( 'eazydocs-ajax', 'security' );
	[ , $json ] = audit_capture( 'eazydocs_feedback_email' );
	audit_assert( false === $json['success'] && [] === $mails, 'Private feedback accepted or sent mail' );
} );
audit_test( 'selected feedback enforces configured role restrictions', function () use ( $contributor, $options ) {
	$id = audit_doc(); update_option( 'eazydocs_settings', array_merge( $options, [ 'selected-comment-roles' => [ 'editor' ] ] ) );
	wp_set_current_user( $contributor ); $_POST = [ 'post_id' => $id, 'content' => 'Feedback', 'para_id' => 'p1' ]; audit_nonce( 'eazydocs_local_nonce' );
	[ , $json ] = audit_capture( 'ezd_selected_save_comment' );
	audit_assert( false === $json['success'], 'Disallowed role submitted feedback' );
} );
audit_test( 'subscriptions reject arbitrary post IDs', function () use ( $users ) {
	$id = audit_doc( [ 'post_type' => 'page' ] ); wp_set_current_user( $users['subscriber'] );
	$_POST = [ 'post_id' => $id ]; audit_nonce( 'eazydocs_local_nonce' );
	[ , $json ] = audit_capture( 'ezd_auto_confirm_subscription' );
	audit_assert( false === $json['success'] && '' === get_post_meta( $id, 'ezd_subscription_confirmed', true ), 'Subscription metadata written to a page' );
} );
audit_test( 'disabled subscriptions reject direct requests', function () use ( $users, $options ) {
	$id = audit_doc(); update_option( 'eazydocs_settings', array_merge( $options, [ 'subscriptions' => false ] ) ); wp_set_current_user( $users['subscriber'] );
	$_POST = [ 'post_id' => $id ]; audit_nonce( 'eazydocs_local_nonce' );
	[ , $json ] = audit_capture( 'ezd_auto_confirm_subscription' );
	audit_assert( false === $json['success'], 'Disabled feature accepted a subscription' );
} );
audit_test( 'automatic subscriptions preserve other visitors pending confirmations', function () use ( $users ) {
	$id = audit_doc(); $pending = [ [ 'email' => 'pending@example.test', 'name' => 'Pending', 'token' => 'pending-token' ] ];
	update_post_meta( $id, 'ezd_subscription_data', maybe_serialize( $pending ) ); wp_set_current_user( $users['subscriber'] );
	$_POST = [ 'post_id' => $id ]; audit_nonce( 'eazydocs_local_nonce' );
	[ , $json ] = audit_capture( 'ezd_auto_confirm_subscription' );
	audit_assert( true === $json['success'] && $pending === maybe_unserialize( get_post_meta( $id, 'ezd_subscription_data', true ) ), 'Pending subscriptions overwritten' );
} );
audit_test( 'subscription confirmation replay is idempotent', function () {
	$id = audit_doc(); $token = wp_generate_password( 32, false );
	update_post_meta( $id, 'ezd_subscription_data', maybe_serialize( [ [ 'email' => 'confirm@example.test', 'name' => 'Confirm', 'token' => $token ] ] ) );
	$_POST = [ 'token_id' => $id, 'token' => $token ]; audit_nonce( 'eazydocs_local_nonce' );
	audit_capture( 'ezd_confirm_subscription' ); audit_capture( 'ezd_confirm_subscription' );
	$confirmed = maybe_unserialize( get_post_meta( $id, 'ezd_subscription_confirmed', true ) );
	audit_assert( 1 === count( $confirmed ), 'Replayed token duplicated subscriber records' );
} );
audit_test( 'different confirmation tokens do not duplicate the same email', function () {
	$id = audit_doc();
	update_post_meta( $id, 'ezd_subscription_confirmed', maybe_serialize( [ [ 'email' => 'confirm@example.test', 'name' => 'Confirm', 'token' => 'old-token' ] ] ) );
	update_post_meta( $id, 'ezd_subscription_data', maybe_serialize( [ [ 'email' => 'CONFIRM@example.test', 'name' => 'Confirm', 'token' => 'new-token' ] ] ) );
	$_POST = [ 'token_id' => $id, 'token' => 'new-token' ]; audit_nonce( 'eazydocs_local_nonce' );
	[ , $json ] = audit_capture( 'ezd_confirm_subscription' );
	audit_assert( true === $json['success'] && 'old-token' === $json['data']['token'], 'Confirmation did not return the canonical subscription token' );
	audit_assert( 1 === count( maybe_unserialize( get_post_meta( $id, 'ezd_subscription_confirmed', true ) ) ), 'Email duplicated across tokens' );
	$_POST['token'] = $json['data']['token']; $_REQUEST = $_POST;
	[ , $unsubscribed ] = audit_capture( 'ezd_unsubscription_create' );
	audit_assert( true === $unsubscribed['success'], 'Canonical confirmation token cannot unsubscribe' );
} );
audit_test( 'subscription form blocks protected docs before sending mail', function () use ( &$mails ) {
	$id = audit_doc( [ 'post_password' => 'secret' ] );
	$_POST = [ 'ezd_subscription_id' => $id, 'ezd_subscription_name' => 'Guest', 'ezd_subscription_email' => 'guest@example.test' ]; audit_nonce( 'eazydocs_local_nonce' );
	[ , $json ] = audit_capture( 'ezd_subscription_form' );
	audit_assert( false === $json['success'] && [] === $mails, 'Protected doc accepted subscription' );
} );
audit_test( 'readable public docs accept confirmation email requests', function () use ( &$mails ) {
	$id = audit_doc(); $_POST = [ 'ezd_subscription_id' => $id, 'ezd_subscription_name' => 'Guest', 'ezd_subscription_email' => 'guest@example.test' ]; audit_nonce( 'eazydocs_local_nonce' );
	[ , $json ] = audit_capture( 'ezd_subscription_form' );
	audit_assert( true === $json['success'] && 1 === count( $mails ), 'Valid subscription unavailable' );
} );
audit_test( 'subscription email requests are rate limited across documents', function () use ( &$mails ) {
	$first = audit_doc(); $second = audit_doc();
	$_POST = [ 'ezd_subscription_id' => $first, 'ezd_subscription_name' => 'Guest', 'ezd_subscription_email' => 'first@example.test' ]; audit_nonce( 'eazydocs_local_nonce' ); audit_capture( 'ezd_subscription_form' );
	$_POST['ezd_subscription_id'] = $second; $_POST['ezd_subscription_email'] = 'second@example.test'; $_REQUEST = $_POST;
	[ , $json ] = audit_capture( 'ezd_subscription_form' );
	audit_assert( false === $json['success'] && 1 === count( $mails ), 'Repeated mail requests were not limited' );
} );
audit_test( 'subscription updates do not email restricted child content to guests', function () use ( $users, &$mails ) {
	$root = audit_doc();
	update_post_meta( $root, 'ezd_subscription_confirmed', maybe_serialize( [
		[ 'name' => 'Guest', 'email' => 'guest@example.test', 'token' => 'guest-token' ],
		[ 'name' => 'Editor', 'email' => get_userdata( $users['editor'] )->user_email, 'token' => 'editor-token' ],
	] ) );
	$child = audit_doc( [ 'post_status' => 'draft', 'post_parent' => $root, 'meta_input' => [ 'ezd_role_visibility' => [ 'editor' ] ] ] );
	$mails = []; wp_update_post( [ 'ID' => $child, 'post_status' => 'publish' ] );
	$recipients = array_column( $mails, 'to' );
	audit_assert( ! in_array( 'guest@example.test', $recipients, true ) && in_array( get_userdata( $users['editor'] )->user_email, $recipients, true ), 'Restricted notification recipients incorrect' );
} );
audit_test( 'profile activity cache is filtered per viewer', function () use ( $author ) {
	$id = audit_doc(); update_post_meta( $id, 'ezd_role_visibility', [ 'editor' ] );
	wp_set_current_user( $author ); $owner_feed = ezdpro_get_activities_by_user( $author, 50 );
	audit_assert( in_array( 'doc_' . $id, array_column( $owner_feed['activities'], 'id' ), true ), 'Owner feed fixture not cached' );
	wp_set_current_user( 0 ); $public_feed = ezdpro_get_activities_by_user( $author, 50 );
	audit_assert( ! in_array( 'doc_' . $id, array_column( $public_feed['activities'], 'id' ), true ), 'Shared cache disclosed restricted activity' );
} );
audit_test( 'public contributions hide restricted docs', function () use ( $author ) {
	$id = audit_doc(); update_post_meta( $id, 'ezd_role_visibility', [ 'editor' ] );
	$data = ezdpro_get_contributions_by_user( $author, -1 );
	audit_assert( ! in_array( $id, array_column( $data, 'id' ), true ), 'Restricted contribution disclosed' );
} );
audit_test( 'assistant default list does not share restricted or protected excerpts', function () use ( $editor, $options ) {
	$hidden = audit_doc( [ 'meta_input' => [ 'ezd_role_visibility' => [ 'editor' ] ] ] );
	$protected = audit_doc( [ 'post_password' => 'secret', 'post_content' => 'PROTECTED_ASSISTANT_BODY' ] );
	update_option( 'eazydocs_settings', array_merge( $options, [ 'assistant_tab_settings' => [ 'assistant_breadcrumb' => '0' ] ] ) );
	$filter = function ( $args ) use ( $hidden, $protected ) { $args['post__in'] = [ $hidden, $protected ]; return $args; };
	add_filter( 'eazydocs_assistant_default_query_args', $filter );
	$assistant = audit_instance( eazyDocsPro\Frontend\Assistant::class ); wp_set_current_user( $editor );
	$authorized = $assistant->default_posts(); wp_set_current_user( 0 ); $public = $assistant->default_posts();
	remove_filter( 'eazydocs_assistant_default_query_args', $filter );
	audit_assert( false !== strpos( $authorized, get_the_title( $hidden ) ) && false === strpos( $public, get_the_title( $hidden ) ), 'Shared assistant HTML disclosed restricted content' );
	audit_assert( false === strpos( $public, 'PROTECTED_ASSISTANT_BODY' ), 'Assistant excerpt bypassed password' );
} );
audit_test( 'nested ordering rejects an unrelated parent before any writes', function () use ( $author ) {
	$page = audit_doc( [ 'post_type' => 'page' ] ); $root = audit_doc(); $child = audit_doc( [ 'post_parent' => $root ] ); wp_set_current_user( $author );
	$_POST = [ 'data' => wp_slash( wp_json_encode( [ [ 'id' => $page, 'children' => [ [ 'id' => $child ] ] ] ] ) ) ]; audit_nonce( 'eazydocs-admin-nonce', 'security' );
	[ , $json ] = audit_capture( [ audit_instance( EazyDocs\Admin\Admin::class ), 'nestable_callback' ] );
	audit_assert( false === $json['success'] && $root === (int) get_post( $child )->post_parent, 'Doc moved under unrelated post type' );
} );
audit_test( 'authorized nested ordering remains available', function () use ( $author ) {
	$root = audit_doc(); $first = audit_doc( [ 'post_parent' => $root ] ); $second = audit_doc( [ 'post_parent' => $root ] ); wp_set_current_user( $author );
	$_POST = [ 'data' => wp_slash( wp_json_encode( [ [ 'id' => $second ], [ 'id' => $first ] ] ) ) ]; audit_nonce( 'eazydocs-admin-nonce', 'security' );
	[ , $json ] = audit_capture( [ audit_instance( EazyDocs\Admin\Admin::class ), 'nestable_callback' ] );
	audit_assert( true === $json['success'] && 1 === (int) get_post( $second )->menu_order && $root === (int) get_post( $second )->post_parent, 'Authorized reorder failed' );
} );
audit_test( 'mixed-author trees allow reordering owned docs around unchanged foreign items', function () use ( $author, $admin ) {
	$root = audit_doc( [ 'post_author' => $admin ] );
	$first = audit_doc( [ 'post_parent' => $root, 'menu_order' => 1 ] );
	$second = audit_doc( [ 'post_parent' => $root, 'menu_order' => 2 ] );
	$foreign = audit_doc( [ 'post_parent' => $root, 'post_author' => $admin, 'menu_order' => 3 ] );
	wp_set_current_user( $author );
	$_POST = [ 'data' => wp_slash( wp_json_encode( [ [ 'id' => $second ], [ 'id' => $first ], [ 'id' => $foreign ] ] ) ) ]; audit_nonce( 'eazydocs-admin-nonce', 'security' );
	[ , $json ] = audit_capture( [ audit_instance( EazyDocs\Admin\Admin::class ), 'nestable_callback' ] );
	audit_assert( true === $json['success'] && 1 === (int) get_post( $second )->menu_order && 3 === (int) get_post( $foreign )->menu_order, 'Unchanged foreign item blocked an authorized reorder' );
} );
audit_test( 'configured documentation publishers can generate their own OnePage docs', function () use ( $users, $options ) {
	update_option( 'eazydocs_settings', array_merge( $options, [ 'docs-write-access' => [ 'administrator', 'subscriber' ] ] ) );
	ezd_sync_docs_capabilities( true ); wp_set_current_user( $users['subscriber'] );
	$id = audit_doc( [ 'post_author' => $users['subscriber'] ] );
	audit_assert( current_user_can( 'publish_docs' ) && ! current_user_can( 'publish_posts' ), 'Publisher fixture is invalid' );
	$_GET = [ 'make_onepage' => 'yes', 'parentID' => $id, '_wpnonce' => wp_create_nonce( 'ezd_make_onepage' ) ];
	audit_capture( [ audit_instance( EazyDocs\One_Page::class ), 'doc_one_page' ] );
	$created = get_posts( [ 'post_type' => 'onepage-docs', 'meta_key' => 'ezd_onepage_parent_id', 'meta_value' => $id, 'fields' => 'ids' ] );
	audit_assert( 1 === count( $created ), 'A documentation publisher cannot generate a OnePage doc' );
} );
audit_test( 'assistant search renders its empty state after access filtering', function () use ( $options ) {
	$keyword = 'RegressionNoMatch' . bin2hex( random_bytes( 4 ) );
	audit_doc( [ 'post_title' => $keyword, 'meta_input' => [ 'ezd_role_visibility' => [ 'editor' ] ] ] );
	update_option( 'eazydocs_settings', array_merge( $options, [ 'assistant_tab_settings' => [ 'docs_not_found' => 'Nothing matched your search' ] ] ) );
	$_POST = [ 'keyword' => $keyword ];
	[ , , $html ] = audit_capture( [ audit_instance( eazyDocsPro\Frontend\Search::class ), 'fetch_posts' ] );
	audit_assert( false !== strpos( $html, 'Nothing matched your search' ), 'Search displayed a blank panel instead of the empty state' );
} );
audit_test( 'assistant default list renders its empty state after access filtering', function () use ( $options ) {
	$id = audit_doc( [ 'meta_input' => [ 'ezd_role_visibility' => [ 'editor' ] ] ] );
	update_option( 'eazydocs_settings', array_merge( $options, [ 'assistant_tab_settings' => [ 'docs_not_found' => 'Nothing matched your search' ] ] ) );
	$filter = function ( $args ) use ( $id ) { $args['post__in'] = [ $id ]; return $args; };
	add_filter( 'eazydocs_assistant_default_query_args', $filter );
	$html = audit_instance( eazyDocsPro\Frontend\Assistant::class )->default_posts();
	remove_filter( 'eazydocs_assistant_default_query_args', $filter );
	audit_assert( false !== strpos( $html, 'Nothing matched your search' ), 'Assistant displayed a blank panel instead of the empty state' );
} );
audit_test( 'Markdown zip rejects excessive folder depth', function () {
	$file = wp_tempnam(); $zip = new ZipArchive(); $zip->open( $file, ZipArchive::OVERWRITE );
	$zip->addFromString( str_repeat( 'nested/', 25 ) . 'index.md', '# Title' ); $zip->close();
	$result = EazyDocs\Markdown\Importer::import_zip( $file ); unlink( $file );
	audit_assert( is_wp_error( $result ) && 'ezd_md_depth' === $result->get_error_code(), 'Deep archive accepted' );
} );
audit_test( 'Markdown zip rejects excessive decompressed size', function () {
	$file = wp_tempnam(); $zip = new ZipArchive(); $zip->open( $file, ZipArchive::OVERWRITE );
	for ( $i = 0; $i < 11; ++$i ) { $zip->addFromString( $i . '.md', str_repeat( 'a', 2 * 1024 * 1024 ) ); }
	$zip->close(); $result = EazyDocs\Markdown\Importer::import_zip( $file ); unlink( $file );
	audit_assert( is_wp_error( $result ) && 'ezd_md_total_size' === $result->get_error_code(), 'Compressed oversized archive accepted' );
} );
audit_test( 'ordinary Markdown archives preserve their hierarchy and formatting', function () use ( $author ) {
	wp_set_current_user( $author ); $root = audit_doc();
	$file = wp_tempnam(); $zip = new ZipArchive(); $zip->open( $file, ZipArchive::OVERWRITE );
	$zip->addFromString( 'guide/install.md', "# Installation\n\nUse **safe defaults**.\n\n- First step\n- Second step\n" ); $zip->close();
	$result = EazyDocs\Markdown\Importer::import_zip( $file, $root ); unlink( $file );
	audit_assert( ! is_wp_error( $result ) && 2 === $result['created'] && 0 === $result['failed'], 'Ordinary archive import failed' );
	$folder = get_posts( [ 'post_type' => 'docs', 'post_status' => 'draft', 'post_parent' => $root ] );
	$children = get_posts( [ 'post_type' => 'docs', 'post_status' => 'draft', 'post_parent' => $folder[0]->ID ] );
	audit_assert( 1 === count( $folder ) && 1 === count( $children ) && false !== strpos( $children[0]->post_content, '<strong>safe defaults</strong>' ), 'Hierarchy or Markdown formatting changed' );
} );
audit_test( 'REST visibility filtering does not load excluded document bodies', function () {
	$hidden = [];
	for ( $i = 0; $i < 12; ++$i ) {
		$id = audit_doc( [ 'post_content' => str_repeat( 'large-content ', 80000 ), 'meta_input' => [ 'ezd_role_visibility' => [ 'editor' ] ] ] );
		$hidden[] = $id; clean_post_cache( $id );
	}
	$open = audit_doc();
	$before = memory_get_usage();
	$request = new WP_REST_Request( 'GET', '/wp/v2/docs' ); $request->set_param( 'include', array_merge( $hidden, [ $open ] ) );
	$response = rest_do_request( $request );
	audit_assert( 200 === $response->get_status() && [ $open ] === array_column( $response->get_data(), 'id' ), 'Visibility result incorrect' );
	audit_assert( memory_get_usage() - $before < 6 * 1024 * 1024, 'Excluded document bodies were loaded while filtering' );
} );
echo "$checks integration checks, $failures failures\n";
exit( $failures ? 1 : 0 );
