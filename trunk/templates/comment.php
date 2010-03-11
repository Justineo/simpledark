<?php
	if(defined('DOING_AJAX')) {
		$post = get_post($comment->comment_post_ID);
		$email = get_the_author_meta('user_email', $post->post_author);
	}
	else {
		$email = get_the_author_email();
	}
	if(!$options['strict_comment'] && $comment->comment_author_email == $email) {
		$classes = get_comment_class('bypostauthor');
	} else {
		$classes = get_comment_class('byreader');
	}
	if(!get_option('show_avatars')) $classes[] = 'no-avatar';
	$comment_class = 'class="' . join(' ', $classes) . '"';
?>
				<li <?php echo $comment_class; ?> id="comment-<?php comment_ID(); ?>">
					<?php echo get_avatar($comment, 28); ?><p class="comment-meta"><span class="datetime"><?php comment_time(simpledark_time_format('datetime')); ?></span><span class="author"><?php comment_author_link(); ?></span><span class="actions">&nbsp; / <a class="reply-button" rel="nofollow" href="#comment" title="<?php _e('Reply to this comment', THEME_NAME) ?>"><?php _e('Reply', THEME_NAME); ?></a><?php edit_comment_link(__('Edit', THEME_NAME), ' / '); ?></span></p>
					<div class="comment-body">
						<p class="notice"><?php if(!$comment->comment_approved) {_e('Your comment is awaiting moderation.', THEME_NAME);} ?></p>
						<?php comment_text(); ?>
					</div>
