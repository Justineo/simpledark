<?php
if(function_exists('wp_syntax_highlight')) {

function simpledark_custom_geshi_styles(&$geshi)
{
	$geshi->set_overall_style('color: #FFF; font-family:&quot;Consolas&quot;,monospace,&quot;Courier New&quot;');
    $geshi->set_keyword_group_style(1, 'color: #3D9EDD;');
    $geshi->set_keyword_group_style(2, 'color: #F2B646;');
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
	$geshi->set_regexps_style(1, 'color: #7AB9BE;');
	$geshi->set_regexps_style(2, 'color: #7AB9BE;');
	$geshi->set_regexps_style(3, 'color: #7AB9BE;');
	$geshi->set_regexps_style(4, 'color: #7AB9BE;');
	$geshi->set_regexps_style(5, 'color: #00E813;');
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

function simpledark_display_code_language($content) {
	return preg_replace_callback('|(<div class="wp_syntax">[\s\S]*?<pre class=\")([^\"]+)([\s\S]*?<\/div>)|i', 'refine_code', $content);
}
function refine_code($matches) {
	return '<div class="wp_syntax_wrapper"><span class="wp_syntax_lang">' . get_display_lang_name($matches[2]) . '</span>' . $matches[0] . '</div>';
}
add_filter('the_content', 'simpledark_display_code_language', 100);
add_filter('the_excerpt', 'simpledark_display_code_language', 100);
add_filter('comment_text', 'simpledark_display_code_language', 100);

function get_display_lang_name($lang) {
	$name_map = array(
		'abap'			=> 'ABAP',
		'actionscript3'	=> 'ActionScript 3',
		'actionscript'	=> 'ActionScript',
		'ada'			=> 'Ada',
		'apache'		=> 'Apache configuration',
		'applescript'	=> 'AppleScript',
		'apt_sources'	=> 'APT sources',
		'asm'			=> 'ASM',
		'asp'			=> 'ASP',
		'autoit'		=> 'AutoIt',
		'avisynth'		=> 'AviSynth',
		'bash'			=> 'Bash',
		'basic4gl'		=> 'Basic4GL',
		'bf'			=> 'Brainfuck',
		'bibtex'		=> 'BibTeX',
		'blitzbasic'	=> 'Blitz Basic',
		'bnf'			=> 'BNF',
		'boo'			=> 'Boo',
		'c'				=> 'C',
		'c_mac'			=> 'C (Mac)',
		'caddcl'		=> 'CAD DCL',
		'cadlisp'		=> 'CAD Lisp',
		'cfdg'			=> 'CFDG',
		'cfm'			=> 'ColdFusion',
		'cil'			=> 'CIL',
		'cmake'			=> 'CMake',
		'cobol'			=> 'COBOL',
		'cpp'			=> 'C++',
		'cpp-qt'		=> 'C++ (QT)',
		'csharp'		=> 'C#',
		'css'			=> 'CSS',
		'd'				=> 'D',
		'dcs'			=> 'DCS',
		'delphi'		=> 'Delphi',
		'diff'			=> 'diff output',
		'div'			=> 'DIV',
		'dos'			=> 'DOS',
		'dot'			=> 'DOT',
		'eiffel'		=> 'Eiffel',
		'email'			=> 'mbox',
		'erlang'		=> 'Erlang',
		'fo'			=> 'FO',
		'fortran'		=> 'Fortran',
		'freebasic'		=> 'FreeBASIC',
		'genero'		=> 'Genero',
		'gettext'		=> 'GNU Gettext',
		'glsl'			=> 'GLSL',
		'gml'			=> 'GML',
		'gnuplot'		=> 'gnuplot',
		'groovy'		=> 'Groovy',
		'haskell'		=> 'Haskell',
		'hq9plus'		=> 'HQ9+',
		'html4strict'	=> 'HTML',
		'idl'			=> 'IDL',
		'ini'			=> 'INI',
		'inno'			=> 'Inno',
		'intercal'		=> 'INTERCAL',
		'io'			=> 'Io',
		'java5'			=> 'Java',
		'java'			=> 'Java',
		'javascript'	=> 'JavaScript',
		'kixtart'		=> 'KiXtart',
		'klonec'		=> 'KLone C',
		'klonecpp'		=> 'KLone C++',
		'latex'			=> 'LaTeX',
		'lisp'			=> 'Lisp',
		'locobasic'		=> 'Locomotive Basic',
		'lolcode'		=> 'LOLCODE',
		'lotusformulas'	=> '@Formula',
		'lotusscript'	=> 'LotusScript',
		'lscript'		=> 'LScript',
		'lsl2'			=> 'LSL2',
		'lua'			=> 'Lua',
		'm68k'			=> 'Motorola 68000 Assembler',
		'make'			=> 'GNU make',
		'matlab'		=> 'Matlab M',
		'mirc'			=> 'mIRC scripting',
		'modula3'		=> 'Modula-3',
		'mpasm'			=> 'Microchip Assembly',
		'mxml'			=> 'MXML',
		'mysql'			=> 'MySQL SQL',
		'nsis'			=> 'NSIS',
		'oberon2'		=> 'Oberon-2',
		'objc'			=> 'Objective-C',
		'ocaml'			=> 'OCaml',
		'ocaml-brief'	=> 'OCaml',
		'oobas'			=> 'OpenOffice.org Basic',
		'oracle8'		=> 'Oracle 8 SQL',
		'oracle11'		=> 'Oracle 11 SQL',
		'pascal'		=> 'Pascal',
		'per'			=> 'per',
		'perl'			=> 'Perl',
		'php'			=> 'PHP',
		'php-brief'		=> 'PHP',
		'pic16'			=> 'PIC16',
		'pixelblender'	=> 'Pixel Blender',
		'plsql'			=> 'PL/SQL',
		'povray'		=> 'POV-Ray',
		'powershell'	=> 'PowerShell',
		'progress'		=> 'Progress',
		'prolog'		=> 'Prolog',
		'properties'	=> 'Properties',
		'providex'		=> 'ProvideX',
		'python'		=> 'Python',
		'qbasic'		=> 'QuickBASIC',
		'rails'			=> 'Rails',
		'rebol'			=> 'REBOL',
		'reg'			=> 'Microsoft Registry',
		'robots'		=> 'robots.txt',
		'ruby'			=> 'Ruby',
		'sas'			=> 'SAS',
		'scala'			=> 'Scala',
		'scheme'		=> 'Scheme',
		'scilab'		=> 'Scilab',
		'sdlbasic'		=> 'sdlBasic',
		'smalltalk'		=> 'Smalltalk',
		'smarty'		=> 'Smarty',
		'sql'			=> 'SQL',
		'tcl'			=> 'Tcl',
		'teraterm'		=> 'Tera Term Macro',
		'text'			=> 'Text',
		'thinbasic'		=> 'thinBasic',
		'tsql'			=> 'T-SQL',
		'typoscript'	=> 'TypoScript',
		'vb'			=> 'VB',
		'vbnet'			=> 'VB.NET',
		'verilog'		=> 'Verilog',
		'vhdl'			=> 'VHDL',
		'vim'			=> 'Vimscript',
		'visualfoxpro'	=> 'Visual Fox Pro',
		'visualprolog'	=> 'Visual Prolog',
		'whitespace'	=> 'Whitespace',
		'whois'			=> 'RPSL (whois response)',
		'winbatch'		=> 'WinBatch',
		'xml'			=> 'XML',
		'xorg_conf'		=> 'Xorg configuration',
		'xpp'			=> 'X++',
		'z80'			=> 'ZiLOG Z80 Assembler'
	);
	if(isset($name_map[$lang])) {
		return $name_map[$lang];
	} else {
		return strtoupper($lang);
	}
}

function simpledark_wp_syntax_feed_background($content) {
	if(is_feed()) {
		$new = preg_replace('/<td class="line_numbers">/i', '<td class="line_numbers" style="color:#eee; background:#3c3c3c">', $content);
		return preg_replace('/<div class="wp_syntax">/i', '<div class="wp_syntax" style="background:#2c2c2c; overflow:auto">', $new);
	}
	return $content;
}
add_filter('the_content', 'simpledark_wp_syntax_feed_background', 100);

}
?>