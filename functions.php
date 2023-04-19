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


use GuzzleHttp\Exception\GuzzleException;
use HaoZiTeam\ChatGPT\V2 as ChatGPTV2;

function argon_generate_chatgpt_client(): ?ChatGPTV2 {

	$apikey  = get_option( "argon_openai_api_key" );
	$baseurl = $GLOBALS["openai_baseurl"];

	if ( ! $apikey || ! isset( $baseurl ) ) {
		return null;
	}

	return new ChatGPTV2( $apikey, $baseurl , model: get_option('argon_ai_model', 'gpt-3.5-turbo'), timeout: 30 );
}

/**
 * @throws GuzzleException
 */
function argon_generate_article_summary( int $post_id, WP_Post $post ): string {
	$client = argon_generate_chatgpt_client();

	$client->addMessage(
		"You are an article summary generator, ".
	 "please generate the summary of the given article ".
	 "with provided title and content, ".
	 "the language of the summary must equals to the article's mainly used language, ".
	 "do not write summary in third person.".
	 "If there's a previous summary of an article provided, ".
	 "please generate the summary similar with the previous one.",
		"system" );
	$client->addMessage( "The title of the article：" . $post->post_title );

	$content = $post->post_content;
	$content = wp_strip_all_tags(apply_filters('the_content', $content));
	$max_content_length = get_option( 'argon_ai_max_content_length', 4000 );
	if ($max_content_length > 0){
		$content = substr($content, 0, $max_content_length);
	}

	$client->addMessage( "The content of the article：" . $content );
	$previous_summary = get_post_meta( $post->ID, "argon_ai_summary", true );
	if ( $previous_summary != "" ) {
		$client->addMessage( "The previous summary of the article: " . $previous_summary);
	}

	$post_argon_ai_extra_prompt_mode = get_post_meta( $post->ID, "argon_ai_extra_prompt_mode", true );
	$post_argon_ai_extra_prompt      = get_post_meta( $post->ID, "argon_ai_extra_prompt", true );
	$global_argon_ai_extra_prompt    = get_option( 'argon_ai_extra_prompt', "" );
	switch ( $post_argon_ai_extra_prompt_mode ) {
		case 'replace':
			$client->addMessage( $post_argon_ai_extra_prompt, 'system' );
			break;
		case 'append':
			$client->addMessage( $global_argon_ai_extra_prompt . $post_argon_ai_extra_prompt, 'system' );
			break;
		case 'none':
			break;
		case 'default':
		default:
			$client->addMessage( $global_argon_ai_extra_prompt, 'system' );
			break;
	}

	$result = "";
	foreach ( $client->ask( "Now please generate the summary of the article given before" ) as $item ) {
		$result .= $item['answer'];
	}

	return $result;
}


add_action( "save_post_post", function ( int $post_id, WP_Post $post, bool $update ) {
	if ( get_option( 'argon_ai_post_summary', false ) == 'false' || get_post_meta( $post_id, "argon_ai_post_summary", true ) == 'false' ) {
		return;
	}
	$post_argon_ai_no_update_post_summary   = get_post_meta( $post_id, "argon_ai_no_update_post_summary", true );
	$global_argon_ai_no_update_post_summary = get_option( 'argon_ai_no_update_post_summary', true );
	if ( $update && $post_argon_ai_no_update_post_summary != 'false' && ( $post_argon_ai_no_update_post_summary == 'true' || $global_argon_ai_no_update_post_summary == 'true' ) ) {
		return;
	}
	try {
		$summary = argon_generate_article_summary( $post_id, $post );
		update_post_meta( $post_id, "argon_ai_summary", $summary );
	} catch ( GuzzleException ) {
	}
}, 10, 3 );
