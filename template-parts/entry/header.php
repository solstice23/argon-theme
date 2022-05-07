<?php
/**
 * 展示文章的头部，包括特色图片、标题（外部模板）、元数据（外部模板）
 * 此文件内根据特色图片位置及与标题之间的布局，判断载入哪个 template-part
 * Template part for displaying post title, meta, thumbnail 
 *
 */

if ( get_option('argon_show_thumbnail_in_banner_in_content_page') == 'true' ){
    get_template_part( 'template-parts/entry/header', 'thumbnail-in-banner' );
} else{
    $article_list_layout = get_option('argon_article_list_layout', '1');
    switch ( $article_list_layout ) {
        case '2':
            get_template_part( 'template-parts/entry/header', '1' );
            break;
        
        default:
            get_template_part( 'template-parts/entry/header', $article_list_layout );
            break;
    }
}
