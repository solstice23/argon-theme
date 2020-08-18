<?php get_header(); ?>

<div class="page-information-card-container"></div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'single' );

			if (get_option("argon_show_sharebtn") != 'false') {
				get_template_part( 'template-parts/share' );
			}

			if (comments_open() || get_comments_number()) {
				comments_template();
			}

			if ( is_singular( 'post' ) ) {
				if (get_previous_post() || get_next_post()){
					echo '<div class="post-navigation card shadow-sm">';
					if (get_previous_post()){ 
						previous_post_link('<div class="post-navigation-item post-navigation-pre"><span class="page-navigation-extra-text"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i>' . __("上一篇", 'argon') . '</span>%link</div>' , '%title');
					}else{
						echo '<div class="post-navigation-item post-navigation-pre"></div>';
					}
					if (get_next_post()){
						next_post_link('<div class="post-navigation-item post-navigation-next"><span class="page-navigation-extra-text">' . __("下一篇", 'argon') . ' <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></span>%link</div>' , '%title');
					}else{
						echo '<div class="post-navigation-item post-navigation-next"></div>';
					}
					echo '</div>';
				}
			}

			$relatedPosts = get_option('argon_related_post', 'disabled');
			if ($relatedPosts != "disabled"){
				global $post;
				$args = array(
					'post__not_in' => array($post -> ID),
					'showposts' => 10,
					'caller_get_posts' => 1
				);
				if (strpos($relatedPosts, 'category') !== false){
					$args['category__in'] = wp_get_post_categories($post -> ID);
				}
				if (strpos($relatedPosts, 'tag') !== false){
					$tagArray = wp_get_post_tags($post -> ID);
					$tags = array();
					foreach ($tagArray as $tag){
						array_push($tags, $tag -> term_id);
					}
					$args['tag__in'] = $tags;
				}
				query_posts($args);
				if (have_posts()) {
					echo '<div class="related-posts card shadow-sm">';
					while (have_posts()) {
						the_post();
						update_post_caches($posts);
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
					echo '</div>';
					wp_reset_query();
				}
			}

		endwhile;
		?>

<?php get_footer(); ?>
