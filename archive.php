<?php get_header(); ?>

<div class="page-infomation-card card bg-gradient-secondary shadow-lg border-0">
	<div class="card-body">
		<h3 class="text-black">	<?php the_archive_title();?> </h3>
		<?php if (the_archive_description() != ''){ ?>
			<p class="text-black mt-3">
				<?php the_archive_description(); ?>
			</p>
		<?php } ?>
		<p class="text-black mt-3 mb-0 opacity-8">
			<i class="fa fa-file-o mr-1"></i>
			<?php echo $wp_query -> queried_object -> count; ?> 篇文章
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
		get_template_part( 'template-parts/content', 'none-tag' );
	endif;
	?>

<?php get_footer(); ?>
