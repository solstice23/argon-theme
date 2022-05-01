<?php
//更新主题版本后的兼容
$argon_last_version = get_option("argon_last_version");
if ($argon_last_version == ""){
	$argon_last_version = "0.0";
}
if (version_compare($argon_last_version, $GLOBALS['theme_version'], '<' )){
	if (version_compare($argon_last_version, '0.940', '<')){
		if (get_option('argon_mathjax_v2_enable') == 'true' && get_option('argon_mathjax_enable') != 'true'){
			update_option("argon_math_render", 'mathjax2');
		}
		if (get_option('argon_mathjax_enable') == 'true'){
			update_option("argon_math_render", 'mathjax3');
		}
	}
	if (version_compare($argon_last_version, '0.970', '<')){
		if (get_option('argon_show_author') == 'true'){
			update_option("argon_article_meta", 'time|views|comments|categories|author');
		}
	}
	if (version_compare($argon_last_version, '1.1.0', '<')){
		if (get_option('argon_enable_zoomify') != 'false'){
			update_option("argon_enable_fancybox", 'true');
			update_option("argon_enable_zoomify", 'false');
		}
	}
	if (version_compare($argon_last_version, '1.3.4', '<')){
		switch (get_option('argon_search_post_filter', 'post,page')){
			case 'post,page':
				update_option("argon_enable_search_filters", 'true');
				update_option("argon_search_filters_type", '*post,*page,shuoshuo');
				break;
			case 'post,page,shuoshuo':
				update_option("argon_enable_search_filters", 'true');
				update_option("argon_search_filters_type", '*post,*page,*shuoshuo');
				break;
			case 'post,page,hide_shuoshuo':
				update_option("argon_enable_search_filters", 'true');
				update_option("argon_search_filters_type", '*post,*page');
				break;
			case 'off':
			default:
				update_option("argon_enable_search_filters", 'false');
				break;
		}		
	}
	update_option("argon_last_version", $GLOBALS['theme_version']);
}
