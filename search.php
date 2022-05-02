<?php get_header(); ?>

<?php do_action( 'argon_page_info_card' ); ?>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main article-list search-result" role="main">
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
	else :
		get_template_part( 'template-parts/preview/content', 'none-search' );
	endif;
	?>

<?php get_footer(); ?>
