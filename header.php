<!DOCTYPE html>
<?php
	$htmlclasses = "";
	if (get_option('argon_page_layout') == "single"){
		$htmlclasses .= "single-column ";
	}
	if (get_option('argon_page_layout') == "triple"){
		$htmlclasses .= "triple-column ";
	}
	if (get_option('argon_page_layout') == "double-reverse"){
		$htmlclasses .= "double-column-reverse ";
	}
	if (get_option('argon_enable_amoled_dark') == "true"){
		$htmlclasses .= "amoled-dark ";
	}
	if (get_option('argon_card_shadow') == 'big'){
		$htmlclasses .= 'use-big-shadow ';
	}
	if (get_option('argon_font') == 'serif'){
		$htmlclasses .= 'use-serif ';
	}
	if (get_option('argon_disable_codeblock_style') == 'true'){
		$htmlclasses .= 'disable-codeblock-style ';
	}
	if (get_option('argon_enable_headroom') == 'absolute'){
		$htmlclasses .= 'navbar-absolute ';
	}
	$banner_size = get_option('argon_banner_size', 'full');
	if ($banner_size != 'full'){
		if ($banner_size == 'mini'){
			$htmlclasses .= 'banner-mini ';
		}else if ($banner_size == 'hide'){
			$htmlclasses .= 'no-banner ';
		}else if ($banner_size == 'fullscreen'){
			$htmlclasses .= 'banner-as-cover ';
		}
	}
	if (get_option('argon_toolbar_blur', 'false') == 'true'){
		$htmlclasses .= 'toolbar-blur ';
	}
	$htmlclasses .= get_option('argon_article_header_style', 'article-header-style-default') . ' ';
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false){
		$htmlclasses .= ' using-safari';
	}
?>
<html <?php language_attributes(); ?> class="no-js <?php echo $htmlclasses;?>">
<?php
	$themecolor = get_option('argon_theme_color');
	if ($themecolor == ""){
		$themecolor = "#5e72e4";
	}
	$themecolor_origin = $themecolor;
	if (isset($_COOKIE["argon_custom_theme_color"])){
		if (checkHEX($_COOKIE["argon_custom_theme_color"]) && get_option('argon_show_customize_theme_color_picker') != 'false'){
			$themecolor = $_COOKIE["argon_custom_theme_color"];
		}
	}
	if (hex2gray($themecolor) < 50){
		echo '<script>document.getElementsByTagName("html")[0].classList.add("themecolor-toodark");</script>';
	}
?>
<?php
	$cardradius = get_option('argon_card_radius');
	if ($cardradius == ""){
		$cardradius = "4";
	}
	$cardradius_origin = $cardradius;
	if (isset($_COOKIE["argon_card_radius"]) && $_COOKIE["argon_card_radius"] != ""){
		$cardradius = $_COOKIE["argon_card_radius"];
	}
