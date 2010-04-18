<?php
require_once(TEMPLATEPATH . '/utilities/options.php');

class SimpleDarkAdmin {
	private $string_options = array();
	private $bool_options = array();
	private $options = array();
	private $options_default = array();

	public function __construct(array $options, array $options_default) {
		$this->options_default = $options_default;
		if(!get_option(SIMPLEDARK_OPTIONS))
			update_option(SIMPLEDARK_OPTIONS, SimpleDarkOptions::getInstance()->merge_array($this->options_default));
		$this->string_options = isset($options['string']) ? (array)$options['string'] : array();
		$this->bool_options = isset($options['bool']) ? (array)$options['bool'] : array();
		add_action('admin_menu', array($this,'_admin'));
//		delete_option(SIMPLEDARK_OPTIONS);
	}

	public function _admin() {
		$page = add_theme_page(__('SimpleDark Options', THEME_NAME), __('SimpleDark Options', THEME_NAME), 10, 'SimpleDarkAdmin', array($this, '_admin_panel'));
		if ( function_exists('add_contextual_help') ) {
			$help = '<a href="http://lync.in/" target="_blank">'.__('Check here for more information.', THEME_NAME).'</a>';
			add_contextual_help($page,$help);
		}
	}

	public function save($data) {
		foreach($data as $key=>$value) {
			if(in_array($key, $this->string_options)){
				$this->options[$key] = rtrim(preg_replace('/\n\s*\r/', '', $value));
				$this->options[$key] = str_replace('<!--', '', $this->options[$key]);
				$this->options[$key] = str_replace('-->', '', $this->options[$key]);
			}elseif(in_array($key, $this->bool_options)){
				$this->options[$key] = (bool)$value;
			}
		}
		if(!isset($this->options['feed_url']) || empty($this->options['feed_url'])) {
			$this->options['feed_url'] = get_bloginfo('rss2_url');
		}
		update_option(SIMPLEDARK_OPTIONS, $this->options);
	}

