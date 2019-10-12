<div class="shuoshuo-operations">
	<a href="<?php the_permalink(); ?>#post_comment" class="shuoshuo-preview-add-comment-out-container-a" <?php if (!comments_open()) {?>style="pointer-events: none;"<?php } ?>>
		<button class="shuoshuo-preview-add-comment btn btn-icon btn-outline-primary btn-sm" type="button"<?php if (!comments_open()) {?> disabled<?php } ?>>
			<span class="btn-inner--icon"><i class="fa fa-comments-o"></i></span>
			<span class="btn-inner--text" style="margin-left: 2px;">评论<?php if (!comments_open()) {?>已关闭<?php } ?></span>
		</button>
	</a>
	<?php $upvoted = isset($_COOKIE['argon_shuoshuo_' . get_the_ID() . '_upvoted']);?>
	<button class="shuoshuo-upvote btn btn-icon btn-outline-primary btn-sm<?php if ($upvoted) {?> upvoted<?php } ?>" type="button" data-id="<?php the_ID(); ?>">
		<span class="btn-inner--icon"><i class="fa fa-thumbs<?php if (!$upvoted) {?>-o<?php } ?>-up"></i></span>
		<span class="btn-inner--text" style="margin-left: 2px;">
			<span class="shuoshuo-upvote-num"><?php echo get_shuoshuo_upvotes(get_the_ID());?></span>
			<i class="fa fa-spinner fa-spin" style="margin-left: 0;"></i>
		</span>
	</button>
</div>