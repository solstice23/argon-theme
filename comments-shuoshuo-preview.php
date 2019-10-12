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