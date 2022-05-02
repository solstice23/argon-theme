<article class="post card bg-white shadow-sm border-0 <?php if (get_option('argon_enable_into_article_animation') == 'true'){echo 'post-preview';} ?> post-preview-layout-3" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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

	<header class="post-header">
		<?php 
			do_action( 'argon_entry_title' );
			do_action( 'argon_entry_meta' );
		?>	
	</header>

	<?php
		do_action( 'argon_entry_content_preview' );
		do_action( 'argon_entry_tags' );
	?>
</article>