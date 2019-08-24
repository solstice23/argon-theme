<article class="post card bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header text-center">
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
			<div class="post-meta-devide">|</div>
			<div class="post-meta-detail post-meta-detail-catagories">
				<i class="fa fa-bookmark-o" aria-hidden="true"></i>
				<?php
					$categories = get_the_category();
					foreach ($categories as $index => $category){
						echo "<a href='" . get_category_link($category -> term_id) . "' target='_blink' class='post-meta-detail-catagory-link'>" . $category -> cat_name . "</a>";
						if ($index != count($categories) - 1){
							echo "<span class='post-meta-detail-catagory-space'>,</span>";
						}
					}
				?>
			</div>
			<?php if (!post_password_required() && get_option("argon_show_readingtime") != "false" && is_readingtime_meta_hidden() == False) { ?>
				</br>
				<div class="post-meta-detail post-meta-detail-words">
					<i class="fa fa-file-word-o" aria-hidden="true"></i>
					<?php
						echo get_article_words(get_the_content()) . " 字";
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
	</header>

	<div class="post-content">
		<?php
			$preview = wp_trim_words(get_the_content(), 175);
			if (post_password_required()){
				$preview = "这篇文章受密码保护，输入密码才能阅读";
			}
			if ($preview == ""){
				$preview = "这篇文章没有摘要";
			}
			echo $preview;
		?>
	</div>

	<?php if (has_tag()) { ?>
		<div class="post-tags">
			<i class="fa fa-tags" aria-hidden="true"></i>
			<?php
				$tags = get_the_tags();
				foreach ($tags as $tag) {
					echo "<a href='" . get_category_link($tag -> term_id) . "' target='_blink' class='tag badge badge-secondary post-meta-detail-tag'>" . $tag -> name . "</a>";
				}
			?>
		</div>
	<?php } ?>
</article>