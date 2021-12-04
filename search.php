<?php get_header(); ?>

<div class="page-information-card-container">
	<div class="page-information-card card bg-gradient-secondary shadow-lg border-0" <?php if (isset($_GET['post_type'])){echo 'style="animation: none;"';}?>>
		<div class="card-body">
			<h3 class="text-black mr-2 d-inline-block">	<?php echo get_search_query();?> </h3>
			<p class="lead text-black mt-0 d-inline-block">
				<?php _e('的搜索结果', 'argon');?>
			</p>
			<?php 
				$search_post_type = argon_get_search_post_type_array();
				if (get_option('argon_search_post_filter', 'post,page') != 'off'){ ?>
					<div class="search-filters">
						<div class="custom-control custom-checkbox search-filter-wrapper">
							<input class="custom-control-input search-filter" name="post" id="search_filter_post" type="checkbox" <?php echo in_array('post', $search_post_type) ? 'checked="true"' : ''; ?>>
							<label class="custom-control-label" for="search_filter_post">文章</label>
						</div>
						<div class="custom-control custom-checkbox search-filter-wrapper">
							<input class="custom-control-input search-filter" name="page" id="search_filter_page" type="checkbox" <?php echo in_array('page', $search_post_type) ? 'checked="true"' : ''; ?>>
							<label class="custom-control-label" for="search_filter_page">页面</label>
						</div>
						<?php if (strpos(get_option('argon_search_post_filter', 'post,page'), 'hide_shuoshuo') === false) { ?>
							<div class="custom-control custom-checkbox search-filter-wrapper">
								<input class="custom-control-input search-filter" name="shuoshuo" id="search_filter_shuoshuo" type="checkbox" <?php echo in_array('shuoshuo', $search_post_type) ? 'checked="true"' : ''; ?>>
								<label class="custom-control-label" for="search_filter_shuoshuo">说说</label>
							</div>
						<?php } ?>
					</div>
					<script>
						$(".search-filter").prop("checked", false);
						$("<?php echo "#search_filter_" . implode(",#search_filter_", $search_post_type); ?>").prop("checked", true);
					</script>
			<?php } ?>
			<p class="text-black mt-3 mb-0 opacity-8">
				<i class="fa fa-file-o mr-1"></i>
				<?php global $wp_query; echo $wp_query -> found_posts; ?> <?php _e('个结果', 'argon');?>
			</p>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main article-list search-result" role="main">
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
