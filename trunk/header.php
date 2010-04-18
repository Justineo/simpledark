<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes('xhtml'); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?php if ( is_singular() || is_archive() ) { wp_title(''); } else { bloginfo('name'); } ?></title>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); _e('/style.css', THEME_NAME); ?>" media="screen" />
<link rel="alternate" type="application/rss+xml" title="Lync.in RSS Feed" href="<?php bloginfo('rss2_url'); ?>" /> 
<link rel="alternate" type="application/rss+xml" title="Lync.in RSS Feed" href="<?php bloginfo('comments_rss2_url'); ?>" /> 
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="page">
<div id="header">
	<h1 id="blog-name"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name') ?></a></h1>
	<div id="blog-description"><?php bloginfo('description') ?></div>
<?php $options = &$GLOBALS['simpledark_options'];
	$menu_args = 'menu_class=top-menu&depth=1';
	if($options['top_menu_show_home']) {
		$menu_args = $menu_args . '&show_home=1';
	}
	if(!$options['top_category_menu']) {
		wp_page_menu($menu_args);
	} else {
		simpledark_category_menu($menu_args);
	} 
?>
</div>
<div id="main">
	<div id="content">
