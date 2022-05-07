<div class="shuoshuo-preview-container shuoshuo-foldable card bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php 
			do_action( 'argon_entry_title' ); 
			do_action( 'argon_entry_excerpt' );
		?>

		<!-- <div class="shuoshuo-content">
			<?php the_content(); ?>
		</div> -->
	
	<div class="shuoshuo-preview-meta">
		<?php do_action( 'argon_entry_meta' ); ?>
		<?php 
			$upvote_count = get_shuoshuo_upvotes(get_the_ID());
			$comment_count = get_post(get_the_ID()) -> comment_count;
		?>
		<?php if ( $upvote_count > 0 ) : ?>
			<div class="post-meta-devide">|</div>
			<div class="post-meta-detail post-meta-detail-words">
				<i class="fa fa-regular fa-thumbs-o-up" aria-hidden="true"></i>
				<?php echo $upvote_count; ?>
			</div>
		<?php endif; ?>
		<?php if ( $comment_count > 0 ) : ?>
			<div class="post-meta-devide">|</div>
			<div class="post-meta-detail post-meta-detail-words">
				<i class="fa fa-regular fa-comments" aria-hidden="true"></i>
				<?php echo $comment_count; ?>
			</div>
		<?php endif; ?>
	</div>		
	<a class="shuoshuo-preview-link btn btn-outline-primary btn-icon-only rounded-circle" type="button" href="<?php the_permalink(); ?>">
		<span class="btn-inner--icon"><i class="fa fa-arrow-right"></i></span>
	</a>
</div>