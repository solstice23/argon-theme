<?php 
/*
Template Name: 说说
* 该页面可以用 归档页 替代
*/
$paged = isset($_GET['current_page']) ? $_GET['current_page'] : 1;
query_posts("post_type=shuoshuo&post_status=publish&posts_per_page=30&paged=$paged");
?>

<?php get_header(); ?>

<?php do_action( 'argon_page_info_card', 'shuoshuo' ); ?>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
	<?php if ( have_posts() ) : ?>
		<?php
			while ( have_posts() ) :
				the_post();
				do_action( 'argon_single_content', 'shuoshuo' );
			endwhile;
		?>
		<?php
			echo get_argon_formatted_paginate_links_for_all_platforms(array(
				'format' => '?current_page=%#%',
			));
		?>
		<?php
	else :
		get_template_part( 'template-parts/preview/content', 'none-tag' );
	endif;
	?>

<?php get_footer(); ?>
