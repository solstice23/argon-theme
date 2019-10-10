<?php
	if ( post_password_required() ) {
		return;
	}
?>

<?php if ( have_comments() ){?>
	<div class="shuoshuo-comments">
		<?php /*the_comments_navigation();*/ ?>
		<ol class="comment-list">
			<?php
				wp_list_comments(
					array(
						'type'      => 'comment',
						'callback'  => 'argon_comment_shuoshuo_preview_format'
					)
				);
			?>
		</ol>
		<?php /*the_comments_navigation();*/ ?>
	</div>
<?php }?>

<?php if (!comments_open()) {?>
	<button class="shuoshuo-preview-add-comment btn btn-icon btn-outline-primary btn-sm" type="button" disabled>
		<span class="btn-inner--icon"><i class="fa fa-comments-o"></i></span>
		<span class="btn-inner--text" style="margin-left: 2px;">评论已关闭</span>
	</button>
<?php } else { ?>
	<a href="<?php the_permalink(); ?>#post_comment" class="shuoshuo-preview-add-comment-out-container-a">
		<button class="shuoshuo-preview-add-comment btn btn-icon btn-outline-primary btn-sm" type="button">
			<span class="btn-inner--icon"><i class="fa fa-comments-o"></i></span>
			<span class="btn-inner--text" style="margin-left: 2px;">评论</span>
		</button>
	</a>
<?php } ?>