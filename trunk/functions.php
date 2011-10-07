<?php

define('THEME_NAME', 'SimpleDark');
define('SIMPLEDARK_OPTIONS', 'simpledark_options');
define('SIMPLEDARK_UTILITIES', TEMPLATEPATH . '/utilities');
define('SIMPLEDARK_TEMPLATES', TEMPLATEPATH . '/templates');
define('SIMPLEDARK_PLUGIN_SUPPORT', TEMPLATEPATH . '/plugin-support');
define('SIMPLEDARK_DEBUG', false);

// Make theme available for translation
// Translations can be filed in the /languages/ directory
load_theme_textdomain(THEME_NAME, TEMPLATEPATH . '/languages');

// This theme styles the visual editor with editor-style.css to match the theme style.
add_editor_style();

// This theme uses post thumbnails
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 200, 150, true );

// Add default posts and comments RSS feed links to head
add_theme_support( 'automatic-feed-links' );

// Add support for post formats
add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

if ( ! isset( $content_width ) ) {
	$content_width = 586;
}

function simpledark_include_all($dir){
	$dir = realpath($dir);
	if($dir){
		$files = scandir($dir);
		sort($files);
		foreach($files as $file){
			if($file == '.' || $file == '..'){
				continue;
			}elseif(preg_match('/\.php$/i', $file)){
				include_once $dir.'/'.$file;
			}
		}
	}
}

simpledark_include_all(SIMPLEDARK_UTILITIES);
simpledark_include_all(SIMPLEDARK_PLUGIN_SUPPORT);

$GLOBALS['simpledark_options'] = SimpleDarkOptions::getInstance();

function simpledark_check_ajax() {
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') || isset($_REQUEST['action']) && endsWith($_REQUEST['action'], '_ajax')) {
		define('DOING_AJAX', true);
	}
}
add_action('init', 'simpledark_check_ajax');

function simpledark_register_sidebar() {
	if (function_exists('register_sidebar')) {
		register_sidebar(array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
			'name' => 'SimpleDark Sidebar'
		));
	}
}
add_action('init', 'simpledark_register_sidebar');

add_action('widgets_init', 'simpledark_load_widgets');

function simpledark_register_nav_menu() {
	if(function_exists('register_nav_menu')) {
		register_nav_menu('top-nav', __('Top Navigation Menu', THEME_NAME));
	}
}
add_action('init', 'simpledark_register_nav_menu');

function simpledark_scripts() {
	if(!is_admin()) {
		$suffix = SIMPLEDARK_DEBUG ? '' : '.min';
		$options = &$GLOBALS['simpledark_options'];
		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', get_template_directory_uri() . "/js/jquery-1.6.4.min.js", null, '1.6.4');
		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_deregister_script( 'comment-reply' );
			wp_register_script( 'comment-reply', get_template_directory_uri() . "/js/simpledark-threaded-comment$suffix.js", 'jquery' );
			wp_enqueue_script( 'comment-reply' );
		}
		wp_enqueue_script('scrollto', get_template_directory_uri() . '/js/scrollto.min.js', 'jquery');
		wp_enqueue_script('autoresize', get_template_directory_uri() . '/js/autoresize.min.js', 'jquery', '1.04');
		wp_enqueue_script('simpledark-base', get_template_directory_uri() . "/js/simpledark-base$suffix.js", 'jquery', null, true);
		if($options['enable_ajax']) {
			wp_enqueue_script('simpledark-ajax', get_template_directory_uri() . "/js/simpledark-ajax$suffix.js", 'jquery', null, true);
		}
	}
}
add_action('wp_print_scripts', 'simpledark_scripts');

function simpledark_admin_scripts() {
	$suffix = SIMPLEDARK_DEBUG ? '' : '.min';
	wp_enqueue_script('autoresize', get_template_directory_uri() . '/js/autoresize.min.js', 'jquery', '1.04');
	wp_enqueue_script('simpledark-admin', get_template_directory_uri() . "/js/simpledark-admin$suffix.js", 'jquery', null, true);
}
add_action('admin_enqueue_scripts', 'simpledark_admin_scripts');

