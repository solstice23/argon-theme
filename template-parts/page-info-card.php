<?php
/**
 * 展示页面信息框，主要用于归档页、搜索页的标题和条目数
 * Template part for displaying page info card
 *
 * do_action( 'argon_page_info_card', $args='' );
 * $args 被转为 array 后传入
 * 当 $args 为空时，首页和当前当前内容类型的详情页 不显示信息框，否则显示
 * 当 $args 不为空时，无论怎样都显示信息框
 */
?>
<div class="page-information-card-container">
	<?php if( !( is_singular( get_post_type() ) OR is_home() ) OR !empty( $args ) ): ?>
		<div class="page-information-card card bg-gradient-secondary shadow-lg border-0" <?php if (isset($_GET['post_type'])){echo 'style="animation: none;"';}?>>
			<div class="card-body">
				<?php 
					if( empty( $args ) ){
						$template_name = get_post_type();
					}else{
						$template_name = $args[0];
					}
					if( is_search() ){
						$template_name = 'search';
					}
					echo $template_name;
					get_template_part( 'template-parts/archive/page-info-card-body', $template_name ); ?>
			</div>
		</div>
	<? endif ?>
</div>