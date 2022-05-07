<?php get_header(); ?>

<div class="page-information-card-container">
	<div class="page-information-card card bg-gradient-secondary shadow-lg border-0" <?php if (isset($_GET['post_type'])){echo 'style="animation: none;"';}?>>
		<div class="card-body">
			<h3 class="text-black">	<?php the_archive_title();?> </h3>
			<?php if (the_archive_description() != ''){ ?>
				<p class="text-black mt-3">
					<?php the_archive_description(); ?>
				</p>
			<?php } ?>
			<p class="text-black mt-3 mb-0 opacity-8">
				<i class="fa fa-file mr-1"></i>
				<?php echo $wp_query -> found_posts; ?> <?php _e('篇文章', 'argon');?>
			</p>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main article-list" role="main">
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
		get_template_part( 'template-parts/preview/content', 'none-tag' );
	endif;
	?>

<?php get_footer(); ?>