?>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php if (get_option('argon_enable_mobile_scale') != 'true'){ ?>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<?php }else{ ?>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
	<?php } ?>
	<meta property="og:title" content="<?php echo wp_get_document_title();?>">
	<meta property="og:type" content="article">
	<meta property="og:url" content="<?php echo home_url(add_query_arg(array(),$wp->request));?>">
	<?php
		$seo_description = get_seo_description();
		if ($seo_description != ''){ ?>
			<meta name="description" content="<?php echo $seo_description?>">
			<meta property="og:description" content="<?php echo $seo_description?>">
	<?php } ?>

	<?php
		$seo_keywords = get_seo_keywords();
		if ($seo_keywords != ''){ ?>
			<meta name="keywords" content="<?php echo get_seo_keywords();?>">
	<?php } ?>

	<?php
		if (is_single() || is_page()){
			$og_image = get_og_image();
			if ($og_image != ''){ ?>
				<meta property="og:image" content="<?php echo $og_image?>" />
	<?php 	}
		} ?>

	<meta name="theme-color" content="<?php echo $themecolor; ?>">
	<meta name="theme-color-rgb" content="<?php echo hex2str($themecolor); ?>">
	<meta name="theme-color-origin" content="<?php echo $themecolor_origin; ?>">
	<meta name="argon-enable-custom-theme-color" content="<?php echo (get_option('argon_show_customize_theme_color_picker') != 'false' ? 'true' : 'false'); ?>">


	<meta name="theme-card-radius" content="<?php echo $cardradius; ?>">
	<meta name="theme-card-radius-origin" content="<?php echo $cardradius_origin; ?>">

	<meta name="theme-version" content="<?php echo $GLOBALS['theme_version']; ?>">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
	<?php endif; ?>
	<?php
		wp_enqueue_style("argon_css_merged", $GLOBALS['assets_path'] . "/assets/argon_css_merged.css", null, $GLOBALS['theme_version']);
		wp_enqueue_style("style", $GLOBALS['assets_path'] . "/style.css", null, $GLOBALS['theme_version']);
		if (get_option('argon_disable_googlefont') != 'true') {wp_enqueue_style("googlefont", "//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Noto+Serif+SC:300,600&display=swap");}
		wp_enqueue_script("argon_js_merged", $GLOBALS['assets_path'] . "/assets/argon_js_merged.js", null, $GLOBALS['theme_version']);
		wp_enqueue_script("argonjs", $GLOBALS['assets_path'] . "/assets/js/argon.min.js", null, $GLOBALS['theme_version']);
	?>
	<?php wp_head(); ?>
	<?php $GLOBALS['wp_path'] = get_option('argon_wp_path') == '' ? '/' : get_option('argon_wp_path'); ?>
	<script>
		document.documentElement.classList.remove("no-js");
		var argonConfig = {
			wp_path: "<?php echo $GLOBALS['wp_path']; ?>",
			language: "<?php echo argon_get_locate(); ?>",
			dateFormat: "<?php echo get_option('argon_dateformat', 'YMD'); ?>",
			<?php if (get_option('argon_enable_zoomify') == 'true'){ ?>
				zoomify: {
					duration: <?php echo get_option('argon_zoomify_duration', 200); ?>,
					easing: "<?php echo get_option('argon_zoomify_easing', 'cubic-bezier(0.4,0,0,1)'); ?>",
					scale: <?php echo get_option('argon_zoomify_scale', 0.9); ?>
				},
			<?php } else { ?>
				zoomify: false,
			<?php } ?>
			pangu: "<?php echo get_option('argon_enable_pangu', 'false'); ?>",
			<?php if (get_option('argon_enable_lazyload') != 'false'){ ?>
				lazyload: {
					threshold: <?php echo get_option('argon_lazyload_threshold', 800); ?>,
					effect: "<?php echo get_option('argon_lazyload_effect', 'fadeIn'); ?>"
				},
			<?php } else { ?>
				lazyload: false,
			<?php } ?>
			fold_long_comments: <?php echo get_option('argon_fold_long_comments', 'false'); ?>,
			fold_long_shuoshuo: <?php echo get_option('argon_fold_long_shuoshuo', 'false'); ?>,
			disable_pjax: <?php echo get_option('argon_pjax_disabled', 'false'); ?>,
			pjax_animation_durtion: <?php echo (get_option("argon_disable_pjax_animation") == 'true' ? '0' : '600'); ?>,
			headroom: "<?php echo get_option('argon_enable_headroom', 'false'); ?>",
			waterflow_columns: "<?php echo get_option('argon_article_list_waterflow', '1'); ?>",
			code_highlight: {
				enable: <?php echo get_option('argon_enable_code_highlight', 'false'); ?>,
				hide_linenumber: <?php echo get_option('argon_code_highlight_hide_linenumber', 'false'); ?>,
				linenumber_background_transparent: <?php echo get_option('argon_code_highlight_linenumber_background_transparent', 'true'); ?>,
				break_line: <?php echo get_option('argon_code_highlight_break_line', 'false'); ?>
			}
		}
	</script>
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
			if (<?php echo apply_filters("argon_darkmode_time_check", "hour < 7 || hour >= 22")?>){
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
	<script>
		if (navigator.userAgent.indexOf("Safari") !== -1 && navigator.userAgent.indexOf("Chrome") === -1){
			$("html").addClass("using-safari");
		}
	</script>

	<?php if (get_option('argon_enable_smoothscroll_type') == '2') { /*平滑滚动*/?>
		<script src="<?php echo $GLOBALS['assets_path']; ?>/assets/vendor/smoothscroll/smoothscroll2.js"></script>
	<?php }else if (get_option('argon_enable_smoothscroll_type') == '3'){?>
		<script src="<?php echo $GLOBALS['assets_path']; ?>/assets/vendor/smoothscroll/smoothscroll3.min.js"></script>
	<?php }else if (get_option('argon_enable_smoothscroll_type') == '1_pulse'){?>
		<script src="<?php echo $GLOBALS['assets_path']; ?>/assets/vendor/smoothscroll/smoothscroll1_pulse.js"></script>
	<?php }else if (get_option('argon_enable_smoothscroll_type') != 'disabled'){?>
		<script src="<?php echo $GLOBALS['assets_path']; ?>/assets/vendor/smoothscroll/smoothscroll1.js"></script>
	<?php }?>
</head>

<?php echo get_option('argon_custom_html_head'); ?>

<style id="themecolor_css">
	<?php
		$themecolor_rgbstr = hex2str($themecolor);
		$RGB = hexstr2rgb($themecolor);
		$HSL = rgb2hsl($RGB['R'], $RGB['G'], $RGB['B']);

		$RGB_dark0 = hsl2rgb($HSL['h'], $HSL['s'], max($HSL['l'] - 0.025, 0));
		$themecolor_dark0 = rgb2hex($RGB_dark0['R'],$RGB_dark0['G'],$RGB_dark0['B']);

		$RGB_dark = hsl2rgb($HSL['h'], $HSL['s'], max($HSL['l'] - 0.05, 0));
		$themecolor_dark = rgb2hex($RGB_dark['R'], $RGB_dark['G'], $RGB_dark['B']);

		$RGB_dark2 = hsl2rgb($HSL['h'], $HSL['s'], max($HSL['l'] - 0.1, 0));
		$themecolor_dark2 = rgb2hex($RGB_dark2['R'],$RGB_dark2['G'],$RGB_dark2['B']);

		$RGB_dark3 = hsl2rgb($HSL['h'], $HSL['s'], max($HSL['l'] - 0.15, 0));
		$themecolor_dark3 = rgb2hex($RGB_dark3['R'],$RGB_dark3['G'],$RGB_dark3['B']);

		$RGB_light = hsl2rgb($HSL['h'], $HSL['s'], min($HSL['l'] + 0.1, 1));
		$themecolor_light = rgb2hex($RGB_light['R'],$RGB_light['G'],$RGB_light['B']);
	?>
	:root{
		--themecolor: <?php echo $themecolor; ?>;
		--themecolor-dark0: <?php echo $themecolor_dark0; ?>;
		--themecolor-dark: <?php echo $themecolor_dark; ?>;
		--themecolor-dark2: <?php echo $themecolor_dark2; ?>;
		--themecolor-dark3: <?php echo $themecolor_dark3; ?>;
		--themecolor-light: <?php echo $themecolor_light; ?>;
		--themecolor-rgbstr: <?php echo $themecolor_rgbstr; ?>;
		--themecolor-gradient: linear-gradient(150deg,var(--themecolor-light) 15%, var(--themecolor) 70%, var(--themecolor-dark0) 94%);

	}
</style>
<style id="theme_cardradius_css">
	:root{
		--card-radius: <?php echo $cardradius; ?>px;
	}
</style>

<body <?php body_class(); ?>>
<?php /*wp_body_open();*/ ?>
<div id="toolbar">
	<header class="header-global">
		<nav id="navbar-main" class="navbar navbar-main navbar-expand-lg navbar-transparent navbar-light bg-primary headroom--not-bottom headroom--not-top headroom--pinned">
			<div class="container">
				<button id="open_sidebar" class="navbar-toggler" type="button" aria-expanded="false" aria-label="Toggle sidebar">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="navbar-brand mr-0">
					<?php if (get_option('argon_toolbar_icon') != '') { /*顶栏ICON(如果选项中开启)*/?>
						<a class="navbar-brand navbar-icon mr-lg-5" href="<?php echo get_option('argon_toolbar_icon_link'); ?>">
							<img src="<?php echo get_option('argon_toolbar_icon'); ?>">
						</a>
					<?php }?>
					<?php
						//顶栏标题
						$toolbar_title = get_option('argon_toolbar_title') == '' ? get_bloginfo('name') : get_option('argon_toolbar_title');
						if ($toolbar_title == '--hidden--'){
							$toolbar_title = '';
						}
					?>
					<a class="navbar-brand navbar-title" href="<?php bloginfo('url'); ?>"><?php echo $toolbar_title;?></a>
				</div>
				<div class="navbar-collapse collapse" id="navbar_global">
					<div class="navbar-collapse-header">
						<div class="row" style="display: none;">
							<div class="col-6 collapse-brand"></div>
							<div class="col-6 collapse-close">
								<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
									<span></span>
									<span></span>
								</button>
							</div>
						</div>
						<div class="input-group input-group-alternative">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-search"></i></span>
							</div>
							<input id="navbar_search_input_mobile" class="form-control" placeholder="搜索什么..." type="text" autocomplete="off">
						</div>
					</div>
					<?php
						/*顶栏菜单*/
						class toolbarMenuWalker extends Walker_Nav_Menu{
							public function start_lvl( &$output, $depth = 0, $args = array() ) {
								$indent = str_repeat("\t", $depth);
								$output .= "\n$indent<div class=\"dropdown-menu\">\n";
							}
							public function end_lvl( &$output, $depth = 0, $args = array() ) {
								$indent = str_repeat("\t", $depth);
								$output .= "\n$indent</div>\n";
							}
							public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
								if ($depth == 0){
									if ($args -> walker -> has_children == 1){
										$output .= "\n
										<li class='nav-item dropdown'>
											<a href='" . $object -> url . "' class='nav-link' data-toggle='dropdown' no-pjax onclick='return false;' title='" . $object -> description . "'>
										  		<i class='ni ni-book-bookmark d-lg-none'></i>
												<span class='nav-link-inner--text'>" . $object -> title . "</span>
										  </a>";
									}else{
										$output .= "\n
										<li class='nav-item'>
											<a href='" . $object -> url . "' class='nav-link' target='" . $object -> target . "' title='" . $object -> description . "'>
										  		<i class='ni ni-book-bookmark d-lg-none'></i>
												<span class='nav-link-inner--text'>" . $object -> title . "</span>
										  </a>";
									}
								}else if ($depth == 1){
									$output .= "<a href='" . $object -> url . "' class='dropdown-item' target='" . $object -> target . "' title='" . $object -> description . "'>" . $object -> title . "</a>";
								}
							}
							public function end_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
								if ($depth == 0){
									$output .= "\n</li>";
								}
							}
						}
						if ( has_nav_menu('toolbar_menu') ){
							echo "<ul class='navbar-nav navbar-nav-hover align-items-lg-center'>";
							wp_nav_menu( array(
								'container'  => '',
								'theme_location'  => 'toolbar_menu',
								'items_wrap'  => '%3$s',
								'depth' => 0,
								'walker' => new toolbarMenuWalker()
							) );
							echo "</ul>";
						}
					?>
					<ul class="navbar-nav align-items-lg-center ml-lg-auto">
						<li id="navbar_search_container" class="nav-item" data-toggle="modal">
							<div id="navbar_search_input_container">
								<div class="input-group input-group-alternative">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fa fa-search"></i></span>
									</div>
									<input id="navbar_search_input" class="form-control" placeholder="<?php _e('搜索什么...', 'argon');?>" type="text" autocomplete="off">
								</div>
							</div>
						</li>
					</ul>
				</div>
				<div id="navbar_menu_mask" data-toggle="collapse" data-target="#navbar_global"></div>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon navbar-toggler-searcg-icon"></span>
				</button>
			</div>
		</nav>
	</header>
