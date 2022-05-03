<?php 
/*
Template Name: 归档时间轴
*/
?>

<?php get_header(); ?>

<div class="page-information-card-container"></div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
			while ( have_posts() ) :
				the_post();
				
				do_action( 'argon_single_content', 'timeline' );

				do_action( 'argon_show_sharebtn' );
				do_action( 'argon_show_comment' );

			endwhile;
		?>

<?php get_footer(); ?>
