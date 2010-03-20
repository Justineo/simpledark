	</div><!-- content end -->
<?php get_sidebar(); ?>
</div><!-- main end -->
<div id="footer"><?php printf(__('Powered by %s', THEME_NAME), '<a href="http://wordpress.org/">WordPress</a>'); ?> / <?php printf(__('Theme %1$s by %2$s', THEME_NAME), '<a href="http://lync.in/">SimpleDark</a>', '<a href="http://lync.in/">Justice</a>'); ?>
<?php
	$info = '';
	$options = &$GLOBALS['simpledark_options'];
	if($options['show_footer_license']) {
		$info .= ' / '. sprintf(__('Licensed under a <a rel="license" href="%s" title="%s">%s</a>', THEME_NAME), $options['license_url'], __('License Details', THEME_NAME), $options['license_display_name']);
	}
	if($options['show_custom_footer_info']) {
		$info .= ' / ' . $options['custom_footer_info_code'];
	}
	echo $info;
?>
</div>
</div><!-- page end -->
<div id="fixed-nav"><div class="buttons"><a class="top" href="#header"></a><?php if(is_single() || is_page()) { ?><a class="cmnts" href="#comments"></a><?php } ?><a class="bottom" href="#footer"></a></div></div>
<?php simpledark_script_params($options['enable_ajax']); ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/simpledark-base.min.js"></script>
<?php if($options['enable_ajax']) { ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/simpledark-ajax.min.js"></script>
<?php } ?>
<?php wp_footer(); ?>
</body>
</html>