<div id="share_container">
	<div id="share" data-initialized="true">
		<a class="no-pjax icon-wechat">
			<button class="btn btn-icon btn-success">
				<span class="btn-inner--icon"><i class="fa fa-weixin"></i></span>
			</button>
		</a>
		<a target="_blink" class="no-pjax icon-douban">
			<button class="btn btn-icon btn-primary" style="background: #209261;border: none;">
				豆
			</button>
		</a>
		<a target="_blink" class="no-pjax icon-qq">
			<button class="btn btn-icon btn-primary" style="background: #2196f3;border: none;">
				<span class="btn-inner--icon"><i class="fa fa-qq"></i></span>
			</button>
		</a>
		<a target="_blink" class="no-pjax icon-qzone">
			<button class="btn btn-icon btn-primary" style="background: #ffc107;border: none;">
				<span class="btn-inner--icon"><i class="fa fa-star"></i></span>
			</button>
		</a>
		<a target="_blink" class="no-pjax icon-weibo">
			<button class="btn btn-icon btn-warning">
				<span class="btn-inner--icon"><i class="fa fa-weibo"></i></span>
			</button>
		</a>
		<a target="_blink" class="no-pjax icon-facebook">
			<button class="btn btn-icon btn-primary" style="background: #283593;border: none;">
				<span class="btn-inner--icon"><i class="fa fa-facebook"></i></span>
			</button>
		</a>
		<a target="_blink" class="no-pjax icon-twitter">
			<button class="btn btn-icon btn-primary" style="background: #03a9f4;border: none;">
				<span class="btn-inner--icon"><i class="fa fa-twitter"></i></span>
			</button>
		</a>
		<a target="_blink" class="no-pjax icon-telegram" href="https://telegram.me/share/url?url=<?php echo urlencode(
$_SERVER['HTTP_HOST']);?>&text=<?php echo urlencode(get_the_title());?>">
			<button class="btn btn-icon btn-primary" style="background: #42a5f5;border: none;">
				<span class="btn-inner--icon"><i class="fa fa-telegram"></i></span>
			</button>
		</a>
		<a target="_blink" class="no-pjax icon-copy-link" id="share_copy_link">
			<button class="btn btn-icon btn-default">
				<span class="btn-inner--icon"><i class="fa fa-link"></i></span>
			</button>
		</a>
	</div>
	<button id="share_show" class="btn btn-icon btn-primary">
		<span class="btn-inner--icon"><i class="fa fa-share"></i></span>
	</button>
</div>
<script type="text/javascript">
	socialShare("#share", {
	    title : '<?php the_title(); ?>',
	    description : '<?php echo wp_trim_words(get_the_content(), 50);?>',
	    wechatQrcodeTitle : "分享到微信",
	    wechatQrcodeHelper : '微信扫描二维码',
	    source : '<?php bloginfo('url'); ?>'
	});
	$("#share_show")[0].onclick = function(){
		$("#share_container").addClass("opened");
	};
	$("#share_copy_link")[0].onclick = function(){
		let input = document.createElement('input');
		document.body.appendChild(input);
		input.setAttribute("value", window.location.href);
		input.setAttribute("readonly", "readonly");
		input.setAttribute("style", "opacity: 0;mouse-events:none;");
		input.select();
		if (document.execCommand('copy')){
			iziToast.show({
				title: '链接已复制',
				message: "链接已复制到剪贴板",
				class: 'shadow',
				position: 'topRight',
				backgroundColor: '#2dce89',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-check',
				timeout: 5000
			});
		}else{
			iziToast.show({
				title: '复制失败',
				message: "请手动复制链接",
				class: 'shadow',
				position: 'topRight',
				backgroundColor: '#f5365c',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-close',
				timeout: 5000
			});
		}
		document.body.removeChild(input);
	};
</script>