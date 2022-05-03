<?php
    $relatedPosts = get_option('argon_related_post', 'disabled');
    if ($relatedPosts != "disabled"){
        global $post;
        $cat_array = array();
        if (strpos($relatedPosts, 'category') !== false){
            $cats = get_the_category($post -> ID);
            if ($cats){
                foreach($cats as $key1 => $cat) {
                    $cat_array[$key1] = $cat -> slug;
                }
            }
        }
        $tag_array = array();
        if (strpos($relatedPosts, 'tag') !== false){
            $tags = get_the_tags($post -> ID);
            if ($tags){
                foreach($tags as $key2 => $tag) {
                    $tag_array[$key2] = $tag -> slug;
                }
            }
        }	
        $query = new WP_Query(array(
            'posts_per_page' => get_option('argon_related_post_limit' , '10'),
            'order' => get_option('argon_related_post_sort_order', 'DESC'),
            'orderby' => get_option('argon_related_post_sort_orderby', 'date'),
            'meta_key' => 'views',
            'post__not_in' => array($post -> ID),
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $cat_array,
                    'include_children' => false
                ),
                array(
                    'taxonomy' => 'post_tag',
                    'field' => 'slug',
                    'terms' => $tag_array,
                )
            )
        ));
        if ($query -> have_posts()) {
            echo '<div class="related-posts card shadow-sm">
            <h2 class="post-comment-title">
            <i class="fa fa-book"></i>
            <span>' . __("推荐文章", 'argon') . '</span>
            </h2>
            <div class="related-post-container horizontal-scroll">';
            while ($query -> have_posts()) {
                $query -> the_post();
                $hasThumbnail = argon_has_post_thumbnail(get_the_ID());
                echo '<a class="related-post-card" href="' . get_the_permalink() . '">';
                echo '<div class="related-post-card-container' . ($hasThumbnail ? ' has-thumbnail' : '') . '">
                    <div class="related-post-title clamp" clamp-line="3">' . get_the_title() . '</div>
                    <i class="related-post-arrow fa fa-chevron-right" aria-hidden="true"></i>
                    </div>';
                if ($hasThumbnail){
                    echo '<img class="related-post-thumbnail lazyload" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABBJREFUeNpi+P//PwNAgAEACPwC/tuiTRYAAAAASUVORK5CYII=" data-original="' .  argon_get_post_thumbnail(get_the_ID()) . '"/>';
                }
                echo '</a>';
            }
            echo '</div></div>';
            wp_reset_query();
        }
    }
