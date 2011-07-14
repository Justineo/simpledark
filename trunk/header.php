<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes('xhtml'); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?php if ( is_singular() || is_archive() ) { wp_title(''); } else { bloginfo('name'); } ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); _e('/style.css', THEME_NAME); ?>" media="screen" />
<!--[if IE]><link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/ie.css" media="screen" /><![endif]-->
<?php
	$options = &$GLOBALS['simpledark_options'];
	wp_head();
	if($options['enable_google_analytics']) {
		if(!$options['exclude_admin_analytics'] || !current_user_can('manage_options')) {
			echo $options['google_analytics_code'];
		}
	}
?>
</head>
<body <?php body_class(); ?>>
<div id="page">
<div id="header">
	<h1 id="blog-name"><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a></h1>
	<div id="blog-description"><?php bloginfo('description'); ?></div>
	<div class="top-menu">
<?php
		$menu_args = 'echo=0&depth=2&container=div&container_class=top-menu-window&theme_location=top-nav&fallback_cb=simpledark_menu';
		if($options['top_menu_show_home']) {
			$menu_args = $menu_args . '&show_home=1';
		}
		echo function_exists('wp_nav_menu') ? wp_nav_menu($menu_args) : simpledark_menu($menu_args);
?>
	</div>
</div>
<div id="main">
	<div id="content">
