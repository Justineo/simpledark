<?php
if(!(is_single() || is_home() || is_page())) {
?>
	<div class="content-header">
<?php
	echo '&laquo; ';
	if(is_search()) { global $s; printf(__('Search results for <strong>&ldquo;%s&rdquo;</strong>', THEME_NAME), htmlspecialchars($s)); }
	else if(is_category()) { printf(__('Posts under <strong>%s</strong>', THEME_NAME), single_cat_title('', false)); }
	else if(is_tag()) { printf(__('Posts tagged <strong>%s</strong>', THEME_NAME), single_cat_title('', false)); }
	else if(is_author()) { printf(__('Posts by <strong>%s</strong>', THEME_NAME), get_userdata(get_query_var('author'))->display_name); }
	else if(is_day()) { printf(__('Archives on <strong>%s</strong>', THEME_NAME), get_the_time(simpledark_time_format('date_with_year'))); }
	else if(is_month()) { printf(__('Archives in <strong>%s</strong>', THEME_NAME), get_the_time(simpledark_time_format('month'))); }
	else if(is_year()) { printf(__('Archives in <strong>%s</strong>', THEME_NAME), get_the_time(simpledark_time_format('year'))); }
	else { _e('Archives page', THEME_NAME); }
?>
	</div>
<?php
}
?>
