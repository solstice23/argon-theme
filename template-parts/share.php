<?php
/**
 * 展示文章的分享按钮
 * Template part for displaying share btn
 *
 */
?>
<div id="share_container">
	<div id="share" data-initialized="true">
			<?php if (get_option('argon_show_sharebtn') != 'abroad') { ?>
			<a target="_blank" class="no-pjax icon-douban" tooltip="<?php _e('分享到豆瓣', 'argon'); ?>">
				<button class="btn btn-icon btn-primary" style="background: #209261;border: none;">
					<span aria-hidden="true">豆</span>
				</button>
			</a>
			<a target="_blank" class="no-pjax icon-qq" tooltip="<?php _e('分享到 QQ', 'argon'); ?>">
				<button class="btn btn-icon btn-primary" style="background: #2196f3;border: none;">
					<span class="btn-inner--icon"><i class="fa fa-brands fa-qq"></i></span>
				</button>
			</a>
			<a target="_blank" class="no-pjax icon-qzone" tooltip="<?php _e('分享到 QQ 空间', 'argon'); ?>">
				<button class="btn btn-icon btn-primary" style="background: #ffc107;border: none;">
					<span class="btn-inner--icon"><i class="fa fa-star"></i></span>
				</button>
			</a>
			<a target="_blank" class="no-pjax icon-weibo" tooltip="<?php _e('分享到微博', 'argon'); ?>">
				<button class="btn btn-icon btn-warning">
					<span class="btn-inner--icon"><i class="fa fa-brands fa-weibo"></i></span>
				</button>
			</a>
		<?php } if (get_option('argon_show_sharebtn') != 'domestic') { ?>
		<a target="_blank" class="no-pjax icon-facebook" tooltip="<?php _e('分享到 Facebook', 'argon'); ?>">
			<button class="btn btn-icon btn-primary" style="background: #283593;border: none;">
				<span class="btn-inner--icon"><i class="fa fa-brands fa-facebook"></i></span>
			</button>
		</a>
		<a target="_blank" class="no-pjax icon-twitter" tooltip="<?php _e('分享到 Twitter', 'argon'); ?>">
			<button class="btn btn-icon btn-primary" style="background: #03a9f4;border: none;">
				<span class="btn-inner--icon"><i class="fa fa-brands fa-twitter"></i></span>
			</button>
		</a>
		<a target="_blank" class="no-pjax icon-telegram" tooltip="<?php _e('分享到 Telegram', 'argon'); ?>">
			<button class="btn btn-icon btn-primary" style="background: #42a5f5;border: none;">
				<span class="btn-inner--icon"><i class="fa fa-brands fa-telegram"></i></span>
			</button>
		</a>
		<?php } ?>
		<a target="_blank" class="no-pjax icon-qrcode" id="share_qrcode" tooltip="<?php _e('二维码', 'argon'); ?>">
			<button class="btn btn-icon btn-default">
				<span class="btn-inner--icon"><i class="fa fa-qrcode"></i></span>
			</button>
		</a>
		<a target="_blank" class="no-pjax icon-copy-link" id="share_copy_link" tooltip="<?php _e('复制链接', 'argon'); ?>">
			<button class="btn btn-icon btn-default">
				<span class="btn-inner--icon"><i class="fa fa-link"></i></span>
			</button>
		</a>
	</div>
	<button id="share_show" class="btn btn-icon btn-primary" tooltip="<?php _e('分享', 'argon'); ?>">
		<span class="btn-inner--icon"><i class="fa fa-share"></i></span>
	</button>
</div>
<script type="text/javascript">
	var shareInfo = {
	    title: '<?php echo addslashes(html_entity_decode(get_the_title())); ?>',
	    description: '<?php echo addslashes(html_entity_decode(wp_trim_words(html_entity_decode(get_the_content()), 50)));?>',
	    url: '<?php global $post; echo get_permalink($post -> ID); ?>',
		origin: '<?php echo get_bloginfo('name'); ?>',
		source: '<?php echo get_site_url();  ?>',
	};
</script>