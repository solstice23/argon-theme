<?php
//注册新后台主题配色方案
function argon_add_admin_color(){
	wp_admin_css_color(
		'argon',
		'Argon',
		get_bloginfo('template_directory') . "/admin.css",
		array("#5e72e4", "#324cdc", "#e8ebfb"),
		array('base' => '#525f7f', 'focus' => '#5e72e4', 'current' => '#fff')
	);
}
add_action('admin_init', 'argon_add_admin_color');
function argon_admin_themecolor_css(){
	$themecolor = get_option("argon_theme_color", "#5e72e4");
	$RGB = hexstr2rgb($themecolor);
	$HSL = rgb2hsl($RGB['R'], $RGB['G'], $RGB['B']);
	echo "
		<style id='themecolor_css'>
			:root{
				--themecolor: {$themecolor} ;
				--themecolor-R: {$RGB['R']} ;
				--themecolor-G: {$RGB['G']} ;
				--themecolor-B: {$RGB['B']} ;
				--themecolor-H: {$HSL['H']} ;
				--themecolor-S: {$HSL['S']} ;
				--themecolor-L: {$HSL['L']} ;
			}
		</style>
	";
	if (get_option("argon_enable_color_immersion", "false") == "true"){
		echo "<script> document.documentElement.classList.add('color-immersion'); </script>";
	}
}
add_filter('admin_head', 'argon_admin_themecolor_css');

/*登录界面 CSS*/
function argon_login_page_style() {
	wp_enqueue_style("argon_login_css", $GLOBALS['assets_path'] . "/login.css", null, $GLOBALS['theme_version']);
}
if (get_option('argon_enable_login_css') == 'true'){
	add_action('login_head', 'argon_login_page_style');
}

//主题选项页面
function themeoptions_admin_menu(){
	/*后台管理面板侧栏添加选项*/
	add_menu_page(__("Argon 主题设置", 'argon'), __("Argon 主题选项", 'argon'), 'edit_theme_options', basename(__FILE__), 'themeoptions_page');
}
include_once(get_template_directory() . '/settings.php');

/*主题菜单*/
add_action('init', 'init_nav_menus');
function init_nav_menus(){
	register_nav_menus( array(
		'toolbar_menu' => __('顶部导航', 'argon'),
		'leftbar_menu' => __('左侧栏菜单', 'argon'),
		'leftbar_author_links' => __('左侧栏作者个人链接', 'argon'),
		'leftbar_friend_links' => __('左侧栏友情链接', 'argon')
	));
}	
