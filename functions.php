<?php
if (version_compare( $GLOBALS['wp_version'], '4.4-alpha', '<' )) {
	echo "<div style='background: #5e72e4;color: #fff;font-size: 30px;padding: 50px 30px;position: fixed;width: 100%;left: 0;right: 0;bottom: 0px;z-index: 2147483647;'>Argon 主题不支持 Wordpress 4.4 以下版本，请更新 Wordpress</div>";
}
function theme_slug_setup() {
   add_theme_support('title-tag');
   add_theme_support('post-thumbnails');
}
add_action('after_setup_theme','theme_slug_setup');
//检测更新
require_once(get_template_directory() . '/theme-update-checker/plugin-update-checker.php'); 
if (get_option('argon_update_source') == 'stop'){}
else if (get_option('argon_update_source') == 'abc233site'){
	$argonThemeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://api.solstice23.top/argon/info.json?source=0',
		get_template_directory() . '/functions.php',
		'argon'
	);
}else{
	$argonThemeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://raw.githubusercontent.com/solstice23/argon-theme/master/info.json',
		get_template_directory() . '/functions.php',
		'argon'
	);
}

//初次使用时发送安装量统计信息 (数据仅用于统计安装量)
function post_analytics_info(){
	if(function_exists('file_get_contents')){
		$contexts = stream_context_create(
			array(
				'http' => array(
					'method'=>"GET",
					'header'=>"User-Agent: ArgonTheme\r\n"
				)
			)
		);
		$result = file_get_contents('http://api.solstice23.top/argon_analytics/index.php?domain=' . urlencode($_SERVER['HTTP_HOST']) . '&version='. urlencode(wp_get_theme('argon') -> Version), false, $contexts);
		update_option('argon_has_inited', 'true');
		return $result;
	}else{
		update_option('argon_has_inited', 'true');
	}
}
if (get_option('argon_has_inited') != 'true'){
	post_analytics_info();
}
//时区修正
if (get_option('argon_enable_timezone_fix') == 'true'){
	date_default_timezone_set('UTC');
}
//注册小工具
function argon_widgets_init() {
	register_sidebar(
		array(
			'name'          => '左侧栏小工具',
			'id'            => 'leftbar-tools',
			'description'   => __( '左侧栏小工具 (如果设置会在侧栏增加一个 Tab)' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s card bg-white border-0">',
			'after_widget'  => '</div>',
			'before_title'  => '<h6 class="font-weight-bold text-black">',
			'after_title'   => '</h6>',
		)
	);
}
add_action('widgets_init','argon_widgets_init');
//输出分页页码
function get_argon_formatted_paginate_links(){
	$args = array(
		'prev_text' => '',
		'next_text' => '',
		'before_page_number' => '',
		'after_page_number' => '',
		'show_all' => True
	);
	$res = paginate_links($args);
	//单引号转双引号 & 去除上一页和下一页按钮
	$res = preg_replace(
		'/\'/',
		'"',
		$res
	);
	$res = preg_replace(
		'/<a class="prev page-numbers" href="(.*?)">(.*?)<\/a>/',
		'',
		$res
	);
	$res = preg_replace(
		'/<a class="next page-numbers" href="(.*?)">(.*?)<\/a>/',
		'',
		$res
	);
	//寻找所有页码标签
	preg_match_all('/<(.*?)>(.*?)<\/(.*?)>/' , $res , $pages);
	$total = count($pages[0]);
	$current = 0;
	$urls = array();
	for ($i = 0; $i < $total; $i++){
		if (preg_match('/<span(.*?)>(.*?)<\/span>/' , $pages[0][$i])){
			$current = $i + 1;
		}else{
			preg_match('/<a(.*?)href="(.*?)">(.*?)<\/a>/' , $pages[0][$i] , $tmp);
			$urls[$i + 1] = $tmp[2];
		}
	}

	if ($total == 0){
		return "";
	}

	//计算页码起始
	$from = max($current - 3 , 1);
	$to = min($current + 7 - ( $current - $from + 1 ) , $total);
	if ($to - $from + 1 < 7){
		$to = min($current + 3 , $total);
		$from = max($current -( 7 - ( $to - $current + 1 ) ) , 1);
	}
	//生成新页码
	$html = "";
	if ($from > 1){
		$html .= '<li class="page-item"><a aria-label="First Page" class="page-link" href="' . $urls[1] . '"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>';
	}
	if ($current > 1){
		$html .= '<li class="page-item"><a aria-label="Previous Page" class="page-link" href="' . $urls[$current - 1] . '"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>';
	}
	for ($i = $from; $i <= $to; $i++){
		if ($current == $i){
			$html .= '<li class="page-item active"><span class="page-link" style="cursor: default;">' . $i . '</span></li>';
		}else{
			$html .= '<li class="page-item"><a class="page-link" href="' . $urls[$i] . '">' . $i . '</a></li>';
		}
	}
	if ($current < $total){
		$html .= '<li class="page-item"><a aria-label="Next Page" class="page-link" href="' . $urls[$current + 1] . '"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>';
	}
	if ($to < $total){
		$html .= '<li class="page-item"><a aria-label="Last Page" class="page-link" href="' . $urls[$total] . '"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>';
	}
	return '<nav><ul class="pagination">' . $html . '</ul></nav>';
}
//页面 Description Meta
function get_seo_description(){
	global $post;
	if ((is_single() || is_page())){
		if (!post_password_required()){
			return  
			htmlspecialchars(mb_substr(str_replace("\n", '', strip_tags(get_post($post -> ID) -> post_content)), 0, 50)) . "...";
		}else{
			return "这是一个加密页面，需要密码来查看";
		}
	}else{
		return get_option('argon_seo_description');
	}
}
//页面浏览量
function get_post_views($post_id){
    $count_key = 'views';
    $count = get_post_meta($post_id, $count_key, true);
    if ($count==''){
        delete_post_meta($post_id, $count_key);
        add_post_meta($post_id, $count_key, '0');
        $count = '0';
    }
    echo number_format_i18n($count);
}
function set_post_views(){
	if (post_password_required($post_id)){
		return;
	}
    global $post;   
    $post_id = $post -> ID;
    $count_key = 'views';
    $count = get_post_meta($post_id, $count_key, true);
    if (is_single() || is_page()) {
        if ($count==''){
            delete_post_meta($post_id, $count_key);
            add_post_meta($post_id, $count_key, '0');
        } else {
            update_post_meta($post_id, $count_key, $count + 1);
        }
    }
}
add_action('get_header', 'set_post_views');
//字数和预计阅读时间
function get_article_words($str){
	return mb_strlen(
		preg_replace(
			'/\s/',
			'',
			html_entity_decode(
				strip_tags($str)
			)
		),
		'UTF-8'
	);
}
function get_reading_time($len){
	$speed = get_option('argon_reading_speed');
	if ($speed == ""){
		$speed = 300;
	}
	$reading_time = $len / $speed;
	if ($reading_time < 0.3){
		return "几秒读完";
	}
	if ($reading_time < 1){
		return "1 分钟内";
	}
	if ($reading_time < 60){
		return ceil($reading_time) . " 分钟";
	}
	return round($reading_time / 60 , 1) . " 小时";
}
//当前文章是否可以生成目录
function have_catalog(){
	if (!is_single() && !is_page()){
		return false;
	}
	if (post_password_required()){
		return false;
	}
	$content = get_post(get_the_ID()) -> post_content;
	if (preg_match('/<h[1-6]>/',$content)){
		return true;
	}else{
		return false;
	}
}
//当前文章是否隐藏 阅读时间 Meta
function is_readingtime_meta_hidden(){
	if (strpos(get_the_content() , "[hide_reading_time][/hide_reading_time]") === False){
		return False;
	}
	return True;
}
//评论样式格式化
function argon_comment_format($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;?>
	<li class="comment-item" id="comment-<?php comment_ID(); ?>">
		<div class="comment-item-avatar">
			<?php if(function_exists('get_avatar') && get_option('show_avatars')){
				echo get_avatar($comment, 40);
			}?>
		</div>
		<div class="comment-item-inner" id="comment-inner-<?php comment_ID();?>">
			<div class="comment-item-title">
				<?php echo get_comment_author_link();?>
				<?php if( user_can($comment -> user_id , "update_core") ){
					echo '<span class="badge badge-primary badge-admin">博主</span>';}
				?>
				<?php if( $comment -> comment_approved == 0 ){
					echo '<span class="badge badge-warning badge-unapproved">待审核</span>';}
				?>
			</div>
			<div class="comment-item-text">
				<?php comment_text();?>
			</div>
			<div class="comment-reply-time">
				<?php echo human_time_diff(get_comment_time('U') , current_time('timestamp')) . "前";?>
				<div class="comment-reply-time-details"><?php echo get_comment_time('Y-n-d G:i:s');?></div>
			</div>
			<?php /*comment_reply_link(array_merge($args,array('reply_text'=>'回复','depth'=>$depth,'max_depth'=>$args['max_depth'])))*/?>
			<button class="comment-reply btn btn-sm btn-outline-primary" data-id="<?php comment_ID(); ?>" type="button">回复</button>
		</div>
	</li>
	<li class="comment-divider"></li>
	<li>
<?php }
//评论样式格式化 (说说预览界面)
function argon_comment_shuoshuo_preview_format($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;?>
	<li class="comment-item" id="comment-<?php comment_ID(); ?>">
		<div class="comment-item-inner " id="comment-inner-<?php comment_ID();?>">
			<span class="shuoshuo-comment-item-title">
				<?php echo get_comment_author_link();?>
				<?php if( user_can($comment -> user_id , "update_core") ){
					echo '<span class="badge badge-primary badge-admin">博主</span>';}
				?>
				<?php if( $comment -> comment_approved == 0 ){
					echo '<span class="badge badge-warning badge-unapproved">待审核</span>';}
				?>
				: 
			</span>
			<span class="shuoshuo-comment-item-text">
				<?php echo strip_tags(get_comment_text());?>
			</span>
		</div>
	</li>
	<li>
<?php }
//评论验证码生成 & 验证
function get_comment_captcha_seed(){
	$captchaSeed = rand(0 , 500000000);
	return $captchaSeed;
}
function get_comment_captcha($captchaSeed){
	mt_srand($captchaSeed + 10007);
	$oper = mt_rand(1 , 4);
	$num1 = 0;
	$num2 = 0;
	switch ($oper){
		case 1:
			$num1 = mt_rand(1 , 20);
			$num2 = mt_rand(0 , 20 - $num1);
			return $num1 . " + " . $num2 . " = ";
			break;
		case 2:
			$num1 = mt_rand(10 , 20);
			$num2 = mt_rand(1 , $num1);
			return $num1 . " - " . $num2 . " = ";
			break;
		case 3:
			$num1 = mt_rand(3 , 9);
			$num2 = mt_rand(3 , 9);
			return $num1 . " * " . $num2 . " = ";
			break;
		case 4:
			$num2 = mt_rand(2 , 9);
			$num1 = $num2 * mt_rand(2 , 9);
			return $num1 . " / " . $num2 . " = ";
			break;
		default:
			break;
	}
}
function get_comment_captcha_answer($captchaSeed){
	mt_srand($captchaSeed + 10007);
	$oper = mt_rand(1 , 4);
	$num1 = 0;
	$num2 = 0;
	switch ($oper){
		case 1:
			$num1 = mt_rand(1 , 20);
			$num2 = mt_rand(0 , 20 - $num1);
			return $num1 + $num2;
			break;
		case 2:
			$num1 = mt_rand(10 , 20);
			$num2 = mt_rand(1 , $num1);
			return $num1 - $num2;
			break;
		case 3:
			$num1 = mt_rand(3 , 9);
			$num2 = mt_rand(3 , 9);
			return $num1 * $num2;
			break;
		case 4:
			$num2 = mt_rand(2 , 9);
			$num1 = $num2 * mt_rand(2 , 9);
			return $num1 / $num2;
			break;
		default:
			break;
	}
	return "";
}
function wrong_captcha(){
	wp_die('验证码错误，评论失败');
}
function check_comment_captcha($comment){
	if (get_option('argon_comment_need_captcha') == 'false'){
		return $comment;
	}
	$answer = $_POST['comment_captcha'];
	if(current_user_can('level_7')){
		return $comment;
	}
	mt_srand($_POST['comment_captcha_seed'] + 10007);
	$oper = mt_rand(1 , 4);
	$num1 = 0;
	$num2 = 0;
	switch ($oper){
		case 1:
			$num1 = mt_rand(1 , 20);
			$num2 = mt_rand(0 , 20 - $num1);
			if (($num1 + $num2) != $answer){
				wrong_captcha();
			}
			break;
		case 2:
			$num1 = mt_rand(10 , 20);
			$num2 = mt_rand(1 , $num1);
			if (($num1 - $num2) != $answer){
				wrong_captcha();
			}
			break;
		case 3:
			$num1 = mt_rand(3 , 9);
			$num2 = mt_rand(3 , 9);
			if ($num1 * $num2 != $answer){
				wrong_captcha();
			}
			break;
		case 4:
			$num2 = mt_rand(2 , 9);
			$num1 = $num2 * mt_rand(2 , 9);
			if ($num1 / $num2 != $answer){
				wrong_captcha();
			}
			break;
		default:
			break;
	}
	return $comment;
}
if($comment_data['comment_type'] == ''){
	add_filter('preprocess_comment' , 'check_comment_captcha');
}
//评论 Markdown 解析
require_once(get_template_directory() . '/parsedown.php');
function comment_markdown_render($comment_content){
	if ($_POST['use_markdown'] != 'true'){
		return $comment_content;
	}
	//HTML 过滤
	global $allowedtags; 
	//$comment_content = wp_kses($comment_content, $allowedtags);
	//允许评论中额外的 HTML Tag
	$allowedtags['pre'] = array('class' => array());
	$allowedtags['i'] = array('class' => array(), 'aria-hidden' => array()); 
	$allowedtags['img'] = array('src' => array(), 'alt' => array(), 'class' => array());
	$allowedtags['a']['class'] = array();
	$allowedtags['a']['data-src'] = array();
	$allowedtags['a']['target'] = array();

	//解析 Markdown
	$parsedown = new Parsedown();
	$res = $parsedown -> text($comment_content);
	/*$res = preg_replace(
		'/<code>([\s\S]*?)<\/code>/',
		'<pre>$1</pre>',
		$res
	);*/
	$res = preg_replace(
		'/<img src="(.*?)" alt="(.*?)" \/>/',
		'<a data-src="$1" title="$2" class="comment-image">
			<i class="fa fa-image" aria-hidden="true"></i>
			查看图片
			<img src="" alt="$2" class="comment-image-preview">
			<i class="comment-image-preview-mask"></i>
		</a>',
		$res
	);
	$res = preg_replace(
		'/<a (.*?)>(.*?)<\/a>/',
		'<a $1 target="_blank">$2</a>',
		$res
	);
	return $res;
}
add_filter('pre_comment_content' , 'comment_markdown_render');
//获取顶部 Banner 背景图（用户指定或必应日图）
function get_banner_background_url(){
	$url = get_option("argon_banner_background_url");
	if ($url == "--bing--"){
		$lastUpdated = get_option("argon_bing_banner_background_last_updated_time");
		if ($lastUpdated == ""){
			$lastUpdated = 0;
		}
		$now = time();
		if ($now - $lastUpdated < 3600){
			return get_option("argon_bing_banner_background_last_updated_url");
		}else{
			$data = json_decode(@file_get_contents('https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1') , true);
			$url = "//bing.com" . $data['images'][0]['url'];
			update_option("argon_bing_banner_background_last_updated_time" , $now);
			update_option("argon_bing_banner_background_last_updated_url" , $url);
			return $url;
		}
	}else{
		return $url;
	}
}
//Lazyload 对 <img> 标签预处理和加入 <script> 以加载 Lazyload
function argon_lazyload($content){
	$lazyload_effect = get_option('argon_lazyload_effect');
	if ($lazyload_effect == ''){
		$lazyload_effect = 'fadeIn';
	}
	$lazyload_threshold = get_option('argon_lazyload_threshold');
	if ($lazyload_threshold == ''){
		$lazyload_threshold = 800;
	}
	$lazyload_loading_style = get_option('argon_lazyload_loading_style');
	if ($lazyload_loading_style == ''){
		$lazyload_loading_style = 'none';
	}
	$lazyload_loading_style = "lazyload-style-" . $lazyload_loading_style;

	if(!is_feed() || !is_robots()){
		$content = preg_replace('/<img(.+)src=[\'"]([^\'"]+)[\'"](.*)>/i',"<img class=\"lazyload " . $lazyload_loading_style . "\" src=\"data:image/svg+xml;base64,PCEtLUFyZ29uTG9hZGluZy0tPgo8c3ZnIHdpZHRoPSIxIiBoZWlnaHQ9IjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgc3Ryb2tlPSIjZmZmZmZmMDAiPjxnPjwvZz4KPC9zdmc+\" \$1data-original=\"\$2\" src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC\"\$3>\n<noscript>\$0</noscript>" , $content);
	}

	$content .= '<script>
	$(function() {
		$("article img.lazyload").lazyload(
			{
				threshold: ' . $lazyload_threshold;
	if ($lazyload_effect != "none"){
		$content .= ',effect: "' . $lazyload_effect . '"' ;
	}
	$content .= '
			}
		);
	});
	</script>';
	
	$content .= '<noscript><style>article img.lazyload[src^="data:image/svg+xml;base64,PCEtLUFyZ29uTG9hZGluZy0tPg"]{display: none;}</style></noscript>';

	return $content;
}
//zoomify 插件图片缩放预览
function argon_zoomify($content){
	$zoomify_duration = get_option('argon_zoomify_duration');
	if ($zoomify_duration == ''){
		$zoomify_duration = 200;
	}
	$zoomify_easing = get_option('argon_zoomify_easing');
	if ($zoomify_easing == ''){
		$zoomify_easing = 'ease-out';
	}
	$zoomify_scale = get_option('argon_zoomify_scale');
	if ($zoomify_scale == ''){
		$zoomify_scale = 0.9;
	}

	$content .= '<script>
	$(function() {
		$("article img").zoomify(
			{
				duration: ' . $zoomify_duration . ',
				easing: "' . $zoomify_easing . '",
				scale: ' . $zoomify_scale . '
			}
		);
	});
	</script>';

	return $content;
}
function the_content_filter($content){
	if (get_option('argon_enable_lazyload') != 'false'){
		$content = argon_lazyload($content);
	}
	if (get_option('argon_enable_zoomify') != 'false'){
		$content = argon_zoomify($content);
	}
	return $content;
}
add_filter('the_content' , 'the_content_filter');
//使用 v2ex CDN 的 gravatar 头像源加速
function get_avatar_from_v2ex($avatar){
	$avatar = preg_replace("/http:\/\/(www|\d).gravatar.com\/avatar\//" , "//cdn.v2ex.com/gravatar/" , $avatar);
	return $avatar;
}
if (get_option('argon_enable_v2ex_gravatar') == 'true'){
	add_filter('get_avatar', 'get_avatar_from_v2ex');
}
//说说点赞
function get_shuoshuo_upvotes($ID){
    $count_key = 'upvotes';
    $count = get_post_meta($ID, $count_key, true);
    if ($count==''){
        delete_post_meta($ID, $count_key);
        add_post_meta($ID, $count_key, '0');
        $count = '0';
    }
    return number_format_i18n($count);
}
function set_shuoshuo_upvotes($ID){
    $count_key = 'upvotes';
	$count = get_post_meta($ID, $count_key, true);
	if ($count==''){
		delete_post_meta($ID, $count_key);
		add_post_meta($ID, $count_key, '1');
	} else {
		update_post_meta($ID, $count_key, $count + 1);
	}
}
function upvote_shuoshuo(){
	header('Content-Type:application/json; charset=utf-8');
	$ID = $_POST["shuoshuo_id"];
	if (isset($_COOKIE['argon_shuoshuo_' . $ID . '_upvoted'])){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => '该说说已被赞过',
			'total_upvote' => get_shuoshuo_upvotes($ID)
		)));
	}
	set_shuoshuo_upvotes($ID);
	setcookie('argon_shuoshuo_' . $ID . '_upvoted' , 'true' , time() + 3153600000 , '/');
	exit(json_encode(array(
		'ID' => $ID,
		'status' => 'success',
		'msg' => '点赞成功',
		'total_upvote' => get_shuoshuo_upvotes($ID)
	)));
}
add_action('wp_ajax_upvote_shuoshuo' , 'upvote_shuoshuo');
add_action('wp_ajax_nopriv_upvote_shuoshuo' , 'upvote_shuoshuo');
//检测页面底部版权是否被修改
function alert_footer_copyright_changed(){ ?>
	<div class='notice notice-warning is-dismissible'>
		<p>警告：你可能修改了 Argon 主题页脚的版权声明，Argon 主题要求你至少保留主题的 Github 链接或主题的发布文章链接。</p>
	</div>
<?php }
function check_footer_copyright(){
	$footer = file_get_contents(get_theme_root() . "/argon/footer.php");
	if ((strpos($footer, "github.com/solstice23/argon-theme") === false) && (strpos($footer, "solstice23.top") === false) && (strpos($footer, "abc233.site") === false)){
		add_action('admin_notices', 'alert_footer_copyright_changed');
	}
}
check_footer_copyright();
//颜色计算
function rgb2hsl($R,$G,$B){
	$r = $R / 255;
	$g = $G / 255;
	$b = $B / 255;

	$var_Min = min($r, $g, $b);
	$var_Max = max($r, $g, $b);
	$del_Max = $var_Max - $var_Min;

	$L = ($var_Max + $var_Min) / 2;

	if ($del_Max == 0){
		$H = 0;
		$S = 0;
	}else{
		if ($L < 0.5){
			$S = $del_Max / ($var_Max + $var_Min);
		}else{
			$S = $del_Max / (2 - $var_Max - $var_Min);
		}

		$del_R = ((($var_Max - $r) / 6) + ($del_Max / 2)) / $del_Max;
		$del_G = ((($var_Max - $g) / 6) + ($del_Max / 2)) / $del_Max;
		$del_B = ((($var_Max - $b) / 6) + ($del_Max / 2)) / $del_Max;

		if ($r == $var_Max){
			$H = $del_B - $del_G;
		}
		else if ($g == $var_Max){
			$H = (1 / 3) + $del_R - $del_B;
		}
		else if ($b == $var_Max){
			$H = (2 / 3) + $del_G - $del_R;
		}
		if ($H < 0) $H += 1;
		if ($H > 1) $H -= 1;
	}
	return array(
		'h' => $H,//0~1
		's' => $S,
		'l' => $L
	);
}
function Hue_2_RGB($v1,$v2,$vH){
	if ($vH < 0) $vH += 1;
	if ($vH > 1) $vH -= 1;
	if ((6 * $vH) < 1) return ($v1 + ($v2 - $v1) * 6 * $vH);
	if ((2 * $vH) < 1) return $v2;
	if ((3 * $vH) < 2) return ($v1 + ($v2 - $v1) * ((2 / 3) - $vH) * 6);
	return $v1;
}
function hsl2rgb($h,$s,$l){
	if ($s == 0){
		$r = $l;
		$g = $l;
		$b = $l;
	}
	else{
		if ($l < 0.5){
			$var_2 = $l * (1 + $s);
		}
		else{
			$var_2 = ($l + $s) - ($s * $l);
		}
		$var_1 = 2 * $l - $var_2;
		$r = Hue_2_RGB($var_1, $var_2, $h + (1 / 3));
		$g = Hue_2_RGB($var_1, $var_2, $h);
		$b = Hue_2_RGB($var_1, $var_2, $h - (1 / 3));
	}
	return array(
		'R' => round($r * 255),//0~255
		'G' => round($g * 255),
		'B' => round($b * 255),
		'r' => $r,//0~1
		'g' => $g,
		'b' => $b
	);
}
function rgb2hex($r,$g,$b){
	$hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
	$rh = "";
	$gh = "";
	$bh = "";
	while (strlen($rh) < 2){
		$rh = $hex[$r%16] . $rh;
		$r = floor($r / 16);
	}
	while (strlen($gh) < 2){
		$gh = $hex[$g%16] . $gh;
		$g = floor($g / 16);
	}
	while (strlen($bh) < 2){
		$bh = $hex[$b%16] . $bh;
		$b = floor($b / 16);
	}
	return "#".$rh.$gh.$bh;
}
function hex2rgb($hex){
	//$hex: #XXXXXX
	return array(
		'R' => hexdec(substr($hex,1,2)),//0~255
		'G' => hexdec(substr($hex,3,2)),
		'B' => hexdec(substr($hex,5,2)),
		'r' => hexdec(substr($hex,1,2)) / 255,//0~1
		'g' => hexdec(substr($hex,3,2)) / 255,
		'b' => hexdec(substr($hex,5,2)) / 255
	);
}
function rgb2str($rgb){
	return $rgb['R']. "," .$rgb['G']. "," .$rgb['B'];
}
function hex2str($hex){
	return rgb2str(hex2rgb($hex));
}
function checkHEX($hex){
	if (strlen($hex) != 7){
		return False;
	}
	if (substr($hex,0,1) != "#"){
		return False;
	}
	return True;
}
//主题文章短代码解析
add_shortcode('br','shortcode_br');
function shortcode_br($attr,$content=""){
	return "</br>";
}
add_shortcode('label','shortcode_label');
function shortcode_label($attr,$content=""){
	$out = "<span class='badge";
	$color = isset($attr['color']) ? $attr['color'] : 'indigo';
	switch ($color){
		case 'indigo':
			$out .= " badge-primary";
			break;
		case 'green':
			$out .= " badge-success";
			break;
		case 'red':
			$out .= " badge-danger";
			break;
		case 'orange':
			$out .= " badge-warning";
			break;
		case 'blue':
			$out .= " badge-info";
			break;
		default:
			$out .= " badge-primary";
			break;
	}
	$shape = isset($attr['shape']) ? $attr['shape'] : 'square';
	if ($shape=="round"){
		$out .= " badge-pill";
	}
	$out .= "'>" . $content . "</span>";
	return $out;
}
add_shortcode('progressbar','shortcode_progressbar');
function shortcode_progressbar($attr,$content=""){
	$out = "<div class='progress-wrapper'><div class='progress-info'>";
	if ($content != ""){
		$out .= "<div class='progress-label'><span>" . $content . "</span></div>";
	}
	$progress = isset($attr['progress']) ? $attr['progress'] : 100;
	$out .= "<div class='progress-percentage'><span>" . $progress . "%</span></div>";
	$out .= "</div><div class='progress'><div class='progress-bar";
	$color = isset($attr['color']) ? $attr['color'] : 'indigo';
	switch ($color){
		case 'indigo':
			$out .= " bg-primary";
			break;
		case 'green':
			$out .= " bg-success";
			break;
		case 'red':
			$out .= " bg-danger";
			break;
		case 'orange':
			$out .= " bg-warning";
			break;
		case 'blue':
			$out .= " bg-info";
			break;
		default:
			$out .= " bg-primary";
			break;
	}
	$out .= "' style='width: " . $progress . "%;'></div></div></div>";
	return $out;  
}
add_shortcode('checkbox','shortcode_checkbox');
function shortcode_checkbox($attr,$content=""){
	$checked = isset($attr['checked']) ? $attr['checked'] : 'false';
	$inline = isset($attr['inline']) ? $attr['checked'] : 'false';
	$out = "<div class='shortcode-todo custom-control custom-checkbox";
	if ($inline == 'true'){
		$out .= " inline";
	}
	$out .= "'>
				<input class='custom-control-input' type='checkbox'" . ($checked == 'true' ? ' checked' : '') . ">
				<label class='custom-control-label'>
					<span>" . $content . "</span>
				</label>
			</div>";
	return $out;
}
add_shortcode('alert','shortcode_alert');
function shortcode_alert($attr,$content=""){
	$out = "<div class='alert";
	$color = isset($attr['color']) ? $attr['color'] : 'indigo';
	switch ($color){
		case 'indigo':
			$out .= " alert-primary";
			break;
		case 'green':
			$out .= " alert-success";
			break;
		case 'red':
			$out .= " alert-danger";
			break;
		case 'orange':
			$out .= " alert-warning";
			break;
		case 'blue':
			$out .= " alert-info";
			break;
		case 'black':
			$out .= " alert-default";
			break;
		default:
			$out .= " alert-primary";
			break;
	}
	$out .= "'>";
	if (isset($attr['icon'])){
		$out .= "<span class='alert-inner--icon'><i class='fa fa-" . $attr['icon'] . "'></i></span>";
	}
	$out .= "<span class='alert-inner--text'>";
	if (isset($attr['title'])){
		$out .= "<strong>" . $attr['title'] . "</strong> ";
	}
	$out .= $content . "</div>";
	return $out;
}
add_shortcode('admonition','shortcode_admonition');
function shortcode_admonition($attr,$content=""){
	$out = "<div class='admonition shadow-sm";
	$color = isset($attr['color']) ? $attr['color'] : 'indigo';
	switch ($color){
		case 'indigo':
			$out .= " admonition-primary";
			break;
		case 'green':
			$out .= " admonition-success";
			break;
		case 'red':
			$out .= " admonition-danger";
			break;
		case 'orange':
			$out .= " admonition-warning";
			break;
		case 'blue':
			$out .= " admonition-info";
			break;
		case 'black':
			$out .= " admonition-default";
			break;
		case 'grey':
			$out .= " admonition-grey";
			break;
		default:
			$out .= " admonition-primary";
			break;
	}
	$out .= "'>";
	if (isset($attr['title'])){
		$out .= "<div class='admonition-title'>";
		if (isset($attr['icon'])){
			$out .= "<i class='fa fa-" . $attr['icon'] . "'></i> ";
		}
		$out .= $attr['title'] . "</div>";
	}
	if ($content != ''){
		$out .= "<div class='admonition-body'>" . $content . "</div>";
	}
	$out .= "</div>";
	return $out;
}
add_shortcode('collapse','shortcode_collapse_block');
function shortcode_collapse_block($attr,$content=""){
	$collapse_id = mt_rand(1000000000 , 9999999999);
	$collapsed = isset($attr['collapsed']) ? $attr['collapsed'] : 'true';
	$show_border_left = isset($attr['showleftborder']) ? $attr['showleftborder'] : 'false';
	$out = "<div collapse-id='" . $collapse_id . "'" ;
	$out .= " class='collapse-block shadow-sm";
	$color = isset($attr['color']) ? $attr['color'] : 'none';
	switch ($color){
		case 'indigo':
			$out .= " collapse-block-primary";
			break;
		case 'green':
			$out .= " collapse-block-success";
			break;
		case 'red':
			$out .= " collapse-block-danger";
			break;
		case 'orange':
			$out .= " collapse-block-warning";
			break;
		case 'blue':
			$out .= " collapse-block-info";
			break;
		case 'black':
			$out .= " collapse-block-default";
			break;
		case 'grey':
			$out .= " collapse-block-grey";
			break;
		case 'none':
			$out .= " collapse-block-transparent";
			break;
		default:
			$out .= " collapse-block-transparent";
			break;
	}
	if ($collapsed == 'true'){
		$out .= " collapsed";
	}
	if ($show_border_left != 'true'){
		$out .= " hide-border-left";
	}
	$out .= "'>";

	$out .= "<div class='collapse-block-title' collapse-id='" . $collapse_id . "'>";
	if (isset($attr['icon'])){
		$out .= "<i class='fa fa-" . $attr['icon'] . "'></i> ";
	}
	$out .= "<span class='collapse-block-title-inner'>" . $attr['title'] . "</span><i class='collapse-icon fa fa-angle-down'></i></div>";

	$out .= "<div class='collapse-block-body'";
	if ($collapsed != 'false'){
		$out .= " style='display:none;'";
	}
	$out .= ">" . $content . "</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode('friendlinks','shortcode_friend_link');
function shortcode_friend_link($attr,$content=""){
	$content = trim(strip_tags($content));
	$entries = explode("\n" , $content);
	$row_tag_open = False;
	$out = "<div class='friend-links'>";
	foreach($entries as $index => $value){
		$now = explode("|" , $value);
		if ($now[0] == 'category'){
			if ($row_tag_open == True){
				$row_tag_open = False;
				$out .= "</div>";
			}
			$out .= "<div class='friend-category-title text-black'>" . $now[1] . "</div>";
		}
		if ($now[0] == 'link'){
			if ($row_tag_open == False){
				$row_tag_open = True;
				$out .= "<div class='row'>";
			}
			$out .= "
			<div class='link mb-2 col-lg-4 col-md-6'>
				<div class='card shadow-sm'>
					<div class='d-flex'>
						<div class='friend-link-avatar'>
							<a target='_blank' href='" . $now[1] . "'>";
			if (!ctype_space($now[4]) && $now[4] != '' && isset($now[4])){
				$out .= "<img src='" . $now[4] . "' class='icon bg-gradient-secondary rounded-circle text-white' style='pointer-events: none;'>
						</img>";
			}else{
				$out .= "<div class='icon icon-shape bg-gradient-primary rounded-circle text-white'>" . mb_substr($now[2], 0, 1) . "
						</div>";
			}
						
			$out .= "		</a>
						</div>
						<div class='pl-3'>
							<div class='friend-link-title title text-primary'><a target='_blank' href='" . $now[1] . "'>" . $now[2] . "</a>
						</div>";
			if (!ctype_space($now[3]) && $now[3] != ''  && isset($now[3])){
				$out .= "<p class='friend-link-description'>" . $now[3] . "</p>";
			}else{
				/*$out .= "<p class='friend-link-description'>&nbsp;</p>";*/
			}
			$out .= "		<a target='_blank' href='" . $now[1] . "' class='text-primary opacity-8'>前往</a>
						</div>
					</div>
				</div>
			</div>";
		}
	}
	if ($row_tag_open == True){
		$row_tag_open = False;
		$out .= "</div>";
	}
	$out .= "</div>";
	return $out;
}
add_shortcode('timeline','shortcode_timeline');
function shortcode_timeline($attr,$content=""){
	$content = trim(strip_tags($content));
	$entries = explode("\n" , $content);
	$out = "<div class='argon-timeline'>";
	foreach($entries as $index => $value){
		$now = explode("|" , $value);
		$now[0] = str_replace("/" , "</br>" , $now[0]);
		$out .= "<div class='argon-timeline-node'>
					<div class='argon-timeline-time'>" . $now[0] . "</div>
					<div class='argon-timeline-card card bg-gradient-secondary shadow-sm'>";
		if ($now[1] != ''){
			$out .= "	<div class='argon-timeline-title'>" . $now[1] . "</div>";
		}
		$out .= "		<div class='argon-timeline-content'>";
		foreach($now as $index => $value){
			if ($index < 2){
				continue;
			}
			if ($index > 2){
				$out .= "</br>";
			}
			$out .= $value;
		}
		$out .= "		</div>
					</div>
				</div>";
	}
	$out .= "</div>";
	return $out;
}
add_shortcode('hidden','shortcode_hidden');
function shortcode_hidden($attr,$content=""){
	$out = "<span class='argon-hidden-text";
	$tip = isset($attr['tip']) ? $attr['tip'] : '';
	$type = isset($attr['type']) ? $attr['type'] : 'blur';
	if ($type == "background"){
		$out .= " argon-hidden-text-background";
	}else{
		$out .= " argon-hidden-text-blur";
	}
	$out .= "'";
	if ($tip != ''){
		$out .= " title='" . $tip ."'";
	}
	$out .= ">" . $content . "</span>";
	return $out;
}
add_shortcode('github','shortcode_github');
function shortcode_github($attr,$content=""){
	$github_info_card_id = mt_rand(1000000000 , 9999999999);
	$author = isset($attr['author']) ? $attr['author'] : '';
	$project = isset($attr['project']) ? $attr['project'] : '';
	$out = "<div class='github-info-card card shadow-sm' data-author='" . $author . "' data-project='" . $project . "' githubinfo-card-id='" . $github_info_card_id . "'>";
	$out .= "<div class='github-info-card-header'><a href='https://github.com/' ref='nofollow' target='_blank' title='Github' no-pjax><span><i class='fa fa-github'></i> Github</span></a></div>";
	$out .= "<div class='github-info-card-body'>
			<div class='github-info-card-name-a'>
				<a href='https://github.com/" . $author . "/" . $project . "' target='_blank' no-pjax>
					<span class='github-info-card-name'>" . $author . "/" . $project . "</span>
				</a>
				</div>
			<div class='github-info-card-description'>Loading...</div>
		</div>";
	$out .= "<div class='github-info-card-bottom'>
				<span class='github-info-card-meta'>
					<i class='fa fa-star'></i> <span class='github-info-card-stars'>-</span>
				</span>
				<span class='github-info-card-meta'>
					<i class='fa fa-code-fork'></i> <span class='github-info-card-forks'>-</span>
				</span>
			</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode('video','shortcode_video');
function shortcode_video($attr,$content=""){
	$url = isset($attr['url']) ? $attr['url'] : '';
	$width = isset($attr['width']) ? $attr['width'] : '';
	$height = isset($attr['height']) ? $attr['height'] : '';
	$autoplay = isset($attr['autoplay']) ? $attr['autoplay'] : 'false';
	$out = "<video";
	if ($width != ''){
		$out .= " width='" . $width . "'";
	}
	if ($height != ''){
		$out .= " height='" . $height . "'";
	}
	if ($autoplay == 'true'){
		$out .= " autoplay";
	}
	$out .= " controls>";
	$out .= "<source src='" . $url . "'>";
	$out .= "</video>";
	return $out;
}
add_shortcode('hide_reading_time','shortcode_hide_reading_time');
function shortcode_hide_reading_time($attr,$content=""){
	return "";
}
add_shortcode('post_time','shortcode_post_time');
function shortcode_post_time($attr,$content=""){
	$format = isset($attr['format']) ? $attr['format'] : 'Y-n-d G:i:s';
	return get_the_time($format);
}
add_shortcode('post_modified_time','shortcode_post_modified_time');
function shortcode_post_modified_time($attr,$content=""){
	$format = isset($attr['format']) ? $attr['format'] : 'Y-n-d G:i:s';
	return get_the_modified_time($format);
}
//主题选项页面
function themeoptions_admin_menu(){
	/*后台管理面板侧栏添加选项*/
	add_menu_page("Argon 主题设置", "Argon 主题选项", 'edit_themes', basename(__FILE__), 'themeoptions_page');
}
function themeoptions_page(){
	/*具体选项*/
?>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/jquery/jquery.min.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/headindex/headindex.js"></script>
	<div>
		<style type="text/css">
			h2{
				font-size: 22px;
			}
			h3{
				font-size: 18px;
			}
			th.subtitle {
				padding: 0;
			}
		</style>
		<h1>Argon 主题设置</h1>
		<form method="POST" action="" id="main_form">
			<input type="hidden" name="update_themeoptions" value="true" />
			<table class="form-table">
				<tbody>
					<tr><th class="subtitle"><h2>主题色</h2></th></tr>
					<tr>
						<th><label>主题颜色</label></th>
						<td>
							<input type="color" class="regular-text" name="argon_theme_color" value="<?php echo get_option('argon_theme_color') == "" ? "#5e72e4" : get_option('argon_theme_color'); ?>" style="height:40px;width: 80px;cursor: pointer;"/>
							<input type="text" readonly name="argon_theme_color_hex_preview" value="<?php echo get_option('argon_theme_color') == "" ? "#5e72e4" : get_option('argon_theme_color'); ?>" style="height: 40px;width: 80px;vertical-align: bottom;background: #fff;cursor: pointer;" onclick="$('input[name=\'argon_theme_color\']').click()"/></p>
							<p class="description"><div style="margin-top: 15px;">选择预置颜色 或 <span onclick="$('input[name=\'argon_theme_color\']').click()" style="text-decoration: underline;cursor: pointer;">自定义色值</span>
								</br></br>预置颜色：</div>
								<div class="themecolor-preview-container">
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#5e72e4;" color="#5e72e4"></div><div class="themecolor-name">Argon (默认)</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#fa7298;" color="#fa7298"></div><div class="themecolor-name">粉</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#009688;" color="#009688"></div><div class="themecolor-name">水鸭青</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#607d8b;" color="#607d8b"></div><div class="themecolor-name">蓝灰</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#2196f3;" color="#2196f3"></div><div class="themecolor-name">天蓝</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#3f51b5;" color="#3f51b5"></div><div class="themecolor-name">靛蓝</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#ff9700;" color="#ff9700"></div><div class="themecolor-name">橙</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#109d58;" color="#109d58"></div><div class="themecolor-name">绿</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#dc4437;" color="#dc4437"></div><div class="themecolor-name">红</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#673bb7;" color="#673bb7"></div><div class="themecolor-name">紫</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#212121;" color="#212121"></div><div class="themecolor-name">黑</div></div>
									<div class="themecolor-preview-box"><div class="themecolor-preview" style="background:#795547;" color="#795547"></div><div class="themecolor-name">棕</div></div>
								</div>
								</br>主题色与 <strong onclick="$('#headindex_box a[href=\'#header-id-5\']').click()" style="text-decoration: underline;cursor: pointer;">Banner 渐变背景样式</strong> 选项搭配使用效果更佳
								<script>
									$("input[name='argon_theme_color']").on("change" , function(){
										$("input[name='argon_theme_color_hex_preview']").val($("input[name='argon_theme_color']").val());
									});
									$(".themecolor-preview").on("click" , function(){
										$("input[name='argon_theme_color']").val($(this).attr("color"));
										$("input[name='argon_theme_color']").trigger("change");
									});
								</script>
								<style>
									.themecolor-name{width: 100px;text-align: center;}
									.themecolor-preview{width: 50px;height: 50px;margin: 20px 25px 5px 25px;line-height: 50px;color: #fff;margin-right: 0px;font-size: 15px;text-align: center;display: inline-block;border-radius: 50px;transition: all .3s ease;cursor: pointer;}
									.themecolor-preview-box{width: max-content;width: -moz-max-content;display: inline-block;}
									div.themecolor-preview:hover{transform: scale(1.1);}
									div.themecolor-preview:active{transform: scale(1.2);}
									.themecolor-preview-container{
										max-width: calc(100% - 180px);
									}
									@media screen and (max-width:960px){
										.themecolor-preview-container{
											max-width: unset;
										}
									}
								</style>

								<?php $argon_show_customize_theme_color_picker = get_option('argon_show_customize_theme_color_picker');?>
								<div style="margin-top: 15px;">
									<label>
										<input type="checkbox" name="argon_show_customize_theme_color_picker" value="true" <?php if ($argon_show_customize_theme_color_picker!='false'){echo 'checked';}?>/>	允许用户自定义主题色（位于博客浮动操作栏设置菜单中）
									</label>
								</div>
							</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>顶栏</h2></th></tr>
					<tr><th class="subtitle"><h3>标题</h3></th></tr>
					<tr>
						<th><label>顶栏标题</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_toolbar_title" value="<?php echo get_option('argon_toolbar_title'); ?>"/></p>
							<p class="description">留空则显示博客名称</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h3>顶栏图标</h3></th></tr>
					<tr>
						<th><label>图标地址</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_toolbar_icon" value="<?php echo get_option('argon_toolbar_icon'); ?>"/>
							<p class="description">图片地址，留空则不显示</p>
						</td>
					</tr>
					<tr>
						<th><label>图标链接</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_toolbar_icon_link" value="<?php echo get_option('argon_toolbar_icon_link'); ?>"/>
							<p class="description">点击图标后会跳转到的链接，留空则不跳转</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>顶部 Banner (封面)</h2></th></tr>
					<tr>
						<th><label>Banner 标题</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_banner_title" value="<?php echo get_option('argon_banner_title'); ?>"/>
							<p class="description">留空则显示博客名称</p>
						</td>
					</tr>
					<tr>
						<th><label>Banner 副标题</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_banner_subtitle" value="<?php echo get_option('argon_banner_subtitle'); ?>"/>
							<p class="description">显示在 Banner 标题下，留空则不显示</p>
						</td>
					</tr>
					<tr>
						<th><label>Banner 背景图 (地址)</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_banner_background_url" value="<?php echo get_option('argon_banner_background_url'); ?>"/>
							<p class="description">需带上 http(s) ，留空则显示默认背景</br>输入 <code>--bing--</code> 调用必应每日一图</p>
						</td>
					</tr>
					<tr>
						<th><label>Banner 渐变背景样式</label></th>
						<td>
							<select name="argon_banner_background_color_type">
								<?php $color_type = get_option('argon_banner_background_color_type'); ?>
								<option value="shape-primary" <?php if ($color_type=='shape-primary'){echo 'selected';} ?>>样式1</option>
								<option value="shape-default" <?php if ($color_type=='shape-default'){echo 'selected';} ?>>样式2</option>
								<option value="shape-dark" <?php if ($color_type=='shape-dark'){echo 'selected';} ?>>样式3</option>
								<option value="bg-gradient-success" <?php if ($color_type=='bg-gradient-success'){echo 'selected';} ?>>样式4</option>
								<option value="bg-gradient-info" <?php if ($color_type=='bg-gradient-info'){echo 'selected';} ?>>样式5</option>
								<option value="bg-gradient-warning" <?php if ($color_type=='bg-gradient-warning'){echo 'selected';} ?>>样式6</option>
								<option value="bg-gradient-danger" <?php if ($color_type=='bg-gradient-danger'){echo 'selected';} ?>>样式7</option>
							</select>
							<?php $hide_shapes = get_option('argon_banner_background_hide_shapes'); ?>
							<label>
								<input type="checkbox" name="argon_banner_background_hide_shapes" value="true" <?php if ($hide_shapes=='true'){echo 'checked';}?>/>	隐藏背景半透明圆
							</label>
							<p class="description"><strong>如果设置了背景图则不生效</strong>
								</br><div style="margin-top: 15px;">样式预览 (推荐选择前三个样式)</div>
								<div style="margin-top: 10px;">
									<div class="banner-background-color-type-preview" style="background:linear-gradient(150deg,#281483 15%,#8f6ed5 70%,#d782d9 94%);">样式1</div>
									<div class="banner-background-color-type-preview" style="background:linear-gradient(150deg,#7795f8 15%,#6772e5 70%,#555abf 94%);">样式2</div>
									<div class="banner-background-color-type-preview" style="background:linear-gradient(150deg,#32325d 15%,#32325d 70%,#32325d 94%);">样式3</div>
									<div class="banner-background-color-type-preview" style="background:linear-gradient(87deg,#2dce89 0,#2dcecc 100%);">样式4</div>
									<div class="banner-background-color-type-preview" style="background:linear-gradient(87deg,#11cdef 0,#1171ef 100%);">样式5</div>
									<div class="banner-background-color-type-preview" style="background:linear-gradient(87deg,#fb6340 0,#fbb140 100%);">样式6</div>
									<div class="banner-background-color-type-preview" style="background:linear-gradient(87deg,#f5365c 0,#f56036 100%);">样式7</div>
								</div>
								<style>
									div.banner-background-color-type-preview{width:100px;height:50px;line-height:50px;color:#fff;margin-right:0px;font-size:15px;text-align:center;display:inline-block;border-radius:5px;transition:all .3s ease;}
									div.banner-background-color-type-preview:hover{transform: scale(1.2);}
								</style>
							</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>页面背景</h2></th></tr>
					<tr>
						<th><label>页面背景</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_page_background_url" value="<?php echo get_option('argon_page_background_url'); ?>"/>
							<p class="description">页面背景的地址，需带上 http(s)。留空则不设置页面背景。如果设置了背景，推荐修改以下选项来增强页面整体观感。</p>
						</td>
					</tr>
					<tr>
						<th><label>背景不透明度</label></th>
						<td>
							<input type="number" name="argon_page_background_opacity" min="0" max="1" step="0.01" value="<?php echo (get_option('argon_page_background_opacity') == '' ? '1' : get_option('argon_page_background_opacity')); ?>"/>
							<p class="description">0 ~ 1 的小数，越小透明度越高，默认为 1 不透明</p>
						</td>
					</tr>
					<tr>
						<th><label>Banner 样式</label></th>
						<td>
							<input type="text" style="width:150px" class="regular-text" name="argon_page_background_banner_style" value="<?php echo get_option('argon_page_background_banner_style'); ?>"/>
							<p class="description">留空则不修改 Banner 样式。填 0 可以将 Banner 设为透明。填入正整数可以将 Banner 设为毛玻璃（高斯模糊）特效，模糊半径为填入的数。推荐填入 0。该选项仅在设置页面背景后才会生效。</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>左侧栏</h2></th></tr>
					<tr>
						<th><label>左侧栏标题</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_sidebar_banner_title" value="<?php echo get_option('argon_sidebar_banner_title'); ?>"/>
							<p class="description">留空则显示博客名称</p>
						</td>
					</tr>
					<tr>
						<th><label>左侧栏子标题（格言）</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_sidebar_banner_subtitle" value="<?php echo get_option('argon_sidebar_banner_subtitle'); ?>"/>
							<p class="description">留空则不显示</p>
						</td>
					</tr>
					<tr>
						<th><label>左侧栏作者名称</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_sidebar_auther_name" value="<?php echo get_option('argon_sidebar_auther_name'); ?>"/>
							<p class="description">留空则显示博客名</p>
						</td>
					</tr>
					<tr>
						<th><label>左侧栏作者头像地址</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_sidebar_auther_image" value="<?php echo get_option('argon_sidebar_auther_image'); ?>"/>
							<p class="description">需带上 http(s) 开头</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>左侧栏文章目录</h2></th></tr>
					<tr>
						<th><label>在目录中显示序号</label></th>
						<td>
							<select name="argon_show_headindex_number">
								<?php $argon_show_headindex_number = get_option('argon_show_headindex_number'); ?>
								<option value="false" <?php if ($argon_show_headindex_number=='false'){echo 'selected';} ?>>不显示</option>
								<option value="true" <?php if ($argon_show_headindex_number=='true'){echo 'selected';} ?>>显示</option>	
							</select>
							<p class="description">例：3.2.5</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>博客公告</h2></th></tr>
					<tr>
						<th><label>公告内容</label></th>
						<td>
							<textarea type="text" rows="5" cols="50" name="argon_sidebar_announcement"><?php echo htmlspecialchars(get_option('argon_sidebar_announcement')); ?></textarea>
							<p class="description">显示在左侧栏顶部，留空则不显示，支持 HTML 标签</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>浮动操作按钮</h2></th></tr>
					<tr><th class="subtitle"><p class="description">浮动操作按钮位于页面右下角（或左下角）</p></th></tr>
					<tr>
						<th><label>显示设置按钮</label></th>
						<td>	
							<select name="argon_fab_show_settings_button">
							<?php $argon_fab_show_settings_button = get_option('argon_fab_show_settings_button'); ?>
								<option value="true" <?php if ($argon_fab_show_settings_button=='true'){echo 'selected';} ?>>显示</option>
								<option value="false" <?php if ($argon_fab_show_settings_button=='false'){echo 'selected';} ?>>不显示</option>	
							</select>
							<p class="description">是否在浮动操作按钮栏中显示设置按钮。点击设置按钮可以唤出设置菜单修改夜间模式/字体/滤镜等外观选项。</p>
						</td>
					</tr>
					<tr>
						<th><label>显示夜间模式切换按钮</label></th>
						<td>	
							<select name="argon_fab_show_darkmode_button">
							<?php $argon_fab_show_darkmode_button = get_option('argon_fab_show_darkmode_button'); ?>
								<option value="false" <?php if ($argon_fab_show_darkmode_button=='false'){echo 'selected';} ?>>不显示</option>
								<option value="true" <?php if ($argon_fab_show_darkmode_button=='true'){echo 'selected';} ?>>显示</option>	
							</select>
							<p class="description">如果开启了设置按钮显示，建议关闭此选项。（夜间模式选项在设置菜单中已经存在）</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>SEO</h2></th></tr>
					<tr>
						<th><label>网站描述 (Description Meta 标签)</label></th>
						<td>
							<textarea type="text" rows="5" cols="100" name="argon_seo_description"><?php echo htmlspecialchars(get_option('argon_seo_description')); ?></textarea>
							<p class="description">设置针对搜索引擎的 Description Meta 标签内容。</br>在文章中，Argon 会自动根据文章内容生成描述。在其他页面中，Argon 将使用这里设置的内容。如不填，Argon 将不会在其他页面输出 Description Meta 标签。</p>
						</td>
					</tr>
					<tr>
						<th><label>搜索引擎关键词（Keywords Meta 标签）</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_seo_keywords" value="<?php echo get_option('argon_seo_keywords'); ?>"/>
							<p class="description">设置针对搜索引擎使用的关键词（Keywords Meta 标签内容）。用英文逗号隔开。不设置则不输出该 Meta 标签。</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>文章 Meta 信息</h2></th></tr>
					<tr>
						<th><label>显示字数和预计阅读时间</label></th>
						<td>
							<select name="argon_show_readingtime">
								<?php $argon_show_readingtime = get_option('argon_show_readingtime'); ?>
								<option value="true" <?php if ($argon_show_readingtime=='true'){echo 'selected';} ?>>显示</option>	
								<option value="false" <?php if ($argon_show_readingtime=='false'){echo 'selected';} ?>>不显示</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label>每分钟阅读字数</label></th>
						<td>
							<input type="number" name="argon_reading_speed" min="1" max="5000"  value="<?php echo (get_option('argon_reading_speed') == '' ? '300' : get_option('argon_reading_speed')); ?>"/>
							字/分钟
							<p class="description">预计阅读时间由每分钟阅读字数计算</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>文章头图 (特色图片)</h2></th></tr>
					<tr>
						<th><label>文章头图的位置</label></th>
						<td>
							<select name="argon_show_thumbnail_in_banner_in_content_page">
								<?php $argon_show_thumbnail_in_banner_in_content_page = get_option('argon_show_thumbnail_in_banner_in_content_page'); ?>
								<option value="false" <?php if ($argon_show_thumbnail_in_banner_in_content_page=='false'){echo 'selected';} ?>>文章卡片顶端</option>
								<option value="true" <?php if ($argon_show_thumbnail_in_banner_in_content_page=='true'){echo 'selected';} ?>>Banner (顶部背景)</option>	
							</select>
							<p class="description">阅读界面中文章头图的位置</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>分享</h2></th></tr>
					<tr>
						<th><label>显示文章分享按钮</label></th>
						<td>
							<select name="argon_show_sharebtn">
								<?php $argon_show_sharebtn = get_option('argon_show_sharebtn'); ?>
								<option value="true" <?php if ($argon_show_sharebtn=='true'){echo 'selected';} ?>>显示</option>	
								<option value="false" <?php if ($argon_show_sharebtn=='false'){echo 'selected';} ?>>不显示</option>
							</select>
							<p class="description"></p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>赞赏</h2></th></tr>
					<tr>
						<th><label>赞赏二维码图片链接</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_donate_qrcode_url" value="<?php echo get_option('argon_donate_qrcode_url'); ?>"/>				
							<p class="description">赞赏二维码图片链接，填写后会在文章最后显示赞赏按钮，留空则不显示赞赏按钮</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>页脚</h2></th></tr>
					<tr>
						<th><label>页脚内容</label></th>
						<td>
							<textarea type="text" rows="15" cols="100" name="argon_footer_html"><?php echo htmlspecialchars(get_option('argon_footer_html')); ?></textarea>
							<p class="description">HTML , 支持 script 等标签</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>Mathjax 渲染</h2></th></tr>
					<tr>
						<th><label>启用 Mathjax (v3)</label></th>
						<td>
							<select name="argon_mathjax_enable">
								<?php $argon_mathjax_enable = get_option('argon_mathjax_enable'); ?>
								<option value="false" <?php if ($argon_mathjax_enable=='false'){echo 'selected';} ?>>不启用</option>
								<option value="true" <?php if ($argon_mathjax_enable=='true'){echo 'selected';} ?>>启用</option>	
							</select>
							<p class="description">
								Mathjax 是一个 Latex 前端渲染库，可以自动解析文章中的 Latex 公式并渲染。</br>
								Argon 主题内置了 Mathjax 库的引用 (3.0.0 版本, jsdelivr CDN)</br>
								如果你需要用到公式，请打开这个选项</br>
								Argon 主题提供了一些 Mathjax 的常用配置项</br>
								或者，如果需要更详细的配置选项，你可以在这里禁用 Mathjax ，然后在 "页脚代码" 中引用 Mathjax 并编写配置 JSON (当然也可以用插件来实现)</br>
								一般来说，这里的配置选项已经够用，使用 Argon 主题提供的默认配置即可</br>
								使用 $xxx$ 或 \\xxx\\ 来标记一个行内公式，$$xxx$$ 来标记一个独立公式</br>
							</p>
						</td>
					</tr>
					<tr>
						<th><label>Mathjax CDN 地址</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_mathjax_cdn_url" value="<?php echo get_option('argon_mathjax_cdn_url') == '' ? '//cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js' : get_option('argon_mathjax_cdn_url'); ?>"/>
							<p class="description">Mathjax 3.0+，默认为 <code>//cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js</code></p>
						</td>
					</tr>
					<tr style="opacity: .6;" class="opacity-on-hover-1">
						<th><label>启用旧版 Mathjax (v2)</label></th>
						<td>
							<select name="argon_mathjax_v2_enable">
								<?php $argon_mathjax_v2_enable = get_option('argon_mathjax_v2_enable'); ?>
								<option value="false" <?php if ($argon_mathjax_v2_enable=='false'){echo 'selected';} ?>>不启用</option>
								<option value="true" <?php if ($argon_mathjax_v2_enable=='true'){echo 'selected';} ?>>启用</option>	
							</select>
							<p class="description">
								为了兼容性，Argon 保留了 Mathjax v2 旧版库的引用 (2.7.5 版本, jsdelivr CDN)</br>
								推荐使用 Mathjax 3.0，而不要开启此选项</br>
								该选项仅在 <strong>Mathjax 3.0 选项关闭时才生效</strong></br>
							</p>
						</td>
					</tr>
					<tr style="opacity: .6;" class="opacity-on-hover-1">
						<th><label>Mathjax V2 CDN 地址</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_mathjax_v2_cdn_url" value="<?php echo get_option('argon_mathjax_v2_cdn_url') == '' ? '//cdn.jsdelivr.net/npm/mathjax@2.7.5/MathJax.js?config=TeX-AMS_HTML' : get_option('argon_mathjax_v2_cdn_url'); ?>"/>
							<p class="description">Mathjax 2.0+，默认为 <code>//cdn.jsdelivr.net/npm/mathjax@2.7.5/MathJax.js?config=TeX-AMS_HTML</code></br>
								该地址仅对 Mathjax V2 生效
							</p>
						</td>
					</tr>
					<style>
						.opacity-on-hover-1{
							transition: all .3s ease;
						}
						.opacity-on-hover-1:hover{
							opacity: 1 !important;
						}
					</style>
					<tr><th class="subtitle"><h2>Lazyload</h2></th></tr>
					<tr>
						<th><label>是否启用 Lazyload</label></th>
						<td>
							<select name="argon_enable_lazyload">
								<?php $argon_enable_lazyload = get_option('argon_enable_lazyload'); ?>
								<option value="true" <?php if ($argon_enable_lazyload=='true'){echo 'selected';} ?>>启用</option>
								<option value="false" <?php if ($argon_enable_lazyload=='false'){echo 'selected';} ?>>禁用</option>
							</select>
							<p class="description">是否启用 Lazyload 加载文章内图片</p>
						</td>
					</tr>
					<tr>
						<th><label>提前加载阈值</label></th>
						<td>
							<input type="number" name="argon_lazyload_threshold" min="0" max="2500"  value="<?php echo (get_option('argon_lazyload_threshold') == '' ? '800' : get_option('argon_lazyload_threshold')); ?>"/>
							<p class="description">图片距离页面底部还有多少距离就开始提前加载</p>
						</td>
					</tr>
					<tr>
						<th><label>LazyLoad 图片加载完成过渡</label></th>
						<td>
							<select name="argon_lazyload_effect">
								<?php $argon_lazyload_effect = get_option('argon_lazyload_effect'); ?>
								<option value="fadeIn" <?php if ($argon_lazyload_effect=='fadeIn'){echo 'selected';} ?>>fadeIn</option>
								<option value="slideDown" <?php if ($argon_lazyload_effect=='slideDown'){echo 'selected';} ?>>slideDown</option>
								<option value="none" <?php if ($argon_lazyload_effect=='none'){echo 'selected';} ?>>不使用过渡</option>
							</select>
							<p class="description"></p>
						</td>
					</tr>
					<tr>
						<th><label>LazyLoad 图片加载动效</label></th>
						<td>
							<select name="argon_lazyload_loading_style">
								<?php $argon_lazyload_loading_style = get_option('argon_lazyload_loading_style'); ?>
								<option value="1" <?php if ($argon_lazyload_loading_style=='1'){echo 'selected';} ?>>加载动画 1</option>
								<option value="2" <?php if ($argon_lazyload_loading_style=='2'){echo 'selected';} ?>>加载动画 2</option>
								<option value="3" <?php if ($argon_lazyload_loading_style=='3'){echo 'selected';} ?>>加载动画 3</option>
								<option value="4" <?php if ($argon_lazyload_loading_style=='4'){echo 'selected';} ?>>加载动画 4</option>
								<option value="5" <?php if ($argon_lazyload_loading_style=='5'){echo 'selected';} ?>>加载动画 5</option>
								<option value="6" <?php if ($argon_lazyload_loading_style=='6'){echo 'selected';} ?>>加载动画 6</option>
								<option value="7" <?php if ($argon_lazyload_loading_style=='7'){echo 'selected';} ?>>加载动画 7</option>
								<option value="8" <?php if ($argon_lazyload_loading_style=='8'){echo 'selected';} ?>>加载动画 8</option>
								<option value="9" <?php if ($argon_lazyload_loading_style=='9'){echo 'selected';} ?>>加载动画 9</option>
								<option value="10" <?php if ($argon_lazyload_loading_style=='10'){echo 'selected';} ?>>加载动画 10</option>
								<option value="11" <?php if ($argon_lazyload_loading_style=='11'){echo 'selected';} ?>>加载动画 11</option>
								<option value="none" <?php if ($argon_lazyload_loading_style=='none'){echo 'selected';} ?>>不使用</option>
							</select>
							<p class="description">在图片被加载之前显示的加载效果 , <a target="_blank" href="<?php bloginfo('template_url'); ?>/assets/vendor/svg-loaders">预览所有效果</a></p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>图片放大浏览</h2></th></tr>
					<tr>
						<th><label>是否启用图片放大浏览</label></th>
						<td>
							<select name="argon_enable_zoomify">
								<?php $argon_enable_zoomify = get_option('argon_enable_zoomify'); ?>
								<option value="true" <?php if ($argon_enable_zoomify=='true'){echo 'selected';} ?>>启用</option>
								<option value="false" <?php if ($argon_enable_zoomify=='false'){echo 'selected';} ?>>禁用</option>
							</select>
							<p class="description">开启后，文章中图片被单击时会放大预览</p>
						</td>
					</tr>
					<tr>
						<th><label>缩放动画长度</label></th>
						<td>
							<input type="number" name="argon_zoomify_duration" min="0" max="10000" value="<?php echo (get_option('argon_zoomify_duration') == '' ? '200' : get_option('argon_zoomify_duration')); ?>"/>	ms
							<p class="description">图片被单击后缩放到全屏动画的时间长度</p>
						</td>
					</tr>
					<tr>
						<th><label>缩放动画曲线</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_zoomify_easing" value="<?php echo (get_option('argon_zoomify_easing') == '' ? 'ease-out' : get_option('argon_zoomify_easing')); ?>"/>
							<p class="description">
								例： <code>ease</code> , <code>ease-in-out</code> , <code>ease-out</code> , <code>linear</code> , <code>cubic-bezier(0.4,0,0,1)</code></br>如果你不知道这是什么，参考<a href="https://www.w3school.com.cn/cssref/pr_animation-timing-function.asp" target="_blank">这里</a>
							</p>
						</td>
					</tr>
					<tr>
						<th><label>图片最大缩放比例</label></th>
						<td>
							<input type="number" name="argon_zoomify_scale" min="0.01" max="1" step="0.01" value="<?php echo (get_option('argon_zoomify_scale') == '' ? '0.9' : get_option('argon_zoomify_scale')); ?>"/>
							<p class="description">图片相对于页面的最大缩放比例 (0 ~ 1 的小数)</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>脚本</h2></th></tr>
					<tr>
						<th><label><strong style="color:#ff0000;">注意</strong></label></th>
						<td>
							<p class="description"><strong style="color:#ff0000;">Argon 使用 pjax 方式加载页面 (无刷新加载) , 所以您的脚本除非页面手动刷新，否则只会被执行一次。</br>
							如果您想让每次页面跳转(加载新页面)时都执行脚本，请将脚本写入 <code>window.pjaxLoaded</code> 中</strong> ，示例写法:
							<pre>
window.pjaxLoaded = function(){
	//页面每次跳转都会执行这里的代码
	//do something...
}
							</pre>
							<strong style="color:#ff0000;">当页面第一次载入时，<code>window.pjaxLoaded</code> 中的脚本不会执行，所以您可以手动执行 <code>window.pjaxLoaded();</code> 来让页面初次加载时也执行脚本</strong></p>
						</td>
					</tr>
					<tr>
						<th><label>页头脚本</label></th>
						<td>
							<textarea type="text" rows="15" cols="100" name="argon_custom_html_head"><?php echo htmlspecialchars(get_option('argon_custom_html_head')); ?></textarea>
							<p class="description">HTML , 支持 script 等标签</br>插入到 body 之前</p>
						</td>
					</tr>
					<tr>
						<th><label>页尾脚本</label></th>
						<td>
							<textarea type="text" rows="15" cols="100" name="argon_custom_html_foot"><?php echo htmlspecialchars(get_option('argon_custom_html_foot')); ?></textarea>
							<p class="description">HTML , 支持 script 等标签</br>插入到 body 之后</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>动画</h2></th></tr>
					<tr>
						<th><label>是否启用平滑滚动</label></th>
						<td>
							<select name="argon_enable_smoothscroll_type">
								<?php $enable_smoothscroll_type = get_option('argon_enable_smoothscroll_type'); ?>
								<option value="1" <?php if ($enable_smoothscroll_type=='1'){echo 'selected';} ?>>使用平滑滚动方案 1 (平滑) (推荐)</option>
								<option value="1_pulse" <?php if ($enable_smoothscroll_type=='1_pulse'){echo 'selected';} ?>>使用平滑滚动方案 1 (脉冲式滚动) (仿 Edge) (推荐)</option>
								<option value="2" <?php if ($enable_smoothscroll_type=='2'){echo 'selected';} ?>>使用平滑滚动方案 2 (较稳)</option>
								<option value="3" <?php if ($enable_smoothscroll_type=='3'){echo 'selected';} ?>>使用平滑滚动方案 3</option>
								<option value="disabled" <?php if ($enable_smoothscroll_type=='disabled'){echo 'selected';} ?>>不使用平滑滚动</option>
							</select>
							<p class="description">能增强浏览体验，但可能出现一些小问题，如果有问题请切换方案或关闭平滑滚动</p>
						</td>
					</tr>
					<tr>
						<th><label>是否启用进入文章动画</label></th>
						<td>
							<select name="argon_enable_into_article_animation">
								<?php $argon_enable_into_article_animation = get_option('argon_enable_into_article_animation'); ?>
								<option value="false" <?php if ($argon_enable_into_article_animation=='false'){echo 'selected';} ?>>不启用</option>
								<option value="true" <?php if ($argon_enable_into_article_animation=='true'){echo 'selected';} ?>>启用</option>	
							</select>
							<p class="description">从首页或分类目录进入文章时，使用平滑过渡（可能影响加载文章时的性能）</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>评论区</h2></th></tr>
					<tr>
						<th><label>是否隐藏 "昵称"、"邮箱"、"网站" 输入框</label></th>
						<td>
							<select name="argon_hide_name_email_site_input">
								<?php $argon_hide_name_email_site_input = get_option('argon_hide_name_email_site_input'); ?>
								<option value="false" <?php if ($argon_hide_name_email_site_input=='false'){echo 'selected';} ?>>不隐藏</option>
								<option value="true" <?php if ($argon_hide_name_email_site_input=='true'){echo 'selected';} ?>>隐藏</option>	
							</select>
							<p class="description">该选项仅在 "设置-评论-评论作者必须填入姓名和电子邮件地址" 选项未勾选的前提下生效。如勾选了 "评论作者必须填入姓名和电子邮件地址"，则只有 "网站" 输入框会被隐藏。</p>
						</td>
					</tr>
					<tr>
						<th><label>评论是否需要验证码</label></th>
						<td>
							<select name="argon_comment_need_captcha">
								<?php $argon_comment_need_captcha = get_option('argon_comment_need_captcha'); ?>
								<option value="true" <?php if ($argon_comment_need_captcha=='true'){echo 'selected';} ?>>需要</option>	
								<option value="false" <?php if ($argon_comment_need_captcha=='false'){echo 'selected';} ?>>不需要</option>
							</select>
							<p class="description"></p>
						</td>
					</tr>				
					<tr><th class="subtitle"><h2>其他</h2></th></tr>
					<tr>
						<th><label>是否使用 v2ex CDN 代理的 gravatar</label></th>
						<td>
							<select name="argon_enable_v2ex_gravatar">
								<?php $enable_v2ex_gravatar = get_option('argon_enable_v2ex_gravatar'); ?>
								<option value="false" <?php if ($enable_v2ex_gravatar=='false'){echo 'selected';} ?>>不使用</option>
								<option value="true" <?php if ($enable_v2ex_gravatar=='true'){echo 'selected';} ?>>使用</option>	
							</select>
							<p class="description">建议使用，可以大幅增加国内 gravatar 头像加载的速度</p>
						</td>
					</tr>
					<tr>
						<th><label>是否修正时区错误</label></th>
						<td>
							<select name="argon_enable_timezone_fix">
								<?php $argon_enable_timezone_fix = get_option('argon_enable_timezone_fix'); ?>
								<option value="false" <?php if ($argon_enable_timezone_fix=='false'){echo 'selected';} ?>>关闭</option>
								<option value="true" <?php if ($argon_enable_timezone_fix=='true'){echo 'selected';} ?>>开启</option>	
							</select>
							<p class="description">如遇到时区错误（例如一条刚发的评论显示 8 小时前），这个选项<strong>可能</strong>可以修复这个问题</p>
						</td>
					</tr>
					<tr>
						<th><label>是否在文章列表内容预览中隐藏短代码</label></th>
						<td>
							<select name="argon_hide_shortcode_in_preview">
								<?php $argon_hide_shortcode_in_preview = get_option('argon_hide_shortcode_in_preview'); ?>
								<option value="false" <?php if ($argon_hide_shortcode_in_preview=='false'){echo 'selected';} ?>>否</option>
								<option value="true" <?php if ($argon_hide_shortcode_in_preview=='true'){echo 'selected';} ?>>是</option>	
							</select>
							<p class="description"></p>
						</td>
					</tr>
					<tr>
						<th><label>是否允许移动端缩放页面</label></th>
						<td>
							<select name="argon_enable_mobile_scale">
								<?php $argon_enable_mobile_scale = get_option('argon_enable_mobile_scale'); ?>
								<option value="false" <?php if ($argon_enable_mobile_scale=='false'){echo 'selected';} ?>>否</option>
								<option value="true" <?php if ($argon_enable_mobile_scale=='true'){echo 'selected';} ?>>是</option>	
							</select>
							<p class="description"></p>
						</td>
					</tr>
					<tr>
						<th><label>检测更新源</label></th>
						<td>
							<select name="argon_update_source">
								<?php $argon_update_source = get_option('argon_update_source'); ?>
								<option value="github" <?php if ($argon_update_source=='github'){echo 'selected';} ?>>Github</option>
								<option value="abc233site" <?php if ($argon_update_source=='abc233site'){echo 'selected';} ?>>solstice23.top</option>
								<option value="stop" <?php if ($argon_update_source=='stop'){echo 'selected';} ?>>暂停更新 (不推荐)</option>
							</select>
							<p class="description">如更新主题速度较慢，可考虑更换更新源。</p>
						</td>
					</tr>
					<tr>
						<th><label>页脚附加内容</label></th>
						<td>
							<select name="argon_hide_footer_author">
								<?php $argon_hide_footer_author = get_option('argon_hide_footer_author'); ?>
								<option value="false" <?php if ($argon_hide_footer_author=='false'){echo 'selected';} ?>>Theme Argon By solstice23</option>
								<option value="true" <?php if ($argon_hide_footer_author=='true'){echo 'selected';} ?>>Theme Argon</option>
							</select>
							<p class="description"></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改"></p>
		</form>
	</div>
	<div id="headindex_box">
		<button id="headindex_toggler" onclick="$('#headindex_box').toggleClass('folded');">收起</button>
		<div id="headindex"></div>
	</div>
	<script type="text/javascript">
		$(function () {
			$(document).headIndex({
				articleWrapSelector: '#main_form',
				indexBoxSelector: '#headindex',
				subItemBoxClass: "index-subItem-box",
				itemClass: "index-item",
				linkClass: "index-link",
				offset: 80,
			});
		})
	</script>
	<style>
		#headindex_box {
			position: fixed;
			right: 10px;
			top: 50px;
			max-width: 180px;
			max-height: calc(100vh - 100px);
			opacity: .8;
			transition: all .3s ease;
			background: #fff;
			box-shadow: 0 1px 1px rgba(0,0,0,.04);
			padding: 6px 30px 6px 20px;
			overflow-y: auto;
		}
		.index-subItem-box {
			margin-left: 20px;
			margin-top: 10px;
		}
		.index-link {
			color: #23282d;
			text-decoration: unset;
			transition: all .3s ease;
			box-shadow: none !important;
		}
		.index-item {
			padding: 1px 0;
		}
		.index-item.current > a {
			color: #0073aa;
			font-weight: 600;
			box-shadow: none !important;
		}
		#headindex_toggler{
			position: absolute;
			right: 5px;
			top: 5px;
			color: #555;
			background: #f7f7f7;
			box-shadow: 0 1px 0 #ccc;
			outline: none !important;
			border: 1px solid #ccc;
			border-radius: 2px;
			cursor: pointer;
			width: 40px;
			height: 25px;
			font-size: 12px;
		}
		#headindex_box.folded {
			right: -185px;
		}
		#headindex_box.folded #headindex_toggler{
			position: fixed;
			right: 15px;
			top: 55px;
			font-size: 0px;
		}
		#headindex_box.folded #headindex_toggler:before{
			content: '展开';
			font-size: 12px;
		}
		@media screen and (max-width:960px){
			#headindex_box {
				display: none;
			}
		}
	</style>