function simpledark_admin_style() {
	wp_register_style('simpledark_admin', get_template_directory_uri() . '/admin.css');
	wp_enqueue_style('simpledark_admin');
}
add_action('admin_print_styles', 'simpledark_admin_style');

function simpledark_feed_additional_info($content) {
	global $authordata;
	$options = &$GLOBALS['simpledark_options'];
	$before = '';
	$after = '';
	if(is_feed()) {
		$before = $options['custom_feed_info_before'];
		$after = $options['custom_feed_info_after'];
		if(!preg_match('/^\w*$/', $before))
			$before = '<div class="feed-before" style="margin:15px 0; clear:both;">' . $options['custom_feed_info_before'] . '</div>';
		if(!preg_match('/^\w*$/', $after))
			$after = '<div class="feed-after" style="margin:15px 0; clear:both;">' . $options['custom_feed_info_after'] . '</div>';
		$author_name = the_author($idmode, false);
		$author_link = '<a href="' . get_author_posts_url(0, $authordata->ID, $authordata->user_nicename) . '" title="' . sprintf(__("Posts by %s", THEME_NAME), esc_html(the_author($idmode, false))) . '">' . $author_name . '</a>';
		$blog_link = '<a href="' . home_url() . '">' . get_bloginfo('name') . '</a>';
		$feed_url = get_bloginfo('rss2_url');
		$post_url = get_permalink();
		$before = str_replace('%AUTHOR_NAME%', $author_name, $before);
		$before = str_replace('%AUTHOR_LINK%', $author_link, $before);
		$before = str_replace('%BLOG_LINK%', $blog_link, $before);
		$before = str_replace('%FEED_URL%', $feed_url, $before);
		$before = str_replace('%POST_URL%', $post_url, $before);
		$after = str_replace('%AUTHOR_NAME%', $author_name, $after);
		$after = str_replace('%AUTHOR_LINK%', $author_link, $after);
		$after = str_replace('%BLOG_LINK%', $blog_link, $after);
		$after = str_replace('%FEED_URL%', $feed_url, $after);
		$after = str_replace('%POST_URL%', $post_url, $after);
	}
	return $before . $content . $after;
}
add_filter('the_content', 'simpledark_feed_additional_info');

