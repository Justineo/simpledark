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
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/simpledark-base.min.js<?php simpledark_base_params(); ?>"></script>
<?php if($options['enable_ajax']) { ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/simpledark-ajax.min.js<?php simpledark_ajax_params(); ?>"></script>
<?php } ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-10343575-1");
pageTracker._trackPageview();
} catch(err) {}
</script>
<?php wp_footer(); ?>
</body>
</html>