	public function _admin_panel() {
?>
<div class="wrap">
<?php screen_icon(); ?>
	<h2><?php _e('SimpleDark Options', THEME_NAME); ?></h2>
<?php
$saved_options = &$GLOBALS['simpledark_options'];
if(isset($_POST['Submit'])) {
	$this->save($_POST);
?>
	<div id="message" class="updated fade">
		<p><strong><?php _e('Changes have been saved.', THEME_name); ?></strong></p>
	</div>
<?php
}
$saved_options->refresh();
?>
	<form action="" method="POST">
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<td colspan="2" class="section-title"><h3><?php _e('General', THEME_NAME); ?></h3></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('AJAX', THEME_NAME); ?></th>
			<td>
				<label for="enable_ajax">
					<input type="checkbox" <?php if($saved_options['enable_ajax']) echo 'checked="checked" '; ?>value="checkbox" id="enable_ajax" name="enable_ajax" /> <?php _e('Enable AJAX (paging, comment submission, search, etc.)', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Top Menu', THEME_NAME); ?></th>
			<td>
				<label for="top_menu_show_home">
					<input type="checkbox" <?php if($saved_options['top_menu_show_home']) echo 'checked="checked" '; ?>value="checkbox" id="top_menu_show_home" name="top_menu_show_home" /> <?php _e('Show the link of the homepage on the top menu', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Top Menu Items', THEME_NAME); ?></th>
			<td>
				<label for="top_category_menu">
					<input type="checkbox" <?php if($saved_options['top_category_menu']) echo 'checked="checked" '; ?>value="checkbox" id="top_category_menu" name="top_category_menu" /> <?php _e('Show categories as top menu items instead of pages', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2" class="section-title"><h3><?php _e('Posts', THEME_NAME); ?></h3></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Post Tags', THEME_NAME); ?></th>
			<td>
				<label for="show_tags_on_archive_pages">
					<input type="checkbox" <?php if($saved_options['show_tags_on_archive_pages']) echo 'checked="checked" '; ?>value="checkbox" id="show_tags_on_archive_pages" name="show_tags_on_archive_pages" /> <?php _e('Show post tags on archive pages (home, archive, etc.)', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2" class="section-title"><h3><?php _e('Discussion', THEME_NAME); ?></h3></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Pingbacks and Trackbacks', THEME_NAME); ?></th>
			<td>
				<label for="hide_pingbacks">
					<input type="checkbox" <?php if($saved_options['hide_pingbacks']) echo 'checked="checked" '; ?>value="checkbox" id="hide_pingbacks" name="hide_pingbacks" /> <?php _e('Hide pingbacks and trackbacks', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Allowed Tags', THEME_NAME); ?></th>
			<td>
				<label for="show_allowed_tags">
					<input type="checkbox" <?php if($saved_options['show_allowed_tags']) echo 'checked="checked" '; ?>value="checkbox" id="show_allowed_tags" name="show_allowed_tags" /> <?php _e('Show allowed tags for comments', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Comment Images', THEME_NAME); ?></th>
			<td>
				<label for="enable_comment_images">
					<input type="checkbox" <?php if($saved_options['enable_comment_images']) echo 'checked="checked" '; ?>value="checkbox" id="enable_comment_images" name="enable_comment_images" /> <?php _e('Comments can include the &lt;img&gt; tag', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Comment Submit Shortcut', THEME_NAME); ?></th>
			<td>
				<label for="ctrl_enter_submit_comment">
					<input type="checkbox" <?php if($saved_options['ctrl_enter_submit_comment']) echo 'checked="checked" '; ?>value="checkbox" id="ctrl_enter_submit_comment" name="ctrl_enter_submit_comment" /> <?php _e('Use Ctrl+Enter to submit comment', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2" class="section-title"><h3><?php _e('Sidebar', THEME_NAME); ?></h3></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="search_form_text"><?php _e('Search Form Text', THEME_NAME); ?></label></th>
			<td><input type="text" class="regular-text" value="<?php echo $saved_options['search_form_text']; ?>" id="search_form_text" name="search_form_text" /> <span class="description"><?php _e('Default text of the search form', THEME_NAME); ?></span></td>
		</tr>
		<tr valign="top">
			<td colspan="2" class="section-title"><h3><?php _e('Footer', THEME_NAME); ?></h3></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Footer License', THEME_NAME); ?></th>
			<td>
				<label for="show_footer_license">
					<input type="checkbox" <?php if($saved_options['show_footer_license']) echo 'checked="checked" '; ?>value="checkbox" id="show_footer_license" name="show_footer_license" /> <?php _e('Show footer license', THEME_NAME); ?>
				</label>
				<div class="info"><?php _e('You can choose a way to reserve your rights when sharing your creative works by specifying a copyright-license like <a href="http://creativecommons.org/about/licenses/">Creative Commons</a> in your footer area.'); ?></div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="license_display_name"><?php _e('License Name', THEME_NAME); ?></label></th>
			<td><input type="text" class="regular-text" value="<?php echo $saved_options['license_display_name']; ?>" id="license_display_name" name="license_display_name" /> <span class="description"><?php _e('The anchor text of the license to be displayed', THEME_NAME); ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="license_url"><?php _e('License URL', THEME_NAME); ?></label></th>
			<td><input type="text" class="regular-text" value="<?php echo $saved_options['license_url']; ?>" id="license_url" name="license_url" /> <span class="description"><?php _e('A link to the license details', THEME_NAME); ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Custom Footer Information', THEME_NAME); ?></th>
			<td>
				<label for="show_custom_footer_info">
					<input type="checkbox" <?php if($saved_options['show_custom_footer_info']) echo 'checked="checked" ';?>value="checkbox" id="show_custom_footer_info" name="show_custom_footer_info" /> <?php _e('Show custom information in the footer', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="custom_footer_info_code"><?php _e('Custom Footer Information Code', THEME_NAME); ?></label></th>
			<td><textarea class="mid-text" id="custom_footer_info_code" name="custom_footer_info_code"><?php echo $saved_options['custom_footer_info_code']; ?></textarea><br /><span class="description"><?php _e('Write your custom HTML code to display in the footer', THEME_NAME); ?></span></td>
		</tr>
		<tr valign="top">
			<td colspan="2" class="section-title"><h3><?php _e('RSS Feed', THEME_NAME); ?></h3></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="custom_feed_info_before"><?php _e('Custom Feed Information Before Content', THEME_NAME); ?></label></th>
			<td><textarea class="mid-text" id="custom_feed_info_before" name="custom_feed_info_before"><?php echo $saved_options['custom_feed_info_before']; ?></textarea><br /><span class="description"><?php _e('Write your custom HTML code to display before the content of each entry of your RSS feed', THEME_NAME); ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="custom_feed_info_after"><?php _e('Custom Feed Information After Content', THEME_NAME); ?></label></th>
			<td>
				<textarea class="mid-text" id="custom_feed_info_after" name="custom_feed_info_after"><?php echo $saved_options['custom_feed_info_after']; ?></textarea><br /><span class="description"><?php _e('Write your custom HTML code to display after the content of each entry of your RSS feed', THEME_NAME); ?></span>
				<div class="info">
					<p><?php _e('You can use these placeholders in your code:'); ?></p>
					<ul>
						<li>%AUTHOR_NAME% - <?php _e('The name of the post author without a link', THEME_NAME); ?></li>
						<li>%AUTHOR_LINK% - <?php _e('The link of posts by the post author', THEME_NAME); ?></li>
						<li>%BLOG_LINK% - <?php _e('The link of your blog homepage', THEME_NAME); ?></li>
						<li>%FEED_URL% - <?php _e('The RSS feed URL of your blog posts', THEME_NAME); ?></li>
						<li>%POST_URL% - <?php _e('The URL of your post', THEME_NAME); ?></li>
					</ul>
					<p><?php _e('eg. The post is written by %AUTHOR_LINK%. To read more interesting posts, visit %BLOG_LINK% or &lt;a href="%FEED_URL%"&gt;subscribe our updates&lt;/a&gt;.', THEME_NAME); ?></p>
					<p><?php _e('The output will be wrapped in a &lt;DIV&gt; element.', THEME_NAME); ?></p>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2" class="section-title"><h3><?php _e('Analytics', THEME_NAME); ?></h3></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Google Analytics', THEME_NAME); ?></th>
			<td>
				<label for="enable_google_analytics">
					<input type="checkbox" <?php if($saved_options['enable_google_analytics']) echo 'checked="checked" '; ?>value="checkbox" id="enable_google_analytics" name="enable_google_analytics" /> <?php _e('Enable Google Analytics', THEME_NAME); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="google_analytics_code"><?php _e('Google Analytics Code', THEME_NAME); ?></label></th>
			<td>
				<textarea class="mid-text" id="google_analytics_code" name="google_analytics_code"><?php echo $saved_options['google_analytics_code']; ?></textarea><br /><span class="description"><?php _e('Paste your Google Analytics code snippet here', THEME_NAME); ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Google Analytics for Administrators', THEME_NAME); ?></th>
			<td>
				<label for="exclude_admin_analytics">
					<input type="checkbox" <?php if($saved_options['exclude_admin_analytics']) echo 'checked="checked" '; ?>value="checkbox" id="exclude_admin_analytics" name="exclude_admin_analytics" /> <?php _e('Do not load Google Analytics when administrators are logged in', THEME_NAME); ?>
				</label>
			</td>
		</tr>
	</tbody>
	</table>
	<p class="submit">
		<input class="button-primary" type="submit" value="<?php _e('Save Changes', THEME_NAME); ?>" name="Submit"/>
	</p>
	</form>
</div>
<?php
	}
}

if(is_admin()){
	$simpledark_default_option_types = array(
		'string' => array('search_form_text', 'license_url', 'license_display_name', 'custom_footer_info_code', 'custom_feed_info_before', 'custom_feed_info_after', 'google_analytics_code'),
		'bool' => array('top_menu_show_home', 'top_category_menu', 'show_tags_on_archive_pages', 'hide_pingbacks', 'show_allowed_tags', 'enable_comment_images', 'ctrl_enter_submit_comment', 'strict_comment', 'show_footer_license', 'show_custom_footer_info', 'enable_ajax', 'enable_google_analytics', 'exclude_admin_analytics')
	);
	$options_default = array(
		'enable_ajax'				=>	1,
		'top_menu_show_home'		=>	1,
		'top_category_menu'			=>	0,
		'show_allowed_tags'			=>	1,
		'enable_comment_images'		=>	0,
		'ctrl_enter_submit_comment'	=>	1,
		'enable_google_analytics'	=>	0,
		'exclude_admin_analytics'	=>	1,
		'search_form_text'			=>	__('Search...', THEME_NAME)
	);
	new SimpleDarkAdmin($simpledark_default_option_types, $options_default);
}
?>