<div class="shuoshuo-container">
	<div class="shuoshuo-meta shadow-sm">
		<span>
			<i class="fa fa-calendar-o" aria-hidden="true"></i> 
			<span class="shuoshuo-date-month"><?php echo get_the_time('n')?></span> 月 
			<span class="shuoshuo-date-date"><?php echo get_the_time('d')?></span> 日 , 
			<span class="shuoshuo-date-year"><?php echo get_the_time('Y')?></span>
			<div class="post-meta-devide">|</div>
			<i class="fa fa-clock-o" aria-hidden="true"></i> 
			<span class="shuoshuo-date-time"><?php echo get_the_time('G:i:s')?></span>
		</span>
		<?php if ( is_sticky() ) : ?>
			<div class="post-meta-devide">|</div>
			<div class="post-meta-detail post-meta-detail-words">
				<i class="fa fa-thumb-tack" aria-hidden="true"></i>
				置顶
			</div>
		<?php endif; ?>
	</div>
	<article class="card shuoshuo-main bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( get_the_title() != '' ) : ?>
			<a class="shuoshuo-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<?php endif; ?>

		<div class="shuoshuo-content">
			<?php the_content(); ?>
		</div>
		<?php
			global $withcomments;
			$withcomments = true;
			comments_template( '/comments-shuoshuo-preview.php' );
		?>
	</article>
</div>