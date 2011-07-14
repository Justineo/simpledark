<?php
$comment = get_comment($_GET['id']);
$comment_page_number_cache = array();
$paged = (bool)(get_comment_pages_count() > 1);

function simpledark_ajax_comment_pager() {
	if (isset($_GET['action']) && $_GET['action'] == 'cpage_ajax') {

		nocache_headers();
		// global variables
		global $wp_query, $wpdb, $authordata, $comment, $user_ID, $wp_rewrite, $comment_page_number_cache, $paged;

		// post ID
		$post_id = $_GET["post"];

		// comment page ID
		$page_id = $_GET["page"];

		// callback method name
		$callback = 'simpledark_comment';

		// type
		$type = '&type=comment';

		// set as singular (is_single || is_page || is_attachment)
		$wp_query->is_singular = true;

		// admin data
		$authordata = get_userdata(1);

		// comment author username
		if (isset($_COOKIE['comment_author_'.COOKIEHASH])) {
			$comment_author = apply_filters('pre_comment_author_name', $_COOKIE['comment_author_'.COOKIEHASH]);
			$comment_author = stripslashes($comment_author);
			$comment_author = esc_attr($comment_author);
		}

		// comment author email
		if (isset($_COOKIE['comment_author_email_'.COOKIEHASH])) {
			$comment_author_email = apply_filters('pre_comment_author_email', $_COOKIE['comment_author_email_'.COOKIEHASH]);
			$comment_author_email = stripslashes($comment_author_email);
			$comment_author_email = esc_attr($comment_author_email);
		}

		// comments
		if ($user_ID) {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date", $post_id, $user_ID));
		} else if ( empty($comment_author) ) {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1' ORDER BY comment_date", $post_id));
		} else {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date", $post_id, $comment_author, $comment_author_email));
		}

		// base url of page links
		$baseLink = '';
		if ($wp_rewrite->using_permalinks()) {
			$baseLink = '&base=' . user_trailingslashit(get_permalink($post_id) . 'comment-page-%#%', 'commentpaged');
		}

		// response
		wp_list_comments('callback=' . $callback . '&page=' . $page_id . '&per_page=' . get_option('comments_per_page') . $type, $comments);
?><!-- AJAX Comment Paginate Data Separator -->
				<?php paginate_comments_links('current=' . $page_id . $baseLink); ?>
				<span id="cp_post_id" style="display:none"><?php echo $post_id; ?></span>
				<input id="comment-page-number" type="hidden" value="<?php echo esc_attr( json_encode($comment_page_number_cache) ); ?>" />
<?php
		die();
	}
}
add_action('init', 'simpledark_ajax_comment_pager');

function simpledark_ajax_comment_getter() {
	if (isset($_GET['action']) && $_GET['action'] == 'cget_ajax' && $_GET['id'] != '') {
		
		nocache_headers();
		$comment = get_comment($_GET['id']);
		if(!$comment) {
			fail(__('Cannot retrieve the comment.', THEME_NAME));
		} else {
			simpledark_comment($comment, null,null);
		}
		exit;
	}
}
add_action('init', 'simpledark_ajax_comment_getter');

function simpledark_new_comment( $commentdata ) {
	$commentdata = apply_filters('preprocess_comment', $commentdata);

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	if ( isset($commentdata['user_ID']) )
		$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
	elseif ( isset($commentdata['user_id']) )
		$commentdata['user_id'] = (int) $commentdata['user_id'];

	$commentdata['comment_parent'] = isset($commentdata['comment_parent']) ? absint($commentdata['comment_parent']) : 0;
	$parent_status = ( 0 < $commentdata['comment_parent'] ) ? wp_get_comment_status($commentdata['comment_parent']) : '';
	$commentdata['comment_parent'] = ( 'approved' == $parent_status || 'unapproved' == $parent_status ) ? $commentdata['comment_parent'] : 0;

	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
	$commentdata['comment_agent']     = substr($_SERVER['HTTP_USER_AGENT'], 0, 254);

	$commentdata['comment_date']     = current_time('mysql');
	$commentdata['comment_date_gmt'] = current_time('mysql', 1);

	$commentdata = wp_filter_comment($commentdata);

	$commentdata['comment_approved'] = simpledark_allow_comment($commentdata);

	$comment_ID = wp_insert_comment($commentdata);

	do_action('comment_post', $comment_ID, $commentdata['comment_approved']);

	if ( 'spam' !== $commentdata['comment_approved'] ) { // If it's spam save it silently for later crunching
		if ( '0' == $commentdata['comment_approved'] )
			wp_notify_moderator($comment_ID);

		$post = &get_post($commentdata['comment_post_ID']); // Don't notify if it's your own comment

		if ( get_option('comments_notify') && $commentdata['comment_approved'] && ( ! isset( $commentdata['user_id'] ) || $post->post_author != $commentdata['user_id'] ) )
			wp_notify_postauthor($comment_ID, isset( $commentdata['comment_type'] ) ? $commentdata['comment_type'] : '' );
	}

	return $comment_ID;
}

function simpledark_allow_comment($commentdata) {
	global $wpdb;
	extract($commentdata, EXTR_SKIP);

	// Simple duplicate check
	// expected_slashed ($comment_post_ID, $comment_author, $comment_author_email, $comment_content)
	$dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_approved != 'trash' AND ( comment_author = '$comment_author' ";
	if ( $comment_author_email )
		$dupe .= "OR comment_author_email = '$comment_author_email' ";
	$dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
	if ( $wpdb->get_var($dupe) ) {
		do_action( 'comment_duplicate_trigger', $commentdata );
		if ( defined('DOING_AJAX') )
			fail( __('Sorry, please do not send duplicate comments!', THEME_NAME) );

		wp_die( __('Sorry, please do not send duplicate comments!', THEME_NAME) );
	}

	do_action( 'check_comment_flood', $comment_author_IP, $comment_author_email, $comment_date_gmt );

	if ( isset($user_id) && $user_id) {
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

	$approved = apply_filters( 'pre_comment_approved', $approved, $commentdata );
	return $approved;
}

?>