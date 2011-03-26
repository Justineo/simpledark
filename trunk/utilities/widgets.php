<?php
class SimpleDark_Widget_Search extends WP_Widget {

	function SimpleDark_Widget_Search() {
		$widget_ops = array('classname' => 'widget_search', 'description' => __('A search form for your blog', THEME_NAME));
		$this->WP_Widget('search', __('SimpleDark Search'), $widget_ops);
	}

    function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$options = &$GLOBALS['simpledark_options'];
?>
			<?php echo $before_widget; ?>
				<?php if($title) echo $before_title . $title . $after_title; ?>
				<div id="search-wrapper">
					<form id="search-form" action="<?php echo home_url(); ?>" method="get">
						<label for="s" id="s-msg"><?php echo $options['search_form_text']; ?></label><input type="text" class="textbox" id="s" name="s" value="" /><?php if(!$options['enable_ajax'] || !$options['enable_ajax_search']) { ?><input type="submit" id="search-submit" value="" /><?php } ?>
					</form>
				</div>
			<?php echo $after_widget; ?>
<?php
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
    }

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];
?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', THEME_NAME); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
</p>

<?php
	}
}

class SimpleDark_Widget_Feed extends WP_Widget {

	function SimpleDark_Widget_Feed() {
		$widget_ops = array('classname' => 'widget_feed', 'description' => __('An RSS feed widget for your blog', THEME_NAME));
		$this->WP_Widget('simpledark_feed', __('SimpleDark Feed', THEME_NAME), $widget_ops);
	}

    function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$feed_text = empty($instance['feed_text'])? __('Subscribe Updates', THEME_NAME) : $instance['feed_text'];
		$feed_url = empty($instance['feed_url'])? get_bloginfo('rss2_url') : (substr(strtoupper($options['feed_url']), 0, 7) == 'HTTP://'? $instance['feed_url'] : 'http://' . $instance['feed_url']);
?>
			<?php echo $before_widget; ?>
				<?php if($title) echo $before_title . $title . $after_title; ?>
				<div class="rss-feed"><a href="<?php echo $feed_url; ?>"><?php echo $feed_text; ?></a></div>
			<?php echo $after_widget; ?>
<?php
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'feed_text' => __('Subscribe Updates', THEME_NAME)));
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['feed_text'] = strip_tags($new_instance['feed_text']);
		$instance['feed_url'] = strip_tags($new_instance['feed_url']);
		return $instance;
    }

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('RSS Feed', THEME_NAME) ) );
		$title = $instance['title'];
		$feed_text = $instance['feed_text'];
		$feed_url = $instance['feed_url'];
?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', THEME_NAME); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
	<label for="<?php echo $this->get_field_id('feed_text'); ?>"><?php _e('Feed Text:', THEME_NAME); ?><input class="widefat" id="<?php echo $this->get_field_id('feed_text'); ?>" name="<?php echo $this->get_field_name('feed_text'); ?>" type="text" value="<?php echo esc_attr($feed_text); ?>" /></label>
	<label for="<?php echo $this->get_field_id('feed_url'); ?>"><?php _e('Feed URL:', THEME_NAME); ?><input class="widefat" id="<?php echo $this->get_field_id('feed_url'); ?>" name="<?php echo $this->get_field_name('feed_url'); ?>" type="text" value="<?php echo esc_attr($feed_url); ?>" /></label>
</p>

<?php
	}
}

function simpledark_load_widgets() {
	register_widget('SimpleDark_Widget_Search');
	register_widget('SimpleDark_Widget_Feed');
}

?>