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
	<title>404 - 找不到页面</title>
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
			<div class="container py-lg-md d-flex">
				<div class="col px-0">
					<div class="row">
						<div class="col-lg-6 col-sm-12">
							<div class="display-1 text-white">404</div>
							<p class="lead text-white">Page not found.<br>这个页面不见了</p>
							<div class="btn-wrapper">
								<a href="javascript:window.history.back(-1);" ondragstart="return false;" class="btn btn-info btn-icon mb-3 mb-sm-0">
									<span class="btn-inner--icon"><i class="fa fa-chevron-left"></i></span>
									<span class="btn-inner--text">返回上一页</span>
								</a>
								<a href="<?php bloginfo('url'); ?>" class="btn btn-white btn-icon mb-3 mb-sm-0">
									<span class="btn-inner--icon"><i class="fa fa-home"></i></span>
									<span class="btn-inner--text">回到首页</span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</body>
</html>

<style>
	body{
		overflow: hidden;
	}
</style>