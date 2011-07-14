<?php
/**
 * Handles AJAX Comment Post to WordPress and prevents duplicate comment posting.
 */

if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	header('Allow: POST');
	header('HTTP/1.1 405 Method Not Allowed');
	header('Content-Type: text/plain');
	exit;
}

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/../../../wp-load.php' );

nocache_headers();

$comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;

$post = get_post($comment_post_ID);

if ( empty($post->comment_status) ) {
	do_action('comment_id_not_found', $comment_post_ID);
	exit;
}

// get_post_status() will get the parent status for attachments.
$status = get_post_status($post);

$status_obj = get_post_status_object($status);

if ( !comments_open($comment_post_ID) ) {
	do_action('comment_closed', $comment_post_ID);
	fail( __('Sorry, comments are closed for this item.') );
} elseif ( 'trash' == $status ) {
	do_action('comment_on_trash', $comment_post_ID);
	exit;
} elseif ( !$status_obj->public && !$status_obj->private ) {
	do_action('comment_on_draft', $comment_post_ID);
	exit;
} elseif ( post_password_required($comment_post_ID) ) {
	do_action('comment_on_password_protected', $comment_post_ID);
	exit;
} else {
	do_action('pre_comment_on_post', $comment_post_ID);
}

$comment_author       = ( isset($_POST['author']) )			 ? trim(strip_tags($_POST['author'])) : null;
$comment_author_email = ( isset($_POST['email']) )			 ? trim($_POST['email']) : null;
$comment_author_url   = ( isset($_POST['url']) )			 ? trim($_POST['url']) : null;
$comment_content      = ( isset($_POST['comment']) )		 ? trim($_POST['comment']) : null;
$comment_ID			  = ( isset($_POST['comment_edit_ID']) ) ? trim($_POST['comment_edit_ID']) : null;

$is_new_comment = !$comment_ID;

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
	if ( get_option('comment_registration') || 'private' == $status )
		fail( __('Sorry, you must be logged in to post a comment.') );
}

$comment_type = '';

if ( get_option('require_name_email') && !$user->ID ) {
	if ( 6 > strlen($comment_author_email) || '' == $comment_author )
		fail( __('Error: please fill the required fields (name, email).') );
	elseif ( !is_email($comment_author_email))
		fail( __('Error: please enter a valid email address.') );
}

if ( '' == $comment_content )
	fail( __('Error: please type a comment.') );

$comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

if($is_new_comment) {

	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

	$comment_id = simpledark_new_comment( $commentdata );

	$comment = get_comment($comment_id);

} else { // updating a comment

	if ( !simpledark_user_can_edit_comment($comment_ID)) {
		fail( __('Error: you cannot edit this comment.', THEME_NAME) );
	}

	$old_comment = get_comment($comment_ID);
	if($user->ID) { // logged-in user editing comment
		$commentdata = compact('comment_content', 'comment_ID');
	} else {
		$commentdata = compact('comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_ID');
	}

	if(wp_update_comment($commentdata)){
		$comment = get_comment($comment_ID);
	} else {
		fail( __('Error: fail to update the comment.', THEME_NAME) );
	}

}

$GLOBALS['comment_author_info'] = array();
if ( !$user->ID ) {
	$comment_cookie_lifetime = apply_filters('comment_cookie_lifetime', 30000000);
	setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
	setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
	setcookie('comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
	$comment_author_info['comment_author'] = $comment->comment_author;
	$comment_author_info['comment_author_email'] = $comment->comment_author_email;
	$comment_author_info['comment_author_url'] = $comment->comment_author_url;
}

$GLOBALS['comment'] = $comment;

if ( get_option('thread_comments') ) {
	$comment_depth = 1;
	$ancestor = $comment;
	while($ancestor->comment_parent != 0){
		$comment_depth++;
		$ancestor = get_comment($ancestor->comment_parent);
	}
	$depth = $comment_depth;
	$args = array( 'max_depth' => 5 );
}


include(SIMPLEDARK_TEMPLATES . '/comment.php');
$meta = array();
$meta['message'] = $is_new_comment ? __('Comment submitted!', THEME_NAME) : __('Changes committed!', THEME_NAME);
$meta['type'] = $is_new_comment ? 'new' : 'edit';
?>
<!-- AJAX Comment Data Separator --><?php echo json_encode($meta); ?>