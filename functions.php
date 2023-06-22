<?php

require_once 'vendor/autoload.php';

// 载入主题检测更新、兼容性
require get_template_directory() . '/inc/argon.php';

// 后台的相关功能：注册新后台主题配色方案和登录页样式，设置页
require get_template_directory() . '/inc/fun/admin.php';

// 载入主题页面相关功能
require get_template_directory() . '/inc/fun/post.php';  // post, page 中涉及的函数
require get_template_directory() . '/inc/fun/paginate.php';  //分页
require get_template_directory() . '/inc/fun/template-parts.php';  // 主题页面模板 hooks 及其 functions
require get_template_directory() . '/inc/fun/search.php';  // 搜索相关的功能
require get_template_directory() . '/inc/fun/seo.php';  // seo 相关的功能

// 注册编辑文章界面 Meta box
require get_template_directory() . '/inc/fun/post-extra-meta-editor.php';

// 注册古腾堡区块和简码，编辑器添加相关按钮
require get_template_directory() . '/inc/fun/shortcodes.php';

// 注册小工具
require get_template_directory() . '/inc/fun/widgets.php';

// 注册 shuoshuo 及其配套功能
require get_template_directory() . '/inc/fun/shuoshuo.php';

// 表情包
require_once( get_template_directory() . '/inc/fun/emotions.php' );

// 评论相关函数
require get_template_directory() . '/inc/fun/comment.php';


// 载入除上述以外的一些功能函数
require get_template_directory() . '/inc/fun/functions.php';

//翻译 Hook
function argon_locate_filter( $locate ) {
	if ( substr( $locate, 0, 2 ) == 'zh' ) {
		if ( $locate == 'zh_TW' ) {
			return $locate;
		}

		return 'zh_CN';
	}
	if ( substr( $locate, 0, 2 ) == 'en' ) {
		return 'en_US';
	}
	if ( substr( $locate, 0, 2 ) == 'ru' ) {
		return 'ru_RU';
	}

	return 'en_US';
}

function argon_get_locate() {
	if ( function_exists( "determine_locale" ) ) {
		return argon_locate_filter( determine_locale() );
	}
	$determined_locale = get_locale();
	if ( is_admin() ) {
		$determined_locale = get_user_locale();
	}
}

function theme_locale_hook( $locate, $domain ) {
	if ( $domain == 'argon' ) {
		return argon_locate_filter( $locate );
	}

	return $locate;
}

add_filter( 'theme_locale', 'theme_locale_hook', 10, 2 );

//时区修正
if ( get_option( 'argon_enable_timezone_fix' ) == 'true' ) {
	date_default_timezone_set( 'UTC' );
}

function array_remove( &$arr, $item ) {
	$pos = array_search( $item, $arr );
	if ( $pos !== false ) {
		array_splice( $arr, $pos, 1 );
	}
}

//访问者 Token & Session
function get_random_token() {
	return md5( uniqid( microtime( true ), true ) );
}

function set_user_token_cookie() {
	if ( ! isset( $_COOKIE["argon_user_token"] ) || strlen( $_COOKIE["argon_user_token"] ) != 32 ) {
		$newToken = get_random_token();
		setcookie( "argon_user_token", $newToken, time() + 10 * 365 * 24 * 60 * 60, "/" );
		$_COOKIE["argon_user_token"] = $newToken;
	}
}

function session_init() {
	set_user_token_cookie();
	if ( ! session_id() ) {
		session_start();
	}
}

session_init();
//add_action('init', 'session_init');

//解析 UA 和相应图标
require_once( get_template_directory() . '/inc/lib/useragent-parser.php' );
require_once( get_template_directory() . '/inc/fun/user-agent.php' );


//发送邮件
function send_mail( $to, $subject, $content ) {
	wp_mail( $to, $subject, $content, array( 'Content-Type: text/html; charset=UTF-8' ) );
}

function check_email_address( $email ) {
	return (bool) preg_match( "/^\w+((-\w+)|(\.\w+))*@[A-Za-z0-9]+(([.\-])[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/", $email );
}

//首页隐藏特定分类文章
function argon_home_hide_categories( $query ) {
	if ( is_home() && $query->is_main_query() ) {
		$excludeCategories = explode( ",", get_option( "argon_hide_categories" ) );
		$excludeCategories = array_map( function ( $cat ) {
			return - $cat;
		}, $excludeCategories );
		$query->set( 'category__not_in', $excludeCategories );
		$query->set( 'tag__not_in', $excludeCategories );
	}

	return $query;
}

if ( get_option( "argon_hide_categories" ) != "" ) {
	add_action( 'pre_get_posts', 'argon_home_hide_categories' );
}

//隐藏 admin 管理条
//show_admin_bar(false);

/*恢复链接管理器*/
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

require('inc/fun/nofollow-friendlink.php');

require('inc/fun/chatgpt.php');