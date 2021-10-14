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
	<title><?php _e("404 - 找不到页面", "argon"); ?></title>
	<script>
		var darkmodeAutoSwitch = "<?php echo (get_option("argon_darkmode_autoswitch") == '' ? 'false' : get_option("argon_darkmode_autoswitch"));?>";
		function setDarkmode(enable){
			if (enable == true){
				$("html").addClass("darkmode");
			}else{
				$("html").removeClass("darkmode");
			}
			$(window).trigger("scroll");
		}
		function toggleDarkmode(){
			if ($("html").hasClass("darkmode")){
				setDarkmode(false);
				sessionStorage.setItem("Argon_Enable_Dark_Mode", "false");
			}else{
				setDarkmode(true);
				sessionStorage.setItem("Argon_Enable_Dark_Mode", "true");
			}
		}
		if (sessionStorage.getItem("Argon_Enable_Dark_Mode") == "true"){
			setDarkmode(true);
		}
		function toggleDarkmodeByPrefersColorScheme(media){
			if (sessionStorage.getItem('Argon_Enable_Dark_Mode') == "false" || sessionStorage.getItem('Argon_Enable_Dark_Mode') == "true"){
				return;
			}
			if (media.matches){
				setDarkmode(true);
			}else{
				setDarkmode(false);
			}
		}
		function toggleDarkmodeByTime(){
			if (sessionStorage.getItem('Argon_Enable_Dark_Mode') == "false" || sessionStorage.getItem('Argon_Enable_Dark_Mode') == "true"){
				return;
			}
			let hour = new Date().getHours();
			if (hour < 7 || hour >= 22){
				setDarkmode(true);
			}else{
				setDarkmode(false);
			}
		}
		if (darkmodeAutoSwitch == 'system'){
			var darkmodeMediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
			darkmodeMediaQuery.addListener(toggleDarkmodeByPrefersColorScheme);
			toggleDarkmodeByPrefersColorScheme(darkmodeMediaQuery);
		}
		if (darkmodeAutoSwitch == 'time'){
			toggleDarkmodeByTime();
		}
		if (darkmodeAutoSwitch == 'alwayson'){
			setDarkmode(true);
		}

		function toggleAmoledDarkMode(){
			$("html").toggleClass("amoled-dark");
			if ($("html").hasClass("amoled-dark")){
				localStorage.setItem("Argon_Enable_Amoled_Dark_Mode", "true");
			}else{
				localStorage.setItem("Argon_Enable_Amoled_Dark_Mode", "false");
			}
		}
		if (localStorage.getItem("Argon_Enable_Amoled_Dark_Mode") == "true"){
			$("html").addClass("amoled-dark");
		}else if (localStorage.getItem("Argon_Enable_Amoled_Dark_Mode") == "false"){
			$("html").removeClass("amoled-dark");
		}
	</script>
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
							<p class="lead text-white">Page not found.<br><?php _e("这个页面不见了", "argon"); ?></p>
							<div class="btn-wrapper">
								<a href="javascript:window.history.back(-1);" ondragstart="return false;" class="btn btn-info btn-icon mb-3 mb-sm-0">
									<span class="btn-inner--icon"><i class="fa fa-chevron-left"></i></span>
									<span class="btn-inner--text"><?php _e("返回上一页", "argon"); ?></span>
								</a>
								<a href="<?php bloginfo('url'); ?>" class="btn btn-white btn-icon mb-3 mb-sm-0">
									<span class="btn-inner--icon"><i class="fa fa-home"></i></span>
									<span class="btn-inner--text"><?php _e("回到首页", "argon"); ?></span>
								</a>
							</div>
							<?php apply_filters('argon_404page_extra_html', '');
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
	html.darkmode .section-shaped .shape {
		background: #262626;
	}
	html.darkmode .text-white {
		opacity: .75;
	}
	html.darkmode .btn-white {
		background: #424242;
		border-color: #424242;
		color: #eee;
	}
	html.darkmode .btn-info {
		background: #0a7f94;
		border-color: #0a7f94;
		color: #eee;
	}
	html.darkmode.amoled-dark .section-shaped .shape {
		background: #000;
	}
</style>