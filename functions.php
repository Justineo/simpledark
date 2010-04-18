<?php

define('THEME_NAME', 'SimpleDark');
define('SIMPLEDARK_OPTIONS', 'simpledark_options');
define('SIMPLEDARK_UTILITIES', TEMPLATEPATH . '/utilities');
define('SIMPLEDARK_TEMPLATES', TEMPLATEPATH . '/templates');
define('SIMPLEDARK_PLUGIN_SUPPORT', TEMPLATEPATH . '/plugin-support');

load_theme_textdomain(THEME_NAME, TEMPLATEPATH . '/languages');

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

simpledark_register_sidebar();

add_action('widgets_init', 'simpledark_load_widgets');

function simpledark_scripts() {
	if(!is_admin()) {
		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', null, '1.4.2');
		wp_enqueue_script('scrollto', get_bloginfo('template_directory') . '/js/scrollto.min.js', 'jquery');
	}
}
add_action('wp_print_scripts', 'simpledark_scripts');

function simpledark_admin_style() {
	wp_register_style('simpledark_admin', get_bloginfo('template_directory') . '/admin.css');
	wp_enqueue_style('simpledark_admin');
}
add_action('admin_print_styles', 'simpledark_admin_style');

function simpledark_feed_additional_info($content) {
	global $authordata;
	$options = &$GLOBALS['simpledark_options'];
	if(is_feed()) {
		$before = $options['custom_feed_info_before'];
		$after = $options['custom_feed_info_after'];
		if(!preg_match('/^\w*$/', $before))
			$before = '<div class="feed-before" style="margin:15px 0; clear:both;">' . $options['custom_feed_info_before'] . '</div>';
		if(!preg_match('/^\w*$/', $after))
			$after = '<div class="feed-after" style="margin:15px 0; clear:both;">' . $options['custom_feed_info_after'] . '</div>';
		$author_name = the_author($idmode, false);
		$author_link = '<a href="' . get_author_link(0, $authordata->ID, $authordata->user_nicename) . '" title="' . sprintf(__("Posts by %s"), wp_specialchars(the_author($idmode, false))) . '">' . $author_name . '</a>';
		$blog_link = '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a>';
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

function simpledark_category_menu( $args = array() ) {
	$defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( isset($args['show_home']) && ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . get_option('home') . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_categories($list_args) );

	if ( $menu )
		$menu = '<ul>' . $menu . '</ul>';

	$menu = '<div class="' . esc_attr($args['menu_class']) . '">' . $menu . "</div>\n";
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

function simpledark_the_title($title) {
	return $title == ''? ('[' . __('Untitled', THEME_NAME) . ']') : $title;
}
add_filter('the_title', 'simpledark_the_title', 10);

function simpledark_get_the_author_posts_link() {
	global $authordata;
	$link = sprintf(
		'<a href="%1$s" title="%2$s">%3$s</a>',
		get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
		esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
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

function simpledark_filter_cancel_comment_reply_link($link) {
	return preg_replace('/href="([^"]+)/', 'href="#', $link);
}
add_filter('cancel_comment_reply_link', 'simpledark_filter_cancel_comment_reply_link');

function simpledark_allowed_tags() {
	global $allowedtags;
	if($GLOBALS['simpledark_options']['enable_comment_images'])
		$allowedtags['img'] = array('src' => array(), 'alt' => array());
}
add_action('init', 'simpledark_allowed_tags');

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

function simpledark_extra_post_meta() {
	global $post;
	$extra = array();
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
	if('' != $extra_string) {
?>
			<p class="extra-meta">[<?php echo $extra_string; ?>]</p>
<?php
	}
}

function simpledark_script_params($ajax_enabled = true) {
?>
<script type="text/javascript">
	var scriptParams = new Array();
	scriptParams['blogurl'] = '<?php bloginfo('url'); ?>';
	scriptParams['tmpldir'] = '<?php bloginfo('template_directory'); ?>';
<?php
	if($ajax_enabled) {
		if(function_exists('wp_recentcomments')) {
			$options = get_option('widget_recentcomments');
			$args_binding = 'limit--' . $options['number']
						. '|length--' . $options['length']
						. '|post--' . ($options['post'] ? 'true' : 'false')
						. '|pingback--' . ($options['pingback'] ? 'true' : 'false')
						. '|trackback--' . ($options['trackback'] ? 'true' : 'false')
						. '|avatar--' . ($options['avatar'] ? 'true' : 'false')
						. '|avatar_size--' . $options['avatarsize']
						. '|avatar_position--' . $options['avatarposition']
						. '|avatar_default--' . $options['avatardefault']
						. '|navigator--' . ($options['navigator'] ? 'true' : 'false')
						. '|administrator--' . ($options['administrator'] ? 'true' : 'false')
						. '|smilies--' . ($options['smilies'] ? 'true' : 'false');
?>
	scriptParams['rcparams'] = '<?php echo $args_binding; ?>';
<?php
		}
		if(get_option('thread_comments')) {
?>
	scriptParams['threadcmnts'] = '1';
<?php
		}
	}
?>
</script>
<?php
}

function fail($s) {
	if(!defined('DOING_AJAX')){
		wp_die($s);
		return;
	}
	header('HTTP/1.0 500 Internal Server Error');
	header('Content-Type: text/plain');
	if(is_string($s)){
		die($s);
	}else{
		$s;
		die;
	}
}

!is_admin() || include_once SIMPLEDARK_UTILITIES . '/admin.php';

?>