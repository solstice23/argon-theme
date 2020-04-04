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
else if (get_option('argon_update_source') == 'solstice23top' || get_option('argon_update_source') == 'abc233site'){
	$argonThemeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://api.solstice23.top/argon/info.json?source=0',
		get_template_directory() . '/functions.php',
		'argon'
	);
}else if (get_option('argon_update_source') == 'jsdelivr'){
	$argonThemeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://api.solstice23.top/argon/info.json?source=jsdelivr',
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
function get_argon_formatted_paginate_links($maxPageNumbers, $extraClasses = ''){
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
	$from = max($current - ($maxPageNumbers - 1) / 2 , 1);
	$to = min($current + $maxPageNumbers - ( $current - $from + 1 ) , $total);
	if ($to - $from + 1 < $maxPageNumbers){
		$to = min($current + ($maxPageNumbers - 1) / 2 , $total);
		$from = max($current - ( $maxPageNumbers - ( $to - $current + 1 ) ) , 1);
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
	return '<nav><ul class="pagination' . $extraClasses . '">' . $html . '</ul></nav>';
}
function get_argon_formatted_paginate_links_for_all_platforms(){
	return get_argon_formatted_paginate_links(7) . get_argon_formatted_paginate_links(5, " pagination-mobile");
}
//访问者 Token
function get_random_token(){
	return md5(uniqid(microtime(true), true));
}
function set_user_token_cookie(){
	if (strlen($_COOKIE["argon_user_token"]) != 32){
		$newToken = get_random_token();
		setcookie("argon_user_token", $newToken, time() + 10 * 365 * 24 * 60 * 60, "/");
		$_COOKIE["argon_user_token"] = $newToken;
	}
}
set_user_token_cookie();
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
	if ($_GET['preview'] == 'true'){
		if (current_user_can('publish_posts')){
			return;
		}
	}
	if ($_POST['no_post_view'] == 'true'){
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
	if (strpos(get_the_content() , "[hide_reading_time][/hide_reading_time]") !== False){
		return true;
	}
	global $post;
	if (get_post_meta($post -> ID, 'argon_hide_readingtime', true) == 'true'){
		return true;
	}
	return false;
}
//当前文章是否隐藏 发布时间和分类 (简洁 Meta)
function is_meta_simple(){
	global $post;
	if (get_post_meta($post -> ID, 'argon_meta_simple', true) == 'true'){
		return true;
	}
	return false;
}
//根据文章 id 获取标题
function get_post_title_by_id($id){
	return get_post($id) -> post_title;
}
//发送邮件
function send_mail($to, $subject, $content){
	wp_mail($to, $subject, $content, array('Content-Type: text/html; charset=UTF-8'));
}
function check_email_address($email){
	if (!preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/", $email)) {
		return false;
	}
	return true;
}
//检验评论 Token 和用户 Token 是否一致
function check_comment_token($id){
	if (strlen($_COOKIE['argon_user_token']) != 32){
		return false;
	}
	if ($_COOKIE['argon_user_token'] != get_comment_meta($id, "user_token", true)){
		return false;
	}
	return true;
}
//检验评论发送者 ID 和当前登录用户 ID 是否一致
function check_login_user_same($userid){
	if ($userid == 0){
		return false;
	}
	global $current_user;
	get_currentuserinfo();
	if ($userid != ($current_user -> ID)){
		return false;
	}
	return true;
}
function get_comment_user_id_by_id($comment_ID){
	$comment = get_comment($comment_ID);
	return $comment -> user_id;
}
function check_comment_userid($id){
	if (!check_login_user_same(get_comment_user_id_by_id($id))){
		return false;
	}
	return true;
}
//悄悄话
function is_comment_private_mode($id){
	if (strlen(get_comment_meta($id, "private_mode", true)) != 32){
		return false;
	}
	return true;
}
function user_can_view_comment($id){
	if (!is_comment_private_mode($id)){
		return true;
	}
	if (current_user_can("manage_options")){
		return true;
	}
	if ($_COOKIE['argon_user_token'] == get_comment_meta($id, "private_mode", true)){
		return true;
	}
	return false;
}
//是否可以查看评论编辑记录
function can_visit_comment_edit_history($id){
	$who_can_visit_comment_edit_history = get_option("argon_who_can_visit_comment_edit_history");
	if ($who_can_visit_comment_edit_history == ""){
		$who_can_visit_comment_edit_history = "admin";
	}
	switch ($who_can_visit_comment_edit_history) {
		case 'everyone':
			return true;
			break;

		case 'commentsender':
			if (check_comment_token($id) || check_comment_userid($id)){
				return true;
			}
			return false;
			break;

		default:
			if (current_user_can("manage_options")){
				return true;
			}
			return false;
			break;
	}
	return false;
}
//获取评论编辑记录
function get_comment_edit_history(){
	$id = $_POST['id'];
	if (!can_visit_comment_edit_history($id)){
		exit(json_encode(array(
			'id' => $_POST['id'],
			'history' => ""
		)));
	}
	$editHistory = json_decode(get_comment_meta($id, "comment_edit_history", true));
	$editHistory = array_reverse($editHistory);
	$res = "";
	$position = count($editHistory) + 1;
	date_default_timezone_set(get_option('timezone_string'));
	foreach ($editHistory as $edition){
		$position -= 1;
		$res .= "<div class='comment-edit-history-item'>
					<div class='comment-edit-history-title'>
						<div class='comment-edit-history-id'>
							#" . $position . "
						</div>
						" . ($edition -> isfirst ? "<span class='badge badge-primary badge-admin'>最初版本</span>" : "") . "
					</div>
					<div class='comment-edit-history-time'>" . date('Y-m-d H:i:s', $edition -> time) . "</div>
					<div class='comment-edit-history-content'>" . $edition -> content . "</div>
				</div>";
	}
	exit(json_encode(array(
		'id' => $_POST['id'],
		'history' => $res
	)));
}
add_action('wp_ajax_get_comment_edit_history', 'get_comment_edit_history');
add_action('wp_ajax_nopriv_get_comment_edit_history', 'get_comment_edit_history');
//评论样式格式化
function argon_comment_format($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;
	if (user_can_view_comment(get_comment_ID())){
	?>
	<li class="comment-item" id="comment-<?php comment_ID(); ?>" data-id="<?php comment_ID(); ?>" data-use-markdown="<?php echo get_comment_meta(get_comment_ID(), "use_markdown", true);?>">
		<div class="comment-item-avatar">
			<?php if(function_exists('get_avatar') && get_option('show_avatars')){
				echo get_avatar($comment, 40);
			}?>
		</div>
		<div class="comment-item-inner" id="comment-inner-<?php comment_ID();?>">
			<div class="comment-item-title">
				<?php echo get_comment_author_link();?>
				<?php if (user_can($comment -> user_id , "update_core")){
					echo '<span class="badge badge-primary badge-admin">博主</span>';}
				?>
				<?php if (is_comment_private_mode(get_comment_ID()) && user_can_view_comment(get_comment_ID())){
					echo '<span class="badge badge-success badge-private-comment">悄悄话</span>';}
				?>
				<?php if ($comment -> comment_approved == 0){
					echo '<span class="badge badge-warning badge-unapproved">待审核</span>';}
				?>
			</div>
			<div class="comment-item-text">
				<?php comment_text();?>
			</div>
			<div class="comment-item-source" style="display: none;" aria-hidden="true"><?php echo htmlspecialchars(get_comment_meta(get_comment_ID(), "comment_content_source", true));?></div>
			<div class="comment-info">
				<?php if (get_comment_meta(get_comment_ID(), "edited", true) == "true") { ?>
					<div class="comment-edited<?php if (can_visit_comment_edit_history(get_comment_ID())){echo ' comment-edithistory-accessible';}?>">
						<i class="fa fa-pencil" aria-hidden="true"></i>已编辑
					</div>
				<?php } ?>
				<div class="comment-time">
					<?php echo human_time_diff(get_comment_time('U') , current_time('timestamp')) . "前";?>
					<div class="comment-time-details"><?php echo get_comment_time('Y-n-d G:i:s');?></div>
				</div>
			</div>

			<div class="comment-operations">
				<?php if ((check_comment_token(get_comment_ID()) || check_login_user_same($comment -> user_id)) && (get_option("argon_comment_allow_editing") != "false")) { ?>
					<button class="comment-edit btn btn-sm btn-outline-primary" data-id="<?php comment_ID(); ?>" type="button" style="margin-right: 2px;">编辑</button>
				<?php } ?>
				<button class="comment-reply btn btn-sm btn-outline-primary" data-id="<?php comment_ID(); ?>" type="button">回复</button>
			</div>
		</div>
	</li>
	<li class="comment-divider"></li>
	<li>
<?php }}
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
	exit(json_encode(array(
		'status' => 'failed',
		'msg' => '验证码错误',
		'isAdmin' => current_user_can('level_7')
	)));
	//wp_die('验证码错误，评论失败');
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
//Ajax 发送评论
function ajax_post_comment(){
	$parentID = $_POST['comment_parent'];
	if (is_comment_private_mode($parentID)){
		if (!user_can_view_comment($parentID)){
			//如果父级评论是悄悄话模式且当前 Token 与父级不相同则返回
			exit(json_encode(array(
				'status' => 'failed',
				'msg' => '不能回复其他人的悄悄话评论',
				'isAdmin' => current_user_can('level_7')
			)));
		}
	}
	$comment = wp_handle_comment_submission(wp_unslash($_POST));
	if (is_wp_error($comment)){
		$msg = $comment -> get_error_data();
		if (!empty($msg)){
			$msg = $comment -> get_error_message();
		}
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => $msg,
			'isAdmin' => current_user_can('level_7')
		)));
	}
	$user = wp_get_current_user();
	do_action('set_comment_cookies', $comment, $user);
	$html = wp_list_comments(
		array(
			'type'      => 'comment',
			'callback'  => 'argon_comment_format',
			'echo'      => false
		),
		array($comment)
	);
	$newCaptchaSeed = get_comment_captcha_seed();
	$newCaptcha = get_comment_captcha($newCaptchaSeed);
	if (current_user_can('level_7')){
		$newCaptchaAnswer = get_comment_captcha_answer($newCaptchaSeed);
	}else{
		$newCaptchaAnswer = "";
	}
	exit(json_encode(array(
		'status' => 'success',
		'html' => $html,
		'id' => $comment -> comment_ID,
		'parentID' => $comment -> comment_parent,
		'commentOrder' => (get_option("comment_order") == "" ? "desc" : get_option("comment_order")),
		'newCaptchaSeed' => $newCaptchaSeed,
		'newCaptcha' => $newCaptcha,
		'newCaptchaAnswer' => $newCaptchaAnswer,
		'isAdmin' => current_user_can('level_7'),
		'isLogin' => is_user_logged_in()
	)));
}
add_action('wp_ajax_ajax_post_comment', 'ajax_post_comment');
add_action('wp_ajax_nopriv_ajax_post_comment', 'ajax_post_comment');
//评论 Markdown 解析
require_once(get_template_directory() . '/parsedown.php');
function comment_markdown_parse($comment_content){
	//HTML 过滤
	global $allowedtags; 
	//$comment_content = wp_kses($comment_content, $allowedtags);
	//允许评论中额外的 HTML Tag
	$allowedtags['pre'] = array('class' => array());
	$allowedtags['i'] = array('class' => array(), 'aria-hidden' => array()); 
	$allowedtags['img'] = array('src' => array(), 'alt' => array(), 'class' => array());
	$allowedtags['ol'] = array();
	$allowedtags['ul'] = array();
	$allowedtags['li'] = array();
	$allowedtags['a']['class'] = array();
	$allowedtags['a']['data-src'] = array();
	$allowedtags['a']['target'] = array();
	$allowedtags['h1'] = $allowedtags['h2'] = $allowedtags['h3'] = $allowedtags['h4'] = $allowedtags['h5'] = $allowedtags['h6'] = array();

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
//评论发送处理
function post_comment_preprocessing($comment){
	//保存评论未经 Markdown 解析的源码
	$_POST['comment_content_source'] = $comment['comment_content'];
	//Markdown
	if ($_POST['use_markdown'] == 'true' && get_option("argon_comment_allow_markdown") != "false"){
		$comment['comment_content'] = comment_markdown_parse($comment['comment_content']);
	}
	return $comment;
}
add_filter('preprocess_comment' , 'post_comment_preprocessing');
//发送评论通知邮件
function comment_mail_notify($comment){
	if (get_option("argon_comment_allow_mailnotice") != "true"){
		return;
	}
	if ($comment == null){
		return;
	}
	$id = $comment -> comment_ID;
	$commentPostID = $comment -> comment_post_ID;
	$commentAuthor = $comment -> comment_author;
	$parentID = $comment -> comment_parent;
	if ($parentID == 0){
		return;
	}
	$parentComment = get_comment($parentID);
	$parentEmail =  $parentComment -> comment_author_email;
	if (get_comment_meta($parentID, "enable_mailnotice", true) == "true"){
		if (check_email_address($parentEmail)){
			$title = "您在 「" . wp_trim_words(get_post_title_by_id($commentPostID), 20) . "」 的评论有了新的回复";
			$fullTitle = "您在 「" . get_post_title_by_id($commentPostID) . "」 的评论有了新的回复";
			$content = htmlspecialchars(get_comment_meta($id, "comment_content_source", true));
			$link = get_permalink($commentPostID) . "#comment-" . $id;
			$unsubscribeLink = site_url("unsubscribe-comment-mailnotice?comment=" . $parentID . "&token=" . get_comment_meta($parentID, "mailnotice_unsubscribe_key", true));
			$html = "<div style='background: #fff;box-shadow: 0 15px 35px rgba(50,50,93,.1), 0 5px 15px rgba(0,0,0,.07);border-radius: 6px;margin: 15px auto 50px auto;padding: 35px 30px;max-width: min(calc(100% - 100px), 1200px);'>
					<div style='font-size:30px;text-align:center;margin-bottom:15px;'>" . htmlspecialchars($fullTitle)  ."</div>
					<div style='background: rgba(0, 0, 0, .15);height: 1px;width: 300px;margin: auto;margin-bottom: 35px;'></div>
					<div style='font-size: 18px;border-left: 4px solid rgba(0, 0, 0, .15);width: max-content;width: -moz-max-content;margin: auto;padding: 20px 30px;background: rgba(0,0,0,.08);border-radius: 6px;box-shadow: 0 2px 4px rgba(0,0,0,.075)!important;min-width: 60%;max-width: 90%;margin-bottom: 60px;'>
						<div style='margin-bottom: 10px;'><strong><span style='color: #5e72e4;'>@" . htmlspecialchars($commentAuthor) . "</span> 回复了你:</strong></div>
						" . str_replace("\n", "<div></div>", $content) . " 
					</div>
					<div style='width: max-content;width: --moz-max-content;margin: auto;margin-bottom:50px;'>
						<a href='" . $link . "' style='color: #fff;background-color: #5e72e4;border-color: #5e72e4;box-shadow: 0 4px 6px rgba(50,50,93,.11), 0 1px 3px rgba(0,0,0,.08);padding: 15px 25px;font-size: 18px;border-radius: 4px;text-decoration: none;'>前往查看</a>
					</div>
					<div style='width: max-content;width: --moz-max-content;margin: auto;margin-bottom:30px;'>
						<a href='" . $unsubscribeLink . "' style='color: #5e72e4;font-size: 16px;text-decoration: none;'>退订该评论的邮件提醒</a>
					</div>
				</div>";
			send_mail($parentEmail, $title, $html);
		}
	}
}
//评论发送完成添加 Meta
function post_comment_updatemetas($id){
	$parentID = $_POST['comment_parent'];
	$comment = get_comment($id);
	$commentPostID = $comment -> comment_post_ID;
	$commentAuthor = $comment -> comment_author;
	$mailnoticeUnsubscribeKey = get_random_token();
	//评论 Markdown 源码
	update_comment_meta($id, "comment_content_source", $_POST['comment_content_source']);
	//评论者 Token
	set_user_token_cookie();
	update_comment_meta($id, "user_token", $_COOKIE["argon_user_token"]);
	//保存初次编辑记录
	$editHistory = array(array(
		'content' => $_POST['comment_content_source'],
		'time' => time(),
		'isfirst' => true
	));
	update_comment_meta($id, "comment_edit_history", json_encode($editHistory, JSON_UNESCAPED_UNICODE));
	//是否启用 Markdown
	if ($_POST['use_markdown'] == 'true' && get_option("argon_comment_allow_markdown") != "false"){
		update_comment_meta($id, "use_markdown", "true");
	}else{
		update_comment_meta($id, "use_markdown", "false");
	}
	//是否启用悄悄话模式
	if ($_POST['private_mode'] == 'true' && get_option("argon_comment_allow_privatemode") == "true"){
		update_comment_meta($id, "private_mode", $_COOKIE["argon_user_token"]);
	}else{
		update_comment_meta($id, "private_mode", "false");	
	}
	if (is_comment_private_mode($parentID)){
		//如果父级评论是悄悄话模式则将当前评论可查看者的 Token 跟随父级评论者的 Token
		update_comment_meta($id, "private_mode", get_comment_meta($parentID, "private_mode", true));
	}
	if ($parentID!= 0 && !is_comment_private_mode($parentID)){
		//如果父级评论不是悄悄话模式则当前评论也不是悄悄话模式
		update_comment_meta($id, "private_mode", "false");
	}
	//是否启用邮件通知
	if ($_POST['enable_mailnotice'] == 'true' && get_option("argon_comment_allow_mailnotice") == "true"){
		update_comment_meta($id, "enable_mailnotice", "true");
		update_comment_meta($id, "mailnotice_unsubscribe_key", $mailnoticeUnsubscribeKey);
	}else{
		update_comment_meta($id, "enable_mailnotice", "false");	
	}
	//向父级评论发送邮件
	if ($comment -> comment_approved == 1){
		comment_mail_notify($comment);
	}
}
add_action('comment_post' , 'post_comment_updatemetas');
add_action('comment_unapproved_to_approved', 'comment_mail_notify');
add_rewrite_rule('^unsubscribe-comment-mailnotice/?(.*)$', '/wp-content/themes/argon/unsubscribe-comment-mailnotice.php$1', 'top');
//编辑评论
function user_edit_comment(){
	header('Content-Type:application/json; charset=utf-8');
	if (get_option("argon_comment_allow_editing") == "false"){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => '博主关闭了编辑评论功能'
		)));
	}
	$id = $_POST["id"];
	$content = $_POST["comment"];
	$contentSource = $content;
	if (!check_comment_token($id) && !check_login_user_same(get_comment_user_id_by_id($id))){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => '您不是这条评论的作者或 Token 已过期'
		)));
	}
	if ($_POST["comment"] == ""){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => '新的评论为空'
		)));
	}
	if (get_comment_meta($id, "use_markdown", true) == "true"){
		$content = comment_markdown_parse($content);
	}
	$res = wp_update_comment(array(
		'comment_ID' => $id,
		'comment_content' => $content
	));
	if ($res == 1){
		update_comment_meta($id, "comment_content_source", $contentSource);
		update_comment_meta($id, "edited", "true");
		//保存编辑历史
		$editHistory = json_decode(get_comment_meta($id, "comment_edit_history", true));
		array_push($editHistory, array(
			'content' => htmlspecialchars(stripslashes($contentSource)),
			'time' => time(),
			'isfirst' => false
		));
		update_comment_meta($id, "comment_edit_history", json_encode($editHistory, JSON_UNESCAPED_UNICODE));
		exit(json_encode(array(
			'status' => 'success',
			'msg' => '编辑评论成功',
			'new_comment' => apply_filters('comment_text', get_comment_text($id), $id),
			'new_comment_source' => htmlspecialchars(stripslashes($contentSource)),
			'can_visit_edit_history' => can_visit_comment_edit_history($id)
		)));
	}else{
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => '编辑评论失败，可能原因: 与原评论相同'
		)));
	}
}
add_action('wp_ajax_user_edit_comment', 'user_edit_comment');
add_action('wp_ajax_nopriv_user_edit_comment', 'user_edit_comment');
//输出评论分页页码
function get_argon_formatted_comment_paginate_links($maxPageNumbers, $extraClasses = ''){
	$args = array(
		'prev_text' => '',
		'next_text' => '',
		'before_page_number' => '',
		'after_page_number' => '',
		'show_all' => True,
		'echo' => False
	);
	$res = paginate_comments_links($args);
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
	$from = max($current - ($maxPageNumbers - 1) / 2 , 1);
	$to = min($current + $maxPageNumbers - ( $current - $from + 1 ) , $total);
	if ($to - $from + 1 < $maxPageNumbers){
		$to = min($current + ($maxPageNumbers - 1) / 2 , $total);
		$from = max($current - ( $maxPageNumbers - ( $to - $current + 1 ) ) , 1);
	}
	//生成新页码
	$html = "";
	if ($from > 1){
		$html .= '<li class="page-item"><div aria-label="First Page" class="page-link" href="' . $urls[1] . '"><i class="fa fa-angle-double-left" aria-hidden="true"></i></div></li>';
	}
	if ($current > 1){
		$html .= '<li class="page-item"><div aria-label="Previous Page" class="page-link" href="' . $urls[$current - 1] . '"><i class="fa fa-angle-left" aria-hidden="true"></i></div></li>';
	}
	for ($i = $from; $i <= $to; $i++){
		if ($current == $i){
			$html .= '<li class="page-item active"><span class="page-link" style="cursor: default;">' . $i . '</span></li>';
		}else{
			$html .= '<li class="page-item"><div class="page-link" href="' . $urls[$i] . '">' . $i . '</div></li>';
		}
	}
	if ($current < $total){
		$html .= '<li class="page-item"><div aria-label="Next Page" class="page-link" href="' . $urls[$current + 1] . '"><i class="fa fa-angle-right" aria-hidden="true"></i></div></li>';
	}
	if ($to < $total){
		$html .= '<li class="page-item"><div aria-label="Last Page" class="page-link" href="' . $urls[$total] . '"><i class="fa fa-angle-double-right" aria-hidden="true"></i></div></li>';
	}
	return '<nav id="comments_navigation" class="comments-navigation"><ul class="pagination' . $extraClasses . '">' . $html . '</ul></nav>';
}
function get_argon_formatted_comment_paginate_links_for_all_platforms(){
	return get_argon_formatted_comment_paginate_links(7) . get_argon_formatted_comment_paginate_links(5, " pagination-mobile");
}
function get_argon_comment_paginate_links_prev_url(){
	$args = array(
		'prev_text' => '',
		'next_text' => '',
		'before_page_number' => '',
		'after_page_number' => '',
		'show_all' => True,
		'echo' => False
	);
	$str = paginate_comments_links($args);
	//单引号转双引号
	$str = preg_replace(
		'/\'/',
		'"',
		$str
	);
	//获取上一页地址
	$url = "";
	preg_match(
		'/<a class="prev page-numbers" href="(.*?)">(.*?)<\/a>/',
		$str,
		$url
	);
	return $url[1];
}
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
//Pangu 插入空格
function argon_pangu($content){
	$argon_option_enable_pangu = get_option('argon_enable_pangu');
	$content .= '<script>
	$(function() {';
	if (strpos($argon_option_enable_pangu, 'article') !== false){
		$content .= "pangu.spacingElementById('post_content');";
	}
	if (strpos($argon_option_enable_pangu, 'comment') !== false){
		$content .= "pangu.spacingElementById('comments');";
	}
	$content .= '});
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
	if (get_option('argon_enable_pangu') != 'false' && get_option('argon_enable_pangu') != ''){
		$content = argon_pangu($content);
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
	if ((strpos($footer, "github.com/solstice23/argon-theme") === false) && (strpos($footer, "solstice23.top") === false) && (strpos($footer, "solstice23.top") === false)){
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
//编辑文章界面新增 Meta 编辑模块
function argon_meta_box_1(){
	wp_nonce_field("argon_meta_box_nonce_action", "argon_meta_box_nonce");
	global $post;
	?>
		<h4>显示字数和预计阅读时间</h4>
		<?php $argon_meta_hide_readingtime = get_post_meta($post->ID, "argon_hide_readingtime", true);?>
		<select name="argon_meta_hide_readingtime" id="argon_meta_hide_readingtime">
			<option value="false" <?php if ($argon_meta_hide_readingtime=='false'){echo 'selected';} ?>>跟随全局设置</option>
			<option value="true" <?php if ($argon_meta_hide_readingtime=='true'){echo 'selected';} ?>>不显示</option>
		</select>
		<p style="margin-top: 15px;">是否显示字数和预计阅读时间 Meta 信息</p>
		<h4>Meta 中隐藏发布时间和分类</h4>
		<?php $argon_meta_simple = get_post_meta($post->ID, "argon_meta_simple", true);?>
		<select name="argon_meta_simple" id="argon_meta_simple">
			<option value="false" <?php if ($argon_meta_simple=='false'){echo 'selected';} ?>>不隐藏</option>
			<option value="true" <?php if ($argon_meta_simple=='true'){echo 'selected';} ?>>隐藏</option>
		</select>
		<p style="margin-top: 15px;">适合固定的页面，例如友链页面。开启后文章 Meta 的第一行只显示阅读数和评论数。</p>
	<?php
}
function argon_add_meta_boxes(){
	add_meta_box('argon_meta_box_1', "文章设置", 'argon_meta_box_1', array('post', 'page'), 'side', 'low');
}
add_action('admin_menu', 'argon_add_meta_boxes');
function argon_save_meta_data($post_id){
	if (!isset($_POST['argon_meta_box_nonce'])){
		return $post_id; 
	}
	$nonce = $_POST['argon_meta_box_nonce'];
	if (!wp_verify_nonce($nonce, 'argon_meta_box_nonce_action')){
		return $post_id; 
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return $post_id; 
	}
	if ($_POST['post_type'] == 'post'){
		if (!current_user_can('edit_post', $post_id)){
			return $post_id;
		}
	}
	if ($_POST['post_type'] == 'page'){
		if (!current_user_can('edit_page', $post_id)){
			return $post_id;
		}
	}
	update_post_meta($post_id, 'argon_hide_readingtime', $_POST['argon_meta_hide_readingtime']);
	update_post_meta($post_id, 'argon_meta_simple', $_POST['argon_meta_simple']);
}
add_action('save_post', 'argon_save_meta_data');
//首页显示说说
function argon_home_add_post_type_shuoshuo($query){
	if (is_home() && $query -> is_main_query()){
		$query -> set('post_type', array('post', 'shuoshuo'));
	}
	return $query;
}
if (get_option("argon_home_show_shuoshuo") == "true"){
	add_action('pre_get_posts', 'argon_home_add_post_type_shuoshuo');
}
//文章过时信息显示
function argon_get_post_outdated_info(){
	if (get_option("argon_outdated_info_tip_type") == "toast"){
		$before = "<div id='post_outdate_toast' style='display:none;' data-text='";
		$after = "'></div>";
	}else{
		$before = "<div class='post-outdated-info'><i class='fa fa-info-circle' aria-hidden='true'></i>";
		$after = "</div>";
	}
	$content = get_option('argon_outdated_info_tip_content') == '' ? '本文最后更新于 %date_delta% 天前，其中的信息可能已经有所发展或是发生改变。' : get_option('argon_outdated_info_tip_content');
	$delta = get_option('argon_outdated_info_days') == '' ? (-1) : get_option('argon_outdated_info_days');
	if ($delta == -1){
		$delta = 2147483647;
	}
	if (get_option("argon_outdated_info_time_type") == "createdtime"){
		$post_delta = floor((time() - get_the_time("U")) / (60 * 60 * 24));
	}else{
		$post_delta = floor((time() - get_the_modified_time("U")) / (60 * 60 * 24));
	}
	if ($post_delta <= $delta){
		return "";
	}
	$content = str_replace("%date_delta%", $post_delta, $content);
	return $before . $content . $after;
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
	$sort = isset($attr['sort']) ? $attr['sort'] : 'name';
	$order = isset($attr['order']) ? $attr['order'] : 'ASC';
	$friendlinks = get_bookmarks( array(
		'orderby' => $sort ,
		'order'   => $order
	));
	$style = isset($attr['style']) ? $attr['style'] : '1';
	switch ($style) {
		case '1':
			$class = "friend-links-style1";
			break;
		case '1-square':
			$class = "friend-links-style1 friend-links-style1-square";
			break;
		case '2':
			$class = "friend-links-style2";
			break;
		case '2-big':
			$class = "friend-links-style2 friend-links-style2-big";
			break;
		default:
			$class = "friend-links-style1";
			break;
	}
	$out = "<div class='friend-links " . $class . "'><div class='row'>";
	foreach ($friendlinks as $friendlink){
		$out .= "
			<div class='link mb-2 col-lg-6 col-md-6'>
				<div class='card shadow-sm friend-link-container'>";
		if ($friendlink -> link_image != ''){
			$out .= "
					<img src='" . $friendlink -> link_image . "' class='friend-link-avatar bg-gradient-secondary'>";
		}else{
			$out .= "
					<img class='friend-link-avatar bg-gradient-secondary'></img>";
		}
		$out .= "	<div class='friend-link-content'>
						<div class='friend-link-title title text-primary'>
							<a target='_blank' href='" . esc_url($friendlink -> link_url) . "'>" . esc_html($friendlink -> link_name) . "</a>
						</div>
						<div class='friend-link-description'>" . esc_html($friendlink -> link_description) . "</div>";
		$out .= "		<div class='friend-link-links'>";
		foreach (explode("\n", $friendlink -> link_notes) as $line){
			$item = explode("|", trim($line));
			if(stripos($item[0], "fa-") !== 0){
				continue;
			}
			$out .= "<a href='" . esc_url($item[1]) . "' target='_blank'><i class='fa " . sanitize_html_class($item[0]) . "'></i></a>";
		}
		$out .= "<a href='" . esc_url($friendlink -> link_url) . "' target='_blank' style='float:right; margin-right: 10px;'><i class='fa fa-angle-right' style='font-weight: bold;'></i></a>";
		$out .= "
						</div>
					</div>
				</div>
			</div>";
	}
	$out .= "</div></div>";
	?>
	<?php
	get_template_part( 'template-parts/friendlinks', "style1" );
	return $out;
}
add_shortcode('sfriendlinks','shortcode_friend_link_simple');
function shortcode_friend_link_simple($attr,$content=""){
	$content = trim(strip_tags($content));
	$entries = explode("\n" , $content);

	$shuffle = isset($attr['shuffle']) ? $attr['shuffle'] : 'false';
	if ($shuffle == "true"){
		mt_srand();
		$group_start = 0;
		foreach ($entries as $index => $value){
			$now = explode("|" , $value);
			if ($now[0] == 'category'){
				echo ($index-1).",".$group_start." | ";
				for ($i = $index - 1; $i >= $group_start; $i--){
					echo $i."#";
					$tar = mt_rand($group_start , $i);
					$tmp = $entries[$tar];
					$entries[$tar] = $entries[$i];
					$entries[$i] = $tmp;
				}
				$group_start = $index + 1;
			}
		}
		for ($i = count($entries) - 1; $i >= $group_start; $i--){
			$tar = mt_rand($group_start , $i);
			$tmp = $entries[$tar];
			$entries[$tar] = $entries[$i];
			$entries[$i] = $tmp;
		}
	}

	$row_tag_open = False;
	$out = "<div class='friend-links-simple'>";
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
	$getdata = isset($attr['getdata']) ? $attr['getdata'] : 'frontend';

	$description = "";
	$stars = "";
	$forks = "";

	if ($getdata == "backend"){
		set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
			if (error_reporting() === 0) {
				return false;
			}
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		});
		try{
			$contexts = stream_context_create(
				array(
					'http' => array(
						'method'=>"GET",
						'header'=>"User-Agent: ArgonTheme\r\n"
					)
				)
			);
			$json = file_get_contents("https://api.github.com/repos/" . $author . "/" . $project, false, $contexts);
			if (empty($json)){ 
				throw new Exception("");
			}
			$json = json_decode($json);
			$description = esc_html($json -> description);
			if (!empty($json -> homepage)){
				$description .= esc_html(" <a href='" . $json -> homepage . "' target='_blank' no-pjax>" . $json -> homepage . "</a>");
			}
			$stars = $json -> stargazers_count;
			$forks = $json -> forks_count;
		}catch (Exception $e){
			$getdata = "frontend";
		}
		restore_error_handler();
	}

	$out = "<div class='github-info-card card shadow-sm' data-author='" . $author . "' data-project='" . $project . "' githubinfo-card-id='" . $github_info_card_id . "' data-getdata='" . $getdata . "' data-description='" . $description . "' data-stars='" . $stars . "' data-forks='" . $forks . "'>";
	$out .= "<div class='github-info-card-header'><a href='https://github.com/' ref='nofollow' target='_blank' title='Github' no-pjax><span><i class='fa fa-github'></i> Github</span></a></div>";
	$out .= "<div class='github-info-card-body'>
			<div class='github-info-card-name-a'>
				<a href='https://github.com/" . $author . "/" . $project . "' target='_blank' no-pjax>
					<span class='github-info-card-name'>" . $author . "/" . $project . "</span>
				</a>
				</div>
			<div class='github-info-card-description'></div>
		</div>";
	$out .= "<div class='github-info-card-bottom'>
				<span class='github-info-card-meta'>
					<i class='fa fa-star'></i> <span class='github-info-card-stars'></span>
				</span>
				<span class='github-info-card-meta'>
					<i class='fa fa-code-fork'></i> <span class='github-info-card-forks'></span>
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
add_shortcode('noshortcode','shortcode_noshortcode');
function shortcode_noshortcode($attr,$content=""){
	return $content;
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
				font-size: 25px;
			}
			h2:before {
				content: '';
				background: #000;
				height: 16px;
				width: 6px;
				display: inline-block;
				border-radius: 15px;
				margin-right: 15px;
			}
			h3{
				font-size: 18px;
			}
			th.subtitle {
				padding: 0;
			}
		</style>
		<h1>Argon 主题设置</h1>
		<p>按下 <kbd style="font-family: sans-serif;">Ctrl + F</kbd> 来查找设置，或在右侧目录中查找设置</p>
		<form method="POST" action="" id="main_form">
			<input type="hidden" name="update_themeoptions" value="true" />
			<?php wp_nonce_field("argon_update_themeoptions", "argon_update_themeoptions_nonce");?>
			<table class="form-table">
				<tbody>
					<tr><th class="subtitle"><h2>全局</h2></th></tr>
					<tr><th class="subtitle"><h3>主题色</h3></th></tr>
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
					<tr><th class="subtitle"><h3>夜间模式</h3></th></tr>
					<tr>
						<th><label>夜间模式切换方案</label></th>
						<td>
							<select name="argon_darkmode_autoswitch">
								<?php $argon_darkmode_autoswitch = get_option('argon_darkmode_autoswitch'); ?>
								<option value="false" <?php if ($argon_darkmode_autoswitch=='false'){echo 'selected';} ?>>默认使用日间模式</option>
								<option value="alwayson" <?php if ($argon_darkmode_autoswitch=='alwayson'){echo 'selected';} ?>>默认使用夜间模式</option>
								<option value="system" <?php if ($argon_darkmode_autoswitch=='system'){echo 'selected';} ?>>跟随系统夜间模式</option>
								<option value="time" <?php if ($argon_darkmode_autoswitch=='time'){echo 'selected';} ?>>根据时间切换夜间模式 (22:00 ~ 7:00)</option>
							</select>
							<p class="description">Argon 主题会根据这里的选项来决定是否默认使用夜间模式。</br>用户也可以手动切换夜间模式，用户的设置将保留到标签页关闭为止。</p>
						</td>
					</tr>
					<tr>
						<th><label>夜间模式颜色方案</label></th>
						<td>
							<select name="argon_enable_amoled_dark">
								<?php $argon_enable_amoled_dark = get_option('argon_enable_amoled_dark'); ?>
								<option value="false" <?php if ($argon_enable_amoled_dark=='false'){echo 'selected';} ?>>灰黑</option>
								<option value="true" <?php if ($argon_enable_amoled_dark=='true'){echo 'selected';} ?>>暗黑 (AMOLED Black)</option>
							</select>
							<p class="description">夜间模式默认的配色方案。</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h3>圆角</h3></th></tr>
					<tr>
						<th><label>卡片圆角大小</label></th>
						<td>
							<input type="number" name="argon_card_radius" min="0" max="30" step="0.5" value="<?php echo (get_option('argon_card_radius') == '' ? '4' : get_option('argon_card_radius')); ?>"/>	px
							<p class="description">卡片的圆角大小，默认为 <code>4px</code>。建议设置在 <code>2px</code> 和 <code>15px</code> 之间。</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h3>布局</h3></th></tr>
					<tr>
						<th><label>页面布局</label></th>
						<td>
							<div class="radio-with-img">
								<?php $argon_page_layout = (get_option('argon_page_layout') == '' ? 'double' : get_option('argon_page_layout')); ?>
								<div class="radio-img">
									<svg width="250" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080"><rect width="1920" height="1080" style="fill:#e6e6e6"/><g style="opacity:0.5"><rect width="1920" height="381" style="fill:#5e72e4"/></g><rect x="388.5" y="256" width="258" height="179" style="fill:#5e72e4"/><rect x="388.5" y="470" width="258" height="485" style="fill:#fff"/><rect x="689.5" y="256.5" width="842" height="250" style="fill:#fff"/><rect x="689.5" y="536.5" width="842" height="250" style="fill:#fff"/><rect x="689.5" y="817" width="842" height="250" style="fill:#fff"/></svg>
								</div>
								<label><input name="argon_page_layout" type="radio" value="double" <?php if ($argon_page_layout=='double'){echo 'checked';} ?>> 双栏</label>
							</div>
							<div class="radio-with-img">
								<div class="radio-img">
									<svg width="250" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080"><rect width="1920" height="1080" style="fill:#e6e6e6"/><g style="opacity:0.5"><rect width="1920" height="381" style="fill:#5e72e4"/></g><rect x="428.25" y="256.5" width="1063.5" height="250" style="fill:#fff"/><rect x="428.25" y="536.5" width="1063.5" height="250" style="fill:#fff"/><rect x="428.25" y="817" width="1063.5" height="250" style="fill:#fff"/></svg>
								</div>
								<label><input name="argon_page_layout" type="radio" value="single" <?php if ($argon_page_layout=='single'){echo 'checked';} ?>> 单栏</label>
							</div>
							<p class="description" style="margin-top: 15px;">使用单栏时，关于侧栏的设置将失效。</p>
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
					<tr><th class="subtitle"><h3>动画</h3></th></tr>
					<tr>
						<th><label>Banner 标题打字动画</label></th>
						<td>
							<select name="argon_enable_banner_title_typing_effect">
							<?php $argon_enable_banner_title_typing_effect = get_option('argon_enable_banner_title_typing_effect'); ?>
								<option value="false" <?php if ($argon_enable_banner_title_typing_effect=='false'){echo 'selected';} ?>>不启用</option>
								<option value="true" <?php if ($argon_enable_banner_title_typing_effect=='true'){echo 'selected';} ?>>启用</option>
							</select>
							<p class="description">启用后 Banner 标题会以打字的形式出现。</p>
						</td>
					</tr>
					<tr>
						<th><label>Banner 标题打字动画时长</label></th>
						<td>
							<input type="number" name="argon_banner_typing_effect_interval" min="1" max="10000"  value="<?php echo (get_option('argon_banner_typing_effect_interval') == '' ? '100' : get_option('argon_banner_typing_effect_interval')); ?>"/> ms/字
							<p class="description"></p>
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
						<th><label>页面背景（夜间模式时）</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_page_background_dark_url" value="<?php echo get_option('argon_page_background_dark_url'); ?>"/>
							<p class="description">夜间模式时页面背景的地址，需带上 http(s)。设置后日间模式和夜间模式会使用不同的背景。留空则跟随日间模式背景。该选项仅在设置了日间模式背景时生效。</p>
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
						<th><label>Banner 透明化</label></th>
						<td>
							<select name="argon_page_background_banner_style">
								<?php $argon_page_background_banner_style = get_option('argon_page_background_banner_style'); ?>
								<option value="false" <?php if ($argon_page_background_banner_style=='false'){echo 'selected';} ?>>关闭</option>	
								<option value="transparent" <?php if ($argon_page_background_banner_style=='transparent' || ($argon_page_background_banner_style!='' && $argon_page_background_banner_style!='false')){echo 'selected';} ?>>开启</option>
							</select>
							<div style="margin-top: 15px;margin-bottom: 15px;">
								<label>
									<?php $argon_show_toolbar_mask = get_option('argon_show_toolbar_mask');?>
									<input type="checkbox" name="argon_show_toolbar_mask" value="true" <?php if ($argon_show_toolbar_mask=='true'){echo 'checked';}?>/>	在顶栏添加浅色遮罩，Banner 标题添加阴影（当背景过亮影响文字阅读时勾选）
								</label>
							</div>
							<p class="description">Banner 透明化可以使博客背景沉浸。建议在设置背景时开启此选项。该选项仅会在设置页面背景时生效。</p>
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
					<tr>
						<th><label>显示跳转到评论按钮</label></th>
						<td>	
							<select name="argon_fab_show_gotocomment_button">
							<?php $argon_fab_show_gotocomment_button = get_option('argon_fab_show_gotocomment_button'); ?>
								<option value="false" <?php if ($argon_fab_show_gotocomment_button=='false'){echo 'selected';} ?>>不显示</option>
								<option value="true" <?php if ($argon_fab_show_gotocomment_button=='true'){echo 'selected';} ?>>显示</option>
							</select>
							<p class="description">仅在允许评论的文章中显示</p>
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
					<tr><th class="subtitle"><h2>文章</h2></th></tr>
					<tr><th class="subtitle"><h3>文章 Meta 信息</h3></th></tr>
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
					<tr><th class="subtitle"><h3>文章头图 (特色图片)</h3></th></tr>
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
					<tr><th class="subtitle"><h3>分享</h3></th></tr>
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
					<tr><th class="subtitle"><h3>左侧栏文章目录</h3></th></tr>
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
					<tr><th class="subtitle"><h3>赞赏</h3></th></tr>
					<tr>
						<th><label>赞赏二维码图片链接</label></th>
						<td>
							<input type="text" class="regular-text" name="argon_donate_qrcode_url" value="<?php echo get_option('argon_donate_qrcode_url'); ?>"/>				
							<p class="description">赞赏二维码图片链接，填写后会在文章最后显示赞赏按钮，留空则不显示赞赏按钮</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h3>其他</h3></th></tr>
					<tr>
						<th><label>文章过时信息显示</label></th>
						<td>
							当一篇文章的
							<select name="argon_outdated_info_time_type">
								<?php $argon_outdated_info_time_type = get_option('argon_outdated_info_time_type'); ?>
								<option value="modifiedtime" <?php if ($argon_outdated_info_time_type=='modifiedtime'){echo 'selected';} ?>>最后修改时间</option>
								<option value="createdtime" <?php if ($argon_outdated_info_time_type=='createdtime'){echo 'selected';} ?>>发布时间</option>
							</select>
							距离现在超过 
							<input type="number" name="argon_outdated_info_days" min="-1" max="99999"  value="<?php echo (get_option('argon_outdated_info_days') == '' ? '-1' : get_option('argon_outdated_info_days')); ?>"/>
							天时，用
							<select name="argon_outdated_info_tip_type">
								<?php $argon_outdated_info_tip_type = get_option('argon_outdated_info_tip_type'); ?>
								<option value="inpost" <?php if ($argon_outdated_info_tip_type=='inpost'){echo 'selected';} ?>>在文章顶部显示信息条</option>
								<option value="toast" <?php if ($argon_outdated_info_tip_type=='toast'){echo 'selected';} ?>>在页面右上角弹出提示条</option>
							</select>
							的方式提示
							</br>
							<textarea type="text" name="argon_outdated_info_tip_content" rows="3" cols="100" style="margin-top: 15px;"><?php echo get_option('argon_outdated_info_tip_content') == '' ? '本文最后更新于 %date_delta% 天前，其中的信息可能已经有所发展或是发生改变。' : get_option('argon_outdated_info_tip_content'); ?></textarea>	
							<p class="description">天数为 -1 表示永不提示。</br>%date_delta% 表示文章发布/修改时间与当前时间的差距（单位: 天）。</p>
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
						<th><label>是否启用图片放大浏览 (Zoomify)</label></th>
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
							<input type="text" class="regular-text" name="argon_zoomify_easing" value="<?php echo (get_option('argon_zoomify_easing') == '' ? 'cubic-bezier(0.4,0,0,1)' : get_option('argon_zoomify_easing')); ?>"/>
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
					<tr><th class="subtitle"><h2>Pangu.js</h2></th></tr>
					<tr>
						<th><label>启用 Pangu.js (自动在中英文之间添加空格)</label></th>
						<td>
							<select name="argon_enable_pangu">
								<?php $argon_enable_pangu = get_option('argon_enable_pangu'); ?>
								<option value="true" <?php if ($argon_enable_pangu=='true'){echo 'selected';} ?>>禁用</option>
								<option value="article" <?php if ($argon_enable_pangu=='article'){echo 'selected';} ?>>格式化文章内容</option>
								<option value="comment" <?php if ($argon_enable_pangu=='comment'){echo 'selected';} ?>>格式化评论区</option>
								<option value="article|comment" <?php if ($argon_enable_pangu=='article|comment'){echo 'selected';} ?>>格式化文章内容和评论区</option>
							</select>
							<p class="description">开启后，会自动在中文和英文之间添加空格</p>
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
					<tr><th class="subtitle"><h2>评论</h2></th></tr>
					<tr><th class="subtitle"><h3>评论分页</h3></th></tr>
					<tr>
						<th><label>评论分页方式</label></th>
						<td>
							<select name="argon_comment_pagination_type">
								<?php $argon_comment_pagination_type = get_option('argon_comment_pagination_type'); ?>
								<option value="feed" <?php if ($argon_comment_pagination_type=='feed'){echo 'selected';} ?>>无限加载</option>
								<option value="page" <?php if ($argon_comment_pagination_type=='page'){echo 'selected';} ?>>页码</option>	
							</select>
							<p class="description">无限加载：点击 "加载更多" 按钮来加载更多评论。</br>页码：显示页码来分页。</br>推荐选择"无限加载"时将 Wordpress 设置中的讨论设置项设为 "默认显示最后一页，在每个页面顶部显示新的评论"。</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h3>发送评论</h3></th></tr>
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
					<tr>
						<th><label>是否允许在评论中使用 Markdown 语法</label></th>
						<td>
							<select name="argon_comment_allow_markdown">
								<?php $argon_comment_allow_markdown = get_option('argon_comment_allow_markdown'); ?>
								<option value="true" <?php if ($argon_comment_allow_markdown=='true'){echo 'selected';} ?>>允许</option>	
								<option value="false" <?php if ($argon_comment_allow_markdown=='false'){echo 'selected';} ?>>不允许</option>
							</select>
							<p class="description"></p>
						</td>
					</tr>
					<tr>
						<th><label>是否允许评论者再次编辑评论</label></th>
						<td>
							<select name="argon_comment_allow_editing">
								<?php $argon_comment_allow_editing = get_option('argon_comment_allow_editing'); ?>
								<option value="true" <?php if ($argon_comment_allow_editing=='true'){echo 'selected';} ?>>允许</option>	
								<option value="false" <?php if ($argon_comment_allow_editing=='false'){echo 'selected';} ?>>不允许</option>
							</select>
							<p class="description">同一个评论者可以再次编辑评论。</p>
						</td>
					</tr>
					<tr>
						<th><label>是否允许评论者使用悄悄话模式</label></th>
						<td>
							<select name="argon_comment_allow_privatemode">
								<?php $argon_comment_allow_privatemode = get_option('argon_comment_allow_privatemode'); ?>
								<option value="false" <?php if ($argon_comment_allow_privatemode=='false'){echo 'selected';} ?>>不允许</option>
								<option value="true" <?php if ($argon_comment_allow_privatemode=='true'){echo 'selected';} ?>>允许</option>	
							</select>
							<p class="description">评论者使用悄悄话模式发送的评论和其下的所有回复只有发送者和博主能看到。</p>
						</td>
					</tr>
					<tr>
						<th><label>是否允许评论者接收评论回复邮件提醒</label></th>
						<td>
							<select name="argon_comment_allow_mailnotice">
								<?php $argon_comment_allow_mailnotice = get_option('argon_comment_allow_mailnotice'); ?>
								<option value="false" <?php if ($argon_comment_allow_mailnotice=='false'){echo 'selected';} ?>>不允许</option>
								<option value="true" <?php if ($argon_comment_allow_mailnotice=='true'){echo 'selected';} ?>>允许</option>	
							</select>
							<div style="margin-top: 15px;margin-bottom: 15px;">
								<label>
									<?php $argon_comment_mailnotice_checkbox_checked = get_option('argon_comment_mailnotice_checkbox_checked');?>
									<input type="checkbox" name="argon_comment_mailnotice_checkbox_checked" value="true" <?php if ($argon_comment_mailnotice_checkbox_checked=='true'){echo 'checked';}?>/>	评论时默认勾选 "启用邮件通知" 复选框
								</label>
							</div>
							<p class="description">评论者开启邮件提醒后，其评论有回复时会有邮件通知。</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h3>评论区</h3></th></tr>
					<tr>
						<th><label>评论头像垂直位置</label></th>
						<td>
							<select name="argon_comment_avatar_vcenter">
								<?php $argon_comment_avatar_vcenter = get_option('argon_comment_avatar_vcenter'); ?>
								<option value="false" <?php if ($argon_comment_avatar_vcenter=='false'){echo 'selected';} ?>>居上</option>
								<option value="true" <?php if ($argon_comment_avatar_vcenter=='true'){echo 'selected';} ?>>居中</option>	
							</select>
							<p class="description"></p>
						</td>
					</tr>
					<tr>
						<th><label>谁可以查看评论编辑记录</label></th>
						<td>
							<select name="argon_who_can_visit_comment_edit_history">
								<?php $argon_who_can_visit_comment_edit_history = get_option('argon_who_can_visit_comment_edit_history'); ?>
								<option value="admin" <?php if ($argon_who_can_visit_comment_edit_history=='admin'){echo 'selected';} ?>>只有博主</option>
								<option value="commentsender" <?php if ($argon_who_can_visit_comment_edit_history=='commentsender'){echo 'selected';} ?>>评论发送者和博主</option>
								<option value="everyone" <?php if ($argon_who_can_visit_comment_edit_history=='everyone'){echo 'selected';} ?>>任何人</option>
							</select>
							<p class="description">点击评论右侧的 "已编辑" 标记来查看编辑记录</p>
						</td>
					</tr>
					<tr><th class="subtitle"><h2>其他</h2></th></tr>
					<tr>
						<th><label>是否启用 Pjax</label></th>
						<td>
							<select name="argon_pjax_disabled">
								<?php $argon_pjax_disabled = get_option('argon_pjax_disabled'); ?>
								<option value="false" <?php if ($argon_pjax_disabled=='false'){echo 'selected';} ?>>启用</option>
								<option value="true" <?php if ($argon_pjax_disabled=='true'){echo 'selected';} ?>>不启用</option>
							</select>
							<p class="description">Pjax 可以增强页面的跳转体验</p>
						</td>
					</tr>
					<tr>
						<th><label>博客首页是否显示说说</label></th>
						<td>
							<select name="argon_home_show_shuoshuo">
								<?php $argon_home_show_shuoshuo = get_option('argon_home_show_shuoshuo'); ?>
								<option value="false" <?php if ($argon_home_show_shuoshuo=='false'){echo 'selected';} ?>>不显示</option>
								<option value="true" <?php if ($argon_home_show_shuoshuo=='true'){echo 'selected';} ?>>显示</option>
							</select>
							<p class="description">开启后，博客首页文章和说说穿插显示</p>
						</td>
					</tr>
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
								<option value="jsdelivr" <?php if ($argon_update_source=='jsdelivr'){echo 'selected';} ?>>jsdelivr</option>
								<option value="solstice23top" <?php if ($argon_update_source=='solstice23top' || $argon_update_source=='abc233site'){echo 'selected';} ?>>solstice23.top</option>
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
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改">
				<a class="button button-secondary" style="margin-left: 8px;" onclick="importSettings()">导入设置</a>
				<a class="button button-secondary" style="margin-left: 5px;" onclick="exportSettings()">导出设置</a>
			</p>
		</form>
	</div>
	<div id="headindex_box">
		<button id="headindex_toggler" onclick="$('#headindex_box').toggleClass('folded');">收起</button>
		<div id="headindex"></div>
	</div>
	<div id="exported_settings_json_box" class="closed"><div>请复制并保存导出后的 JSON</div><textarea id="exported_settings_json" readonly="true" onclick="$(this).select();"></textarea><div style="width: 100%;margin: auto;margin-top: 15px;cursor: pointer;user-select: none;" onclick="$('#exported_settings_json_box').addClass('closed');">关闭</div></div>
	<style>
		.radio-with-img {
			display: inline-block;
			margin-right: 15px;
			text-align: center;
		}
		.radio-with-img > .radio-img {
			cursor: pointer;
		}
		.radio-with-img > label {
			display: inline-block;
			margin-top: 10px;
		}
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
		#exported_settings_json_box{
			position: fixed;
			z-index: 99999;
			left: calc(50vw - 400px);
			right: calc(50vw - 400px);
			top: 50px;
			width: 800px;
			height: 500px;
			max-width: 100vw;
			max-height: calc(100vh - 50px);
			background: #fff;
			padding: 25px;
			border-radius: 5px;
			box-shadow: 0 5px 10px rgba(0, 0, 0, .1);
			text-align: center;
			font-size: 20px;
			transition: all .3s ease;
		}
		#exported_settings_json{
			width: 100%;
			height: calc(100% - 70px);
			margin-top: 25px;
			font-size: 18px;
			background: #fff;
			resize: none;
		}
		#exported_settings_json::selection{
			background: #cce2ff;
		}
		#exported_settings_json_box.closed{
			transform: translateY(-30px) scale(.9);
			opacity: 0;
			pointer-events: none;
		}
		@media screen and (max-width:800px){
			#exported_settings_json_box{
				left: 0;
				right: 0;
				top: 0;
				width: calc(100vw - 50px);
			}
		}
	</style>
	<script type="text/javascript">
		$(document).on("click" , ".radio-with-img .radio-img" , function(){
			$("input", this.parentNode).click();
		});
		$(function () {
			$(document).headIndex({
				articleWrapSelector: '#main_form',
				indexBoxSelector: '#headindex',
				subItemBoxClass: "index-subItem-box",
				itemClass: "index-item",
				linkClass: "index-link",
				offset: 80,
			});
		});
		function setInputValue(name, value){
			let input = $("*[name='" + name + "']");
			let inputType = input.attr("type");
			if (inputType == "checkbox"){
				if (value == "true"){
					value = true;
				}else if (value == "false"){
					value = false;
				}
				input[0].checked = value;
			}else{
				input.val(value);
			}
		}
		function getInputValue(input){
			let inputType = input.attr("type");
			if (inputType == "checkbox"){
				return input[0].checked;
			}else{
				return input.val();
			}
		}
		function exportArgonSettings(){
			let json = {};
			let pushIntoJson = function (){
				name = $(this).attr("name");
				value = getInputValue($(this));
				json[name] = value;
			};
			$("#main_form > .form-table input:not([name='submit']) , #main_form > .form-table select , #main_form > .form-table textarea").each(function(){
				name = $(this).attr("name");
				value = getInputValue($(this));
				json[name] = value;
			});
			return JSON.stringify(json);
		}
		function importArgonSettings(json){
			if (typeof(json) == "string"){
				json = JSON.parse(json);
			}
			let info = "";
			for (let name in json){
				try{
					if ($("*[name='" + name + "']").length == 0){
						throw "Input Not Found";
					}
					setInputValue(name, json[name]);
				}catch{
					info += name + " 字段导入失败\n";
				}
			}
			return info;
		}
		function exportSettings(){
			$("#exported_settings_json").val(exportArgonSettings());
			$("#exported_settings_json").select();
			$("#exported_settings_json_box").removeClass("closed");
		}
		function importSettings(){
			let json = prompt("请输入要导入的备份 JSON");
			if (json){
				let res = importArgonSettings(json);
				alert("已导入，请保存更改\n" + res)
			}
		}
	</script>
