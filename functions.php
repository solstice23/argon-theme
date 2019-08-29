<?php
if (version_compare( $GLOBALS['wp_version'], '4.4-alpha', '<' )) {
	echo "<div style='background: #5e72e4;color: #fff;font-size: 30px;padding: 50px 30px;position: fixed;width: 100%;left: 0;right: 0;bottom: 0px;z-index: 2147483647;'>Argon 主题不支持 Wordpress 4.4 以下版本，请更新 Wordpress</div>";
}
function theme_slug_setup() {
   add_theme_support('title-tag');
}
add_action('after_setup_theme','theme_slug_setup');
//检测更新
require_once(get_template_directory() . '/theme-update-checker.php'); 
$argonThemeUpdateChecker = new ThemeUpdateChecker(
	'argon',
	'https://raw.githubusercontent.com/abc2237512422/argon-theme/master/theme_update_info.json'
);
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
		$html .= '<li class="page-item"><a class="page-link" href="' . $urls[1] . '"><i class="fa fa-angle-double-left"></i></a></li>';
	}
	if ($current > 1){
		$html .= '<li class="page-item"><a class="page-link" href="' . $urls[$current - 1] . '"><i class="fa fa-angle-left"></i></a></li>';
	}
	for ($i = $from; $i <= $to; $i++){
		if ($current == $i){
			$html .= '<li class="page-item active"><span class="page-link" style="cursor: default;">' . $i . '</span></li>';
		}else{
			$html .= '<li class="page-item"><a class="page-link" href="' . $urls[$i] . '">' . $i . '</a></li>';
		}
	}
	if ($current < $total){
		$html .= '<li class="page-item"><a class="page-link" href="' . $urls[$current + 1] . '"><i class="fa fa-angle-right"></i></a></li>';
	}
	if ($to < $total){
		$html .= '<li class="page-item"><a class="page-link" href="' . $urls[$total] . '"><i class="fa fa-angle-double-right"></i></a></li>';
	}
	return '<nav><ul class="pagination">' . $html . '</ul></nav>';
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
				<?php if( user_can($comment->user_id , "update_core") ){
					echo '<span class="badge badge-primary badge-admin">博主</span>';}
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
function wrong_captcha(){
	wp_die('验证码错误，评论失败');
}
function check_comment_captcha($comment){
	$answer = $_POST['comment_captcha'];
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

	if(!is_feed() || !is_robots){
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
							<a target='_blink' href='" . $now[1] . "'>";
			if (!ctype_space($now[4]) && $now[4] != '' && isset($now[4])){
				$out .= "<img src='" . $now[4] . "' class='icon bg-gradient-secondary rounded-circle text-white'>
						</img>";
			}else{
				$out .= "<div class='icon icon-shape bg-gradient-primary rounded-circle text-white'>" . mb_substr($now[2], 0, 1) . "
						</div>";
			}
						
			$out .= "		</a>
						</div>
						<div class='pl-3'>
							<div class='friend-link-title title text-primary'><a target='_blink' href='" . $now[1] . "'>" . $now[2] . "</a>
						</div>";
			if (!ctype_space($now[3]) && $now[3] != ''  && isset($now[3])){
				$out .= "<p class='friend-link-description'>" . $now[3] . "</p>";
			}else{
				/*$out .= "<p class='friend-link-description'>&nbsp;</p>";*/
			}
			$out .= "		<a target='_blink' href='" . $now[1] . "' class='text-primary opacity-8'>前往</a>
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
add_shortcode('hide_reading_time','shortcode_hide_reading_time');
function shortcode_hide_reading_time($attr,$content=""){
	return "";
}
//主题选项页面
function themeoptions_admin_menu(){
	/*后台管理面板侧栏添加选项*/
	add_theme_page("Argon 主题设置", "Argon 主题选项", 'edit_themes', basename(__FILE__), 'themeoptions_page');
}
function themeoptions_page(){
	/*具体选项*/
?>
	<div>
		<h1>Argon 主题设置</h1>
		<form method="POST" action="">
			<input type="hidden" name="update_themeoptions" value="true" />
			<h2>顶栏</h2>
			<h4>顶栏标题</h4>
			<p><input type="text" name="argon_toolbar_title" value="<?php echo get_option('argon_toolbar_title'); ?>"/> 留空则显示博客名称</p>
			<h4>顶栏图标</h4>
			<p><input type="text" name="argon_toolbar_icon" value="<?php echo get_option('argon_toolbar_icon'); ?>"/> 图片地址，留空则不显示</p>
			<p><input type="text" name="argon_toolbar_icon_link" value="<?php echo get_option('argon_toolbar_icon_link'); ?>"/> 图片链接</p>
			<h2>顶部 Banner (封面)</h2>
			<h4>Banner 标题</h4>
			<p><input type="text" name="argon_banner_title" value="<?php echo get_option('argon_banner_title'); ?>"/> 留空则显示博客名称</p>
			<h4>Banner 背景图</h4>
			<p><input type="text" name="argon_banner_background_url" value="<?php echo get_option('argon_banner_background_url'); ?>"/> 需带上 http(s)
			，留空则显示默认背景 (即将支持必应每日一图)</p>
			<h4>Banner 渐变背景样式（如果设置了背景图则不生效）</h4>
			<p>
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
				<input type="checkbox" name="argon_banner_background_hide_shapes" value="true" <?php if ($hide_shapes=='true'){echo 'checked';}?>/>
				隐藏背景半透明圆
				</br></br>样式预览 (推荐选择前三个样式)</br>
				<div class="banner-background-color-type-preview" style="background:linear-gradient(150deg,#281483 15%,#8f6ed5 70%,#d782d9 94%);">样式1</div>
				<div class="banner-background-color-type-preview" style="background:linear-gradient(150deg,#7795f8 15%,#6772e5 70%,#555abf 94%);">样式2</div>
				<div class="banner-background-color-type-preview" style="background:linear-gradient(150deg,#32325d 15%,#32325d 70%,#32325d 94%);">样式3</div>
				<div class="banner-background-color-type-preview" style="background:linear-gradient(87deg,#2dce89 0,#2dcecc 100%);">样式4</div>
				<div class="banner-background-color-type-preview" style="background:linear-gradient(87deg,#11cdef 0,#1171ef 100%);">样式5</div>
				<div class="banner-background-color-type-preview" style="background:linear-gradient(87deg,#fb6340 0,#fbb140 100%);">样式6</div>
				<div class="banner-background-color-type-preview" style="background:linear-gradient(87deg,#f5365c 0,#f56036 100%);">样式7</div>
				<style>
					div.banner-background-color-type-preview{width:100px;height:50px;line-height:50px;color:#fff;margin-right:10px;font-size:15px;text-align:center;display:inline-block;border-radius:10px;transition:all .3s ease;}
					div.banner-background-color-type-preview:hover{transform: scale(2);}
				</style>
			</p>

			<h2>左侧栏</h2>
			<h4>左侧栏标题</h4>
			<p><input type="text" name="argon_sidebar_banner_title" value="<?php echo get_option('argon_sidebar_banner_title'); ?>"/> 留空则显示博客名称</p>
			<h4>左侧栏子标题（格言）</h4>
			<p><input type="text" name="argon_sidebar_banner_subtitle" value="<?php echo get_option('argon_sidebar_banner_subtitle'); ?>"/> 
			<h4>左侧栏作者名称</h4>
			<p><input type="text" name="argon_sidebar_auther_name" value="<?php echo get_option('argon_sidebar_auther_name'); ?>"/> 留空则显示博客名</p>
			<h4>左侧栏作者头像地址</h4>
			<p><input type="text" name="argon_sidebar_auther_image" value="<?php echo get_option('argon_sidebar_auther_image'); ?>"/> 需带上 http(s)开头</p>

			<h2>文章 Meta 信息</h2>
			<h4>显示字数和预计阅读时间</h4>
			<p>
				<select name="argon_show_readingtime">
					<?php $argon_show_readingtime = get_option('argon_show_readingtime'); ?>
					<option value="true" <?php if ($argon_show_readingtime=='true'){echo 'selected';} ?>>显示</option>	
					<option value="false" <?php if ($argon_show_readingtime=='false'){echo 'selected';} ?>>不显示</option>
				</select>
			</p>
			<h4>每分钟阅读字数</h4>
			<p>
				预计阅读时间由每分钟阅读字数计算</br>
				<input type="number" name="argon_reading_speed" min="1" max="5000"  value="<?php echo (get_option('argon_reading_speed') == '' ? '300' : get_option('argon_reading_speed')); ?>"/>
				字/分钟
			</p>

			<h2>分享</h2>
			<h4>显示文章分享按钮</h4>
			<p>
				<select name="argon_show_sharebtn">
					<?php $argon_show_sharebtn = get_option('argon_show_sharebtn'); ?>
					<option value="true" <?php if ($argon_show_sharebtn=='true'){echo 'selected';} ?>>显示</option>	
					<option value="false" <?php if ($argon_show_sharebtn=='false'){echo 'selected';} ?>>不显示</option>
				</select>
			</p>
			
			<h2>页脚</h2>
			<h4>页脚内容</h4>
			<p><textarea type="text" rows="15" cols="100" name="argon_footer_html"><?php echo htmlspecialchars(get_option('argon_footer_html')); ?></textarea>
			</br>
			HTML , 支持 script 等标签</p>

			<h2>Mathjax 渲染</h2>
			<p>
				Mathjax 是一个 Latex 前端渲染库，可以自动解析文章中的 Latex 公式并渲染。</br>
				Argon 主题内置了 Mathjax 库的引用 (2.6.0 版本)</br>
				如果你需要用到公式，请打开这个选项</br>
				Argon 主题提供了一些 Mathjax 的常用配置项</br>
				或者，如果你需要更详细的配置选项，你可以在这里禁用 Mathjax ，然后在 "页脚代码" 中引用 Mathjax 并编写配置 JSON (当然你也可以用插件来实现)</br>
				一般来说，这里的配置选项已经够用，使用 Argon 主题提供的默认配置即可</br>
				使用 $xxx$ 或 \\xxx\\ 来标记一个行内公式，$$xxx$$ 来标记一个独立公式</br>
				<h4>启用 Mathjax</h4>
				<p>
					<select name="argon_mathjax_enable">
						<?php $argon_mathjax_enable = get_option('argon_mathjax_enable'); ?>
						<option value="false" <?php if ($argon_mathjax_enable=='false'){echo 'selected';} ?>>不启用</option>
						<option value="true" <?php if ($argon_mathjax_enable=='true'){echo 'selected';} ?>>启用</option>	
					</select>
				</p>
				<h4>在页面左下角显示 Mathjax 加载信息</h4>
				<p>
					<select name="argon_mathjax_loading_msg_type">
						<?php $argon_mathjax_loading_msg_type = get_option('argon_mathjax_loading_msg_type'); ?>
						<option value="none" <?php if ($argon_mathjax_loading_msg_type=='none'){echo 'selected';} ?>>隐藏</option>
						<option value="normal" <?php if ($argon_mathjax_loading_msg_type=='normal'){echo 'selected';} ?>>显示</option>
						<option value="simple" <?php if ($argon_mathjax_loading_msg_type=='simple'){echo 'selected';} ?>>显示 (不显示进度，只显示状态)</option>
					</select>
				</p>
				<h4>公式缩放触发条件</h4>
				<p>
					选择触发公式放大浏览框的条件</br>
					<select name="argon_mathjax_zoom_cond">
						<?php $argon_mathjax_zoom_cond = get_option('argon_mathjax_zoom_cond'); ?>
						<option value="Hover" <?php if ($argon_mathjax_zoom_cond=='Hover'){echo 'selected';} ?>>鼠标悬停</option>
						<option value="Click" <?php if ($argon_mathjax_zoom_cond=='Click'){echo 'selected';} ?>>鼠标单击</option>
						<option value="Double-Click" <?php if ($argon_mathjax_zoom_cond=='Double-Click'){echo 'selected';} ?>>鼠标双击</option>
						<option value="None" <?php if ($argon_mathjax_zoom_cond=='None'){echo 'selected';} ?>>永不显示</option>
					</select>
				</p>
				<h4>公式缩放放大比例</h4>
				<p>
					<input type="number" name="argon_mathjax_zoom_scale" min="100" max="999"  value="<?php echo (get_option('argon_mathjax_zoom_scale') == '' ? '200' : get_option('argon_mathjax_zoom_scale')); ?>"/>
					%
				</p>
				<h4>在公式上右键是否显示 Mathjax 菜单</h4>
				<p>
					<select name="argon_mathjax_show_menu">
						<?php $argon_mathjax_show_menu = get_option('argon_mathjax_show_menu'); ?>
						<option value="false" <?php if ($argon_mathjax_show_menu=='false'){echo 'selected';} ?>>不显示</option>
						<option value="true" <?php if ($argon_mathjax_show_menu=='true'){echo 'selected';} ?>>显示</option>
					</select>
				</p>
			</p>

			<h2>Lazyload</h2>
			<h4>是否启用 Lazyload 加载文章内图片</h4>
			<p>
				<select name="argon_enable_lazyload">
					<?php $argon_enable_lazyload = get_option('argon_enable_lazyload'); ?>
					<option value="true" <?php if ($argon_enable_lazyload=='true'){echo 'selected';} ?>>启用</option>
					<option value="false" <?php if ($argon_enable_lazyload=='false'){echo 'selected';} ?>>禁用</option>
				</select>
			</p>
			<h4>提前加载阈值</h4>
			<p>
				图片距离页面底部还有多少距离就开始提前加载</br>
				<input type="number" name="argon_lazyload_threshold" min="0" max="2500"  value="<?php echo (get_option('argon_lazyload_threshold') == '' ? '800' : get_option('argon_lazyload_threshold')); ?>"/>
				px
			</p>
			<h4>LazyLoad 图片加载完成过渡</h4>
			<p>
				<select name="argon_lazyload_effect">
					<?php $argon_lazyload_effect = get_option('argon_lazyload_effect'); ?>
					<option value="fadeIn" <?php if ($argon_lazyload_effect=='fadeIn'){echo 'selected';} ?>>fadeIn</option>
					<option value="slideDown" <?php if ($argon_lazyload_effect=='slideDown'){echo 'selected';} ?>>slideDown</option>
					<option value="none" <?php if ($argon_lazyload_effect=='none'){echo 'selected';} ?>>不使用过渡</option>
				</select>
			</p>
			<h4>LazyLoad 图片加载动效</h4>
			<p>
				在图片被加载之前显示的加载效果 , <a target="_blink" href="<?php bloginfo('template_url'); ?>/assets/vendor/svg-loaders">预览所有效果</a></br>
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
			</p>

			<h2>图片放大浏览</h2>
			<p>开启后，文章中图片被单击时会放大预览</p>
			<h4>是否启用图片放大浏览</h4>
			<p>
				<select name="argon_enable_zoomify">
					<?php $argon_enable_zoomify = get_option('argon_enable_zoomify'); ?>
					<option value="true" <?php if ($argon_enable_zoomify=='true'){echo 'selected';} ?>>启用</option>
					<option value="false" <?php if ($argon_enable_zoomify=='false'){echo 'selected';} ?>>禁用</option>
				</select>
			</p>
			<h4>缩放动画长度</h4>
			<p>
				图片被单击后缩放到全屏动画的时间长度</br>
				<input type="number" name="argon_zoomify_duration" min="0" max="10000" value="<?php echo (get_option('argon_zoomify_duration') == '' ? '200' : get_option('argon_zoomify_duration')); ?>"/>
				ms
			</p>
			<h4>缩放动画曲线</h4>
			<p>
				例： <code>ease</code> , <code>ease-in-out</code> , <code>ease-out</code> , <code>linear</code> , <code>cubic-bezier(0.68,-0.55,0.27,1.55)</code></br>
				如果你不知道这是什么，参考<a href="https://www.w3school.com.cn/cssref/pr_animation-timing-function.asp" target="_blink">这里</a></br>
				<input type="text" name="argon_zoomify_easing" value="<?php echo (get_option('argon_zoomify_easing') == '' ? 'ease-out' : get_option('argon_zoomify_easing')); ?>"/></p>
			<h4>图片最大缩放比例</h4>
			<p>
				图片相对于页面的最大缩放比例 (0 ~ 1 的小数)</br>
				<input type="number" name="argon_zoomify_scale" min="0.01" max="1" step="0.01" value="<?php echo (get_option('argon_zoomify_scale') == '' ? '0.9' : get_option('argon_zoomify_scale')); ?>"/>
			</p>

			<h2>脚本</h2>
			<p>
				<div style="border-left: 3px solid rgba(0, 0, 0, .1);padding-left: 15px;">
					<strong style="color:#ff0000;">注意： Argon 使用 pjax 方式加载页面 (无刷新加载) , 所以您的脚本除非页面手动刷新，否则只会被执行一次。</br>
					如果您想让每次页面跳转(加载新页面)时都执行脚本，请将脚本写入 <code>window.pjaxLoaded</code> 中</strong> ，示例写法:
					<pre>
window.pjaxLoaded = function(){
	//页面每次跳转都会执行这里的代码
	//do something...
}
					</pre>
					<strong style="color:#ff0000;">当页面第一次载入时，<code>window.pjaxLoaded</code> 中的脚本不会执行，所以您可以手动执行 <code>window.pjaxLoaded();</code> 来让页面初次加载时也执行脚本</strong>
				</div>
				<h4>页头脚本</h4>
				<p><textarea type="text" rows="15" cols="100" name="argon_custom_html_head"><?php echo htmlspecialchars(get_option('argon_custom_html_head')); ?></textarea>
				</br>
				HTML , 支持 script 等标签</br>插入到 body 之前</p>
				<h4>页尾脚本</h4>
				<p><textarea type="text" rows="15" cols="100" name="argon_custom_html_foot"><?php echo htmlspecialchars(get_option('argon_custom_html_foot')); ?></textarea>
				</br>
				HTML , 支持 script 等标签</br>插入到 body 之后</p>
			</p>

			<h2>其他</h2>
			<h4>是否使用 v2ex CDN 代理的 gravatar</h4>
			<p>
				建议使用，可以大幅增加 gravatar 头像加载的速度</br>
				<select name="argon_enable_v2ex_gravatar">
					<?php $enable_v2ex_gravatar = get_option('argon_enable_v2ex_gravatar'); ?>
					<option value="false" <?php if ($enable_v2ex_gravatar=='false'){echo 'selected';} ?>>不使用</option>
					<option value="true" <?php if ($enable_v2ex_gravatar=='true'){echo 'selected';} ?>>使用</option>	
				</select>
			</p>
			<h4>是否启用平滑滚动</h4>
			<p>
				能增强浏览体验，但可能出现一些小问题，如果有问题请切换方案或关闭平滑滚动</br>
				<select name="argon_enable_smoothscroll_type">
					<?php $enable_smoothscroll_type = get_option('argon_enable_smoothscroll_type'); ?>
					<option value="1" <?php if ($enable_smoothscroll_type=='1'){echo 'selected';} ?>>使用平滑滚动方案1 (推荐)</option>
					<option value="2" <?php if ($enable_smoothscroll_type=='2'){echo 'selected';} ?>>使用平滑滚动方案2 (较稳)</option>
					<option value="3" <?php if ($enable_smoothscroll_type=='3'){echo 'selected';} ?>>使用平滑滚动方案3</option>
					<option value="disabled" <?php if ($enable_smoothscroll_type=='disabled'){echo 'selected';} ?>>不使用平滑滚动</option>
				</select>
			</p>
			<input type="submit" name="admin_options" value="确认"/></p>
		</form>
	</div>
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
	update_option('argon_banner_background_url', $_POST['argon_banner_background_url']);
	update_option('argon_banner_background_color_type', $_POST['argon_banner_background_color_type']);
	update_option('argon_banner_background_hide_shapes', $_POST['argon_banner_background_hide_shapes']);
	update_option('argon_enable_smoothscroll_type', $_POST['argon_enable_smoothscroll_type']);
	update_option('argon_enable_v2ex_gravatar', $_POST['argon_enable_v2ex_gravatar']);
	update_option('argon_footer_html', stripslashes($_POST['argon_footer_html']));
	update_option('argon_show_readingtime', $_POST['argon_show_readingtime']);
	update_option('argon_reading_speed', $_POST['argon_reading_speed']);
	update_option('argon_show_sharebtn', $_POST['argon_show_sharebtn']);

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
	update_option('argon_mathjax_loading_msg_type', $_POST['argon_mathjax_loading_msg_type']);
	update_option('argon_mathjax_zoom_cond', $_POST['argon_mathjax_zoom_cond']);
	update_option('argon_mathjax_zoom_scale', $_POST['argon_mathjax_zoom_scale']);
	update_option('argon_mathjax_show_menu', $_POST['argon_mathjax_show_menu']);

	//页头页尾脚本
	update_option('argon_custom_html_head', stripslashes($_POST['argon_custom_html_head']));
	update_option('argon_custom_html_foot', stripslashes($_POST['argon_custom_html_foot']));
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