<?php
/**
 * 展示文章的头部，包括特色图片、标题（外部模板）、元数据（外部模板）
 * 布局 3 , 特色图片在标题上方
 * Template part for displaying post title, meta, thumbnail 
 *
 */
?>
<?php
    $article_cover = argon_post_cover_image('article');
    if ($article_cover){
        echo "<header class='post-header post-header-with-thumbnail'>";
        echo $article_cover;
        echo "</header>";
    }
?>

<header class="post-header text-center">
    <?php 
        do_action( 'argon_entry_title' );
        do_action( 'argon_entry_meta' );
    ?>	
</header>


