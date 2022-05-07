<div class="shuoshuo-container">
	<div class="shuoshuo-meta shadow-sm">
		<?php do_action( 'argon_entry_meta' ); ?>
	</div>
	<article class="card shuoshuo-main <?php if( !is_singular( 'shuoshuo' ) ){echo 'shuoshuo-foldable';}?> bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php 
			do_action( 'argon_entry_title' ); 
			if( is_singular( 'shuoshuo' ) ){
				do_action( 'argon_entry_content' );
			}else{
				do_action( 'argon_entry_excerpt' );
				global $withcomments;
				$withcomments = true;
				comments_template( '/comments-shuoshuo-preview.php' );
	
			}
		?>

		<!-- <div class="shuoshuo-content">
			<?php the_content(); ?>
		</div> -->
		

		<?php
			get_template_part( 'template-parts/shuoshuo', 'operations' );
		?>

		<?php if( is_singular( 'shuoshuo' ) ): // 若是说说详情页，隐藏操作栏评论按钮 ?>
			<style>
			.shuoshuo-preview-add-comment-out-container-a{
				display: none;
			}
		</style>
		<?php endif ?>

	</article>
</div>