function simpledark_menu( $args = array(), $show_type = 0 ) {
	$options = &$GLOBALS['simpledark_options'];
	$defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'top-menu-window', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_menu_args', $args );
	$args['menu_class'] = 'top-menu-window';

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( isset($args['show_home']) && ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home', THEME_NAME);
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . home_url() . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', $options['top_category_menu']? wp_list_categories($list_args) : wp_list_pages($list_args) );

	if ( $menu )
		$menu = '<div class="' . esc_attr($args['menu_class']) . '">' . '<ul>' . $menu . '</ul>' . '</div>';

	$menu = apply_filters( 'wp_page_menu', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}

// $type - 'time', 'date', 'datetime', 'day', 'month', 'year'
function simpledark_time_format($type) {
	$options = &$GLOBALS['simpledark_options'];
	$format;
	switch($type) {
		case 'time':
			$format = get_option('time_format');
			break;
		case 'date':
			$format = get_option('date_format');
			break;
		case 'datetime':
			$format = get_option('time_format') . ', ' . get_option('date_format');
			break;
		case 'month':
			$format = __('F, Y', THEME_NAME);
			break;
		case 'year':
			$format = __('Y', THEME_NAME);
			break;
		default:
			$format = get_option('date_format');
	}
	return $format;
}

function simpledark_content_header() {
	include(SIMPLEDARK_TEMPLATES . '/content-header.php');
}

function simpledark_get_host($url) {
	$info = parse_url(trim($url));
	return trim($info[host] ? $info[host] : array_shift(explode('/', $info[path], 2))); 
}

function simpledark_is_paged(){
	global $wp_query;
	$posts_per_page = $wp_query->query_vars['posts_per_page'];
	$found_posts = $wp_query->found_posts;
	$paged = $wp_query->query_vars['paged'];
	if($found_posts/$posts_per_page > 1 || $paged){
		return true;
	}else{
		return false;
	}
}

function simpledark_post_title($title) {
	return $title == ''? ('[' . __('Untitled', THEME_NAME) . ']') : $title;
}
add_filter('the_title', 'simpledark_post_title', 10);
add_filter('single_post_title', 'simpledark_post_title', 10);

function simpledark_get_the_author_posts_link() {
	global $authordata;
	$link = sprintf(
		'<a href="%1$s" title="%2$s">%3$s</a>',
		get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
		esc_attr( sprintf( __( 'Posts by %s', THEME_NAME ), get_the_author() ) ),
		get_the_author()
	);
	return apply_filters( 'the_author_posts_link', $link );
}

function simpledark_clear_title_format() {
	return '%s';
}
add_filter('protected_title_format', 'simpledark_clear_title_format', 10);
add_filter('private_title_format', 'simpledark_clear_title_format', 10);

function simpledark_refine_content_text_box($content) {
	$refined = preg_replace('/(<input [^>]*class=")([^>]*type="(?:password|text)")/i', '\\1textbox \\2', $content);
	$refined = preg_replace('/(<input [^>]*type="(?:password|text)"[^>]*class=")([^"]+")/i', '\\1textbox \\2', $refined);
	return preg_replace('/(<input )((?!class)[^>]*type="(?:password|text)"(?!class))/i', '\\1 class="textbox"\\2', $refined);
}
add_filter('the_content', 'simpledark_refine_content_text_box', 10);

function simpledark_refine_content_submit($content) {
	$refined = preg_replace('/(<input [^>]*class=")([^>]*type="submit")/i', '\\1submit \\2', $content);
	$refined = preg_replace('/(<input [^>]*type="submit"[^>]*class=")([^"]+")/i', '\\1submit \\2', $refined);
	return preg_replace('/(<input )((?!class)[^>]*type="submit"(?!class))/i', '\\1 class="submit"\\2', $refined);
}
add_filter('the_content', 'simpledark_refine_content_submit', 10);

function simpledark_filter_more_link($link) {
	return '<span class="more-link">' . str_replace(' class="more-link"', '', $link) . '</span>';
}
add_filter('the_content_more_link', 'simpledark_filter_more_link');

function simpledark_allowed_tags() {
	global $allowedtags;
	if($GLOBALS['simpledark_options']['enable_comment_images'])
		$allowedtags['img'] = array('src' => array(), 'alt' => array());
}
add_action('init', 'simpledark_allowed_tags');

function simpledark_show_allowed_tags() {
	$options = &$GLOBALS['simpledark_options'];
	if($options['show_allowed_tags']) {
		echo '<div class="allowed-tags"><p class="form-allowed-tags">' . __('<strong>Allowed Tags</strong> - You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes in your comment.', THEME_NAME) . '</p><p><code>' . allowed_tags() . '</code></p></div>';
	}
}
add_action('comment_form_logged_in_after', 'simpledark_show_allowed_tags');
add_action('comment_form_after_fields', 'simpledark_show_allowed_tags');

function simpledark_smileys_toolbar() {
	$options = &$GLOBALS['simpledark_options'];
	global $wpsmiliestrans;
	$smileys = array();
	if(!$options['show_smileys_toolbar']) {
		echo '<div class="smileys-toolbar">';
		foreach($wpsmiliestrans as $alt => $src_path) {
			if(!in_array($src_path, $smileys)) {
				$smileys[$src_path] = $alt;
			} else {
				if(strlen($alt) < strlen($smileys[$src_path])) {
					$smileys[$src_path] = $alt;
				}
			}
		}
		foreach($smileys as $src_path => $alt) {
			echo '<img class="smiley-select" alt=" ' . $alt . ' " title=" ' . $alt . ' " src="'  . home_url() . '/wp-includes/images/smilies/' . $src_path . '" />';
		}
		echo '</div>';
	}
}
if(get_option('use_smilies')) {
	add_action('comment_form_logged_in_after', 'simpledark_smileys_toolbar');
	add_action('comment_form_after_fields', 'simpledark_smileys_toolbar');
}

// comment count from iNove
function simpledark_comment_count( $commentcount ) {
	global $id;
	$_commnets = get_comments('status=approve&post_id=' . $id);
	$comments_by_type = &separate_comments($_commnets);
	return count($comments_by_type['comment']);
}
add_filter('get_comments_number', 'simpledark_comment_count', 0);

function simpledark_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	include(SIMPLEDARK_TEMPLATES . '/comment.php');
}

function simpledark_get_current_commenter($commenter) {
	global $comment_author_info;
	return isset($comment_author_info)? $comment_author_info : $commenter;
}
add_filter('wp_get_current_commenter', 'simpledark_get_current_commenter');

function simpledark_get_comment_edit_key($comment_ID = 0) {
	return md5(get_comment_author_email($comment_ID) . get_comment_author_IP($comment_ID));
}

function simpledark_generate_comment_cookie($comment_ID) {
	$comment_hash = md5($comment_ID . COOKIEHASH);
	setcookie('comment_edit_key_id_' . $comment_hash, simpledark_get_comment_edit_key($comment_ID), time() + (60 * 30), COOKIEPATH, COOKIE_DOMAIN);
}
add_action('comment_post', 'simpledark_generate_comment_cookie', 10, 1);
add_action('edit_comment', 'simpledark_generate_comment_cookie', 10, 1);

function simpledark_user_can_edit_comment($comment_ID = 0) {
	if(current_user_can('edit_comment', $comment_ID)) {
		return true;
	}
	$comment_hash = md5($comment_ID . COOKIEHASH);
	$edit_key = isset($_COOKIE['comment_edit_key_id_' . $comment_hash])? $_COOKIE['comment_edit_key_id_' . $comment_hash] : null;
	return $edit_key == simpledark_get_comment_edit_key($comment_ID);
}

$comment_page_number_cache = array();
function simpledark_get_comment_page_number($comment_id) {
	if(isset($comment_page_number_cache[$comment_id]) && $comment_page_number_cache[$comment_id] > 0) {
		return $comment_page_number_cache[$comment_id];
	}
	global $wpdb;
	$comment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_ID = %d", $comment_id));
	if(!$comment) { // no comment retrieved
		return -1;
	}
	if($comment->comment_parent != 0 && get_option('thread_comments')) { // thread comments enabled and the comment has a parent
		return simpledark_get_comment_page_number($comment->comment_parent); // recursively get page number
	} else { // calculate the page number
		if(get_option('thread_comments')) {
			$comment_root_position = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_parent = 0 AND comment_approved = 1 AND comment_ID <= %d", $comment->comment_post_ID, $comment_id));
			$page_number = ceil($comment_root_position / get_option('comments_per_page'));
		} else {
			$comment_position = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = 1 AND comment_ID <= %d", $comment->comment_post_ID, $comment_id));
			$page_number = ceil($comment_position / get_option('comments_per_page'));
		}
		$comment_page_number_cache[$comment_id] = $page_number;
		return $page_number;
	}
}

function simpledark_get_comment_link($useless, $comment) {
	global $wp_rewrite;
	
	if( get_option('page_comments') ) {
		$page = simpledark_get_comment_page_number( $comment->comment_ID );

		if ( $wp_rewrite->using_permalinks() )
			$link = user_trailingslashit( trailingslashit( get_permalink( $comment->comment_post_ID ) ) . 'comment-page-' . $page, 'comment' );
		else
			$link = add_query_arg( 'cpage', $page, get_permalink( $comment->comment_post_ID ) );
	} else {
		$link = get_permalink( $comment->comment_post_ID );
	}

	return $link . '#comment-' . $comment->comment_ID;
}
add_filter('get_comment_link', 'simpledark_get_comment_link', 10, 2);

function simpledark_edit_comment_id_field($fields) {
	$options = &$GLOBALS['simpledark_options'];
	if($options['comment_quick_edit']) {
		return $fields . "<input type='hidden' name='comment_edit_ID' id='comment_edit_ID' value='0' />\n";
	}
	return $fields;
}
add_filter('comment_id_fields', 'simpledark_edit_comment_id_field');

function simpledark_cancel_reply_link($link) {
	$options = &$GLOBALS['simpledark_options'];
	if($options['comment_quick_edit']) {
		return $link . '<span id="edit-or-reply-title" class="hide">' . __('Edit comment', THEME_NAME) . '</span><span id="edit-or-reply-cancel" class="hide">' . __('Cancel edit', THEME_NAME) . '</span>';
	}
	return $link;
}
add_filter('cancel_comment_reply_link', 'simpledark_cancel_reply_link');

function simpledark_extra_post_meta() {
	global $post;
	$extra = array();
	if(!is_attachment()) {
		if(!empty($post->post_password)) {
			$extra[] = __('Protected', THEME_NAME);
		} else if (isset($post->post_status) && 'private' == $post->post_status) {
			$extra[] = __('Private', THEME_NAME);
		}
		if(function_exists('is_stickied')) { // WP-Sticky is activated
			if(is_announcement()) { $extra[] = __('Announcement', THEME_NAME); }
			else if(is_stickied() || is_sticky()) { $extra[] = __('Sticky', THEME_NAME); }
		} else if (is_sticky()) { $extra[] = __('Sticky', THEME_NAME); }
		$extra_string = join(' / ', $extra);
	} else {
		$extra_string = __('Attachment', THEME_NAME);
	}
	if('' != $extra_string) {
?>
			<p class="extra-meta">[<?php echo $extra_string; ?>]</p>
<?php
	}
}

function simpledark_get_attachment_link($id = 0, $size = 'full', $permalink = false, $icon = false, $before, $after) {
	$id = intval($id);
	$_post = & get_post( $id );
	 
	if ( ('attachment' != $_post->post_type) || !$url = wp_get_attachment_url($_post->ID) )
	return __('Missing Attachment', THEME_NAME);

	if ( $permalink )
	$url = get_attachment_link($_post->ID);

	$post_title = esc_attr($_post->post_title);

	$link_text = $before . $_post->post_title . $after;

	return "<a href='$url' title='$post_title'>$link_text</a>";
}

function simpledark_adjacent_image_link($prev = true) {
	global $post;
	$post = get_post($post);
	$attachments = array_values(get_children("post_parent=$post->post_parent&post_type=attachment&post_mime_type=image&orderby=menu_order ASC, ID ASC"));

	foreach ( $attachments as $k => $attachment )
		if ( $attachment->ID == $post->ID )
			break;

	$k = $prev ? $k - 1 : $k + 1;
	$before = $prev ? '&laquo; ' : '';
	$after = $prev ? '' : ' &raquo;';
	
	if ( isset($attachments[$k]) )
		echo simpledark_get_attachment_link($attachments[$k]->ID, array(120, 90), true, true, $before, $after);
}

function simpledark_previous_image_link() {
	simpledark_adjacent_image_link(true);
}

function simpledark_next_image_link() {
	simpledark_adjacent_image_link(false);
}

function simpledark_gallery($to_show = 3, $columns = 3, $size = array('width' => 150, 'height' => 150)) {
	global $attachments;
	static $instance = 0;
	$instance++;
	$image_count = count($attachments);
	
	if( $image_count ) {
		$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
		$float = is_rtl() ? 'right' : 'left';
		$selector = "gallery-s{$instance}";
		$gallery_style = $gallery_div = '';
		if ( apply_filters( 'use_default_gallery_style', true ) )
			$gallery_style = "
				<style type='text/css'>
					#{$selector} {
						margin: auto;
					}
					#{$selector} .gallery-item {
						float: {$float};
						margin-top: 10px;
						text-align: center;
						width: {$itemwidth}%;
					}
					#{$selector} img {
						border: 2px solid #cfcfcf;
					}
					#{$selector} .gallery-caption {
						margin-left: 0;
					}
				</style>";
		$gallery_div = "<div id='$selector' class='gallery gallery-columns-{$columns}'>";
		$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );
		$i = 0;
		foreach($attachments as $attachment) {
			if($to_show > 0) {
				if(is_object($attachment)) {
					$link = wp_get_attachment_link($attachment->ID, 'thumbnail');
					$caption = $attachment->post_excerpt;
				} else if(is_array($attachment)) {
					$link = '';
					$image = '';
					if($attachment['source']) {
						$image = "<img";
						$resize = false;
						if($attachment['width'] && $attachment['height']) {
							$w = intval($attachment['width']);
							$h = intval($attachment['height']);
							$ratio = $w / $h;
							if($w > $size['width']) {
								$w = $size['width'];
								$h = floor($w / $ratio);
								$resize = true;
							}
							if($h > $size['height']) {
								$h = $size['height'];
								$w = floor($h * $ratio);
								$resize = true;
							}
							$image .= " width=\"$w\" height=\"$h\"";
						} else {
							$image .= " width=\"" . $size['width'] . "\"";
							$resize = true;
						}
						if(!$attachment['link'] && $resize) {
							$attachment['link'] = $attachment['source'];
						}
						$image .= " src=\"" . $attachment['source'] . "\" class=\"attachment-thumbnail\"";
						if($attachment['alt'])
							$image .= " alt=\"" . $attachment['alt'] . "\"";
						if($attachment['title'])
							$image .= " title=\"" . $attachment['title'] . "\"";
						$image .= ">";

						if($attachment['link']) {
							$link = "<a href=\"" . $attachment['link'] . "\"";
							if($attachment['title']) {
								$link .= " title=\"" . $attachment['title'] . "\"";
							}
							$link .= ">$image</a>";
						} else {
							$link = $image;
						}
						$caption = $attachment['caption'] ? $attachment['caption'] : '';
					} else
						continue;
				}

				$output .= "<dl class='gallery-item'>";
				$output .= "
						<dt class='gallery-icon'>
								$link
						</dt>";
				if ( trim($caption) ) {
						$output .= "
								<dd class='wp-caption-text gallery-caption'>
								" . wptexturize($caption) . "
								</dd>";
				}
				$output .= "</dl>";
				if ( $columns > 0 && ++$i % $columns == 0 )
					$output .= '<br style="clear: both" />';
			} else {
				break;
			}
			$to_show --;
		}
		if ( $columns > 0 && $i % $columns != 0 )
			$output .= '<br style="clear: both" />';
		$output .= "
						\n</div>";
		echo apply_filters('the_content', apply_filters('get_the_content', $output));
	}
}

