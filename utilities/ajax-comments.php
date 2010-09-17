<?php
$comment = get_comment($_GET['id']);

function simpledark_ajax_comment_pager() {
	if (isset($_GET['action']) && $_GET['action'] == 'cpage_ajax') {

		defined('DOING_AJAX') || define('DOING_AJAX', true);

		nocache_headers();
		// global variables
		global $wp_query, $wpdb, $authordata, $comment, $user_ID, $wp_rewrite;

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
			$comment_author = attribute_escape($comment_author);
		}

		// comment author email
		if (isset($_COOKIE['comment_author_email_'.COOKIEHASH])) {
			$comment_author_email = apply_filters('pre_comment_author_email', $_COOKIE['comment_author_email_'.COOKIEHASH]);
			$comment_author_email = stripslashes($comment_author_email);
			$comment_author_email = attribute_escape($comment_author_email);
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
?><!-- AJAX Comment Paginate Data Separator --><?php		
		if(paginate_comments_links('echo=0')) {
?>
				<?php paginate_comments_links('current=' . $page_id . $baseLink); ?>
				<span id="cp_post_id" style="display:none"><?php echo $post_id; ?></span>
<?php
		}
		die();
	}
}
add_action('init', 'simpledark_ajax_comment_pager');

function simpledark_ajax_comment_getter() {
	if (isset($_GET['action']) && $_GET['action'] == 'cget_ajax' && $_GET['id'] != '') {
		
		defined('DOING_AJAX') || define('DOING_AJAX', true);

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
?>