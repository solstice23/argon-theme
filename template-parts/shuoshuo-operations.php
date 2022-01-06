<div class="shuoshuo-operations">
	<a href="<?php the_permalink(); ?>#post_comment" class="shuoshuo-preview-add-comment-out-container-a" <?php if (!comments_open()) {?>style="pointer-events: none;"<?php } ?>>
		<button class="shuoshuo-preview-add-comment btn btn-icon btn-outline-primary btn-sm" type="button"<?php if (!comments_open()) {?> disabled<?php } ?>>
			<span class="btn-inner--icon"><i class="fa fa-comments-o"></i></span>
			<?php 
				$comment_count = get_post(get_the_ID()) -> comment_count;
				if ($comment_count > 0){ ?>
					<span class="btn-inner--text" style="margin-left: 2px;"><?php echo $comment_count;?></span>
			<?php } else { ?>
					<span class="btn-inner--text" style="margin-left: 2px;"><?php _e('评论', 'argon');?><?php if (!comments_open()) {?><?php _e('已关闭', 'argon');?><?php } ?></span>
			<?php } ?>
		</button>
	</a>

	<?php 
		$upvotedList = isset($_COOKIE['argon_shuoshuo_upvoted']) ? $_COOKIE['argon_shuoshuo_upvoted'] : '';
		$upvoted = in_array(get_the_ID(), explode(',', $upvotedList));
	?>
	<button class="shuoshuo-upvote btn btn-icon btn-outline-primary btn-sm<?php if ($upvoted) {?> upvoted<?php } ?>" type="button" data-id="<?php the_ID(); ?>">
		<span class="btn-inner--icon"><i class="fa fa-thumbs<?php if (!$upvoted) {?>-o<?php } ?>-up"></i></span>
		<span class="btn-inner--text" style="margin-left: 2px;">
			<span class="shuoshuo-upvote-num"><?php echo get_shuoshuo_upvotes(get_the_ID());?></span>
			<i class="fa fa-spinner fa-spin" style="margin-left: 0;"></i>
		</span>
	</button>
</div>