function simpledark_gallery_meta() {
	global $image_count;
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } echo _n('1 image', sprintf('%s images', $image_count), $image_count, THEME_NAME); ?> / <?php if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
<?php
}

function simpledark_script_params() {
	$options = &$GLOBALS['simpledark_options'];
?>
<script type="text/javascript">
	var scriptParams = new Array();
	scriptParams['blogurl'] = '<?php echo home_url(); ?>';
	scriptParams['tmpldir'] = '<?php echo get_template_directory_uri(); ?>';
	scriptParams['quicksubmit'] = <?php echo $options['ctrl_enter_submit_comment'] ? 'true' : 'false'; ?>;
	scriptParams['atreply'] = <?php echo $options['enable_at_reply'] ? 'true' : 'false'; ?>;
	scriptParams['commentquickedit'] = <?php echo $options['comment_quick_edit'] ? 'true' : 'false'; ?>;
<?php
	if($options['hide_borders_for_small_images']) {
?>
	scriptParams['hidesmallimgbdr'] = true;
	scriptParams['smallimgwidth'] = <?php echo empty($options['small_image_width']) ? '0' : $options['small_image_width']; ?>;
	scriptParams['smallimgheight'] = <?php echo empty($options['small_image_height']) ? '0' : $options['small_image_height']; ?>;
	scriptParams['smallimglogic'] = '<?php echo $options['small_image_size_logic']; ?>';
<?php
	}
	if(get_option('thread_comments')) {
?>
	scriptParams['threadcmnts'] = true;
<?php
	}
	if($options['enable_ajax']) {
		if(function_exists('wp_recentcomments') && !function_exists('get_wp_recentcomments')) {
			$rc_options = get_option('widget_recentcomments');
			$args_binding = 'limit--' . $rc_options['number']
						. '|length--' . $rc_options['length']
						. '|post--' . ($rc_options['post'] ? 'true' : 'false')
						. '|pingback--' . ($rc_options['pingback'] ? 'true' : 'false')
						. '|trackback--' . ($rc_options['trackback'] ? 'true' : 'false')
						. '|avatar--' . ($rc_options['avatar'] ? 'true' : 'false')
						. '|avatar_size--' . $rc_options['avatarsize']
						. '|avatar_position--' . $rc_options['avatarposition']
						. '|avatar_default--' . $rc_options['avatardefault']
						. '|navigator--' . ($rc_options['navigator'] ? 'true' : 'false')
						. '|administrator--' . ($rc_options['administrator'] ? 'true' : 'false')
						. '|smilies--' . ($rc_options['smilies'] ? 'true' : 'false');
?>
	scriptParams['rcparams'] = '<?php echo $args_binding; ?>';
<?php
		}
?>
	var ajaxParams = new Array();
	ajaxParams['cmntpost'] = <?php echo $options['enable_ajax_commemt_post'] ? 'true' : 'false'; ?>;
	ajaxParams['cmntpagenav'] = <?php echo $options['enable_ajax_commemt_pagenav'] ? 'true' : 'false'; ?>;
	ajaxParams['postcntntpagnav'] = <?php echo $options['enable_ajax_post_content_pagenav'] ? 'true' : 'false'; ?>;
	ajaxParams['postpagenav'] = <?php echo $options['enable_ajax_post_pagenav'] ? 'true' : 'false'; ?>;
	ajaxParams['search'] = <?php echo $options['enable_ajax_search'] ? 'true' : 'false'; ?>;
	ajaxParams['cmntinfotxt'] = {
		'zero'	: '<?php _e('No Comments', THEME_NAME); ?>',
		'one'	: '<?php _e('1 Comment', THEME_NAME); ?>',
		'more'	: '<?php _e('% Comments', THEME_NAME); ?>'
	}
<?php
	}
?>
</script>
<?php
}

function endsWith($haystack, $needle){
    return strrpos($haystack, $needle) === strlen($haystack)-strlen($needle);
}

function fail($s) {
	if(!defined('DOING_AJAX')) {
		wp_die($s);
		return;
	}
	header('HTTP/1.0 500 Internal Server Error');
	header('Content-Type: text/plain');
	if(is_string($s)) {
		die($s);
	} else {
		$s;
		die;
	}
}

!is_admin() || include_once SIMPLEDARK_UTILITIES . '/admin.php';

?>