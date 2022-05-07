<article class="post card bg-white shadow-sm border-0 <?php if (get_option('argon_enable_into_article_animation') == 'true'){echo 'post-preview';} ?> post-preview-layout-1" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
		get_template_part( 'template-parts/entry/header', '1' );
		do_action( 'argon_entry_excerpt' );
		do_action( 'argon_entry_tags' );
	?>
</article>