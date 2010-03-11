<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes('xhtml'); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?php if ( is_singular() || is_archive() ) { wp_title(''); } else { bloginfo('name'); } ?></title>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); _e('/style.css', THEME_NAME); ?>" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/404.css" media="screen" />
</head>
<body <?php body_class(); ?>>
<div id="window-wrapper">
	<div id="window">
		<h3><?php _e('Sorry, the page you are looking for cannot be found.', THEME_NAME); ?></h3>
		<div id="error-info">
			<p><?php printf(__('The requested URL <code>%s</code> was not found on this server.', THEME_NAME), $_SERVER['REQUEST_URI']); ?></p>
			<p><?php _e('It is possible that the address is incorrect, or that the page no longer exists.', THEME_NAME); ?></p>
			<p><?php _e('We suggest you to try these links:', THEME_NAME); ?></p>
			<ul>
				<li><a href="<?php echo 'javascript:history.go(-1);' ?>"><?php _e('Go back to last page', THEME_NAME); ?></a></li>
				<li><a href="<?php bloginfo('url'); ?>"><?php printf(__('Go to the home page of %s', THEME_NAME), get_bloginfo('name')); ?></a></li>
			</ul>

		</div>
	</div>
</div>
</body>
</html>