<?php
}
add_action('admin_menu', 'themeoptions_admin_menu');
if ($_POST['update_themeoptions']== 'true'){
	//配置项
	update_option('argon_toolbar_icon', $_POST['argon_toolbar_icon']);
	update_option('argon_toolbar_icon_link', $_POST['argon_toolbar_icon_link']);
	update_option('argon_toolbar_title', $_POST['argon_toolbar_title']);
	update_option('argon_sidebar_banner_title', $_POST['argon_sidebar_banner_title']);
	update_option('argon_sidebar_banner_subtitle', $_POST['argon_sidebar_banner_subtitle']);
	update_option('argon_sidebar_auther_name', $_POST['argon_sidebar_auther_name']);
	update_option('argon_sidebar_auther_image', $_POST['argon_sidebar_auther_image']);
	update_option('argon_banner_title', $_POST['argon_banner_title']);
	update_option('argon_banner_subtitle', $_POST['argon_banner_subtitle']);
	update_option('argon_banner_background_url', $_POST['argon_banner_background_url']);
	update_option('argon_banner_background_color_type', $_POST['argon_banner_background_color_type']);
	update_option('argon_banner_background_hide_shapes', $_POST['argon_banner_background_hide_shapes']);
	update_option('argon_enable_smoothscroll_type', $_POST['argon_enable_smoothscroll_type']);
	update_option('argon_enable_v2ex_gravatar', $_POST['argon_enable_v2ex_gravatar']);
	update_option('argon_footer_html', stripslashes($_POST['argon_footer_html']));
	update_option('argon_show_readingtime', $_POST['argon_show_readingtime']);
	update_option('argon_reading_speed', $_POST['argon_reading_speed']);
	update_option('argon_show_sharebtn', $_POST['argon_show_sharebtn']);
	update_option('argon_enable_timezone_fix', $_POST['argon_enable_timezone_fix']);
	update_option('argon_donate_qrcode_url', $_POST['argon_donate_qrcode_url']);
	update_option('argon_hide_shortcode_in_preview', $_POST['argon_hide_shortcode_in_preview']);
	update_option('argon_show_thumbnail_in_banner_in_content_page', $_POST['argon_show_thumbnail_in_banner_in_content_page']);
	update_option('argon_update_source', $_POST['argon_update_source']);
	update_option('argon_enable_into_article_animation', $_POST['argon_enable_into_article_animation']);
	update_option('argon_fab_show_darkmode_button', $_POST['argon_fab_show_darkmode_button']);
	update_option('argon_fab_show_settings_button', $_POST['argon_fab_show_settings_button']);
	update_option('argon_show_headindex_number', $_POST['argon_show_headindex_number']);
	update_option('argon_theme_color', $_POST['argon_theme_color']);
	update_option('argon_show_customize_theme_color_picker', ($_POST['argon_show_customize_theme_color_picker'] == 'true')?'true':'false');
	update_option('argon_seo_description', stripslashes($_POST['argon_seo_description']));
	update_option('argon_seo_keywords', $_POST['argon_seo_keywords']);
	update_option('argon_enable_mobile_scale', $_POST['argon_enable_mobile_scale']);
	update_option('argon_page_background_url', $_POST['argon_page_background_url']);
	update_option('argon_page_background_opacity', $_POST['argon_page_background_opacity']);
	update_option('argon_page_background_banner_style', $_POST['argon_page_background_banner_style']);
	update_option('argon_hide_name_email_site_input', $_POST['argon_hide_name_email_site_input']);
	update_option('argon_comment_need_captcha', $_POST['argon_comment_need_captcha']);
	update_option('argon_hide_footer_author', $_POST['argon_hide_footer_author']);

	//LazyLoad 相关
	update_option('argon_enable_lazyload', $_POST['argon_enable_lazyload']);
	update_option('argon_lazyload_effect', $_POST['argon_lazyload_effect']);
	update_option('argon_lazyload_threshold', $_POST['argon_lazyload_threshold']);
	update_option('argon_lazyload_loading_style', $_POST['argon_lazyload_loading_style']);

	//Zoomify 相关
	update_option('argon_enable_zoomify', $_POST['argon_enable_zoomify']);
	update_option('argon_zoomify_duration', $_POST['argon_zoomify_duration']);
	update_option('argon_zoomify_easing', $_POST['argon_zoomify_easing']);
	update_option('argon_zoomify_scale', $_POST['argon_zoomify_scale']);

	//Mathjax 相关配置项
	update_option('argon_mathjax_enable', $_POST['argon_mathjax_enable']);
	update_option('argon_mathjax_cdn_url', $_POST['argon_mathjax_cdn_url']);
	update_option('argon_mathjax_v2_enable', $_POST['argon_mathjax_v2_enable']);
	update_option('argon_mathjax_v2_cdn_url', $_POST['argon_mathjax_v2_cdn_url']);
	/*update_option('argon_mathjax_loading_msg_type', $_POST['argon_mathjax_loading_msg_type']);
	update_option('argon_mathjax_zoom_cond', $_POST['argon_mathjax_zoom_cond']);
	update_option('argon_mathjax_zoom_scale', $_POST['argon_mathjax_zoom_scale']);
	update_option('argon_mathjax_show_menu', $_POST['argon_mathjax_show_menu']);*/

	//页头页尾脚本
	update_option('argon_custom_html_head', stripslashes($_POST['argon_custom_html_head']));
	update_option('argon_custom_html_foot', stripslashes($_POST['argon_custom_html_foot']));

	//公告
	update_option('argon_sidebar_announcement', stripslashes($_POST['argon_sidebar_announcement']));
}
/*主题菜单*/
register_nav_menus( array(
	'toolbar_menu' => '顶部导航',
	'leftbar_menu' => '左侧栏菜单',
	'leftbar_author_links' => '左侧栏作者个人链接',
	'leftbar_friend_links' => '左侧栏友情链接'
));


//隐藏 admin 管理条
/*show_admin_bar(false);*/

/*说说*/
add_action('init', 'init_shuoshuo');
function init_shuoshuo(){
	$labels = array(
		'name' => '说说',
		'singular_name' => '说说',
		'add_new' => '发表说说',
		'add_new_item' => '发表说说',
		'edit_item' => '编辑说说',
		'new_item' => '新说说',
		'view_item' => '查看说说',
		'search_items' => '搜索说说',
		'not_found' => '暂无说说',
		'not_found_in_trash' => '没有已遗弃的说说',
		'parent_item_colon' => '',
		'menu_name' => '说说'
	);
	$args = array(
		'labels' => $labels,
		'public' => true, 
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'exclude_from_search' => true,
		'query_var' => true, 
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => 'shuoshuo',
		'hierarchical' => false, 
		'menu_position' => null,
		'menu_icon' => 'dashicons-format-quote',
		'supports' => array('editor', 'author', 'title', 'custom-fields', 'comments')
	);
	register_post_type('shuoshuo', $args); 
}