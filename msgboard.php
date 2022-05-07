<?php 
/*
Template Name: 留言板 (请打开页面的评论功能)
*/
?>

<?php get_header(); ?>

<div class="page-information-card-container"></div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();
			do_action( 'argon_single_content' );
			do_action( 'argon_show_sharebtn' );
			do_action( 'argon_show_comment' );

		endwhile;
		?>
<style>
#main article {
	display: none !important;
}
#share_container {
	display: none;
}
.comments-area .comments-title {
	font-size: 0px;
}
.comments-area .comments-title:after {
	content: '<?php _e("留言板", "argon"); ?>';
	font-size: 20px;
}
.comments-area .comments-title i {
	font-size: 20px;
	margin-right: 10px;
}
.post-comment-title {
	font-size: 0px;
}
.post-comment-title:after {
	content: '<?php _e("发送留言", "argon"); ?>';
	font-size: 20px;
}
.post-comment-title i {
	font-size: 20px;
	margin-right: 10px;
}
</style>
<?php get_footer(); ?>
