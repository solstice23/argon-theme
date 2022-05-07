<?php 
/*
Template Name: 说说
* 该页面可以用 归档页 替代
*/
$paged = isset($_GET['current_page']) ? $_GET['current_page'] : 1;
query_posts("post_type=shuoshuo&post_status=publish&posts_per_page=30&paged=$paged");
?>

<?php get_header(); ?>

<div class="page-information-card-container">
	<div class="page-information-card card bg-gradient-secondary shadow-lg border-0" <?php if (isset($_GET['post_type'])){echo 'style="animation: none;"';}?>>
		<div class="card-body">
			<h3 class="text-black"><?php _e('说说', 'argon');?></h3>
			<?php if (the_archive_description() != ''){ ?>
				<p class="text-black mt-3">
					<?php the_archive_description(); ?>
				</p>
			<?php } ?>
			<p class="text-black mt-3 mb-0 opacity-8">
				<i class="fa fa-quote-left mr-1"></i>
				<?php echo wp_count_posts('shuoshuo','') -> publish; ?> <?php _e('条说说', 'argon');?>
			</p>
		</div>
	</div>
</div>

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
