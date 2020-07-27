<article class="post post-full card bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header text-center<?php if (has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){echo " post-header-with-thumbnail";}?>">
		<?php
			if (has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){
				$thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "full")[0];
				echo "<img class='post-thumbnail' src='" . $thumbnail_url . "'></img>";
				echo "<div class='post-header-text-container'>";
			}
			if (has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') == 'true'){
				$thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "full")[0];
				echo "
				<style>
					body section.banner {
						background-image: url(" . $thumbnail_url . ") !important;
					}
				</style>";
			}
		?>
		<a class="post-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<div class="post-meta">
			<?php
				$metaList = explode('|', get_option('argon_article_meta', 'time|views|comments|categories'));
				if (is_sticky() && is_home() && ! is_paged()){
					array_unshift($metaList, "sticky");
				}
				if (post_password_required()){
					array_unshift($metaList, "needpassword");
				}
				if (is_meta_simple()){
					array_remove($metaList, "time");
					array_remove($metaList, "edittime");
					array_remove($metaList, "categories");
					array_remove($metaList, "author");
				}
				for ($i = 0; $i < count($metaList); $i++){
					if ($i > 0){
						echo ' <div class="post-meta-devide">|</div> ';
					}
					echo get_article_meta($metaList[$i]);
				}
			?>
			<?php if (!post_password_required() && get_option("argon_show_readingtime") != "false" && is_readingtime_meta_hidden() == False) { ?>
				</br>
				<div class="post-meta-detail post-meta-detail-words">
					<i class="fa fa-file-word-o" aria-hidden="true"></i>
					<?php
						echo get_article_words(get_the_content()) . " " . __("字", 'argon');
					?>
				</div>
				<div class="post-meta-devide">|</div>
				<div class="post-meta-detail post-meta-detail-words">
					<i class="fa fa-hourglass-end" aria-hidden="true"></i>
					<?php
						echo get_reading_time(get_article_words(get_the_content()));
					?>
				</div>
			<?php } ?>
		</div>
		<?php
			if (has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){
				echo "</div>";
			}
		?>
	</header>

	<div class="post-content" id="post_content">
		<?php if (post_password_required()){ ?>
			<div class="text-center container">
				<form action="/wp-login.php?action=postpass" class="post-password-form" method="post">
					<div class="post-password-form-text"><?php _e('这是一篇受密码保护的文章，您需要提供访问密码', 'argon');?></div>
					<div class="row">
						<div class="form-group col-lg-6 col-md-8 col-sm-10 col-xs-12 post-password-form-input">
							<div class="input-group input-group-alternative">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-key"></i></span>
								</div>
								<input name="post_password" class="form-control" placeholder="<?php _e('密码', 'argon');?>" type="password">
							</div>
							<?php
								$post_password_hint = get_post_meta(get_the_ID(), 'password_hint', true);
								if (!empty($post_password_hint)){
									echo '<div class="post-password-hint">' . $post_password_hint . '</div>';
								}
							?>
						</div>
					</div>
					<input class="btn btn-primary" type="submit" name="Submit" value="<?php _e('确认', 'argon');?>">
				</form>
			</div>
		<?php
			}else{
				echo argon_get_post_outdated_info();
				the_content();
			}
		?>
	</div>

	<?php if (get_option("argon_donate_qrcode_url") != '') { ?>
		<div class="post-donate">
			<button class="btn donate-btn btn-danger"><?php _e('赞赏', 'argon');?></button>
			<div class="donate-qrcode card shadow-sm bg-white">
				<img src="<?php echo get_option("argon_donate_qrcode_url"); ?>">
			</div>
		</div>
	<?php } ?>

	<?php if (has_tag()) { ?>
		<div class="post-tags">
			<i class="fa fa-tags" aria-hidden="true"></i>
			<?php
				$tags = get_the_tags();
				foreach ($tags as $tag) {
					echo "<a href='" . get_category_link($tag -> term_id) . "' target='_blank' class='tag badge badge-secondary post-meta-detail-tag'>" . $tag -> name . "</a>";
				}
			?>
		</div>
	<?php } ?>
</article>