<?php
/**
 * 展示页面信息框，主要用于归档页、搜索页的标题和条目数
 * Template part for displaying page info card
 *
 */
?>
<div class="page-information-card-container">
	<?php if( !(is_singular( get_post_type() ) OR is_home()) ): ?>
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
					get_template_part( 'template-parts/archive/page-info-card-body', $template_name ); ?>
			</div>
		</div>
	<? endif ?>
</div>