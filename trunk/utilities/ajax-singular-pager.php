<?php
function simpledark_ajax_singular_pager() {

	defined('DOING_AJAX') || define('DOING_AJAX', true);

	if ($_GET['action'] == 'spage_ajax') {
		nocache_headers();
		if(have_posts()) {
			the_post();
			the_content();
			$args = array(
				'before'			=> '<div class="post-pages">' . __('Pages:', THEME_NAME),
				'after'				=> '</div>',
				'nextpagelink'		=> __('Next Page', THEME_NAME),
				'previouspagelink'	=> __('Previous Page', THEME_NAME)
			);
			wp_link_pages($args);
		}
		die();
	}
}
?>