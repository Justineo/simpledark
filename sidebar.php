			<div id="sidebar">
<?php
	if(!function_exists('dynamic_sidebar') || !dynamic_sidebar()) {
		$options = &$GLOBALS['simpledark_options'];
?>
				<div class="widget widget_search" id="search">
					<div id="search-wrapper">
						<form id="search-form" action="<?php bloginfo('url'); ?>" method="get">
							<div><label for="s" id="s-msg"><?php echo $options['search_form_text']; ?></label><input type="text" class="textbox" id="s" name="s" value="" /><?php if(!$options['enable_ajax'] || !$options['enable_ajax_search']) { ?><input type="submit" id="search-submit" value="" /><?php } ?></div>
						</form>
					</div>
				</div>
				<div class="widget widget_feed" id="simpledark_feed">
					<h4><?php _e('RSS Feed', THEME_NAME); ?></h4>
					<div class="rss-feed"><a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Subscribe Updates', THEME_NAME); ?></a></div>
				</div>
<?php
	}
?>
			</div>
