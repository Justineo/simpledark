<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes('xhtml'); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?php if ( is_singular() || is_archive() ) { wp_title(''); } else { bloginfo('name'); } ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); _e('/style.css', THEME_NAME); ?>" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/404.css" media="screen" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
</head>
<body <?php body_class(); ?>>
<?php $options = &$GLOBALS['simpledark_options']; ?>
<div id="window-wrapper">
	<div id="window">
		<h3><?php _e('Sorry, the page you are looking for cannot be found.', THEME_NAME); ?></h3>
		<div id="error-info">
			<p><?php printf(__('The requested URL <code>%s</code> was not found on this server.', THEME_NAME), $_SERVER['REQUEST_URI']); ?></p>
			<p><?php _e('It is possible that the address is incorrect, or that the page no longer exists.', THEME_NAME); ?></p>
			<p><?php _e('We suggest you to try these links:', THEME_NAME); ?></p>
			<ul>
				<li><a href="<?php echo 'javascript:history.go(-1);' ?>"><?php _e('Go back to last page', THEME_NAME); ?></a></li>
				<li><a href="<?php echo home_url(); ?>"><?php printf(__('Go to the home page of %s', THEME_NAME), get_bloginfo('name')); ?></a></li>
			</ul>
			<p><?php _e('Or you can search for the content you need:', THEME_NAME); ?></p>
			<div id="search-wrapper">
				<form id="search-form" action="<?php echo home_url(); ?>" method="get">
					<div>
						<label for="s" id="s-msg"><?php echo $options['search_form_text']; ?></label>
						<input type="text" class="textbox" id="s" name="s" value="" />
						<input type="submit" id="search-submit" value="" />
					</div>
				</form>
			</div>

		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {
	var searchBox = jQuery('#s');
	var msgBox = jQuery('#s-msg');

	if(searchBox.val() != '')
		msgBox.fadeTo(0, 0);
	searchBox.focus(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, .25);
		else
			msgBox.stop().fadeTo(200, 0, function() {
				jQuery(this).hide();
			});
	}).blur(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, 1);
	}).keyup(function() {
		if(searchBox.val() == '')
			msgBox.stop().fadeTo(200, .25);
		else
			msgBox.stop().fadeTo(200, 0, function() {
				jQuery(this).hide();
			});
	});
});
</script>
</body>
</html>