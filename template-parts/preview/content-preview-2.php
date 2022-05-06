	<header class="post-header <?php if (argon_has_post_thumbnail()){echo " post-header-with-thumbnail";}?>">
		<?php
			if (argon_has_post_thumbnail()){
				$thumbnail_url = argon_get_post_thumbnail();
				if (get_option('argon_enable_lazyload') != 'false'){
					echo "<img class='post-thumbnail lazyload' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABBJREFUeNpi+P//PwNAgAEACPwC/tuiTRYAAAAASUVORK5CYII=' data-original='" . $thumbnail_url . "' alt='thumbnail' style='opacity: 0;'></img>";
				}else{
					echo "<img class='post-thumbnail' src='" . $thumbnail_url . "'></img>";
				}				
			}
		?>
	</header>

	<div class="post-content-container">
	<?php 
		do_action( 'argon_entry_title' );
		do_action( 'argon_entry_excerpt' );
		do_action( 'argon_entry_meta' );
		do_action( 'argon_entry_tags' );
	?>	
	</div>
