<?php $article_cover = argon_post_cover_image('side-card'); ?>
<article class="post card bg-white shadow-sm border-0 <?php if (get_option('argon_enable_into_article_animation') == 'true'){echo 'post-preview';} ?> post-preview-layout-2" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header <?php if ($article_cover){echo " post-header-with-thumbnail";}?>">
		<?php
			if ($article_cover){
				echo $article_cover;
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
</article>