<?php
}
add_action('admin_menu', 'themeoptions_admin_menu');
function argon_update_option($name){
	update_option($name, htmlspecialchars(stripslashes($_POST[$name])));
}
function argon_update_option_allow_tags($name){
	update_option($name, stripslashes($_POST[$name]));
}
function argon_update_themeoptions(){
	if ($_POST['update_themeoptions'] == 'true'){
		if (!isset($_POST['argon_update_themeoptions_nonce'])){
			return;
		}
		$nonce = $_POST['argon_update_themeoptions_nonce'];
		if (!wp_verify_nonce($nonce, 'argon_update_themeoptions')){
			return;
		}
		//配置项
		argon_update_option('argon_toolbar_icon');
		argon_update_option('argon_toolbar_icon_link');
		argon_update_option('argon_toolbar_title');
		argon_update_option('argon_sidebar_banner_title');
		argon_update_option('argon_sidebar_banner_subtitle');
		argon_update_option('argon_sidebar_auther_name');
		argon_update_option('argon_sidebar_auther_image');
		argon_update_option('argon_banner_title');
		argon_update_option('argon_banner_subtitle');
		argon_update_option('argon_banner_background_url');
		argon_update_option('argon_banner_background_color_type');
		argon_update_option('argon_banner_background_hide_shapes');
		argon_update_option('argon_enable_smoothscroll_type');
		argon_update_option('argon_enable_v2ex_gravatar');
		argon_update_option_allow_tags('argon_footer_html');
		argon_update_option('argon_show_readingtime');
		argon_update_option('argon_reading_speed');
		argon_update_option('argon_show_sharebtn');
		argon_update_option('argon_enable_timezone_fix');
		argon_update_option('argon_donate_qrcode_url');
		argon_update_option('argon_hide_shortcode_in_preview');
		argon_update_option('argon_show_thumbnail_in_banner_in_content_page');
		argon_update_option('argon_update_source');
		argon_update_option('argon_enable_into_article_animation');
		argon_update_option('argon_fab_show_darkmode_button');
		argon_update_option('argon_fab_show_settings_button');
		argon_update_option('argon_fab_show_gotocomment_button');
		argon_update_option('argon_show_headindex_number');
		argon_update_option('argon_theme_color');
		update_option('argon_show_customize_theme_color_picker', ($_POST['argon_show_customize_theme_color_picker'] == 'true')?'true':'false');
		argon_update_option_allow_tags('argon_seo_description');
		argon_update_option('argon_seo_keywords');
		argon_update_option('argon_enable_mobile_scale');
		argon_update_option('argon_page_background_url');
		argon_update_option('argon_page_background_dark_url');
		argon_update_option('argon_page_background_opacity');
		argon_update_option('argon_page_background_banner_style');
		argon_update_option('argon_hide_name_email_site_input');
		argon_update_option('argon_comment_need_captcha');
		argon_update_option('argon_hide_footer_author');
		argon_update_option('argon_card_radius');
		argon_update_option('argon_comment_avatar_vcenter');
		argon_update_option('argon_pjax_disabled');
		argon_update_option('argon_comment_allow_markdown');
		argon_update_option('argon_comment_allow_editing');
		argon_update_option('argon_comment_allow_privatemode');
		argon_update_option('argon_comment_allow_mailnotice');
		update_option('argon_comment_mailnotice_checkbox_checked', ($_POST['argon_comment_mailnotice_checkbox_checked'] == 'true')?'true':'false');
		argon_update_option('argon_comment_pagination_type');
		argon_update_option('argon_who_can_visit_comment_edit_history');
		argon_update_option('argon_home_show_shuoshuo');
		argon_update_option('argon_darkmode_autoswitch');
		argon_update_option('argon_enable_amoled_dark');
		argon_update_option('argon_outdated_info_time_type');
		argon_update_option('argon_outdated_info_days');
		argon_update_option('argon_outdated_info_tip_type');
		argon_update_option('argon_outdated_info_tip_content');
		update_option('argon_show_toolbar_mask', ($_POST['argon_show_toolbar_mask'] == 'true')?'true':'false');
		argon_update_option('argon_enable_banner_title_typing_effect');
		argon_update_option('argon_banner_typing_effect_interval');
		argon_update_option('argon_page_layout');
		argon_update_option('argon_enable_pangu');

		//LazyLoad 相关
		argon_update_option('argon_enable_lazyload');
		argon_update_option('argon_lazyload_effect');
		argon_update_option('argon_lazyload_threshold');
		argon_update_option('argon_lazyload_loading_style');

		//Zoomify 相关
		argon_update_option('argon_enable_zoomify');
		argon_update_option('argon_zoomify_duration');
		argon_update_option('argon_zoomify_easing');
		argon_update_option('argon_zoomify_scale');

		//Mathjax 相关配置项
		argon_update_option('argon_mathjax_enable');
		argon_update_option('argon_mathjax_cdn_url');
		argon_update_option('argon_mathjax_v2_enable');
		argon_update_option('argon_mathjax_v2_cdn_url');

		//页头页尾脚本
		argon_update_option_allow_tags('argon_custom_html_head');
		argon_update_option_allow_tags('argon_custom_html_foot');

		//公告
		argon_update_option_allow_tags('argon_sidebar_announcement');
	}	
}
argon_update_themeoptions();

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
		'rewrite' => array(
			'slug' => 'shuoshuo',
			'with_front' => false
		),
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false, 
		'menu_position' => null,
		'menu_icon' => 'dashicons-format-quote',
		'supports' => array('editor', 'author', 'title', 'custom-fields', 'comments')
	);
	register_post_type('shuoshuo', $args); 
}

/*恢复链接管理器*/
add_filter('pre_option_link_manager_enabled', '__return_true');