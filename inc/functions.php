<?php

// 
require get_template_directory() . '/inc/hooks.php';

// 
require get_template_directory() . '/inc/single-functions.php';
require get_template_directory() . '/inc/archive-functions.php';

//更新主题版本后的兼容
require get_template_directory() . '/inc/compatible.php';

// 注册小工具
require get_template_directory() . '/inc/register-widgets.php';

// 注册新后台主题配色方案
require get_template_directory() . '/inc/register-admin-color.php';

// 注册shuoshuo
require get_template_directory() . '/inc/shuoshuo-functions.php';

// 编辑文章界面新增 Meta 编辑模块
require get_template_directory() . '/inc/register-customer-field.php';

// 注册古腾堡区块和简码，编辑器添加相关按钮
require get_template_directory() . '/inc/register-shortcode.php';

// 表情包
require_once(get_template_directory() . '/inc/emotions.php');

// 评论相关函数
require get_template_directory() . '/inc/comment-functions.php';
