<?php get_header(); ?>
<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
	<?php
		if (get_option("argon_home_show_shuoshuo") == "true"){
			global $wp_query;
			$args = array_merge($wp_query->query_vars, array('post_type' => array('post','shuoshuo')));
			query_posts($args);
		}
	?>
	<?php if ( have_posts() ) : ?>
		<?php
			while ( have_posts() ) :
				the_post();
				if (get_post_type() == 'post'){
					get_template_part( 'template-parts/content', get_post_format() );
				}else{
					get_template_part( 'template-parts/content-shuoshuo-preview', get_post_format() );
				}
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
