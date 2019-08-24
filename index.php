<?php get_header(); ?>
<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
	<?php if ( have_posts() ) : ?>
		<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_format() );
			endwhile;
		?>
		<?php
			echo get_argon_formatted_paginate_links();
		?>
		<?php
	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;
	?>

<?php get_footer(); ?>
