<?php
if(function_exists('wp_syntax_highlight')) {

function simpledark_custom_geshi_styles(&$geshi)
{
	$geshi->set_overall_style('color: #FFF; font-family:&quot;Consolas&quot;,monospace,&quot;Courier New&quot;');
    $geshi->set_keyword_group_style(1, 'color: #3D9EDD;');
    $geshi->set_keyword_group_style(2, 'color: #996600;');
    $geshi->set_keyword_group_style(3, 'color: #996699;');
    $geshi->set_keyword_group_style(4, 'color: #99FFFF;');
    $geshi->set_methods_style(1, 'color: #FFF;');
    $geshi->set_methods_style(2, 'color: #FFF;');
	$geshi->set_comments_style(1, 'color: #999;');
	$geshi->set_comments_style(2, 'color: #33CC66;');
	$geshi->set_comments_style('MULTI', 'color: #999;');
	$geshi->set_strings_style('color: #7ACC00;', false, 0);
	$geshi->set_strings_style('color: #7ACC00;', false, 'HARD');
	$geshi->set_regexps_style(0, 'color: #7AB9BE;');
	$geshi->set_numbers_style('color: #FFCC00;');
	$geshi->set_symbols_style('color: #CCC;', false, 0);
	$geshi->set_symbols_style('color: #CCC;', false, 1);
	$geshi->set_symbols_style('color: #CCC;', false, 2);
	$geshi->set_symbols_style('color: #CCC;', false, 3);
	$geshi->set_symbols_style('color: #CCC;', false, 4);
	$geshi->set_escape_characters_style('color: #99FF00;', false, 0);
	$geshi->set_escape_characters_style('color: #99FF00;', false, 1);
	$geshi->set_escape_characters_style('color: #99FF00;', false, 2);
	$geshi->set_escape_characters_style('color: #99FF00;', false, 3);
	$geshi->set_escape_characters_style('color: #99FF00;', false, 4);
	$geshi->set_escape_characters_style('color: #99FF00;', false, 5);
}
add_action('wp_syntax_init_geshi', 'simpledark_custom_geshi_styles');

function simpledark_replce_tab($content) {
	return str_replace('	', '  ', $content);
}
add_filter('the_content', simpledark_replce_tab, 100);

function simpledark_display_code_language($content) {
	return preg_replace('/(<div class="wp_syntax">)([\s\S]*?<pre class=")([^"]+)/i', '\\1<span class="wp_syntax_lang">\\3</span>\\2', $content);
}
add_filter('the_content', simpledark_display_code_language, 100);

}
?>