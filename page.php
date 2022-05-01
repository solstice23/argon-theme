<?php get_header(); ?>

<div class="page-information-card-container"></div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			do_action( 'argon_show_sharebtn' );
			do_action( 'argon_show_comment' );

		endwhile;
		?>

<?php get_footer(); ?>