</div>
<div class="modal fade" id="argon_search_modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php _e('搜索', 'argon');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</div>
<!--Banner-->
<section id="banner" class="banner section section-lg section-shaped">
	<div class="shape <?php echo get_option('argon_banner_background_hide_shapes') == 'true' ? '' : 'shape-style-1' ?> <?php echo get_option('argon_banner_background_color_type') == '' ? 'shape-primary' : get_option('argon_banner_background_color_type'); ?>">
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

	<?php
		$banner_title = get_option('argon_banner_title') == '' ? get_bloginfo('name') : get_option('argon_banner_title');
		$enable_banner_title_typing_effect = get_option('argon_enable_banner_title_typing_effect') != 'true' ? "false" : get_option('argon_enable_banner_title_typing_effect');
	?>
	<div id="banner_container" class="banner-container container text-center">
		<?php if ($enable_banner_title_typing_effect != "true"){?>
			<div class="banner-title text-white"><span class="banner-title-inner"><?php echo apply_filters('argon_banner_title_html', $banner_title); ?></span>
			<?php echo get_option('argon_banner_subtitle') == '' ? '' : '<span class="banner-subtitle d-block">' . get_option('argon_banner_subtitle') . '</span>'; ?></div>
		<?php } else {?>
			<div class="banner-title text-white" data-interval="<?php echo get_option('argon_banner_typing_effect_interval', 100); ?>"><span data-text="<?php echo $banner_title; ?>" class="banner-title-inner">&nbsp;</span>
			<?php echo get_option('argon_banner_subtitle') == '' ? '' : '<span data-text="' . get_option('argon_banner_subtitle') . '" class="banner-subtitle d-block">&nbsp;</span>'; ?></div>
		<?php }?>
	</div>
	<?php if (get_option('argon_banner_background_url') != '') { ?>
		<style>
			section.banner{
				background-image: url(<?php echo get_banner_background_url(); ?>) !important;
			}
		</style>
	<?php } ?>
	<?php if ($banner_size == 'fullscreen') { ?>
		<div class="cover-scroll-down">
			<i class="fa fa-angle-down" aria-hidden="true"></i>
		</div>
	<?php } ?>
