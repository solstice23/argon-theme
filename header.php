<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
	<?php endif; ?>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/nprogress/nprogress.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<link href="<?php bloginfo('template_url'); ?>/assets/vendor/nucleo/css/nucleo.css" rel="stylesheet">
	<link href="<?php bloginfo('template_url'); ?>/assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link type="text/css" href="<?php bloginfo('template_url'); ?>/assets/css/argon.min.css" rel="stylesheet">
	<link href="<?php bloginfo('template_url'); ?>/style.css?v<?php echo wp_get_theme('argon')-> Version; ?>" type='text/css' media='all' rel='stylesheet'>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/jquery/jquery.min.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/bootstrap/bootstrap.min.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/popper/popper.min.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/headindex/headindex.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/headroom/headroom.min.js"></script>
	<link href="<?php bloginfo('template_url'); ?>/assets/vendor/izitoast/css/iziToast.css" rel="stylesheet">
	<script src="<?php bloginfo('template_url'); ?>/assets/vendor/izitoast/js/iziToast.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Noto+Serif+SC:300&display=swap" rel="stylesheet">

	<?php if (get_option('argon_enable_smoothscroll_type') == '2') { /*平滑滚动*/?>
		<script src="<?php bloginfo('template_url'); ?>/assets/vendor/smoothscroll/smoothscroll2.js"></script>
	<?php }else if (get_option('argon_enable_smoothscroll_type') == '3'){?>
		<script src="<?php bloginfo('template_url'); ?>/assets/vendor/smoothscroll/smoothscroll3.min.js"></script>
	<?php }else if (get_option('argon_enable_smoothscroll_type') == '1_pulse'){?>
		<script src="<?php bloginfo('template_url'); ?>/assets/vendor/smoothscroll/smoothscroll1_pulse.js"></script>
	<?php }else if (get_option('argon_enable_smoothscroll_type') != 'disabled'){?>
		<script src="<?php bloginfo('template_url'); ?>/assets/vendor/smoothscroll/smoothscroll1.js"></script>
	<?php }?>

	<?php if (get_option('argon_enable_lazyload') != 'false') { /*LazyLoad*/?>
		<script src="<?php bloginfo('template_url'); ?>/assets/vendor/lazyload/jquery.lazyload.min.js"></script>
	<?php }?>

	<?php if (get_option('argon_enable_lazyload') != 'false') { /*Zoomify*/?>
		<script src="<?php bloginfo('template_url'); ?>/assets/vendor/zoomify/zoomify.js"></script>
	<?php }?>

	<?php if (get_option('argon_show_sharebtn') != 'false') { /*Share.js*/?>
		<script src="<?php bloginfo('template_url'); ?>/assets/vendor/sharejs/share.min.js"></script>
	<?php }?>

	<script src="<?php bloginfo('template_url'); ?>/assets/js/argon.min.js"></script>
	<?php wp_head(); ?>
	<meta name="theme-color" content="#5e72e4">
</head>

<?php echo get_option('argon_custom_html_head'); ?>

