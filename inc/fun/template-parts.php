<?php

/**
 * Template-function
 */


// single content
// do_action('argon_entry_herder');
// do_action('argon_entry_content');
// do_action('argon_entry_footer');
// 调用 hooks 时，如果给了参数 $args，
// 则 template-parts/content-{$args}.php, template-parts/entry/no-passwd-{$args}.php 
// 否则 template-parts/content-{post_type}.php, template-parts/entry/no-passwd-{post_type}.php
// 默认 template-parts/content.php, template-parts/entry/no-passwd.php
add_action( 'argon_single_content', 'argon_single_content', '', '1' );
function argon_single_content( $args = '' ){
    if( $args == '' ) { 
        $args = get_post_type(); 
    }
    get_template_part( 'template-parts/content', $args, $args = array( $args ) );
}

// single header 部分：feature picture 显示在 banner 或 below ，文章标题和 meta
add_action( 'argon_entry_header', 'argon_entry_header' );
function argon_entry_header(){
    get_template_part( 'template-parts/entry/header', get_post_type() );
}

// 文章标题
add_action( 'argon_entry_title', 'argon_entry_title' );
function argon_entry_title(){
    get_template_part( 'template-parts/entry/title', get_post_type() );
}

// 文章元数据
add_action( 'argon_entry_meta', 'argon_entry_meta' );
function argon_entry_meta(){
    get_template_part( 'template-parts/entry/meta', get_post_type() );
}

// 内容主体部分容器：
// 无密码: argon_entry_content_no_passwd_required
// 加密: argon_entry_content_passwd_required
add_action( 'argon_entry_content', 'argon_entry_content', '', '1' );
function argon_entry_content( $args = array() ){
    if( empty( $args ) ) { 
        $args = array(get_post_type()); 
    }
    get_template_part( 'template-parts/entry/content-container', $args[0], $args );
}

// 无密码时的内容模板
add_action( 'argon_entry_content_no_passwd_required', 'argon_entry_content_no_passwd_required', '', '1' );
function argon_entry_content_no_passwd_required( $name = array() ){
    // 防止直接 get_template_part( 'template-parts/content' ) 导致 name array 为空
    if( empty( $name ) ){ 
        $name = get_post_type(); 
    }
    get_template_part( 'template-parts/entry/no-passwd', $name[0] );
}

// 密码保护的内容模板
add_action( 'argon_entry_content_passwd_required', 'argon_entry_content_passwd_required' );
function argon_entry_content_passwd_required(){
    get_template_part( 'template-parts/entry/passwd-required', get_post_type() );
}

// 摘要
add_action( 'argon_entry_excerpt', 'argon_entry_excerpt' );
function argon_entry_excerpt(){
    get_template_part( 'template-parts/entry/excerpt', get_post_type() );
}

// 文末附加内容
add_action( 'argon_after_entry_content', 'argon_after_entry_content' );
function argon_after_entry_content(){
    $additionalContentAfterPost = get_additional_content_after_post();
    if ($additionalContentAfterPost != ""){
        echo "<div class='additional-content-after-post'>" . $additionalContentAfterPost . "</div>";
    }
}

// References 模块
add_action( 'argon_entry_reference_list', 'argon_entry_reference_list' );
function argon_entry_reference_list(){
    get_template_part( 'template-parts/entry/reference-list', get_post_type() );
}

// 捐赠模块
add_action( 'argon_entry_donate', 'argon_entry_donate' );
function argon_entry_donate(){
    get_template_part( 'template-parts/entry/donate', get_post_type() );
}

// 标签模块
add_action( 'argon_entry_tag', 'argon_entry_tag' );
function argon_entry_tag(){
    get_template_part( 'template-parts/entry/tag', get_post_type() );
}


// 内容尾部：捐赠、标签
add_action( 'argon_entry_footer', 'argon_entry_footer' );
function argon_entry_footer(){
    // get_template_part( 'template-parts/entry/footer', get_post_type() );
    do_action( 'argon_entry_reference_list' );
	do_action( 'argon_entry_donate' );
    do_action( 'argon_entry_tags' );
}
// add_action( 'argon_entry_footer_all', 'argon_entry_footer_all' );
// function argon_entry_footer_all(){
//     do_action( 'argon_entry_donate' );
//     do_action( 'argon_entry_tags' );
// }

// 分享按钮模块
add_action( 'argon_show_sharebtn', 'argon_show_sharebtn' );
function argon_show_sharebtn(){
    if (get_option("argon_show_sharebtn") != 'false') {
        get_template_part( 'template-parts/share' );
    }
}

// 显示评论
add_action( 'argon_show_comment', 'argon_show_comment' );
function argon_show_comment(){
    if (comments_open() || get_comments_number()) {
        comments_template();
    }
}

// 上一篇/下一篇.
add_action( 'argon_post_navigation', 'argon_post_navigation' );
function argon_post_navigation() {
	get_template_part( 'template-parts/post-navigation', get_post_type() );
}

// related posts.
add_action( 'argon_related_post', 'argon_related_post' );
function argon_related_post() {
	get_template_part( 'template-parts/related', get_post_type() );
}

