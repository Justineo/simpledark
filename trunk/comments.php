<?php
	$pingbacks = $comments_by_type['pings'];
	$ping_count = count($pingbacks);
	$comment_count = count($comments) - $ping_count;
	$options = &$GLOBALS['simpledark_options'];
?>
			<div id="comments" class="section">
			<h4><?php _e('Comments', THEME_NAME); ?> (<span class="comment-count"><?php echo post_password_required()? __('X', THEME_NAME) : $comment_count; ?></span>)</h4>
<?php
	if(post_password_required()) {
?>
			<p class="message">&rsaquo; <?php _e('You must enter the password to view the comments.', THEME_NAME); ?></p>
<?php
	}
	else {
		if($comment_count > 0) {
?>
			<ol class="comment-list">
<?php wp_list_comments('type=comment&callback=simpledark_comment'); ?>
			</ol>
<?php
		} else {
?>
			<p class="message">&rsaquo; <?php _e('No comments yet.', THEME_NAME); ?></p>
<?php
		}
		if (get_option('page_comments')) {
			if (paginate_comments_links('echo=0')) {
?>
			<div class="commentnavi">
				<?php paginate_comments_links(); ?>
				<span id="cp_post_id" style="display:none"><?php the_ID(); ?></span>
			</div>
<?php
			}
		}
?>
			</div>
			<div id="respond" class="section">
			<h4><?php comment_form_title(__('Leave a Reply', THEME_NAME), __('Leave a Reply to %s', THEME_NAME)); ?></h4>
<?php
		if(comments_open()) {
?>
<?php
			if(get_option('comment_registration') && !is_user_logged_in()) {
?>
			<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', THEME_NAME), wp_login_url(get_permalink())); ?></p>
<?php
			} else {
?>
			<form id="comment-form" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="POST">
<!--				<div id="cancel-comment-reply" style="display:none; height:0;"><?php cancel_comment_reply_link() ?></div>-->
<?php
				if(is_user_logged_in()) {
?>
				<p class="message">&rsaquo; <?php printf(__('Logged in as %s.', THEME_NAME), '<a title="' . __('Manage Profile', THEME_NAME) . '" href="' . get_option('siteurl') . '/wp-admin/profile.php">' . $user_identity . '</a>'); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Logout', THEME_NAME); ?>"><?php _e('Logout', THEME_NAME); ?> &raquo;</a></p>
<?php
	}
				else if($comment_author) {
?>
				<p class="message">&rsaquo; <?php printf(__('Welcome back, %s.', THEME_NAME), $comment_author); ?> <a href="#" id="toggle-info"><?php _e('Change', THEME_NAME); ?> &raquo;</a></p>
<?php
				}
?>
				<div id="comment-author-info"<?php if(is_user_logged_in() || $comment_author) echo ' style="display:none"'; ?>>
					<p>
						<input id="author" name="author" tabindex="1" type="text" size="24" value="<?php echo $comment_author; ?>" />
						<label for="author"><?php _e('Name', THEME_NAME); ?> (*)</label>
					</p>
					<p>
						<input id="email" name="email" tabindex="2" type="text" size="24" value="<?php echo $comment_author_email; ?>" />
						<label for="email"><?php _e('Email', THEME_NAME); ?> (*)</label>
					</p>
					<p>
						<input id="url" name="url" tabindex="3" type="text" size="24" value="<?php echo $comment_author_url; ?>" />
						<label for="url"><?php _e('Website', THEME_NAME); ?></label>
					</p>
				</div>
<?php
		if($options['show_allowed_tags']) {
?>
				<div class="allowed-tags">
					<p><?php _e('<strong>Allowed Tags</strong> - You may use these tags in your comment.', THEME_NAME); ?></p>
					<p><code><?php echo allowed_tags(); ?></code></p>
				</div>
<?php
		}
?>
				<p id="comment-wrapper">
					<textarea id="comment" name="comment" tabindex="4"<?php if($options['ctrl_enter_submit_comment']) { echo ' class="quick-submit"'; } ?>></textarea>
				</p>
				<p>
					<input id="submit-button" type="submit" name="submit" tabindex="5" value="<?php _e('Submit Comment', THEME_NAME); if($options['ctrl_enter_submit_comment']) { echo ' (Ctrl+Enter)'; } ?>" />
				</p>
<?php
		comment_id_fields();
?>
			</form>
<?php
			}
		} else {
?>
			<p class="message">&rsaquo; <?php _e('Comments are closed.', THEME_NAME); ?></p>
<?php
		}
?>
			</div>
<?php
		if(!$options['hide_pingbacks']) {
//		if(pings_open()) {
?>
			<div id="pings" class="section">
			<h4><?php _e('Pingbacks', THEME_NAME); ?> (<?php echo post_password_required()? __('X', THEME_NAME) : $ping_count; ?>)</h4>
<?php
			if($ping_count > 0) {
?>
			<ol class="pingbacks">
<?php
				foreach($pingbacks as $comment) {
?>
				<li class="pingback" id="#comment-<?php comment_ID(); ?>">
					<div class="comment-meta"><span class="datetime"><?php comment_time(simpledark_time_format('datetime')); ?></span><a class="title" href="<?php comment_author_url() ?>" rel="nofollow<?php if(strpos(get_comment_author_url(), get_bloginfo('url')) != 0) echo ' external'; ?>"><img class="favicon" src="http://www.google.com/s2/favicons?domain=<?php $host = simpledark_get_host(get_comment_author_url()); echo $host; ?>" alt="Favicon of <?php echo $host; ?>" width="16" height="16" /><?php comment_author(); ?></a></div>
				</li>
<?php
				}
?>
			</ol>
<?php
			} else {
?>
			<p class="message">&rsaquo; <?php _e('No pingbacks yet.', THEME_NAME); ?></p>
<?php
			}
?>
			</div>
<?php
//		}
		}
	}
	if(post_password_required()) {
?>
			</div>
<?php
	}
?>