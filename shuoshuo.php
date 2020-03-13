<?php 
/*
Template Name: 说说
*/
query_posts("post_type=shuoshuo&post_status=publish&posts_per_page=-1");
?>

<?php get_header(); ?>

<div class="page-infomation-card card bg-gradient-secondary shadow-lg border-0">
	<div class="card-body">
		<h3 class="text-black">说说</h3>
		<?php if (the_archive_description() != ''){ ?>
			<p class="text-black mt-3">
				<?php the_archive_description(); ?>
			</p>
		<?php } ?>
		<p class="text-black mt-3 mb-0 opacity-8">
			<i class="fa fa-quote-left mr-1"></i>
			<?php echo wp_count_posts('shuoshuo','') -> publish; ?> 条说说
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
				get_template_part( 'template-parts/content', 'shuoshuo' );
			endwhile;
		?>
		<?php
			echo get_argon_formatted_paginate_links_for_all_platforms();
		?>
		<?php
	else :
		get_template_part( 'template-parts/content', 'none-tag' );
	endif;
	?>

<?php get_footer(); ?>