<body <?php body_class(); ?>>
<?php /*wp_body_open();*/ ?>
<div id="using_pjax" style="display: none;"></div>
<div id="toolbar">
	<header class="header-global">
		<nav id="navbar-main" class="navbar navbar-main navbar-expand-lg navbar-transparent navbar-light bg-primary headroom--not-bottom headroom--not-top headroom--pinned">
			<div class="container">
				<?php if (get_option('argon_toolbar_icon') != '') { /*顶栏ICON(如果选项中开启)*/?>
					<a class="navbar-brand mr-lg-5" href="<?php echo get_option('argon_toolbar_icon_link'); ?>">
						<img src="<?php echo get_option('argon_toolbar_icon'); ?>">
					</a>
				<?php }?>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<?php /*顶栏标题*/?>
				<a class="navbar-brand" href="<?php bloginfo('url'); ?>"><?php echo get_option('argon_toolbar_title') == '' ? bloginfo('name') : get_option('argon_toolbar_title'); ?></a>
				<div class="navbar-collapse collapse" id="navbar_global">
					<div class="navbar-collapse-header">
						<div class="row">
							<div class="col-6 collapse-brand">
								<?php if (get_option('argon_toolbar_icon') != '') { /*顶栏ICON(小屏折叠菜单中)(如果选项中开启)*/?>
									<a class="navbar-brand mr-lg-5" href="<?php echo get_option('argon_toolbar_icon_link'); ?>">
										<img src="<?php echo get_option('argon_toolbar_icon'); ?>">
									</a>
								<?php }?>
							</div>
							<div class="col-6 collapse-close">
								<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
									<span></span>
									<span></span>
								</button>
							</div>
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
										<li class='nav-item dropdown' onclick='return false;'>
											<a href='" . $object -> url . "' class='nav-link' data-toggle='dropdown' no-pjax onclick='return false;'>
										  		<i class='ni ni-book-bookmark d-lg-none'></i>
												<span class='nav-link-inner--text'>" . $object -> title . "</span>
										  </a>";
									}else{
										$output .= "\n
										<li class='nav-item'>
											<a href='" . $object -> url . "' class='nav-link'>
										  		<i class='ni ni-book-bookmark d-lg-none'></i>
												<span class='nav-link-inner--text'>" . $object -> title . "</span>
										  </a>";
									}
									
								}else if ($depth == 1){
									$output .= "<a href=" . $object -> url . " class='dropdown-item'>" . $object -> title . "</a>";
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
						<li class="nav-item" data-toggle="modal" data-target="#argon_search_modal">
							<a class="nav-link nav-link-icon">
								<i class="fa fa-search"></i>
								<span class="nav-link-inner--text d-lg-none">搜索</span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</header>
</div>
<!--搜索弹出框-->
<div class="modal fade" id="argon_search_modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">搜索</h5>
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
<section id="banner" class="banner section section-lg section-shaped banner-background-loading">
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
	<div id="banner_container" class="banner-container container text-center">
		<h1 class="banner-title text-white"><?php echo get_option('argon_banner_title') == '' ? bloginfo('name') : get_option('argon_banner_title'); ?></h1>
	</div>
	<?php if (get_option('argon_banner_background_url') != '') { ?>
		<style>
			section.banner{
				background-image: url(<?php echo get_banner_background_url(); ?>) !important;
			}
		</style>
	<?php } ?>
</section>

<div id="float_action_buttons" class="float-action-buttons fabs-unloaded">
	<button id="fab_toggle_sides" class="btn btn-icon btn-neutral fab shadow-sm" type="button">
		<span class="btn-inner--icon fab-show-on-right"><i class="fa fa-caret-left"></i></span>
		<span class="btn-inner--icon fab-show-on-left"><i class="fa fa-caret-right"></i></span>
	</button>
	<button id="fab_back_to_top" class="btn btn-icon btn-neutral fab shadow-sm" type="button">
		<span class="btn-inner--icon"><i class="fa fa-angle-up"></i></span>
	</button>
	<button id="fab_toggle_darkmode" class="btn btn-icon btn-neutral fab shadow-sm" type="button" <?php if (get_option('argon_fab_show_darkmode_button') != 'true') echo " style='display: none;'";?>>
		<span class="btn-inner--icon"><i class="fa fa-moon-o"></i></span>
	</button>
	<button id="fab_toggle_blog_settings_popup" class="btn btn-icon btn-neutral fab shadow-sm" type="button" <?php if (get_option('argon_fab_show_settings_button') == 'false') echo " style='display: none;'";?>>
		<span class="btn-inner--icon"><i class="fa fa-cog"></i></span>
	</button>
	<div id="fab_blog_settings_popup" class="card shadow-sm" style="opacity: 0;">
		<div id="close_blog_settings"><i class="fa fa-close"></i></div>
		<div class="blog-setting-item mt-3">
			<div style="flex: 1;transform: translateY(-4px);">夜间模式</div>
			<label class="custom-toggle">
				<input id="blog_setting_darkmode_switch" type="checkbox">
				<span class="custom-toggle-slider rounded-circle"></span>
			</label>
		</div>
		<div class="blog-setting-item mt-3">
			<div style="flex: 1;">字体</div>
			<div>
				<button id="blog_setting_font_sans_serif" type="button" class="blog-setting-font btn btn-outline-primary">Sans Serif</button><button id="blog_setting_font_serif" type="button" class="blog-setting-font btn btn-outline-primary">Serif</button>
			</div>
		</div>
		<div class="blog-setting-item mt-3">
			<div style="flex: 1;">阴影</div>
			<div>
				<button id="blog_setting_shadow_small" type="button" class="blog-setting-shadow btn btn-outline-primary">浅阴影</button><button id="blog_setting_shadow_big" type="button" class="blog-setting-shadow btn btn-outline-primary">深阴影</button>
			</div>
		</div>
		<div class="blog-setting-item mt-3 mb-3">
			<div style="flex: 1;">滤镜</div>
			<div id="blog_setting_filters" class="ml-3">
				<button id="blog_setting_filter_off" type="button" class="blog-setting-filter-btn ml-0" filter-name="off">关闭</button>
				<button id="blog_setting_filter_sunset" type="button" class="blog-setting-filter-btn" filter-name="sunset">日落</button>
				<button id="blog_setting_filter_darkness" type="button" class="blog-setting-filter-btn" filter-name="darkness">暗化</button>
				<button id="blog_setting_filter_grayscale" type="button" class="blog-setting-filter-btn" filter-name="grayscale">灰度</button>
			</div>
		</div>
	</div>
	<button id="fab_open_sidebar" class="btn btn-icon btn-neutral fab shadow-sm" type="button">
		<span class="btn-inner--icon"><i class="fa fa-bars"></i></span>
	</button>
	<button id="fab_reading_progress" class="btn btn-icon btn-neutral fab shadow-sm" type="button">
		<div id="fab_reading_progress_bar" style="width: 0%;"></div>
		<span id="fab_reading_progress_details">0%</span>
	</button>
</div>

<div id="content" class="site-content">
