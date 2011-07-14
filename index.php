<?php
// if AJAX request, only output content section
if(!defined('DOING_AJAX')) { get_header(); }
simpledark_content_header();
if(have_posts()) {
while(have_posts()) {
	the_post();
?>
		<div id="post-<?php the_ID(); ?>" <?php post_class();?>>
<?php
	// give some extra metadata (aka is-sticky, is-protected, is-private, etc.)
	simpledark_extra_post_meta();
	$post_format = function_exists('get_post_format'); // WP3.0 compatibility
	$format = get_post_format();
	if(!$post_format || !in_array($format, array('aside', 'link', 'status', 'quote', 'image', 'video', 'audio'))) {
?>
			<h2 class="post-title"><a rel="bookmark" title="<?php printf(__('Permanent Link to %s', THEME_NAME), the_title_attribute('echo=0')); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
<?php
	}
	if(!$post_format || !in_array($format, array('aside', 'link', 'status', 'quote', 'gallery', 'image', 'video', 'audio', 'chat'))) {
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php if(!is_page()) { the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); } else { printf(__('Posted by %s', THEME_NAME), simpledark_get_the_author_posts_link()); } ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
<?php
		the_post_thumbnail();
		the_content('&raquo;' . __('Read More', THEME_NAME));
		$args = array(
			'before'			=> '<div class="post-pages">' . __('Pages:', THEME_NAME),
			'after'				=> '</div>',
			'nextpagelink'		=> __('Next Page', THEME_NAME),
			'previouspagelink'	=> __('Previous Page', THEME_NAME)
		);
		wp_link_pages($args);
?>
			</div>
<?php if($GLOBALS['simpledark_options']['show_tags_on_archive_pages']) { // show post tags according to theme option ?>
			<div class="post-info"><?php if(get_the_tags()) { the_tags('&lt; ', ', ', ' &gt;'); } else { echo('&lt; ' . __('NO TAGS', THEME_NAME) . ' &gt;'); } ?></div>
<?php } ?>
<?php
	} else if($format == 'aside') {
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
<?php the_content(); ?>
			</div>
<?php
	} else if($format == 'link') {
		$desc = false;
		$anchor_text = $href = filter_var(trim($post->post_content), FILTER_VALIDATE_URL);
		$matches = array();
		if(!$href && preg_match('/<a [^>]*href=[\"\']?([^\"\'\s]+)/i', $post->post_content, $matches)) {
			$anchor_text = $href = $matches[1];
			$desc = get_the_excerpt();
		}
		if($post->post_title) {
			$anchor_text = $post->post_title;
		}
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
<?php
		if($href) {
?>
				<h2><a class="link-title" href="<?php echo $href; ?>"><?php echo $anchor_text; ?></a></h2>
<?php
		}
		if($desc) {
?>

				<div class="link-desc"><?php echo $desc; ?></div>
<?php
		}
?>
			</div>
<?php
	} else if($format == 'status') {
?>
			<div class="status">
				<div class="status-avatar"><?php echo get_avatar($post->post_author, 48); ?></div>
				<div class="status-content">
					<div class="status-author"><?php echo simpledark_get_the_author_posts_link(); ?></div>
					<div class="status-text"><?php the_content(); ?></div>
					<div class="status-timestamp"><?php the_time(simpledark_time_format('datetime')); ?></div>
				</div>
			</div>
<?php
	} else if($format == 'quote') {
		$matches = array();
		if(preg_match('/<cite(?:>|[^>]+>)((?!<\/cite>)[\w\W]*)<\/cite>/i',  $post->post_content, $matches)) {
			$source = $matches[1];
		} else if($post->post_title) {
			$source = $post->post_title;
		} else {
			$source = null;
		}
		$quote_type = $quote = null;
		$matches = array();
		if(preg_match('/<(blockquote|q)(?:>|[^>]+>)((?!<\/(?:blockquote|q)>)[\w\W]*)?<\/(?:blockquote|q)>/i',  $post->post_content, $matches)) {
			$quote_type = $matches[1];
			$quote = $matches[2];
		} else {
			$quote = $post->post_content;
			$quote_type = 'blockquote';
		}
		$quote = "<$quote_type><span class=\"quote-left\">&ldquo;</span>" . trim($quote, '\u0022\u2018\u2019\u0027\u201c\u201d') . "<span class=\"quote-right\">&rdquo;</span></$quote_type>";
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
				<?php echo $quote; ?>
<?php
		if($source) {
?>
				<div class="quote-source">- <cite><?php echo $source; ?></cite></div>
<?php
		}
?>
			</div>
<?php
	} else if($format == 'gallery') {
		$to_show = 3;
		$columns = 3;
		if(preg_match('/\[gallery(?:\]|[^\]]+)[^\]]\]*/', $post->post_content)) { // WordPress Gallery (Images all attached to the post)
			$attachments = array_values( get_children( array( 'post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
			$image_count = count($attachments);
			simpledark_gallery_meta();
?>
			<div class="entry">
<?php
			simpledark_gallery($to_show, $columns);
			if($image_count > $to_show) {
?>
				<p>
					<span class="more-link"><a href="<?php the_permalink(); ?>"><?php echo '&raquo;' . __('View All', THEME_NAME); ?></a></span>
				</p>
<?php
			}
		} else { // Maybe external images
			$matches = array();
			$image_count = preg_match_all('/(?:\[caption(?:\]|[^\]]+)caption="(?P<caption>[^"]+)"[^\]]*])?(?:<a [^>]*href=[\"\']?(?P<link>[^\"\'\s]+)[^>]*>)?\s*<img (?:[^>]*src=[\"\']?(?P<source>[^\"\'\s]+)|[^>]*title=[\"\']?(?P<title>[^\"\'\s]+)|[^>]*alt=[\"\']?(?P<alt>[^\"\'\s]+)|[^>]*width=[\"\']?(?P<width>[^\"\'\s]+)|[^>]*height=[\"\']?(?P<height>[^\"\'\s]+))*/i', $post->post_content, $matches, PREG_SET_ORDER);
			$attachments = array_values( get_children( array( 'post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
			if($image_count == count($attachments)) { // No external images
				simpledark_gallery_meta();
?>
			<div class="entry">
<?php
				simpledark_gallery($to_show, $columns);
				if($image_count > $to_show) {
?>
				<p>
					<span class="more-link"><a href="<?php the_permalink(); ?>"><?php echo '&raquo;' . __('View All', THEME_NAME); ?></a></span>
				</p>
<?php
				}
			} else { // Contains some images not attached to this post
				simpledark_gallery_meta();
				$attachments = array();
				foreach($matches as $match) {
					$attachments[] = array(
						'link'		=> $match['link'],
						'source'	=> $match['source'],
						'title'		=> $match['title'],
						'alt'		=> $match['alt'],
						'caption'	=> $match['caption'],
						'width'		=> $match['width'],
						'height'	=> $match['height']
					);
				}
?>
			<div class="entry">
<?php
				simpledark_gallery($to_show, $columns);
				if($image_count > $to_show) {
?>
				<p>
					<span class="more-link"><a href="<?php the_permalink(); ?>"><?php echo '&raquo;' . __('View All', THEME_NAME); ?></a></span>
				</p>
<?php
				}
			}
		}
?>
			</div>
<?php
	} else if($format == 'image') {
		$match = array();
		$title = null;
		$source = filter_var(trim($post->post_content), FILTER_VALIDATE_URL);
		if(!$source && preg_match('/<img [^>]*src=[\"\']?([^\"\'\s]+)/i', $post->post_content, $match)) {
			$source = $match[1];
		}
		if($post->post_title) {
			$title = $post->post_title;
		}
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
<?php
		if($source) {
?>
				<?php if($title) { ?><h2 class="media-title"><?php echo $title; ?></h2><?php } ?>
				<p><a href="<?php the_permalink(); ?>"><img src="<?php echo $source; ?>"<?php if($title) { echo ' alt="' . $title . '" title="' . $title . '"'; } ?> /></a></p>
<?php
		}
?>
			</div>
<?php
	} else if($format == 'video' || $format == 'audio') {
		$match = array();
		$title = $content = null;
		if(preg_match('/(<(video|audio|object|embed|iframe)[^>]+(?:>(?:(?!<\/\2>)[\w\W]*)?<\/\2>|\/>))/i', apply_filters('the_content', apply_filters('get_the_content', $post->post_content)), $match)) {
			$content = $match[1];
		}
		if($post->post_title) {
			$title = $post->post_title;
		}
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
<?php
		if($content) {
?>
				<?php if($title) { ?><h2 class="media-title"><?php echo $title; ?></h2><?php } ?>
				<p><?php echo $content; ?></p>
<?php
		}
?>
			</div>
<?php
	} else if($format == 'chat') {
		$lines = preg_split("/[\r\n]+/", $post->post_content);
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
				<table>
					<tbody>
<?php
		if(is_array($lines)) {
			foreach($lines as $line) {
				if(trim($line) != '')
?>
						<tr><td><?php echo $line; ?></td></tr>
<?php
			}
		}
?>
					</tbody>
				</table>
			</div>
<?php
	}
?>
		</div>
<?php
}
if(simpledark_is_paged()) { // create page navigation if there are multiple pages, support 2 plugins by default (aka WP-Pagenavi and Paginator)
?>
		<div class="pagenavi">
<?php
if(function_exists('wp_paginator')) {
	wp_paginator();
} else if(function_exists('wp_pagenavi')) {
	wp_pagenavi();
} else {
?>
			<span class="previous-page"><?php next_posts_link('&laquo; ' . __('Older Posts', THEME_NAME)); ?></span><span class="next-page"><?php previous_posts_link(__('Newer Posts', THEME_NAME) . ' &raquo;'); ?></span>
<?php
}
?>
		</div>
<?php
}
}
else { // no posts found and suggest to go backward
?>
		<div class="no-post"><?php printf(__('No posts found. Why not <a title="Go Back" href="%1$s"%2$s>go back to the last page</a> ?', THEME_NAME), (defined('DOING_AJAX'))? '#' : $_SERVER['HTTP_REFERER'], (defined('DOING_AJAX'))? 'onclick="loadContent(contentCache, $(\'#content\')); searched = false;"' : ''); ?></div>
<?php
}
?>
<?php
// if AJAX request, only output content section
if(!isset($_GET['action'])) { get_footer(); }
?>