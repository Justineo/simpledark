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
?>
			<h2 class="post-title"><a rel="bookmark permalink" title="<?php printf(__('Permanent Link to %s', THEME_NAME), the_title_attribute('echo=0')); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="post-meta"><?php the_time(simpledark_time_format('date')); ?> / <?php if(!is_page()) { the_author_posts_link(); _e(' posted in ', THEME_NAME); the_category(', '); } else { printf(__('Posted by %s', THEME_NAME), simpledark_get_the_author_posts_link()); } ?> / <?php if(function_exists('the_views')) { the_views(); echo ' / '; } if(post_password_required()) { _e('X Comments', THEME_NAME); } else { comments_popup_link(__('No Comments', THEME_NAME), __('1 Comment', THEME_NAME), __('% Comments', THEME_NAME), '', __('Comments Off', THEME_NAME)); } edit_post_link(__('Edit', THEME_NAME), ' / '); ?></div>
			<div class="entry">
<?php the_content(); ?>
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
<?php if(is_single()) { ?>
			<div class="post-info"><?php if(get_the_tags()) { the_tags('&lt; ', ', ', ' &gt;'); } else { echo('&lt; ' . __('NO TAGS', THEME_NAME) . ' &gt;'); } ?></div>
<?php } ?>
		</div>
<?php if(is_single()) { ?>
		<div class="pagenavi">
			<span class="previous-page"><?php next_post_link('%link', '&laquo; %title'); ?></span><span class="next-page"><?php previous_post_link('%link', '%title &raquo;'); ?></span>
		</div>
<?php } ?>
		<div id="reaction">
<?php comments_template('', true); ?>
		</div>
<?php
}
?>
<?php get_footer(); ?>