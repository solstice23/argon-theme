<?php
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
	if (get_option("argon_enable_immersion_color", "false") == "true"){
		echo "<script> document.documentElement.classList.add('immersion-color'); </script>";
	}
}
add_filter('admin_head', 'argon_admin_themecolor_css');
