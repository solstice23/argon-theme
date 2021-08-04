<?php get_header(); ?>

<div class="page-information-card-container">
	<div class="page-information-card card bg-gradient-secondary shadow-lg border-0">
		<div class="card-body">
			<h3 class="text-black mr-2 d-inline-block">	<?php echo get_search_query();?> </h3>
			<p class="lead text-black mt-0 d-inline-block">
				<?php _e('的搜索结果', 'argon');?>
			</p>
			<p class="text-black mt-3 mb-0 opacity-8">
				<i class="fa fa-file-o mr-1"></i>
				<?php global $wp_query; echo $wp_query -> found_posts; ?> <?php _e('个结果', 'argon');?>
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
				if (get_post_type() == 'shuoshuo'){
					get_template_part( 'template-parts/content-shuoshuo-preview' );
				}else{
					get_template_part( 'template-parts/content-preview', get_option('argon_article_list_layout', '1'));
				}
			endwhile;
		?>
		<?php
			echo get_argon_formatted_paginate_links_for_all_platforms();
		?>
		<?php
	else :
		get_template_part( 'template-parts/content', 'none-search' );
	endif;
	?>

<?php get_footer(); ?>
