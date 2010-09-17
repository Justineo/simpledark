<?php
	$options = $GLOBALS['simpledark_options'];

	if(defined('DOING_AJAX')) {
		$post = get_post($comment->comment_post_ID);
		$email = get_the_author_meta('user_email', $post->post_author);
	}
	else {
		$email = get_the_author_email();
	}
	if(!(isset($options['strict_comment']) && $options['strict_comment']) && $comment->comment_author_email == $email) {
		$classes = get_comment_class('bypostauthor');
	} else {
		$classes = get_comment_class('byreader');
	}
	if(!get_option('show_avatars')) $classes[] = 'no-avatar';
	$comment_class = 'class="' . join(' ', $classes) . '"';
	
	if(!get_option('thread_comments') /*|| $depth >= $args['max_depth']*/) {
		$reply_link = ' / <a class="comment-reply-link" rel="nofollow" href="#comment" title="' . __('Reply to this comment', THEME_NAME) . '">' . __('Reply', THEME_NAME) . '</a>';
	} else {
		$reply_link = get_comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => ' / ' ) ) );
	}
?>
				<li <?php echo $comment_class; ?> id="comment-<?php comment_ID(); ?>">
					<?php echo get_avatar($comment, 28); ?><p class="comment-meta"><span class="datetime"><?php comment_time(simpledark_time_format('datetime')); ?></span><span class="author"><?php comment_author_link(); ?></span><span class="actions">&nbsp;<?php echo $reply_link; ?><?php edit_comment_link(__('Edit', THEME_NAME), ' / '); ?></span></p>
					<div class="comment-body">
						<p class="notice"><?php if(!$comment->comment_approved) {_e('Your comment is awaiting moderation.', THEME_NAME);} ?></p>
						<?php comment_text(); ?>
					</div>
