<div class="shuoshuo-container">
	<div class="shuoshuo-meta shadow-sm">
		<?php do_action( 'argon_entry_meta' ); ?>
	</div>
	<article class="card shuoshuo-main shuoshuo-foldable bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php 
			do_action( 'argon_entry_title' ); 
			do_action( 'argon_entry_excerpt' );
		?>

		<!-- <div class="shuoshuo-content">
			<?php the_content(); ?>
		</div> -->
		
		<?php
		// 若非说说详情页，则显示评论预览
		if( !is_singular( 'shuoshuo' ) ):
			global $withcomments;
			$withcomments = true;
			comments_template( '/comments-shuoshuo-preview.php' );
			endif
		?>

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