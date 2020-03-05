<?php
	if ( post_password_required() ) {
		return;
	}
?>

<div id="comments" class="comments-area card shadow-sm<?php if (get_option('argon_comment_avatar_vcenter') == 'true'){echo " comment-avatar-vertical-center";} ?>">
	<div class="card-body">
		<?php if ( have_comments() ){?>
			<h2 class="comments-title">
				<i class="fa fa-comments"></i>
				评论
			</h2>
			<?php the_comments_navigation(); ?>
			<ol class="comment-list">
				<?php
					wp_list_comments(
						array(
							'type'      => 'comment',
							'callback'  => 'argon_comment_format'
						)
					);
				?>
			</ol>
			<?php the_comments_navigation(); ?>
		<?php } else {?>
			<span>暂无评论</span>
		<?php } ?>
	</div>
</div>

<?php if (!comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' )) {?>
	<div id="post_comment" class="card shadow-sm">
		<div class="card-body">
			<span>本文评论已关闭</span>
		</div>
	</div>
<?php } else { ?>

<?php $name_and_email_required = get_option('require_name_email')?>

<div id="post_comment" class="card shadow-sm <?php if (is_user_logged_in()) {echo("logged");}?><?php if (!$name_and_email_required) {echo(" no-need-name-email");}?><?php if (get_option('argon_comment_need_captcha') == 'false') {echo(" no-need-captcha");}?>">
	<div class="card-body">
		<h2 class="post-comment-title">
			<i class="fa fa-commenting"></i>
			发送评论
		</h2>
		<div id="post_comment_reply_info" class="post-comment-reply" style="display: none;">
			<span>正在回复 <b><span id="post_comment_reply_name"></span></b> 的评论 :</span>
			<div id="post_comment_reply_preview" class="post-comment-reply-preview"></div>
			<button id="post_comment_reply_cencel" class="btn btn-outline-primary btn-sm">取消回复</button>
		</div>
		<form>
			<div class="row">
				<div class="col-md-12">
					<textarea id="post_comment_content" class="form-control form-control-alternative" placeholder="评论内容" name="comment" style="height: 80px;"></textarea>
				</div>
				<div class="col-md-12" style="height: 0;overflow: hidden;">
					<pre id="post_comment_content_hidden" class=""></pre>
				</div>
			</div>
			<?php 
				$col1_class = "col-md-4";
				$col2_class = "col-md-5";
				$col3_class = "col-md-3";
				if ((get_option('argon_hide_name_email_site_input') == 'true') && ($name_and_email_required != true)){
					if (get_option('argon_comment_need_captcha') == 'false'){
						$col1_class = "d-none";
						$col2_class = "d-none";
						$col3_class = "d-none";
					}else{
						$col1_class = "d-none";
						$col2_class = "d-none";
						$col3_class = "col-md-12";
					}
				}else{
					if (get_option('argon_comment_need_captcha') == 'false'){
						$col1_class = "col-md-6";
						$col2_class = "col-md-6";
						$col3_class = "d-none";
					}else{
						$col1_class = "col-md-4";
						$col2_class = "col-md-5";
						$col3_class = "col-md-3";
					}
				}
			?>
			<div class="row" style="margin-bottom: -10px;">
				<div class="<?php echo $col1_class;?>">
					<div class="form-group">
							
						<div class="input-group input-group-alternative mb-4">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-user-circle"></i></span>
							</div>
							<input id="post_comment_name" class="form-control" placeholder="昵称" type="text" name="author" <?php if (is_user_logged_in()) {echo('value="' . wp_get_current_user() -> user_login . '"');}?>>
						</div>
					</div>
				</div>
				<div class="<?php echo $col2_class;?>">
					<div class="form-group">
						<div class="input-group input-group-alternative mb-4">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-envelope"></i></span>
							</div>
							<input id="post_comment_email" class="form-control" placeholder="邮箱" type="email" name="email" <?php if (is_user_logged_in()) {echo('value="' . wp_get_current_user() -> user_email . '"');}?>>
						</div>
					</div>
				</div>
				<?php $commentCaptchaSeed = get_comment_captcha_seed();?>
				<div class="<?php echo $col3_class;?>">
					<div class="form-group">
						<div class="input-group input-group-alternative mb-4 post-comment-captcha-container">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-key"></i></span>
							</div>
							<input id="post_comment_captcha" class="form-control" placeholder="验证码" type="text" <?php if (current_user_can('level_7')) {echo('value="' . get_comment_captcha_answer($commentCaptchaSeed) . '" disabled');}?>>
							<style>
								.post-comment-captcha-container:before{
									content: "<?php echo get_comment_captcha($commentCaptchaSeed);?>";
								}
							</style>
						</div>
					</div>
				</div>
			</div>
			<div class="row" id="post_comment_extra_input" style="display: none";>
				<div class="col-md-12" style="margin-bottom: -10px;">
					<div class="form-group">
						<div class="input-group input-group-alternative mb-4 post-comment-link-container">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-link"></i></span>
							</div>
							<input id="post_comment_link" class="form-control" placeholder="网站" type="text" name="url">
						</div>
					</div>
				</div>
			</div>
			<div class="row <?php if (get_option('argon_hide_name_email_site_input') == 'true') {echo 'd-none';}?>" style="margin-top: 10px; <?php if (is_user_logged_in()) {echo('display: none');}?>">
				<div class="col-md-12">
					<button id="post_comment_toggle_extra_input" type="button" class="btn btn-icon btn-outline-primary btn-sm">
                <span class="btn-inner--icon"><i class="fa fa-angle-down"></i></span>
              </button>
				</div></div>
			<div class="row" style="margin-top: 5px; margin-bottom: 10px;">
				<div class="col-md-12">
					<?php if (get_option("argon_comment_allow_markdown") != "false") {?>
						<div class="custom-control custom-checkbox comment-post-use-markdown">
							<input class="custom-control-input" id="comment_post_use_markdown" type="checkbox" checked="true">
							<label class="custom-control-label" for="comment_post_use_markdown" style="line-height: 25px;">Markdown</label>
						</div>
					<?php } ?>
					<button id="post_comment_send" class="btn btn-icon btn-primary pull-right" type="button">
						<span class="btn-inner--icon"><i class="fa fa-send"></i></span>
						<span class="btn-inner--text">发送</span>
					</button>
				</div>
			</div>
			<input id="post_comment_captcha_seed" value="<?php echo $commentCaptchaSeed;?>" style="display: none;"></input>
			<input id="post_comment_post_id" value="<?php echo get_the_ID();?>" style="display: none;"></input>
		</form>
	</div>
</div>
<?php } ?>