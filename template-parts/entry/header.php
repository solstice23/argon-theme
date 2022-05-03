<?php
/**
 * 展示文章的头部，包括特色图片、标题（外部模板）、元数据（外部模板）
 * Template part for displaying post title, meta, thumbnail 
 *
 */
?>
<header class="post-header text-center<?php if (argon_has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){echo " post-header-with-thumbnail";}?>">
    <?php
        if (argon_has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){
            $thumbnail_url = argon_get_post_thumbnail();
            echo "<img class='post-thumbnail' src='" . $thumbnail_url . "'></img>";
            echo "<div class='post-header-text-container'>";
        }
        if (argon_has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') == 'true'){
            $thumbnail_url = argon_get_post_thumbnail();
            echo "
            <style>
                body section.banner {
                    background-image: url(" . $thumbnail_url . ") !important;
                }
            </style>";
        }

        do_action( 'argon_entry_title' );
        do_action( 'argon_entry_meta' );

        if (has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){
                echo "</div>";
            }
    ?>
</header>
