<?php
	$pingbacks = $comments_by_type['pings'];
	$ping_count = count($pingbacks);
	$comment_count = count($comments) - $ping_count;
	$options = &$GLOBALS['simpledark_options'];
?>
			<div id="comments" class="section">
			<h3><?php _e('Comments', THEME_NAME); ?> (<span class="comment-count"><?php echo post_password_required()? __('X', THEME_NAME) : $comment_count; ?></span>)</h3>
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
<?php wp_list_comments('type=comment&callback=simpledark_comment&max_depth=5'); ?>
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
<?php
		$aria_req = ( $req ? " aria-required='true'" : '' );
		$comment_form_args = array(
			'fields'				=> apply_filters('comment_form_default_fields', array(
				'before_fields'		=> ($comment_author? '<p class="comment-notes">&rsaquo; ' . sprintf(__('Welcome back, %s.', THEME_NAME), $comment_author) . ' <a href="#" id="toggle-info">' . __('Change', THEME_NAME) . ' &raquo;</a></p>' : '') . '<div id="comment-author-info"' . ($comment_author? ' style="display:none"' : '') . '>',
				'author'			=> '<p class="comment-form-author"><input id="author" name="author" tabindex="1" type="text" size="24" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $aria_req . ' /> <label for="author">' . __( 'Name' ) . ($req ? ' (*)' : '') . '</label></p>',
				'email'				=> '<p class="comment-form-email"><input id="email" name="email" tabindex="2" type="text" size="24" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . $aria_req . ' /> <label for="email">' . __( 'Email' ) . ($req ? ' (*)' : '') . '</label></p>',
				'url'				=> '<p class="comment-form-url"><input id="url" name="url" tabindex="3" type="text" size="24" value="' . esc_attr( $commenter['comment_author_url'] ) . '" /> <label for="url">' . __( 'Website' ) . '</label></p>',
				'after_fields'		=> '</div>'
			)),
			'comment_field'			=> '<p id="comment-wrapper"><textarea id="comment" name="comment" rows="5" cols="70" tabindex="4" aria-required="true"' . ($options['ctrl_enter_submit_comment'] ? ' class="quick-submit"' : '') . '></textarea></p>',
			'must_log_in'			=> '<p class="must-log-in">&rsaquo; ' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.', THEME_NAME ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
			'logged_in_as'			=> '<p class="logged-in-as">&rsaquo; ' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out &raquo;</a>', THEME_NAME ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
			'label_submit'			=> __( 'Post Comment', THEME_NAME ) . ($options['ctrl_enter_submit_comment']? ' (Ctrl+Enter)' : ''),
			'comment_notes_before'	=> '',
			'comment_notes_after'	=> ''
		);
		comment_form($comment_form_args);
		if(!$options['hide_pingbacks']) {
?>
			<div id="pings" class="section">
			<h3><?php _e('Pingbacks', THEME_NAME); ?> (<?php echo post_password_required()? __('X', THEME_NAME) : $ping_count; ?>)</h3>
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
		}
	}
	if(post_password_required()) {
?>
			</div>
<?php
	}
?>