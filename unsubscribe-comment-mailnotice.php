<?php
	require(preg_replace('/wp-content(.*?)$/', '', dirname( __FILE__ )) . 'wp-blog-header.php');
	header('HTTP/1.1 200 OK');
	$id = $_GET['comment'];
	$token = $_GET['token'];
	if (get_comment($id) == null){
		$page_title = __('评论不存在', 'argon');
		$title = "<i class='fa fa-close' style='color: #f5365c;margin-right: 12px;'></i>" . __("错误", 'argon');
		$info = __("评论 #", 'argon') . $id . __(" 不存在", 'argon');
	}
	else if (get_comment_meta($id, "enable_mailnotice", true) != "true"){
		$page_title = __('无需退订', 'argon');
		$title = "<i class='fa fa-info-circle' style='color: #11cdef;margin-right: 12px;'></i>" . __("无需退订", 'argon');
		$info = __("评论 #", 'argon') . $id . __(" 的邮件通知已被退订或没有开启邮件通知", 'argon');
	}
	else if ($token != get_comment_meta($id, "mailnotice_unsubscribe_key", true)){
		$page_title = __('退订失败', 'argon');
		$title = "<i class='fa fa-close' style='color: #f5365c;margin-right: 12px;'></i>" . __("退订失败", 'argon');
		$info = __("Token 不正确", 'argon');
	}
	else{
		update_comment_meta($id, "enable_mailnotice", "false");
		$page_title = __('退订成功', 'argon');
		$title = "<i class='fa fa-check' style='color: #2dce89;margin-right: 12px;'></i>" . __("退订成功", 'argon');
		$info = __("您已成功退订评论 #", 'argon') . $id . __(" 的邮件通知<br>该评论下有新回复时您将不会再收到通知", 'argon');
	}
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<link href="<?php bloginfo('template_url'); ?>/assets/vendor/nucleo/css/nucleo.css" rel="stylesheet">
	<link href="<?php bloginfo('template_url'); ?>/assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link type="text/css" href="<?php bloginfo('template_url'); ?>/assets/css/argon.min.css" rel="stylesheet">
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/jquery/jquery.min.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/bootstrap/bootstrap.min.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/assets/js/argon.min.js"></script>
	<title><?php echo $page_title; ?></title>
</head>
<body>
	<div class="position-relative">
		<section class="section section-lg section-shaped pb-250" style="height: 100vh !important;">
			<div class="shape shape-style-1 shape-default">
				<span></span>
				<span></span>
				<span></span>
				<span></span>
				<span></span>
				<span></span>
				<span></span>
				<span></span>
				<span></span>
			</div>
			<div class="card main-card shadow">
				<div class="display-3 text-black"><?php echo $title; ?></div>
				<p class="lead text-black"><?php echo $info; ?></p>
			</div>
		</section>
	</div>
</body>
</html>

<style>
	body{
		overflow: hidden;
	}
	.main-card {
		width: 700px;
		max-width: calc(100vw - 50px);
		margin: auto;
		padding: 40px 50px;
		position: fixed;
		left: 50vw;
		top: 50vh;
		transform: translate(-50% , calc(-50% - 60px));
	}
</style>