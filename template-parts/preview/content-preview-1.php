<article class="post card bg-white shadow-sm border-0 <?php if (get_option('argon_enable_into_article_animation') == 'true'){echo 'post-preview';} ?>" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header text-center<?php if (argon_has_post_thumbnail()){echo " post-header-with-thumbnail";}?>">
		<?php
			if (argon_has_post_thumbnail()){
				$thumbnail_url = argon_get_post_thumbnail();
				if (get_option('argon_enable_lazyload') != 'false'){
					echo "<img class='post-thumbnail lazyload' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABBJREFUeNpi+P//PwNAgAEACPwC/tuiTRYAAAAASUVORK5CYII=' data-original='" . $thumbnail_url . "' alt='thumbnail' style='opacity: 0;'></img>";
				}else{
					echo "<img class='post-thumbnail' src='" . $thumbnail_url . "'></img>";
				}				
				echo "<div class='post-header-text-container'>";
			}

			do_action( 'argon_entry_title' );
			do_action( 'argon_entry_meta' );

			if (has_post_thumbnail()){
				echo "</div>";
			}
		?>
	</header>
	<?php 
		do_action( 'argon_entry_excerpt' );
		do_action( 'argon_entry_tags' );
	?>	
</article>