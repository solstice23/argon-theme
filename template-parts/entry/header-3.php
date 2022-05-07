<?php
/**
 * 展示文章的头部，包括特色图片、标题（外部模板）、元数据（外部模板）
 * 布局 3 , 特色图片在标题上方
 * Template part for displaying post title, meta, thumbnail 
 *
 */
?>
<?php
    if (argon_has_post_thumbnail()){
        echo "<header class='post-header post-header-with-thumbnail'>";
        $thumbnail_url = argon_get_post_thumbnail();
        if (get_option('argon_enable_lazyload') != 'false'){
            echo "<img class='post-thumbnail lazyload' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABBJREFUeNpi+P//PwNAgAEACPwC/tuiTRYAAAAASUVORK5CYII=' data-original='" . $thumbnail_url . "' alt='thumbnail' style='opacity: 0;'></img>";
        }else{
            echo "<img class='post-thumbnail' src='" . $thumbnail_url . "'></img>";
        }				
        echo "</header>";
    }
?>

<header class="post-header text-center">
    <?php 
        do_action( 'argon_entry_title' );
        do_action( 'argon_entry_meta' );
    ?>	
</header>


