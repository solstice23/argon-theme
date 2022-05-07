<?php get_header(); ?>

<div class="page-information-card-container"></div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main article-list article-list-home" role="main">
	<?php if ( have_posts() ) : ?>
		<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/preview/content-preview', get_post_type() );
			endwhile;
		?>
		<?php
			echo get_argon_formatted_paginate_links_for_all_platforms();
		?>
		<?php
	endif;
	?>

<?php get_footer(); ?>
