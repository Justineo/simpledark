<?php
/**
 * Handles Comment Post to WordPress and prevents duplicate comment posting.
 *
 */
if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	header('Allow: POST');
	header('HTTP/1.1 405 Method Not Allowed');
	header('Content-Type: text/plain');
	exit;
}

defined('DOING_AJAX') || define('DOING_AJAX', true);

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/../../../wp-load.php' );

function ajax_new_comment( $commentdata ) {
	$commentdata = apply_filters('preprocess_comment', $commentdata);

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	$commentdata['user_ID']         = (int) $commentdata['user_ID'];

	$commentdata['comment_parent'] = absint($commentdata['comment_parent']);
	$parent_status = ( 0 < $commentdata['comment_parent'] ) ? wp_get_comment_status($commentdata['comment_parent']) : '';
	$commentdata['comment_parent'] = ( 'approved' == $parent_status || 'unapproved' == $parent_status ) ? $commentdata['comment_parent'] : 0;

	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
	$commentdata['comment_agent']     = $_SERVER['HTTP_USER_AGENT'];

	$commentdata['comment_date']     = current_time('mysql');
	$commentdata['comment_date_gmt'] = current_time('mysql', 1);

	$commentdata = wp_filter_comment($commentdata);

	$commentdata['comment_approved'] = ajax_allow_comment($commentdata);

	$comment_ID = wp_insert_comment($commentdata);

	do_action('comment_post', $comment_ID, $commentdata['comment_approved']);

	if ( 'spam' !== $commentdata['comment_approved'] ) { // If it's spam save it silently for later crunching
		if ( '0' == $commentdata['comment_approved'] )
			wp_notify_moderator($comment_ID);

		$post = &get_post($commentdata['comment_post_ID']); // Don't notify if it's your own comment

		if ( get_option('comments_notify') && $commentdata['comment_approved'] && $post->post_author != $commentdata['user_ID'] )
			wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
	}

	return $comment_ID;
}

function ajax_allow_comment($commentdata) {
	global $wpdb;
	extract($commentdata, EXTR_SKIP);

	// Simple duplicate check
	// expected_slashed ($comment_post_ID, $comment_author, $comment_author_email, $comment_content)
	$dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
	if ( $comment_author_email )
		$dupe .= "OR comment_author_email = '$comment_author_email' ";
	$dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
	if ( $wpdb->get_var($dupe) ) {
		fail( __('Please don\'t send duplicate comment.', THEME_NAME) );
	}

	do_action( 'check_comment_flood', $comment_author_IP, $comment_author_email, $comment_date_gmt );

	if ( $user_id ) {
		$userdata = get_userdata($user_id);
		$user = new WP_User($user_id);
		$post_author = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d LIMIT 1", $comment_post_ID));
	}

	if ( isset($userdata) && ( $user_id == $post_author || $user->has_cap('moderate_comments') ) ) {
		// The author and the admins get respect.
		$approved = 1;
	 } else {
		// Everyone else's comments will be checked.
		if ( check_comment($comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent, $comment_type) )
			$approved = 1;
		else
			$approved = 0;
		if ( wp_blacklist_check($comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent) )
			$approved = 'spam';
	}

	$approved = apply_filters('pre_comment_approved', $approved);
	return $approved;
}

function simpledark_too_frequent_comment() {
	fail(__('You are posting comments too quickly.', THEME_NAME));
}

add_action('comment_flood_trigger', 'simpledark_too_frequent_comment');

nocache_headers();

$comment_post_ID = (int) $_POST['comment_post_ID'];

$status = $wpdb->get_row( $wpdb->prepare("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = %d", $comment_post_ID) );

if ( empty($status->comment_status) ) {
	do_action('comment_id_not_found', $comment_post_ID);
	fail(__('Error: comment ID not found.', THEME_NAME));
	exit;
} elseif ( !comments_open($comment_post_ID) ) {
	do_action('comment_closed', $comment_post_ID);
//	wp_die( __('Sorry, comments are closed for this item.') );
	fail(__('Sorry, comments are closed for this item.'));
} elseif ( in_array($status->post_status, array('draft', 'pending') ) ) {
	do_action('comment_on_draft', $comment_post_ID);
	exit;
} else {
	do_action('pre_comment_on_post', $comment_post_ID);
}

$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
$comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
$comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;

// If the user is logged in
$user = wp_get_current_user();
if ( $user->ID ) {
	if ( empty( $user->display_name ) )
		$user->display_name=$user->user_login;
	$comment_author       = $wpdb->escape($user->display_name);
	$comment_author_email = $wpdb->escape($user->user_email);
	$comment_author_url   = $wpdb->escape($user->user_url);
	if ( current_user_can('unfiltered_html') ) {
		if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
			kses_remove_filters(); // start with a clean slate
			kses_init_filters(); // set up the filters
		}
	}
} else {
	if ( get_option('comment_registration') || 'private' == $status->post_status )
//		wp_die( __('Sorry, you must be logged in to post a comment.') );
		fail(__('Sorry, you must be logged in to post a comment.', THEME_NAME));
}

$comment_type = '';

if ( get_option('require_name_email') && !$user->ID ) {
	if ( 6 > strlen($comment_author_email) || '' == $comment_author )
//		wp_die( __('Error: please fill the required fields (name, email).') );
		fail(__('Error: please fill the required fields (name, email).', THEME_NAME));
	elseif ( !is_email($comment_author_email))
//		wp_die( __('Error: please enter a valid email address.') );
		fail(__('Error: please enter a valid email address.', THEME_NAME));
}

if ( '' == $comment_content )
//	wp_die( __('Error: please type a comment.') );
	fail(__('Error: please type a comment.', THEME_NAME));

$comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

$comment_id = ajax_new_comment( $commentdata );

$comment = get_comment($comment_id);
if ( !$user->ID ) {
	$comment_cookie_lifetime = apply_filters('comment_cookie_lifetime', 30000000);
	setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
	setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
	setcookie('comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
}

$GLOBALS['comment'] = $comment;
$options = &$GLOBALS['SIMPLEDARK_OPTIONS'];
if(!$options['strict_comment'] && $comment->comment_author_email == get_the_author_email()) {
	$classes = get_comment_class('bypostauthor');
} else {
	$classes = get_comment_class();
}
$comment_class = 'class="' . join(' ', $classes) . '"';
include(SIMPLEDARK_TEMPLATES . '/comment.php');
?>
<!-- AJAX Comment Data Separator --><?php _e('Comment submitted!', THEME_NAME); ?>