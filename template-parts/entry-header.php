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
		?>
		<a class="post-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
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
		<?php
			if (has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){
				echo "</div>";
			}
		?>
	</header>
