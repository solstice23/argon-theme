<?php
/**
 * 展示页面信息框，主要用于归档页、搜索页的标题和条目数
 * Template part for displaying page info card
 *
 * do_action( 'argon_page_info_card', $args='' );
 */
?>
<div class="page-information-card-container">
	<div class="page-information-card card bg-gradient-secondary shadow-lg border-0" <?php if (isset($_GET['post_type'])){echo 'style="animation: none;"';}?>>
		<div class="card-body">
			<?php get_template_part( 'template-parts/archive/page-info-card-body', $args[0] ); ?>
		</div>
	</div>
</div>