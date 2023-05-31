<?php
function argon_get_search_post_type_array(){
	$search_filters_type = get_option("argon_search_filters_type", "*post,*page,shuoshuo");
	$search_filters_type = explode(',', $search_filters_type);
	if (!isset($_GET['post_type'])) {
		$default = array_filter($search_filters_type, function ($str) {	return $str[0] == '*'; });
		$default = array_map(function ($str) { return substr($str, 1) ;}, $default);
		return $default;
	}
	$search_filters_type = array_map(function ($str) { return $str[0] == '*' ? substr($str, 1) : $str; }, $search_filters_type);
	$post_type = explode(',', $_GET['post_type']);
	$arr = array();
	foreach ($search_filters_type as $type) {
		if (in_array($type, $post_type)) {
			array_push($arr, $type);
		}
	}
	if (count($arr) == 0) {
		array_push($arr, 'none');
	}
	return $arr;
}
function search_filter($query) {
	if (!$query -> is_search || is_admin()) {
		return $query;
	}
	if (get_option('argon_enable_search_filters', 'true') == 'false'){
		return $query;
	}
	$query -> set('post_type', argon_get_search_post_type_array());
	return $query;
}
add_filter('pre_get_posts', 'search_filter');

/**
 * 修复 WordPress 搜索结果为空，返回为 200 的问题。
 * @author ivampiresp <im@ivampiresp.com>
 */
function search_404_fix_template_redirect()
{
    if (is_search()) {
        global $wp_query;

        if ($wp_query->found_posts == 0) {
            status_header(404);
        }
    }
}

add_action('template_redirect', 'search_404_fix_template_redirect');
