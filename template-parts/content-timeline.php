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
			<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
				<div class="post-meta-detail post-meta-detail-words">
					<i class="fa fa-thumb-tack" aria-hidden="true"></i>
					置顶
				</div>
				<div class="post-meta-devide">|</div>
			<?php endif; ?>
			<?php if (post_password_required()) { ?>
				<div class="post-meta-detail post-meta-detail-needpassword">
					<i class="fa fa-lock" aria-hidden="true"></i>
					需要密码
				</div>
				<div class="post-meta-devide">|</div>
			<?php } ?>
			<div class="post-meta-detail post-meta-detail-time">
				<i class="fa fa-clock-o" aria-hidden="true"></i>
				<time title="<?php echo '发布于 ' . get_the_time('Y-n-d G:i:s') . ' | 修改于 ' . get_the_modified_time('Y-n-d G:i:s'); ?>">
					<?php the_time('Y-n-d G:i'); ?>
				</time>
			</div>
			<div class="post-meta-devide">|</div>
			<div class="post-meta-detail post-meta-detail-views">
				<i class="fa fa-eye" aria-hidden="true"></i>
				<?php get_post_views(get_the_ID()); ?>
			</div>
			<div class="post-meta-devide">|</div>
			<div class="post-meta-detail post-meta-detail-comments">
				<i class="fa fa-comments-o" aria-hidden="true"></i>
				<?php echo get_post(get_the_ID())->comment_count; ?>
			</div>
			<?php if (count(get_the_category()) > 0){ ?>
				<div class="post-meta-devide">|</div>
				<div class="post-meta-detail post-meta-detail-catagories">
					<i class="fa fa-bookmark-o" aria-hidden="true"></i>
					<?php
						$categories = get_the_category();
						foreach ($categories as $index => $category){
							echo "<a href='" . get_category_link($category -> term_id) . "' target='_blank' class='post-meta-detail-catagory-link'>" . $category -> cat_name . "</a>";
							if ($index != count($categories) - 1){
								echo "<span class='post-meta-detail-catagory-space'>,</span>";
							}
						}
					?>
				</div>
			<?php } ?>
		</div>
		<?php
			if (has_post_thumbnail() && get_option('argon_show_thumbnail_in_banner_in_content_page') != 'true'){
				echo "</div>";
			}
		?>
		<?php if (is_meta_simple()){ ?>
			<style>
				.post-meta .post-meta-detail-time ,
				.post-meta .post-meta-detail-time + .post-meta-devide ,
				.post-meta .post-meta-detail-comments + .post-meta-devide ,
				.post-meta .post-meta-detail-catagories{
					display: none;
				}
			</style>
		<?php } ?>
	</header>

	<div class="post-content" id="post_content">
		<?php if (post_password_required()){ ?>
			<div class="text-center container">
				<form action="/wp-login.php?action=postpass" class="post-password-form" method="post">
					<div class="post-password-form-text">这是一篇受密码保护的文章，您需要提供访问密码</div>
					<div class="row">
						<div class="form-group col-lg-6 col-md-8 col-sm-10 col-xs-12 post-password-form-input">
							<div class="input-group input-group-alternative">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-key"></i></span>
								</div>
								<input name="post_password" class="form-control" placeholder="密码" type="password">
							</div>
						</div>
					</div>
					<input class="btn btn-primary" type="submit" name="Submit" value="确认">
				</form>
			</div>
		<?php
			}else{
				$POST = $GLOBALS['post'];
				echo "<div class='argon-timeline'>";
				$last_year = 0;
				$posts = get_posts('numberposts=-1&orderby=post_date&order=DESC');
				foreach ($posts as $post){
					setup_postdata($post);
					$year = mysql2date('Y', $post -> post_date);
					if ($year != $last_year){
						echo "<div class='argon-timeline-node'>
								<div class='argon-timeline-time archive-timeline-year'>" . $year . "</div>
								<div class='argon-timeline-card card bg-gradient-secondary archive-timeline-title'></div>
							</div>";
							$last_year = $year;
					} ?>
					<div class='argon-timeline-node'>
						<div class='argon-timeline-time'><?php echo mysql2date('m-d', $post -> post_date); ?></div>
						<div class='argon-timeline-card card bg-gradient-secondary archive-timeline-title'>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</div>
					</div>
					<?php
				}
				echo '</div>';
				$GLOBALS['post'] = $POST;
			}
		?>
	</div>

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