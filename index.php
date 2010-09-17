<?php
// if AJAX request, only output content section
if(!isset($_GET['action'])) { get_header(); }
else { defined('DOING_AJAX') || define('DOING_AJAX', true); }
?>
<?php
simpledark_content_header();
if(have_posts()) {
while(have_posts()) {
	the_post();
?>
		<div id="post-<?php the_ID(); ?>" <?php post_class();?>>
<?php
	// give some extra metadata (aka is-sticky, is-protected, is-private, etc.)
	simpledark_extra_post_meta();
?>
			<h2 class="post-title"><a rel="bookmark" title="<?php printf(__('Permanent Link to %s', THEME_NAME), the_title_attribute('echo=0')); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php if($post->post_type != page) { the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); } else { printf(__('Posted by %s', THEME_NAME), simpledark_get_the_author_posts_link()); } ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), 'comment-link', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
<?php the_post_thumbnail(); ?>
<?php the_content('&raquo;' . __('Read More', THEME_NAME)); ?>
<?php
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
		<div class="no-post"><?php printf(__('No posts found. Why not <a title="Go Back" href="%1$s"%2$s>go back to the last page</a> ?', THEME_NAME), (defined('DOING_AJAX') && DOING_AJAX)? '#' : $_SERVER['HTTP_REFERER'], (defined('DOING_AJAX') && DOING_AJAX)? 'onclick="loadContent(contentCache, $(\'#content\')); searched = false;"' : ''); ?></div>
<?php
}
?>
<?php
// if AJAX request, only output content section
if(!isset($_GET['action'])) { get_footer(); }
?>