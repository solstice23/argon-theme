<?php
/**
 * 展示文章的 meta：日期、作者、分类等
 *
 */
?>
<div class="post-meta">
    <?php
        $metaList = explode('|', get_option('argon_article_meta', 'time|views|comments|categories'));
        if (is_sticky() && is_home() && ! is_paged()){
            array_unshift($metaList, "sticky");
        }
        if (post_password_required()){
            array_unshift($metaList, "needpassword");
        }
        if (is_meta_simple()){
            array_remove($metaList, "time");
            array_remove($metaList, "edittime");
            array_remove($metaList, "categories");
            array_remove($metaList, "author");
        }
        if (count(get_the_category()) == 0){
            array_remove($metaList, "categories");
        }
        for ($i = 0; $i < count($metaList); $i++){
            if ($i > 0){
                echo ' <div class="post-meta-devide">|</div> ';
            }
            echo get_article_meta($metaList[$i]);
        }
    ?>
    <?php if (!post_password_required() && get_option("argon_show_readingtime") != "false" && is_readingtime_meta_hidden() == False) {
        echo get_article_reading_time_meta(get_the_content());
    } ?>
</div>