</section>

<?php if (apply_filters('argon_page_background_url', get_option('argon_page_background_url')) != '') { ?>
	<style>
		<?php if (get_option('argon_page_background_banner_style', 'false') == 'transparent') { ?>
			#banner, #banner .shape {
				background: transparent !important;
			}
		<?php } ?>
		#content:before {
			content: '';
			display: block;
			position: fixed;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			z-index: -2;
			background: url(<?php echo apply_filters('argon_page_background_url', get_option('argon_page_background_url'));?>);
			background-position: center;
			background-size: cover;
			background-repeat: no-repeat;
			opacity: <?php echo (get_option('argon_page_background_opacity') == '' ? '1' : get_option('argon_page_background_opacity')); ?>;
			transition: opacity .5s ease;
		}
		html.darkmode #content:before{
			filter: brightness(0.65);
		}
		<?php if (apply_filters('argon_page_background_dark_url', get_option('argon_page_background_dark_url')) != '') { ?>
			#content:after {
				content: '';
				display: block;
				position: fixed;
				left: 0;
				right: 0;
				top: 0;
				bottom: 0;
				z-index: -2;
				background: url(<?php echo apply_filters('argon_page_background_dark_url', get_option('argon_page_background_dark_url'));?>);
				background-position: center;
				background-size: cover;
				background-repeat: no-repeat;
				opacity: 0;
				transition: opacity .5s ease;
			}
			html.darkmode #content:after {
				opacity: <?php echo (get_option('argon_page_background_opacity') == '' ? '1' : get_option('argon_page_background_opacity')); ?>;
			}
			html.darkmode #content:before {
				opacity: 0;
			}
		<?php } ?>
	</style>
