<?php get_header(); ?>

<div class="page-information-card-container"></div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();

			do_action( 'argon_single_content' );

			echo get_argon_formatted_paginate_links_for_all_platforms(array(), 'post');

			do_action( 'argon_show_sharebtn' );
			do_action( 'argon_show_comment' );
			do_action( 'argon_post_navigation' );
			do_action( 'argon_related_post' );

		endwhile;
		?>

<?php get_footer(); ?>
