<?php get_header(); ?>

<div class="page-infomation-card card bg-gradient-secondary shadow-lg border-0">
	<div class="card-body">
		<h3 class="text-black mr-2 d-inline-block">	<?php echo get_search_query();?> </h3>
		<p class="lead text-black mt-0 d-inline-block">
			的搜索结果
		</p>
		<p class="text-black mt-3 mb-0 opacity-8">
			<i class="fa fa-file-o mr-1"></i>
			<?php global $wp_query; echo $wp_query -> found_posts; ?> 个结果
		</p>
	</div>
</div>
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
		get_template_part( 'template-parts/content', 'none-search' );
	endif;
	?>

<?php get_footer(); ?>
