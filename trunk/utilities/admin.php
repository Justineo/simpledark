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
			<th scope="row"><?php _e('AJAX', THEME_NAME); ?></th>
			<td>
				<label for="enable_ajax">
				<input type="checkbox" <?php if($saved_options['enable_ajax']) echo 'checked="checked" '; ?>value="checkbox" id="enable_ajax" name="enable_ajax" /> <?php _e('Enable AJAX (paging, comment submission, search, etc.)', THEME_NAME); ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Post Tags', THEME_NAME); ?></th>
			<td>
				<label for="show_tags_on_archive_pages">
				<input type="checkbox" <?php if($saved_options['show_tags_on_archive_pages']) echo 'checked="checked" '; ?>value="checkbox" id="show_tags_on_archive_pages" name="show_tags_on_archive_pages" /> <?php _e('Show post tags on archive pages (home, archive, etc.)', THEME_NAME); ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Pingbacks and Trackbacks', THEME_NAME); ?></th>
			<td>
				<label for="hide_pingbacks">
				<input type="checkbox" <?php if($saved_options['hide_pingbacks']) echo 'checked="checked" '; ?>value="checkbox" id="hide_pingbacks" name="hide_pingbacks" /> <?php _e('Hide pingbacks and trackbacks', THEME_NAME); ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Allowed Tags', THEME_NAME); ?></th>
			<td>
				<label for="show_allowed_tags">
				<input type="checkbox" <?php if($saved_options['show_allowed_tags']) echo 'checked="checked" '; ?>value="checkbox" id="show_allowed_tags" name="show_allowed_tags" /> <?php _e('Show allowed tags for comments', THEME_NAME); ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Comment Images', THEME_NAME); ?></th>
			<td>
				<label for="enable_comment_images">
				<input type="checkbox" <?php if($saved_options['enable_comment_images']) echo 'checked="checked" '; ?>value="checkbox" id="enable_comment_images" name="enable_comment_images" /> <?php _e('Comments can include the &lt;img&gt; tag', THEME_NAME); ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Comment Submit Shortcut', THEME_NAME); ?></th>
			<td>
				<label for="ctrl_enter_submit_comment">
				<input type="checkbox" <?php if($saved_options['ctrl_enter_submit_comment']) echo 'checked="checked" '; ?>value="checkbox" id="ctrl_enter_submit_comment" name="ctrl_enter_submit_comment" /> <?php _e('Use Ctrl+Enter to submit comment', THEME_NAME); ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="search_form_text"><?php _e('Search Form Text', THEME_NAME); ?></label></th>
			<td><input type="text" class="regular-text" value="<?php echo $saved_options['search_form_text']; ?>" id="search_form_text" name="search_form_text" /> <span class="description"><?php _e('Default text of the search form', THEME_NAME); ?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Footer License', THEME_NAME); ?></th>
			<td>
				<label for="show_footer_license">
				<input type="checkbox" <?php if($saved_options['show_footer_license']) echo 'checked="checked" '; ?>value="checkbox" id="show_footer_license" name="show_footer_license" /> <?php _e('Show footer license', THEME_NAME); ?></label>
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
				<input type="checkbox" <?php if($saved_options['show_custom_footer_info']) echo 'checked="checked" ';?>value="checkbox" id="show_custom_footer_info" name="show_custom_footer_info" /> <?php _e('Show custom information in the footer', THEME_NAME); ?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="custom_footer_info_code"><?php _e('Custom Footer Information Code', THEME_NAME); ?></label></th>
			<td><input type="text" class="regular-text" value="<?php echo $saved_options['custom_footer_info_code']; ?>" id="custom_footer_info_code" name="custom_footer_info_code" /> <span class="description"><?php _e('Write your custom HTML code to display in the footer', THEME_NAME); ?></span></td>
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
		'string' => array('search_form_text', 'license_url', 'license_display_name', 'custom_footer_info_code'),
		'bool' => array('show_tags_on_archive_pages', 'hide_pingbacks', 'show_allowed_tags', 'enable_comment_images', 'ctrl_enter_submit_comment', 'strict_comment', 'show_footer_license', 'show_custom_footer_info', 'enable_ajax')
	);
	$options_default = array(
		'enable_ajax'				=>	1,
		'show_allowed_tags'			=>	1,
		'enable_comment_images'		=>	0,
		'ctrl_enter_submit_comment'	=>	1,
		'search_form_text'			=>	__('Search...', THEME_NAME)
	);
	new SimpleDarkAdmin($simpledark_default_option_types, $options_default);
}
?>