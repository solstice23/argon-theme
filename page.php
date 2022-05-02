<?php get_header(); ?>


<?php do_action( 'argon_page_info_card' ); ?>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			do_action( 'argon_show_sharebtn' );
			do_action( 'argon_show_comment' );

		endwhile;
		?>

<?php get_footer(); ?>