<?php } ?>

<?php if (get_option('argon_show_toolbar_mask') == 'true') { ?>
	<style>
		#banner:after {
			content: '';
			width: 100vw;
			position: absolute;
			left: 0;
			top: 0;
			height: 120px;
			background: linear-gradient(180deg, rgba(0,0,0,0.25) 0%, rgba(0,0,0,0.15) 35%, rgba(0,0,0,0) 100%);
			display: block;
			z-index: -1;
		}
		.banner-title {
			text-shadow: 0 5px 15px rgba(0, 0, 0, .2);
		}
	</style>
<?php } ?>

<div id="float_action_buttons" class="float-action-buttons fabtns-unloaded">
	<button id="fabtn_toggle_sides" class="btn btn-icon btn-neutral fabtn shadow-sm" type="button" aria-hidden="true" tooltip-move-to-left="<?php _e('移至左侧', 'argon'); ?>" tooltip-move-to-right="<?php _e('移至右侧', 'argon'); ?>">
		<span class="btn-inner--icon fabtn-show-on-right"><i class="fa fa-caret-left"></i></span>
		<span class="btn-inner--icon fabtn-show-on-left"><i class="fa fa-caret-right"></i></span>
	</button>
	<button id="fabtn_back_to_top" class="btn btn-icon btn-neutral fabtn shadow-sm" type="button" aria-label="Back To Top" tooltip="<?php _e('回到顶部', 'argon'); ?>">
		<span class="btn-inner--icon"><i class="fa fa-angle-up"></i></span>
	</button>
	<button id="fabtn_go_to_comment" class="btn btn-icon btn-neutral fabtn shadow-sm d-none" type="button" <?php if (get_option('argon_fab_show_gotocomment_button') != 'true') echo " style='display: none;'";?> aria-label="Comment" tooltip="<?php _e('评论', 'argon'); ?>">
		<span class="btn-inner--icon"><i class="fa fa-comment-o"></i></span>
	</button>
	<button id="fabtn_toggle_darkmode" class="btn btn-icon btn-neutral fabtn shadow-sm" type="button" <?php if (get_option('argon_fab_show_darkmode_button') != 'true') echo " style='display: none;'";?> aria-label="Toggle Darkmode" tooltip-darkmode="<?php _e('夜间模式', 'argon'); ?>" tooltip-blackmode="<?php _e('暗黑模式', 'argon'); ?>" tooltip-lightmode="<?php _e('日间模式', 'argon'); ?>">
		<span class="btn-inner--icon"><i class="fa fa-moon-o"></i><i class='fa fa-lightbulb-o'></i></span>
	</button>
	<button id="fabtn_toggle_blog_settings_popup" class="btn btn-icon btn-neutral fabtn shadow-sm" type="button" <?php if (get_option('argon_fab_show_settings_button') == 'false') echo " style='display: none;'";?> aria-label="Open Blog Settings Menu" tooltip="<?php _e('设置', 'argon'); ?>">
		<span class="btn-inner--icon"><i class="fa fa-cog"></i></span>
	</button>
	<div id="fabtn_blog_settings_popup" class="card shadow-sm" style="opacity: 0;" aria-hidden="true">
		<div id="close_blog_settings"><i class="fa fa-close"></i></div>
		<div class="blog-setting-item mt-3">
			<div style="transform: translateY(-4px);"><div id="blog_setting_toggle_darkmode_and_amoledarkmode" tooltip-switch-to-darkmode="<?php _e('切换到夜间模式', 'argon'); ?>" tooltip-switch-to-blackmode="<?php _e('切换到暗黑模式', 'argon'); ?>"><span><?php _e('夜间模式', 'argon');?></span><span><?php _e('暗黑模式', 'argon');?></span></div></div>
			<div style="flex: 1;"></div>
			<label id="blog_setting_darkmode_switch" class="custom-toggle">
				<span class="custom-toggle-slider rounded-circle"></span>
			</label>
		</div>
		<div class="blog-setting-item mt-3">
			<div style="flex: 1;"><?php _e('字体', 'argon');?></div>
			<div>
				<button id="blog_setting_font_sans_serif" type="button" class="blog-setting-font btn btn-outline-primary blog-setting-selector-left">Sans Serif</button><button id="blog_setting_font_serif" type="button" class="blog-setting-font btn btn-outline-primary blog-setting-selector-right">Serif</button>
			</div>
		</div>
		<div class="blog-setting-item mt-3">
			<div style="flex: 1;"><?php _e('阴影', 'argon');?></div>
			<div>
				<button id="blog_setting_shadow_small" type="button" class="blog-setting-shadow btn btn-outline-primary blog-setting-selector-left"><?php _e('浅阴影', 'argon');?></button><button id="blog_setting_shadow_big" type="button" class="blog-setting-shadow btn btn-outline-primary blog-setting-selector-right"><?php _e('深阴影', 'argon');?></button>
			</div>
		</div>
		<div class="blog-setting-item mt-3 mb-3">
			<div style="flex: 1;"><?php _e('滤镜', 'argon');?></div>
			<div id="blog_setting_filters" class="ml-3">
				<button id="blog_setting_filter_off" type="button" class="blog-setting-filter-btn ml-0" filter-name="off"><?php _e('关闭', 'argon');?></button>
				<button id="blog_setting_filter_sunset" type="button" class="blog-setting-filter-btn" filter-name="sunset"><?php _e('日落', 'argon');?></button>
				<button id="blog_setting_filter_darkness" type="button" class="blog-setting-filter-btn" filter-name="darkness"><?php _e('暗化', 'argon');?></button>
				<button id="blog_setting_filter_grayscale" type="button" class="blog-setting-filter-btn" filter-name="grayscale"><?php _e('灰度', 'argon');?></button>
			</div>
		</div>
		<div class="blog-setting-item mb-3">
			<div id="blog_setting_card_radius_to_default" style="cursor: pointer;" tooltip="<?php _e('恢复默认', 'argon'); ?>"><?php _e('圆角', 'argon');?></div>
			<div style="flex: 1;margin-left: 20px;margin-right: 8px;transform: translateY(2px);">
				<div id="blog_setting_card_radius"></div>
			</div>
		</div>
		<?php if (get_option('argon_show_customize_theme_color_picker') != 'false') {?>
			<div class="blog-setting-item mt-1 mb-3">
				<div style="flex: 1;"><?php _e('主题色', 'argon');?></div>
				<div id="theme-color-picker" class="ml-3"></div>
			</div>
		<?php }?>
	</div>
	<button id="fabtn_reading_progress" class="btn btn-icon btn-neutral fabtn shadow-sm" type="button" aria-hidden="true" tooltip="<?php _e('阅读进度', 'argon'); ?>">
		<div id="fabtn_reading_progress_bar" style="width: 0%;"></div>
		<span id="fabtn_reading_progress_details">0%</span>
	</button>
</div>

<div id="content" class="site-content">
