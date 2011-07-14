<?php simpledark_ajax_singular_pager(); ?>
<?php get_header(); ?>
<?php
simpledark_content_header();
if(have_posts()) {
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
			<h2 class="post-title"><a rel="bookmark permalink" title="<?php printf(__('Permanent Link to %s', THEME_NAME), the_title_attribute('echo=0')); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
<?php
	}
	if(!$post_format || !in_array($format, array('aside', 'link', 'status', 'quote', 'gallery', 'image', 'video', 'audio', 'chat'))) {
?>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php if(!is_page()) { the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); } else { printf(__('Posted by %s', THEME_NAME), simpledark_get_the_author_posts_link()); } ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
<?php
		the_content();
		$args = array(
			'before'			=> '<div class="post-pages">' . __('Pages:', THEME_NAME),
			'after'				=> '</div>',
			'nextpagelink'		=> __('Next Page', THEME_NAME),
			'previouspagelink'	=> __('Previous Page', THEME_NAME)
		);
		wp_link_pages($args);
?>
			</div>
<?php if(is_single()) { ?>
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
		$content = false;
		$anchor_text = $href = filter_var(trim($post->post_content), FILTER_VALIDATE_URL);
		$matches = array();
		if(!$href && preg_match('/<a [^>]*href=[\"\']?([^\"\'\s]+)/i', $post->post_content, $matches)) {
			$anchor_text = $href = $matches[1];
			$content = apply_filters('the_content', get_the_content());
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
		if($content) {
?>
				<p><?php echo $content; ?></p>
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
		if(preg_match('/\[gallery(?:\]|[^\]]+)[^\]]\]*/', $post->post_content)) { // WordPress Gallery (Images all attached to the post)
			$attachments = array_values( get_children( array( 'post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
			$image_count = count($attachments);
		} else {
			$matches = array();
			$image_count = preg_match_all('/(?:\[caption(?:\]|[^\]]+)caption="(?P<caption>[^"]+)"[^\]]*])?(?:<a [^>]*href=[\"\']?(?P<link>[^\"\'\s]+)[^>]*>)?\s*<img (?:[^>]*src=[\"\']?(?P<source>[^\"\'\s]+)|[^>]*title=[\"\']?(?P<title>[^\"\'\s]+)|[^>]*alt=[\"\']?(?P<alt>[^\"\'\s]+)|[^>]*width=[\"\']?(?P<width>[^\"\'\s]+)|[^>]*height=[\"\']?(?P<height>[^\"\'\s]+))*/i', $post->post_content, $matches, PREG_SET_ORDER);
		}
		simpledark_gallery_meta();
?>
			<div class="entry">
				<?php the_content(); ?>
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
				if(!empty($line)) {
?>
						<tr><td><?php echo $line; ?></td></tr>
<?php
				}
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
<?php if(is_single()) { ?>
		<div class="pagenavi">
			<span class="previous-page"><?php previous_post_link('%link', '&laquo; %title'); ?></span><span class="next-page"><?php next_post_link('%link', '%title &raquo;'); ?></span>
		</div>
<?php } ?>
		<div id="reaction">
<?php comments_template('', true); ?>
		</div>
<?php
}
?>
<?php get_footer(); ?>