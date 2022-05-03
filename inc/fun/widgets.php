<?php
function argon_widgets_init() {
	register_sidebar(
		array(
			'name'          => __('左侧栏小工具', 'argon'),
			'id'            => 'leftbar-tools',
			'description'   => __( '左侧栏小工具 (如果设置会在侧栏增加一个 Tab)', 'argon'),
			'before_widget' => '<div id="%1$s" class="widget %2$s card bg-white border-0">',
			'after_widget'  => '</div>',
			'before_title'  => '<h6 class="font-weight-bold text-black">',
			'after_title'   => '</h6>',
		)
	);
	register_sidebar(
		array(
			'name'          => __('右侧栏小工具', 'argon'),
			'id'            => 'rightbar-tools',
			'description'   => __( '右侧栏小工具 (在 "Argon 主题选项" 中选择 "三栏布局" 才会显示)', 'argon'),
			'before_widget' => '<div id="%1$s" class="widget %2$s card shadow-sm bg-white border-0">',
			'after_widget'  => '</div>',
			'before_title'  => '<h6 class="font-weight-bold text-black">',
			'after_title'   => '</h6>',
		)
	);
	register_sidebar(
		array(
			'name'          => __('站点概览额外内容', 'argon'),
			'id'            => 'leftbar-siteinfo-extra-tools',
			'description'   => __( '站点概览额外内容', 'argon'),
			'before_widget' => '<div id="%1$s" class="widget %2$s card bg-white border-0">',
			'after_widget'  => '</div>',
			'before_title'  => '<h6 class="font-weight-bold text-black">',
			'after_title'   => '</h6>',
		)
	);
}
add_action('widgets_init', 'argon_widgets_init');
