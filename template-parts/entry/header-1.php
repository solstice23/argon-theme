<?php
/**
 * 展示文章的头部，包括特色图片、标题（外部模板）、元数据（外部模板）
 * 布局 1 , 特色图片在标题下层
 * Template part for displaying post title, meta, thumbnail 
 *
 */
?>

<?php
    $article_cover = argon_post_cover_image('article');
?>

<header class="post-header text-center<?php if ($article_cover){echo " post-header-with-thumbnail";}?>">
    <?php
        if ($article_cover){
            echo $article_cover;
            echo "<div class='post-header-text-container'>";
        }

        do_action( 'argon_entry_title' );
        do_action( 'argon_entry_meta' );

        if ($article_cover){
            echo "</div>";
        }
    ?>
</header>


