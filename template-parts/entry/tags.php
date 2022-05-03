<?php
/**
 * 展示页面的标签
 * Template part for displaying post tags
 *
 */
?>
<?php
if (has_tag()) { ?>
    <div class="post-tags">
        <i class="fa fa-tags" aria-hidden="true"></i>
        <?php
            $tags = get_the_tags();
            foreach ($tags as $tag) {
                echo "<a href='" . get_category_link($tag -> term_id) . "' target='_blank' class='tag badge badge-secondary post-meta-detail-tag'>" . $tag -> name . "</a>";
            }
        ?>
    </div>
<?php } ?>