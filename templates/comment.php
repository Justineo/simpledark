<?php
	$options = $GLOBALS['simpledark_options'];

	$post = get_post($comment->comment_post_ID);
	$email = get_the_author_meta('user_email', $post->post_author);
	
	$authordata = get_userdata(3);
	
	if($comment->comment_author_email == $email) {
		$classes = get_comment_class('bypostauthor');
	} else {
		$classes = get_comment_class('byreader');
	}
	if(!get_option('show_avatars')) $classes[] = 'no-avatar';
	$comment_class = 'class="' . join(' ', $classes) . '"';
	
	if(!get_option('thread_comments') /*|| $depth >= $args['max_depth']*/) {
		$reply_link = ' / <a class="comment-reply-link" rel="nofollow" href="#respond" title="' . __('Reply comment', THEME_NAME) . '">' . __('Reply', THEME_NAME) . '</a>';
	} else if(is_array($args)) {
		$reply_link = get_comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => ' / ' ) ) );
	}
	if($options['comment_quick_edit'] && (simpledark_user_can_edit_comment($comment->comment_ID) || $is_new_comment)) {
		$edit_link = ' / <a class="comment-edit-link" rel="nofollow" ' . (get_option('thread_comments')? 'onclick="return addComment.moveForm(\'comment-' . $comment->comment_ID . '\', \'' . $comment->comment_ID . '\', \'respond\', \'' . $comment->comment_post_ID . '\', \'edit\')" ' : '') . 'href="#respond" title="' . __('Edit comment') . '">' . __('Edit', THEME_NAME) . '</a>';
	} else {
		$edit_link = get_edit_comment_link( $comment->comment_ID );
		if($edit_link) {
			$edit_link = '<a class="comment-edit-link" href="' . $edit_link . '" title="' . esc_attr__( 'Edit comment' ) . '">' . __('Edit', THEME_NAME) . '</a>';
			$edit_link = ' / ' . apply_filters( 'edit_comment_link', $edit_link, $comment->comment_ID );
		}
	}
?>
				<li <?php echo $comment_class; ?> id="comment-<?php comment_ID(); ?>">
					<?php echo get_avatar($comment, 28); ?><p class="comment-meta"><span class="datetime"><a href="<?php echo get_comment_link(); ?>" class="permalink"><?php comment_time(simpledark_time_format('datetime')); ?></a></span><span class="author"><?php comment_author_link(); ?></span><span class="actions">&nbsp;<?php echo $reply_link; echo $edit_link; ?></span></p>
					<div id="inline-<?php comment_ID(); ?>" class="hide"><textarea class="comment-text" cols="1" rows="1"><?php echo $comment->comment_content; ?></textarea></div>
					<div class="comment-body">
<?php
	if(!$comment->comment_approved) {
?>
						<p class="notice"><?php _e('Your comment is awaiting moderation.', THEME_NAME); ?></p>
<?php
	}
	global $paged;
//	if($paged) {
		$comment_text = get_comment_text();
		global $comment_page_number_cache;
		preg_match_all('/<a href="#comment-(?P<cid>\d+)/', $comment_text, $out);
		foreach($out['cid'] as $cid) {
			if($cid != -1) {
				$comment_page_number_cache[$cid] = simpledark_get_comment_page_number($cid);
			}
		}
//	}
?>
						<?php comment_text(); ?>
					</div>
