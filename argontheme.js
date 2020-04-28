if (typeof(wp_path) == "undefined"){
	var wp_path = "/";
}
/*Cookies 操作*/
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
} 
function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}

/*根据滚动高度改变顶栏透明度*/
!function(){
	let toolbar = document.getElementById("navbar-main");
	let $bannerContainer = $("#banner_container");
	let $content = $("#content");

	let startTransitionHeight;
	let endTransitionHeight;

	startTransitionHeight = $bannerContainer.offset().top - 75;
	endTransitionHeight = $content.offset().top - 75;

	$(window).resize(function(){
		startTransitionHeight = $bannerContainer.offset().top - 75;
		endTransitionHeight = $content.offset().top - 75;
	});

	function changeToolbarTransparency(){
		let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if (scrollTop < startTransitionHeight){
			toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), 0)', 'important');
			toolbar.style.setProperty('box-shadow', 'none');
			toolbar.classList.add("navbar-ontop");
			return;
		}
		if (scrollTop > endTransitionHeight){
			toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), 0.85)', 'important');
			toolbar.style.setProperty('box-shadow', '');
			toolbar.classList.remove("navbar-ontop");
			return;
		}
		let transparency = (scrollTop - startTransitionHeight) / (endTransitionHeight - startTransitionHeight) * 0.85;
		toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), ' + transparency, 'important');
		toolbar.style.setProperty('box-shadow', '');
		toolbar.classList.remove("navbar-ontop");
	}
	changeToolbarTransparency();
	document.addEventListener("scroll", changeToolbarTransparency, {passive: true});
}();

/*顶栏搜索*/
$(document).on("click" , "#navbar_search_input_container" , function(){
	$(this).addClass("open");
	$("#navbar_search_input").focus();
});
$(document).on("blur" , "#navbar_search_input_container" , function(){
	$(this).removeClass("open");
});
$(document).on("keydown" , "#navbar_search_input_container #navbar_search_input" , function(e){
	if (e.keyCode != 13){
		return;
	}
	let word = $(this).val();
	if (word == ""){
		$("#navbar_search_input_container").blur();
		return;
	}
	let scrolltop = $(document).scrollTop();
	pjaxLoadUrl(wp_path + "?s=" + encodeURI(word) , true , 0 , scrolltop);
});
/*侧栏搜索*/
$(document).on("click" , "#leftbar_search_container" , function(){
	$(".leftbar-search-button").addClass("open");
	$("#leftbar_search_input").removeAttr("readonly").focus();
	$("#leftbar_search_input").focus();
	$("#leftbar_search_input").select();
	return false;
});
$(document).on("blur" , "#leftbar_search_container" , function(){
	$(".leftbar-search-button").removeClass("open");
	$("#leftbar_search_input").attr("readonly", "readonly");
});
$(document).on("keydown" , "#leftbar_search_input" , function(e){
	if (e.keyCode != 13){
		return;
	}
	let word = $(this).val();
	if (word == ""){
		$("#leftbar_search_container").blur();
		return;
	}
	$("html").removeClass("leftbar-opened");
	let scrolltop = $(document).scrollTop();
	pjaxLoadUrl(wp_path + "?s=" + encodeURI(word) , true , 0 , scrolltop);
});

/*左侧栏随页面滚动浮动*/
!function(){
	let $leftbarPart1 = $('#leftbar_part1');
	let $leftbarPart2 = $('#leftbar_part2');
	let leftbarPart1 = document.getElementById('leftbar_part1');
	let leftbarPart2 = document.getElementById('leftbar_part2');
	
	let part1OffsetTop = $('#leftbar_part1').offset().top;
	let part1OuterHeight = $('#leftbar_part1').outerHeight();

	function changeLeftbarStickyStatus(){
		let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if( part1OffsetTop + part1OuterHeight + 10 - scrollTop <= 90 ){
			//滚动条在页面中间浮动状态
			leftbarPart2.classList.add('sticky');
		}else{
			//滚动条在顶部 不浮动状态
			leftbarPart2.classList.remove('sticky');
		}
	}
	changeLeftbarStickyStatus();
	document.addEventListener("scroll", changeLeftbarStickyStatus, {passive: true});
	$(window).resize(function(){
		part1OffsetTop = $('#leftbar_part1').offset().top;
		part1OuterHeight = $('#leftbar_part1').outerHeight();
		changeLeftbarStickyStatus();
	});
	new MutationObserver(function(){
		part1OffsetTop = $('#leftbar_part1').offset().top;
		part1OuterHeight = $('#leftbar_part1').outerHeight();
		changeLeftbarStickyStatus();
	}).observe(leftbarPart1, {attributes: true, childList: true, subtree: true});
}();


/*浮动按钮栏相关 (回顶等)*/
!function(){
	let $fabtns = $('#float_action_buttons');
	let $backToTopBtn = $('#fabtn_back_to_top');
	let $toggleSidesBtn = $('#fabtn_toggle_sides');
	let $toggleDarkmode = $('#fabtn_toggle_darkmode');
	let $toggleAmoledMode = $('#blog_setting_toggle_darkmode_and_amoledarkmode');
	let $toggleBlogSettings = $('#fabtn_toggle_blog_settings_popup');
	let $goToComment = $('#fabtn_go_to_comment');

	let $readingProgressBar = $('#fabtn_reading_progress_bar');
	let $readingProgressDetails = $('#fabtn_reading_progress_details');

	let isScrolling = false;
	$backToTopBtn.on("click" , function(){
		if (!isScrolling){
			isScrolling = true;
			setTimeout(function(){
				isScrolling = false;
			} , 600);
			$("body,html").animate({
				scrollTop: 0
			}, 600);
		}
	});

	$toggleDarkmode.on("click" , function(){
		toggleDarkmode();
	});

	$toggleAmoledMode.on("click" , function(){
		toggleAmoledDarkMode();
	})

	if ($("#post_comment").length > 0){
		$("#fabtn_go_to_comment").removeClass("d-none");
	}else{
		$("#fabtn_go_to_comment").addClass("d-none");
	}
	$goToComment.on("click" , function(){
		gotoHash("#post_comment" , 600);
		$("#post_comment_content").focus();
	});

	if (localStorage['Argon_fabs_Floating_Status'] == "left"){
		$fabtns.addClass("fabtns-float-left");
	}
	$toggleSidesBtn.on("click" , function(){
		$fabtns.addClass("fabtns-unloaded");
		setTimeout(function(){
			$fabtns.toggleClass("fabtns-float-left");
			if ($fabtns.hasClass("fabtns-float-left")){
				localStorage['Argon_fabs_Floating_Status'] = "left";
			}else{
				localStorage['Argon_fabs_Floating_Status'] = "right";
			}
			$fabtns.removeClass("fabtns-unloaded");
		} , 300);
	});
	//博客设置
	$toggleBlogSettings.on("click" , function(){
		$("#float_action_buttons").toggleClass("blog_settings_opened");
	});
	$("#close_blog_settings").on("click" , function(){
		$("#float_action_buttons").removeClass("blog_settings_opened");
	});
	$("#blog_setting_darkmode_switch .custom-toggle-slider").on("click" , function(){
		toggleDarkmode();
	});
	//字体
	$("#blog_setting_font_sans_serif").on("click" , function(){
		$("html").removeClass("use-serif");
		localStorage['Argon_Use_Serif'] = "false";
	});
	$("#blog_setting_font_serif").on("click" , function(){
		$("html").addClass("use-serif");
		localStorage['Argon_Use_Serif'] = "true";
	});
	if (localStorage['Argon_Use_Serif'] == "true"){
		$("html").addClass("use-serif");
	}else if (localStorage['Argon_Use_Serif'] == "false"){
		$("html").removeClass("use-serif");
	}
	//阴影
	$("#blog_setting_shadow_small").on("click" , function(){
		$("html").removeClass("use-big-shadow");
		localStorage['Argon_Use_Big_Shadow'] = "false";
	});
	$("#blog_setting_shadow_big").on("click" , function(){
		$("html").addClass("use-big-shadow");
		localStorage['Argon_Use_Big_Shadow'] = "true";
	});
	if (localStorage['Argon_Use_Big_Shadow'] == "true"){
		$("html").addClass("use-big-shadow");
	}else if (localStorage['Argon_Use_Big_Shadow'] == "false"){
		$("html").removeClass("use-big-shadow");
	}
	//滤镜
	function setBlogFilter(name){
		if (name == undefined || name == ""){
			name = "off";
		}
		if (!$("html").hasClass("filter-" + name)){
			$("html").removeClass("filter-sunset filter-darkness filter-grayscale");
			if (name != "off"){
				$("html").addClass("filter-" + name);
			}
		}
		$("#blog_setting_filters .blog-setting-filter-btn").removeClass("active");
		$("#blog_setting_filters .blog-setting-filter-btn[filter-name='" + name + "']").addClass("active");
		localStorage['Argon_Filter'] = name;
	}
	setBlogFilter(localStorage['Argon_Filter']);
	$(".blog-setting-filter-btn").on("click" , function(){
		setBlogFilter(this.getAttribute("filter-name"));
	});

	function changefabtnDisplayStatus(){
		//阅读进度
		let readingProgress = $(window).scrollTop() / Math.max($(document).height() - $(window).height(), 0.01);
		$readingProgressDetails.html((readingProgress * 100).toFixed(0) + "%");
		$readingProgressBar.css("width" , (readingProgress * 100).toFixed(0) + "%");
		//是否显示回顶
		if ($(window).scrollTop() >= 400 || readingProgress >= 0.5){
			$backToTopBtn.removeClass("fabtn-hidden");
		}else{
			$backToTopBtn.addClass("fabtn-hidden");
		}
	}
	changefabtnDisplayStatus();
	$(window).scroll(function(){
		changefabtnDisplayStatus();
	});
	$fabtns.removeClass("fabtns-unloaded");
}();

/*卡片圆角大小调整*/
!function(){
	function setCardRadius(radius, setcookie){
		document.documentElement.style.setProperty('--card-radius', radius + "px");
		if (setcookie){
			setCookie("argon_card_radius", radius, 365);
		}
	}
	let slider = document.getElementById('blog_setting_card_radius');
	noUiSlider.create(slider, {
		start: [$("meta[name='theme-card-radius']").attr("content")],
		step: 0.5,
		connect: [true, false],
		range: {
			'min': [0],
			'max': [30]
		}
	});
	slider.noUiSlider.on('update', function (values){
		let value = values[0];
		setCardRadius(value, false);
	});
	slider.noUiSlider.on('set', function (values){
		let value = values[0];
		setCardRadius(value, true);
	});
	$(document).on("click" , "#blog_setting_card_radius_to_default" , function(){
		slider.noUiSlider.set($("meta[name='theme-card-radius-origin']").attr("content"));
		setCardRadius($("meta[name='theme-card-radius-origin']").attr("content"), false);
		setCookie("argon_card_radius", $("meta[name='theme-card-radius-origin']").attr("content"), 0);
	});
}();

/*评论区 & 发送评论*/
!function(){
	//回复评论
	replying = false , replyID = 0;
	function reply(commentID){
		cancelEdit(false);
		replying = true;
		replyID = commentID;
		$("#post_comment_reply_name").html($("#comment-" + commentID + " .comment-item-title")[0].innerHTML);
		let preview = $("#comment-" + commentID + " .comment-item-text")[0].innerHTML;
		if ($("#comment-" + commentID + " .comment-item-source")[0].innerHTML != ''){
			preview = $("#comment-" + commentID + " .comment-item-source")[0].innerHTML.replace(/\n/g, "</br>");
		}
		$("#post_comment_reply_preview").html(preview);
		if ($("#comment-" + commentID + " .comment-item-title .badge-private-comment").length > 0){
			$("#post_comment").addClass("post-comment-force-privatemode-on");
		}else{
			$("#post_comment").addClass("post-comment-force-privatemode-off");
		}
		$("body,html").animate({
			scrollTop: $('#post_comment').offset().top - 100
		}, 300);
		$('#post_comment_reply_info').slideDown(600);
	}
	function cancelReply(){
		replying = false;
		replyID = 0;
		$('#post_comment_reply_info').slideUp(300);
		$("#post_comment").removeClass("post-comment-force-privatemode-on post-comment-force-privatemode-off");
	}
	$(document).on("click" , ".comment-reply" , function(){
		reply(this.getAttribute("data-id"));
	});
	$(document).on("click" , "#post_comment_reply_cancel" , function(){
		cancelReply();
	});
	//编辑评论
	editing = false , editID = 0;
	function edit(commentID){
		cancelReply();
		editing = true;
		editID = commentID;
		$('#post_comment').addClass("editing");
		$("#post_comment_content").val($("#comment-" + editID + " .comment-item-source").text());
		$("#post_comment_content").trigger("change");
		if ($("#comment-" + editID).data("use-markdown") == true && document.getElementById("comment_post_use_markdown") != null){
			document.getElementById("comment_post_use_markdown").checked = true;
		}else{
			document.getElementById("comment_post_use_markdown").checked = false;
		}
		if ($("#comment-" + commentID + " .comment-item-title .badge-private-comment").length > 0){
			$("#post_comment").addClass("post-comment-force-privatemode-on");
		}else{
			$("#post_comment").addClass("post-comment-force-privatemode-off");
		}
		$("body,html").animate({
			scrollTop: $('#post_comment').offset().top - 100
		}, 300);
	}
	function cancelEdit(clear){
		editing = false;
		editID = 0;
		$("#post_comment").removeClass("post-comment-force-privatemode-on post-comment-force-privatemode-off");
		if (clear == true) $("#post_comment_content").val("");
		$("#post_comment_content").trigger("change");
		$('#post_comment').removeClass("editing");
	}
	$(document).on("click" , ".comment-edit" , function(){
		edit(this.getAttribute("data-id"));
	});
	$(document).on("click" , "#post_comment_edit_cancel" , function(){
		$("body,html").animate({
			scrollTop: $("#comment-" + editID).offset().top - 100
		}, 300);
		cancelEdit(true);
	});

	//显示/隐藏额外输入框 (评论者网站)
	$(document).on("click" , "#post_comment_toggle_extra_input" , function(){
		$("#post_comment").toggleClass("show-extra-input");
		if ($("#post_comment").hasClass("show-extra-input")){
			$("#post_comment_extra_input").slideDown(300);
		}else{
			$("#post_comment_extra_input").slideUp(300);
		}
	});

	//输入框细节
	$(document).on("change input keydown keyup propertychange" , "#post_comment_content" , function(){
		$("#post_comment_content_hidden")[0].innerText = $("#post_comment_content").val() + "\n";
		$("#post_comment_content").css("height" , $("#post_comment_content_hidden").outerHeight());
	});
	$(document).on("focus" , "#post_comment_link" , function(){
		$(".post-comment-link-container").addClass("active");
	});
	$(document).on("blur" , "#post_comment_link" , function(){
		$(".post-comment-link-container").removeClass("active");
	});
	$(document).on("focus" , "#post_comment_captcha" , function(){
		$(".post-comment-captcha-container").addClass("active");
	});
	$(document).on("blur" , "#post_comment_captcha" , function(){
		$(".post-comment-captcha-container").removeClass("active");
	});

	//发送评论
	function postComment(){
		commentContent = $("#post_comment_content").val();
		commentName = $("#post_comment_name").val();
		commentEmail = $("#post_comment_email").val();
		commentLink = $("#post_comment_link").val();
		commentCaptcha = $("#post_comment_captcha").val();
		if ($("#comment_post_use_markdown").length > 0){
			useMarkdown = $("#comment_post_use_markdown")[0].checked;
		}else{
			useMarkdown = false;
		}
		if ($("#comment_post_privatemode").length > 0){
			privateMode = $("#comment_post_privatemode")[0].checked;
		}else{
			privateMode = false;
		}
		if ($("#comment_post_mailnotice").length > 0){
			mailNotice = $("#comment_post_mailnotice")[0].checked;
		}else{
			mailNotice = false;
		}

		postID = $("#post_comment_post_id").val();
		commentCaptchaSeed = $("#post_comment_captcha_seed").val();

		isError = false;
		errorMsg = "";

		//检查表单合法性
		if (commentContent.match(/^\s*$/)){
			isError = true;
			errorMsg += "评论内容不能为空</br>";
		}
		if (!$("#post_comment").hasClass("no-need-name-email")){
			if (commentName.match(/^\s*$/)){
				isError = true;
				errorMsg += "昵称不能为空</br>";
			}
			if ($("#post_comment").hasClass("enable-qq-avatar")){
				if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail) && !(/^[1-9][0-9]{4,10}$/).test(commentEmail)){
					isError = true;
					errorMsg += "邮箱或 QQ 号格式错误</br>";
				}
			}else{
				if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail)){
					isError = true;
					errorMsg += "邮箱格式错误</br>";
				}
			}
		}else{
			if (document.getElementById("comment_post_mailnotice") != null){
				if (document.getElementById("comment_post_mailnotice").checked == true){
					if ($("#post_comment").hasClass("enable-qq-avatar")){
						if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail) && !(/^[1-9][0-9]{4,10}$/).test(commentEmail)){
							isError = true;
							errorMsg += "邮箱或 QQ 号格式错误</br>";
						}
					}else{
						if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail)){
							isError = true;
							errorMsg += "邮箱格式错误</br>";
						}
					}
				}
			}
		}
		if (commentLink != "" && !(/https?:\/\//).test(commentLink)){
			isError = true;
			errorMsg += "网站格式错误 (不是 http(s):// 开头)</br>";
		}
		if (!$("#post_comment").hasClass("no-need-captcha")){
			if (commentCaptcha == ""){
				isError = true;
				errorMsg += "验证码未输入";
			}
			if (commentCaptcha != "" && !(/^[0-9]+$/).test(commentCaptcha)){
				isError = true;
				errorMsg += "验证码格式错误";
			}
		}
		if (isError){
			iziToast.show({
				title: '评论格式错误',
				message: errorMsg,
				class: 'shadow-sm',
				position: 'topRight',
				backgroundColor: '#f5365c',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-close',
				timeout: 5000
			});
			return;
		}

		//增加 disabled 属性和其他的表单提示
		$("#post_comment").addClass("sending");
		$("#post_comment_content").attr("disabled","disabled");
		$("#post_comment_name").attr("disabled","disabled");
		$("#post_comment_email").attr("disabled","disabled");
		$("#post_comment_captcha").attr("disabled","disabled");
		$("#post_comment_link").attr("disabled","disabled");
		$("#post_comment_send").attr("disabled","disabled");
		$("#post_comment_reply_cancel").attr("disabled","disabled");
		$("#post_comment_send .btn-inner--icon.hide-on-comment-editing").html("<i class='fa fa-spinner fa-spin'></i>");
		$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html("发送中");

		iziToast.show({
			title: '正在发送',
			message: "评论正在发送中...",
			class: 'shadow-sm iziToast-noprogressbar',
			position: 'topRight',
			backgroundColor: 'var(--themecolor)',
			titleColor: '#ffffff',
			messageColor: '#ffffff',
			iconColor: '#ffffff',
			progressBarColor: '#ffffff',
			icon: 'fa fa-spinner fa-spin',
			close: false,
			timeout: 999999999
		});

		$.ajax({
			type: 'POST',
			url: wp_path + "wp-admin/admin-ajax.php",
			dataType : "json",
			data: {
				action: "ajax_post_comment",
				comment: commentContent,
				author: commentName,
				email: commentEmail,
				url: commentLink,
				comment_post_ID: postID,
				comment_parent: replyID,
				comment_captcha_seed: commentCaptchaSeed,
				comment_captcha: commentCaptcha,
				"wp-comment-cookies-consent": "yes",
				use_markdown: useMarkdown,
				private_mode: privateMode,
				enable_mailnotice: mailNotice
			},
			success: function(result){
				$("#post_comment").removeClass("sending");
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_name").removeAttr("disabled");
				$("#post_comment_email").removeAttr("disabled");
				$("#post_comment_link").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_reply_cancel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon.hide-on-comment-editing").html("<i class='fa fa-send'></i>");
				$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html("发送");
				$("#post_comment").removeClass("show-extra-input post-comment-force-privatemode-on post-comment-force-privatemode-off");
				if (!result.isAdmin){
					$("#post_comment_captcha").removeAttr("disabled");
				}

				//判断是否有错误
				if (result.status == "failed"){
					iziToast.destroy();
					iziToast.show({
						title: '评论发送失败',
						message: result.msg,
						class: 'shadow-sm',
						position: 'topRight',
						backgroundColor: '#f5365c',
						titleColor: '#ffffff',
						messageColor: '#ffffff',
						iconColor: '#ffffff',
						progressBarColor: '#ffffff',
						icon: 'fa fa-close',
						timeout: 5000
					});
					return;
				}

				//发送成功
				iziToast.destroy();
				iziToast.show({
					title: '发送成功',
					message: "您的评论已发送",
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#2dce89',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-check',
					timeout: 5000
				});
				//插入新评论
				let parentID = result.parentID;
				if (parentID == "" || parentID == null){
					parentID = 0;
				}
				parentID = parseInt(parentID);
				if (parentID == 0){
					if ($("#comments > .card-body > ol.comment-list").length == 0){
						$("#comments > .card-body").html("<h2 class='comments-title'><i class='fa fa-comments'></i> 评论</h2><ol class='comment-list'></ol>");
					}
					if (result.commentOrder == "asc"){
						$("#comments > .card-body > ol.comment-list").append(result.html);
					}else{
						$("#comments > .card-body > ol.comment-list").prepend(result.html);
					}
				}else{
					if ($("#comment-" + parentID + " + .comment-divider + li > ul.children").length > 0){
						$("#comment-" + parentID + " + .comment-divider + li > ul.children").append(result.html);
					}else{
						$("#comment-" + parentID + " + .comment-divider").after("<li><ul class='children'>" + result.html + "</ul></li>");
					}
				}
				//复位评论表单
				cancelReply();
				$("#post_comment_content").val("");
				$("#post_comment_captcha_seed").val(result.newCaptchaSeed);
				$("#post_comment_captcha + style").html(".post-comment-captcha-container:before{content: '" + result.newCaptcha + "';}");
				$("#post_comment_captcha").val(result.newCaptchaAnswer);
				$("body,html").animate({
					scrollTop: $("#comment-" + result.id).offset().top - 100
				}, 300);
			},
			error: function(result){
				$("#post_comment").removeClass("sending");
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_name").removeAttr("disabled");
				$("#post_comment_email").removeAttr("disabled");
				$("#post_comment_link").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_reply_cancel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon.hide-on-comment-editing").html("<i class='fa fa-send'></i>");
				$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html("发送");
				$("#post_comment").removeClass("show-extra-input post-comment-force-privatemode-on post-comment-force-privatemode-off");
				if (!result.isAdmin){
					$("#post_comment_captcha").removeAttr("disabled");
				}

				iziToast.destroy();
				iziToast.show({
					title: '评论发送失败',
					message: "未知原因",
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#f5365c',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-close',
					timeout: 5000
				});
				return;
			}
		});
	}
	//编辑评论
	function editComment(){
		commentContent = $("#post_comment_content").val();
		isError = false;
		errorMsg = "";
		if (commentContent.match(/^\s*$/)){
			isError = true;
			errorMsg += "评论内容不能为空</br>";
		}
		if (isError){
			iziToast.show({
				title: '评论格式错误',
				message: errorMsg,
				class: 'shadow-sm',
				position: 'topRight',
				backgroundColor: '#f5365c',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-close',
				timeout: 5000
			});
			return;
		}

		//增加 disabled 属性和其他的表单提示
		$("#post_comment_content").attr("disabled","disabled");
		$("#post_comment_send").attr("disabled","disabled");
		$("#post_comment_edit_cancel").attr("disabled","disabled");
		$("#post_comment_send .btn-inner--icon.hide-on-comment-not-editing").html("<i class='fa fa-spinner fa-spin'></i>");
		$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html("编辑中");

		iziToast.show({
			title: '正在编辑',
			message: "评论正在编辑中...",
			class: 'shadow-sm iziToast-noprogressbar',
			position: 'topRight',
			backgroundColor: 'var(--themecolor)',
			titleColor: '#ffffff',
			messageColor: '#ffffff',
			iconColor: '#ffffff',
			progressBarColor: '#ffffff',
			icon: 'fa fa-spinner fa-spin',
			close: false,
			timeout: 999999999
		});

		$.ajax({
			type: 'POST',
			url: wp_path + "wp-admin/admin-ajax.php",
			dataType : "json",
			data: {
				action: "user_edit_comment",
				comment: commentContent,
				id: editID
			},
			success: function(result){
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_edit_cancel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon.hide-on-comment-not-editing").html("<i class='fa fa-pencil'></i>");
				$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html("编辑");

				//判断是否有错误
				if (result.status == "failed"){
					iziToast.destroy();
					iziToast.show({
						title: '评论编辑失败',
						message: result.msg,
						class: 'shadow-sm',
						position: 'topRight',
						backgroundColor: '#f5365c',
						titleColor: '#ffffff',
						messageColor: '#ffffff',
						iconColor: '#ffffff',
						progressBarColor: '#ffffff',
						icon: 'fa fa-close',
						timeout: 5000
					});
					return;
				}

				//发送成功，替换原评论
				$("#comment-" + editID + " .comment-item-text").html(result.new_comment);
				$("#comment-" + editID + " .comment-item-source").html(result.new_comment_source);
				if ($("#comment-" + editID + " .comment-info .comment-edited").length == 0){
					$("#comment-" + editID + " .comment-info").prepend("<div class='comment-edited'><i class='fa fa-pencil' aria-hidden='true'></i>已编辑</div>")
				}
				if (result.can_visit_edit_history){
					$("#comment-" + editID + " .comment-info .comment-edited").addClass("comment-edithistory-accessible");
				}

				iziToast.destroy();
				iziToast.show({
					title: '编辑成功',
					message: "您的评论已编辑",
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#2dce89',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-check',
					timeout: 5000
				});
				$("body,html").animate({
					scrollTop: $("#comment-" + editID).offset().top - 100
				}, 300);
				editing = false;
				editID = 0;
				$("#post_comment_content").val("");
				$('#post_comment').removeClass("editing post-comment-force-privatemode-on post-comment-force-privatemode-off");
				$("#post_comment_content").trigger("change");
			},
			error: function(result){
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_edit_cancel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon.hide-on-comment-not-editing").html("<i class='fa fa-pencil'></i>");
				$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html("编辑");
				if (result.readyState != 4 || result.status == 0){
					iziToast.destroy();
					iziToast.show({
						title: '评论编辑失败',
						message: "未知原因",
						class: 'shadow-sm',
						position: 'topRight',
						backgroundColor: '#f5365c',
						titleColor: '#ffffff',
						messageColor: '#ffffff',
						iconColor: '#ffffff',
						progressBarColor: '#ffffff',
						icon: 'fa fa-close',
						timeout: 5000
					});
					return;
				}
			}
		});
	}
	$(document).on("click" , "#post_comment_send" , function(){
		if ($("#post_comment").hasClass("editing")){
			editComment();
		}else{
			postComment();
		}
	});
}();
/*查看评论编辑记录*/
function showCommentEditHistory(id){
	let requestID = parseInt(new Date().getTime());
	$("#comment_edit_history").data("request-id", requestID);
	$("#comment_edit_history .modal-title").html("评论 #" + id + " 的编辑记录");
	$("#comment_edit_history .modal-body").html("<div class='comment-history-loading'><span class='spinner-border text-primary'></span><span style='display: inline-block;transform: translateY(-4px);margin-left: 15px;font-size: 18px;'>加载中</span></div>");
	$("#comment_edit_history").modal(null);
	$.ajax({
		type: 'POST',
		url: wp_path + "wp-admin/admin-ajax.php",
		dataType : "json",
		data: {
			action: "get_comment_edit_history",
			id: id
		},
		success: function(result){
			if ($("#comment_edit_history").data("request-id") != requestID){
				return;
			}
			$("#comment_edit_history .modal-body").hide();
			$("#comment_edit_history .modal-body").html(result.history);
			$("#comment_edit_history .modal-body").fadeIn(300);
		},
		error: function(result){
			if ($("#comment_edit_history").data("request-id") != requestID){
				return;
			}
			$("#comment_edit_history .modal-body").hide();
			$("#comment_edit_history .modal-body").html("加载失败");
			$("#comment_edit_history .modal-body").fadeIn(300);
		}
	});
}
$(document).on("click" , ".comment-edited.comment-edithistory-accessible" , function(){
	showCommentEditHistory($(this).parent().parent().parent().data("id"));
});
/*需要密码的文章加载*/
$(document).on("submit" , ".post-password-form" , function(){
	$("input[type='submit']", this).attr("disabled", "disabled");
	let url = $(this).attr("action");
	let formdata = $(this).serialize();
	setTimeout(function(){
		pjaxLoadUrl(url , false , 0 , 0 , "POST" , formdata);
	}, 1);
	return false;
});
/*评论分页加载*/
!function(){
	$(document).on("click" , "#comments_navigation .page-item > div" , function(){
		$("#comments").addClass("comments-loading");
		pjaxLoading = true;
		NProgress.set(0.618);
		url = $(this).attr("href");
		$.ajax({
			type: 'POST',
			url: url,
			dataType : "html",
			success : function(result){
				NProgress.done();
				pjaxLoading = false;
				$vdom = $(result);
				$("#comments").html($("#comments", $vdom).html());
				$("#comments").removeClass("comments-loading");
				$("body,html").animate({
					scrollTop: $("#comments").offset().top - 100
				}, 300);
			},
			error : function(){
				pjaxLoading = false;
				window.location.href = url;
			}
		});
	});
	$(document).on("click" , "#comments_more" , function(){
		$("#comments_more").attr("disabled", "disabled");
		pjaxLoading = true;
		NProgress.set(0.618);
		url = $(this).attr("href");
		$.ajax({
			type: 'POST',
			url: url,
			data: {
				no_post_view: 'true'
			},
			dataType : "html",
			success : function(result){
				NProgress.done();
				pjaxLoading = false;
				$vdom = $(result);
				$("#comments > .card-body > ol.comment-list").append($("#comments > .card-body > ol.comment-list", $vdom).html());
				if ($("#comments_more", $vdom).length == 0){
					$("#comments_more").remove();
					$(".comments-navigation-more").html("<div class='comments-navigation-nomore'>没有更多了</div>");
				}else{
					$("#comments_more").attr("href", $("#comments_more", $vdom).attr("href"));
					$("#comments_more").removeAttr("disabled");
				}
			},
			error : function(){
				pjaxLoading = false;
				window.location.href = url;
			}
		});
	});
}();

/*URL 中 # 根据 ID 定位*/
function gotoHash(hash , durtion){
	if (hash.length == 0){
		return;
	}
	if ($(hash).length == 0){
		return;
	}
	if (durtion == null){
		durtion = 200;
	}
	$("body,html").animate({
		scrollTop: $(hash).offset().top - 80
	}, durtion);
}
function getHash(url){
	return url.substring(url.indexOf('#'));
}
!function(){
	$(window).on("hashchange" , function(){
		hash = window.location.hash;
		gotoHash(hash);
	});
	$(window).trigger("hashchange");
}();

/*显示文章过时信息 Toast*/
function showPostOutdateToast(){
	if ($("#primary #post_outdate_toast").length > 0){
		iziToast.show({
			title: '',
			message: $("#primary #post_outdate_toast").data("text"),
			class: 'shadow-sm',
			position: 'topRight',
			backgroundColor: 'var(--themecolor)',
			titleColor: '#ffffff',
			messageColor: '#ffffff',
			iconColor: '#ffffff',
			progressBarColor: '#ffffff',
			icon: 'fa fa-info',
			close: false,
			timeout: 8000
		});
		$("#primary #post_outdate_toast").remove();
	}
}
showPostOutdateToast();

/*Pjax*/
var pjaxUrlChanged , pjaxLoading = false;
function pjaxLoadUrl(url , pushstate , scrolltop , oldscrolltop , requestType , formdata){
	requestType = requestType || "GET";
	formdata = formdata || {};
	if (pjaxLoading == false){
		NProgress.remove();
		NProgress.start();
		pjaxLoading = true;
		pjaxUrlChanged = false;
		try{
			if (pushstate == true){
				if (url.match(/https?:\/\//) != null){
					if (window.location.href.match(/.*\:\/\/([^\/]*).*/)[1] != url.match(/.*\:\/\/([^\/]*).*/)[1]){
						throw "Cross Domain";
					}
					if (window.location.href.match(/https?:\/\//)[0] != url.match(/https?:\/\//)[0]){
						throw "Different Protocols";
					}
				}
			}
			NProgress.set(0.618);
			let ajaxArgs = {
				url : url,
				type : requestType,
				dataType : "html",
				success : function(result){
					NProgress.inc();
					try{
						let vdom = document.createElement('html');
						vdom.innerHTML = result;
						let $vdom = $('<div></div>');
						$vdom.html(result);

						if ($("#using_pjax" , $vdom).length == 0){
							throw "HTML struct not simular";
						}
						document.body.setAttribute("class" , vdom.getElementsByTagName('body')[0].getAttribute("class"));
						$("#leftbar_part2_inner").html($("#leftbar_part2_inner" , $vdom)[0].innerHTML);
						$("#primary").html($("#primary" , $vdom)[0].innerHTML);
						$("#leftbar_part1_menu").html($("#leftbar_part1_menu" , $vdom)[0].innerHTML);
						$("#wpadminbar").html($("#wpadminbar" , $vdom).html());

						$("#content .page-infomation-card").remove();
						if ($(".page-infomation-card" , $vdom).length > 0){
							$("#content").prepend($(".page-infomation-card" , $vdom)[0].outerHTML);
						}

						if ($("#post_comment" , $vdom).length > 0){
							$("#fabtn_go_to_comment").removeClass("d-none");
						}else{
							$("#fabtn_go_to_comment").addClass("d-none");
						}
						
						$("body,html").animate({
							scrollTop: scrolltop
						}, 600);
						
						NProgress.inc();

						if (pushstate == true){
							window.history.replaceState({scrolltop: oldscrolltop , reloadonback: true , lastreloadscrolltop: null} , '' , '')
							window.history.pushState('' , '' , url);
						}
						pjaxLoading = false;
						pjaxUrlChanged = true;
						
						$("title").html($("title" , $vdom)[0].innerHTML);

						try{
							if (MathJax != undefined){
								MathJax.typeset();
							}
						}catch (err){}
						try{
							if ($("script#mathjax_v2_script" , $vdom).length > 0){
								MathJax.Hub.Typeset();
							}
						}catch (err){}
						try{
							if (renderMathInElement != undefined){
								renderMathInElement(document.body,{
									delimiters: [
										{left: "$$", right: "$$", display: true},
										{left: "$", right: "$", display: false},
										{left: "\\(", right: "\\)", display: false}
									]
								});
							}
						}catch (err){}

						highlightJsRender();

						getGithubInfoCardContent();

						showPostOutdateToast();

						let scripts = $("#content script:not([no-pjax]):not(.no-pjax)" , $vdom);
						for (let script of scripts){
							if (script.innerHTML.indexOf("\/*NO-PJAX*\/") == -1){
								try{
									eval(script.innerHTML);
								}catch (err){}
							}
						}

						NProgress.done();

						$(window).trigger("hashchange");
						$(window).trigger("scroll");


						if (window.location.hash != ""){
							gotoHash(window.location.hash);
						}

						if (typeof(window.pjaxLoaded) == "function"){
							window.pjaxLoaded();
						}
					}catch (err){
						console.log(err);
						NProgress.done();
						if (pjaxUrlChanged){
							pjaxLoading = false;
							window.location.reload();
						}else{
							pjaxUrlChanged = true;
							pjaxLoading = false;
							window.location.href = url;
						}
					}
				},
				error : function(){
					NProgress.done();
					pjaxLoading = false;
					pjaxUrlChanged = true;
					window.location.href = url;
				}
			};
			if (requestType == "POST"){
				ajaxArgs.data = formdata;
			}
			$.ajax(ajaxArgs);
		}catch(err){
			console.log(err);
			NProgress.done();
			pjaxLoading = false;
			pjaxUrlChanged = true;
			window.location.href = url;
		}
	}
}
function removeUrlHash(url){
	if (url.indexOf('#') != -1){
		url = url.substring(0, url.indexOf('#'));
	}
	if (url.charAt(url.length - 1) == '/'){
		url = url.substring(0, url.length - 1);
	}
	return url;
}
$(document).ready(function(){
	if ($("html").hasClass("no-pjax")){
		return;
	}
	window.history.scrollRestoration = "manual"; //接管浏览器滚动复位管理
	$(document).on("click" , "a[href]:not([no-pjax]):not(.no-pjax):not([target='_blank']):not([download])" , function(){
		if (pjaxLoading){
			return false;
		}
		let scrolltop = $(document).scrollTop();
		//对文章预览卡片使用过渡动画
		if ($(this).is("#main article.post-preview a.post-title")){
			let $card = $($(this).parents("article.post-preview")[0]);
			$card.append("<div class='loading-css-animation'><div class='loading-dot loading-dot-1' ></div><div class='loading-dot loading-dot-2' ></div><div class='loading-dot loading-dot-3' ></div><div class='loading-dot loading-dot-4' ></div><div class='loading-dot loading-dot-5' ></div><div class='loading-dot loading-dot-6' ></div><div class='loading-dot loading-dot-7' ></div><div class='loading-dot loading-dot-8' ></div></div></div>");
			$card.addClass("post-pjax-loading");
			$("#main").addClass("post-list-pjax-loading");
			let offsetTop = $($card).offset().top - $("#main").offset().top;
			$card.css("transform" , "translateY(-" + offsetTop + "px)");
			$("body,html").animate({
				scrollTop: 0
			}, 450);
		}
		//判断是否是同一个页面，只有 Hash 不同
		let now = window.location.href;
		let url = this.getAttribute("href");
		if ((removeUrlHash(url) == removeUrlHash(now) || url.charAt(0) == '#') && url.indexOf("#") != -1){
			window.history.replaceState({scrolltop: scrolltop , reloadonback: /*false*/true , lastreloadscrolltop: null} , '' , url);
			gotoHash(getHash(url));
			return false;
		}
		//Pjax 加载
		pjaxLoadUrl(url , true , 0 , scrolltop);
		return false;
	});
	$(window).on("popstate" , function(){
		try{
			$("article img.zoomify.zoomed").zoomify('zoomOut');
		}catch(err){}
		let json = window.history.state;
		if (json == null || json == ''){
			setTimeout(function(){
				pjaxLoadUrl(document.location , false , 0 , $(window).scrollTop());
			},1);
			return false;
		}
		if (json.reloadonback != true){
			$("body,html").animate({
				scrollTop: json.scrolltop
			}, 200);
		}else{
			setTimeout(function(){
				pjaxLoadUrl(document.location , false , json.scrolltop , $(window).scrollTop());
			},1);
		}
		return false;
	});
	function recordScrollTop(){
		let json = window.history.state;
		if (json == null || json == ""){
			json = {};
		}
		json.scrolltop = $(document).scrollTop();
		window.history.replaceState(json , '' , '');
	}
	$(window).on("beforeunload" , function(){
		recordScrollTop();
	});
	//网页被载入时检测是否保存了刷新时滚动高度
	let json = window.history.state;
	if (json != null){
		if (json.scrolltop != undefined){
			$(window).scrollTop(json.scrolltop);
			//json.scrolltop = undefined;
			//window.history.replaceState(json , '' , '');
		}
	}
});

/*Tags Dialog pjax 加载后自动关闭*/
$(document).on("click" , "#blog_tags .tag" , function(){
	$("#blog_tags button.close").trigger("click");
});
$(document).on("click" , "#blog_categories .tag" , function(){
	$("#blog_categories button.close").trigger("click");
});

/*侧栏 & 顶栏菜单手机适配*/
!function(){
	$(document).on("click" , "#fabtn_open_sidebar" , function(){
		$("html").addClass("leftbar-opened");
	});
	$(document).on("click" , "#sidebar_mask" , function(){
		$("html").removeClass("leftbar-opened");
	});
	$(document).on("click" , "#leftbar a[href]:not([no-pjax]):not([href^='#'])" , function(){
		$("html").removeClass("leftbar-opened");
	});
	$(document).on("click" , "#navbar_global.show .navbar-nav a[href]:not([no-pjax]):not([href^='#'])" , function(){
		$("#navbar_global .navbar-toggler").click();
	});
	$(document).on("click" , "#navbar_global.show #navbar_search_btn_mobile" , function(){
		$("#navbar_global .navbar-toggler").click();
	});
}();

/*折叠区块小工具*/
$(document).on("click" , ".collapse-block .collapse-block-title" , function(){
	let id = this.getAttribute("collapse-id");
	let selecter = ".collapse-block[collapse-id='" + id +"']";
	$(selecter).toggleClass("collapsed");
	if ($(selecter).hasClass("collapsed")){
		$(selecter + " .collapse-block-body").stop(true , false).slideUp(200);
	}else{
		$(selecter + " .collapse-block-body").stop(true , false).slideDown(200);
	}
});

/*获得 Github Repo Shortcode 信息卡内容*/
function getGithubInfoCardContent(){
	$(".github-info-card").each(function(){
		(function($this){
			if ($this.attr("data-getdata") == "backend"){
				$(".github-info-card-description" , $this).html($this.attr("data-description"));
				$(".github-info-card-stars" , $this).html($this.attr("data-stars"));
				$(".github-info-card-forks" , $this).html($this.attr("data-forks"));
				return;
			}
			$(".github-info-card-description" , $this).html("Loading...");
			$(".github-info-card-stars" , $this).html("-");
			$(".github-info-card-forks" , $this).html("-");
			author = $this.attr("data-author");
			project = $this.attr("data-project");
			$.ajax({
				url : "https://api.github.com/repos/" + author + "/" + project,
				type : "GET",
				dataType : "json",
				success : function(result){
					description = result.description;
					if (result.homepage != ""){
						description += " <a href='" + result.homepage + "' target='_blank' no-pjax>" + result.homepage + "</a>"
					}
					$(".github-info-card-description" , $this).html(description);
					$(".github-info-card-stars" , $this).html(result.stargazers_count);
					$(".github-info-card-forks" , $this).html(result.forks_count);
					//console.log(result);
				},
				error : function(xhr){
					if (xhr.status == 404){
						$(".github-info-card-description" , $this).html("找不到该 Repo");
					}else{
						$(".github-info-card-description" , $this).html("获取 Repo 信息失败");
					}
				}
			});
		})($(this));
	});
}
getGithubInfoCardContent();

/*说说点赞*/
$(document).on("click" , ".shuoshuo-upvote" , function(){
	$this = $(this);
	ID = $this.attr("data-id");
	$this.addClass("shuoshuo-upvoting");
	$.ajax({
		url : wp_path + "wp-admin/admin-ajax.php",
		type : "POST",
		dataType : "json",
		data : {
			action: "upvote_shuoshuo",
			shuoshuo_id : ID,
		},
		success : function(result){
			$this.removeClass("shuoshuo-upvoting");
			if (result.status == "success"){
				$(".shuoshuo-upvote-num" , $this).html(result.total_upvote);
				$("i.fa-thumbs-o-up" , $this).addClass("fa-thumbs-up").removeClass("fa-thumbs-o-up");
				$this.addClass("upvoted");
				$this.addClass("shuoshuo-upvoted-animation");
				iziToast.show({
					title: result.msg,
					class: 'shadow-sm',
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
				$(".shuoshuo-upvote-num" , $this).html(result.total_upvote);
				iziToast.show({
					title: result.msg,
					class: 'shadow-sm',
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
		},
		error : function(xhr){
			$this.removeClass("shuoshuo-upvoting");
			iziToast.show({
				title: "点赞失败",
				class: 'shadow-sm',
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
	});
});

//颜色计算
function rgb2hsl(R,G,B){
	let r = R / 255;
	let g = G / 255;
	let b = B / 255;

	let var_Min = Math.min(r, g, b);
	let var_Max = Math.max(r, g, b);
	let del_Max = var_Max - var_Min;

	let H, S, L = (var_Max + var_Min) / 2;

	if (del_Max == 0){
		H = 0;
		S = 0;
	}else{
		if (L < 0.5){
			S = del_Max / (var_Max + var_Min);
		}else{
			S = del_Max / (2 - var_Max - var_Min);
		}

		del_R = (((var_Max - r) / 6) + (del_Max / 2)) / del_Max;
		del_G = (((var_Max - g) / 6) + (del_Max / 2)) / del_Max;
		del_B = (((var_Max - b) / 6) + (del_Max / 2)) / del_Max;

		if (r == var_Max){
			H = del_B - del_G;
		}
		else if (g == var_Max){
			H = (1 / 3) + del_R - del_B;
		}
		else if (b == var_Max){
			H = (2 / 3) + del_G - del_R;
		}
		if (H < 0) H += 1;
		if (H > 1) H -= 1;
	}
	return {
		'h': H,//0~1
		's': S,
		'l': L
	};
}
function Hue_2_RGB(v1,v2,vH){
	if (vH < 0) vH += 1;
	if (vH > 1) vH -= 1;
	if ((6 * vH) < 1) return (v1 + (v2 - v1) * 6 * vH);
	if ((2 * vH) < 1) return v2;
	if ((3 * vH) < 2) return (v1 + (v2 - v1) * ((2 / 3) - vH) * 6);
	return v1;
}
function hsl2rgb(h,s,l){
	let r, g, b, var_1, var_2;
	if (s == 0){
		r = l;
		g = l;
		b = l;
	}
	else{
		if (l < 0.5){
			var_2 = l * (1 + s);
		}
		else{
			var_2 = (l + s) - (s * l);
		}
		var_1 = 2 * l - var_2;
		r = Hue_2_RGB(var_1, var_2, h + (1 / 3));
		g = Hue_2_RGB(var_1, var_2, h);
		b = Hue_2_RGB(var_1, var_2, h - (1 / 3));
	}
	return {
		'R': Math.round(r * 255),//0~255
		'G': Math.round(g * 255),
		'B': Math.round(b * 255),
		'r': r,//0~1
		'g': g,
		'b': b
	};
}
function rgb2hex(r,g,b){
	let hex = new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
	let rh, gh, bh;
	rh = "", gh ="", bh="";
	while (rh.length < 2){
		rh = hex[r%16] + rh;
		r = Math.floor(r / 16);
	}
	while (gh.length < 2){
		gh = hex[g%16] + gh;
		g = Math.floor(g / 16);
	}
	while (bh.length < 2){
		bh = hex[b%16] + bh;
		b = Math.floor(b / 16);
	}
	return "#" + rh + gh + bh;
}
function hex2rgb(hex){
	//hex: #XXXXXX
	let dec = {
		'0': 0, '1': 1, '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9, 'A': 10, 'B': 11, 'C': 12, 'D': 13, 'E': 14, 'F': 15
	};
	return {
		'R': (dec[hex.substr(1,1)] * 16 + dec[hex.substr(2,1)]),//0~255
		'G': (dec[hex.substr(3,1)] * 16 + dec[hex.substr(4,1)]),
		'B': (dec[hex.substr(5,1)] * 16 + dec[hex.substr(6,1)]),
		'r': (dec[hex.substr(1,1)] * 16 + dec[hex.substr(2,1)]) / 255,//0~1
		'g': (dec[hex.substr(3,1)] * 16 + dec[hex.substr(4,1)]) / 255,
		'b': (dec[hex.substr(5,1)] * 16 + dec[hex.substr(6,1)]) / 255
	};
}
function rgb2str(rgb){
	return rgb['R'] + "," + rgb['G'] + "," + rgb['B'];
}
function hex2str(hex){
	return rgb2str(hex2rgb(hex));
}
//颜色选择器 & 切换主题色
if ($("meta[name='argon-enable-custom-theme-color']").attr("content") == 'true'){
	let themeColorPicker = new Pickr({
		el: '#theme-color-picker',
		container: 'body',
		theme: 'monolith',
		closeOnScroll: false,
		appClass: 'theme-color-picker-box',
		useAsButton: false,
		padding: 8,
		inline: false,
		autoReposition: true,
		sliders: 'h',
		disabled: false,
		lockOpacity: true,
		outputPrecision: 0,
		comparison: false,
		default: $("meta[name='theme-color']").attr("content"),
		swatches: ['#5e72e4', '#fa7298', '#009688', '#607d8b', '#2196f3', '#3f51b5', '#ff9700', '#109d58', '#dc4437', '#673bb7', '#212121', '#795547'],
		defaultRepresentation: 'HEX',
		showAlways: false,
		closeWithKey: 'Escape',
		position: 'top-start',
		adjustableNumbers: false,
		components: {
			palette: true,
			preview: true,
			opacity: false,
			hue: true,
			interaction: {
				hex: true,
				rgba: true,
				hsla: false,
				hsva: false,
				cmyk: false,
				input: true,
				clear: false,
				cancel: true,
				save: true
			}
		},
		strings: {
			save: '确定',
			clear: '清除',
			cancel: '恢复博客默认'
		}
	});
	themeColorPicker.on('change', instance => {
		updateThemeColor(pickrObjectToHEX(instance), true);
	})
	themeColorPicker.on('save', (color, instance) => {
		updateThemeColor(pickrObjectToHEX(instance._color), true);
		themeColorPicker.hide();
	})
	themeColorPicker.on('cancel', instance => {
		themeColorPicker.hide();
		themeColorPicker.setColor($("meta[name='theme-color-origin']").attr("content").toUpperCase());
		updateThemeColor($("meta[name='theme-color-origin']").attr("content").toUpperCase(), false);
		setCookie("argon_custom_theme_color", "", 0);
	});
}
function pickrObjectToHEX(color){
	let HEXA = color.toHEXA();
	return ("#" + HEXA[0] + HEXA[1] + HEXA[2]).toUpperCase();
}
function updateThemeColor(color, setcookie){
	let themecolor = color;
	let themecolor_rgbstr = hex2str(themecolor);
	let RGB = hex2rgb(themecolor);
	let HSL = rgb2hsl(RGB['R'], RGB['G'], RGB['B']);

	let RGB_dark0 = hsl2rgb(HSL['h'], HSL['s'], Math.max(HSL['l'] - 0.025, 0));
	let themecolor_dark0 = rgb2hex(RGB_dark0['R'],RGB_dark0['G'],RGB_dark0['B']);

	let RGB_dark = hsl2rgb(HSL['h'], HSL['s'], Math.max(HSL['l'] - 0.05, 0));
	let themecolor_dark = rgb2hex(RGB_dark['R'], RGB_dark['G'], RGB_dark['B']);

	let RGB_dark2 = hsl2rgb(HSL['h'], HSL['s'], Math.max(HSL['l'] - 0.1, 0));
	let themecolor_dark2 = rgb2hex(RGB_dark2['R'],RGB_dark2['G'],RGB_dark2['B']);

	let RGB_dark3 = hsl2rgb(HSL['h'], HSL['s'], Math.max(HSL['l'] - 0.15, 0));
	let themecolor_dark3 = rgb2hex(RGB_dark3['R'],RGB_dark3['G'],RGB_dark3['B']);

	let RGB_light = hsl2rgb(HSL['h'], HSL['s'], Math.min(HSL['l'] + 0.1, 1));
	let themecolor_light = rgb2hex(RGB_light['R'],RGB_light['G'],RGB_light['B']);

	document.documentElement.style.setProperty('--themecolor', themecolor);
	document.documentElement.style.setProperty('--themecolor-dark0', themecolor_dark0);
	document.documentElement.style.setProperty('--themecolor-dark', themecolor_dark);
	document.documentElement.style.setProperty('--themecolor-dark2', themecolor_dark2);
	document.documentElement.style.setProperty('--themecolor-dark3', themecolor_dark3);
	document.documentElement.style.setProperty('--themecolor-light', themecolor_light);
	document.documentElement.style.setProperty('--themecolor-rgbstr', themecolor_rgbstr);

	$("meta[name='theme-color']").attr("content", themecolor);
	$("meta[name='theme-color-rgb']").attr("content", themecolor_rgbstr);

	if (setcookie){
		setCookie("argon_custom_theme_color", themecolor, 365);
	}
}

/*评论区图片链接点击处理*/
!function(){
	let invid = 0;
	let activeImg = null;
	$(document).on("click" , ".comment-item-text .comment-image" , function(){
		$(".comment-image-preview", this).attr("data-easing", "cubic-bezier(0.4, 0, 0, 1)");
		$(".comment-image-preview", this).attr("data-duration", "500");
		if (!$(this).hasClass("comment-image-preview-zoomed")){
			activeImg = this;
			$(this).addClass("comment-image-preview-zoomed");
			if (!$(this).hasClass("loaded")){
				$(".comment-image-preview", this).attr('src', $(this).attr("data-src"));
			}
			$(".comment-image-preview", this).zoomify('zoomIn');
			if (!$(this).hasClass("loaded")){
				invid = setInterval(function(){
					if (activeImg.width != 0){
						$("html").trigger("scroll");
						$(activeImg).addClass("loaded");
						clearInterval(invid);
						activeImg = null;
					}
				}, 50);
			}
		}else{
			clearInterval(invid);
			activeImg = null;
			$(this).removeClass("comment-image-preview-zoomed");
			$(".comment-image-preview", this).zoomify('zoomOut');
		}
	});
}();

/*打字效果*/
function typeEffect(element, text, now, interval){
	element.classList.add('typing-effect');
	if (now > text.length){
		setTimeout(function(){
			element.classList.remove('typing-effect');
		}, 1000 - ((interval * now) % 1000) - 50);
		return;
	}
	element.innerText = text.substring(0, now);
	setTimeout(function(){typeEffect(element, text, now + 1, interval)}, interval);
}
!function(){
	$bannerTitle = $(".banner-title");
	if ($bannerTitle.data("text") != undefined){
		typeEffect($(".banner-title-inner")[0], $bannerTitle.data("text"), 0, $bannerTitle.data("interval"));
	}
}();

/*一言*/
if ($(".hitokoto").length > 0){
	$.ajax({
		type: 'GET',
		url: "https://v1.hitokoto.cn",
		success: function(result){
			$(".hitokoto").text(result.hitokoto);
		},
		error: function(result){
			$(".hitokoto").text("Hitokoto 获取失败");
		}
	});
}

/*Highlight.js*/
function randomString(len) {
	len = len || 32;
	let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	let res = "";
	for (let i = 0; i < len; i++) {
		res += chars.charAt(Math.floor(Math.random() * chars.length));
	}
	return res;
}
var codeOfBlocks = {};
function getCodeFromBlock(block){
	if (codeOfBlocks[block.id] != undefined){
		return codeOfBlocks[block.id];
	}
	let lines = $(".hljs-ln-code", block);
	let res = "";
	for (let i = 0; i < lines.length - 1; i++){
		res += lines[i].innerText;
		res += "\n";
	}
	res += lines[lines.length - 1].innerText;
	codeOfBlocks[block.id] = res;
	return res;
}
function highlightJsRender(){
	if (typeof(hljs) == "undefined"){
		return;
	}
	if (typeof(argonEnableCodeHighlight) == "undefined"){
		return;
	}
	if (!argonEnableCodeHighlight){
		return;
	}
	$("article pre.code").each(function(index, block) {
		if ($(block).hasClass("no-hljs")){
			return;
		}
		$(block).html("<code>" + $(block).html() + "</code>");
	});
	$("article pre > code").each(function(index, block) {
		if ($(block).hasClass("no-hljs")){
			return;
		}
		$(block).parent().attr("id", randomString());
		hljs.highlightBlock(block);
		hljs.lineNumbersBlock(block, {singleLine: true});
		$(block).parent().addClass("hljs-codeblock");
		$(block).attr("hljs-codeblock-inner", "");
		let copyBtnID = "copy_btn_" + randomString();
		$(block).parent().append(`<div class="hljs-control hljs-title">
				<div class="hljs-control-btn hljs-control-toggle-linenumber">
					<i class="fa fa-list"></i>
				</div>
				<div class="hljs-control-btn hljs-control-toggle-break-line">
					<i class="fa fa-align-left"></i>
				</div>
				<div class="hljs-control-btn hljs-control-copy" id=` + copyBtnID + `>
					<i class="fa fa-clipboard"></i>
				</div>
				<div class="hljs-control-btn hljs-control-fullscreen">
					<i class="fa fa-arrows-alt"></i>
				</div>
			</div>`);
		let clipboard = new ClipboardJS("#" + copyBtnID, {
			text: function(trigger) {
				return getCodeFromBlock($(block).parent()[0]);
			}
		});
		clipboard.on('success', function(e) {
			iziToast.show({
				title: '复制成功',
				message: "代码已复制到剪贴板",
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
		});
		clipboard.on('error', function(e) {
			iziToast.show({
				title: '复制失败',
				message: "请手动复制代码",
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
		});
	});
}
$(document).ready(function(){
	highlightJsRender();
});
$(document).on("click" , ".hljs-control-fullscreen" , function(){
	let block = $(this).parent().parent();
	block.toggleClass("hljs-codeblock-fullscreen");
	if (block.hasClass("hljs-codeblock-fullscreen")){
		$("html").addClass("noscroll");
	}else{
		$("html").removeClass("noscroll");
	}
});
$(document).on("click" , ".hljs-control-toggle-break-line" , function(){
	let block = $(this).parent().parent();
	block.toggleClass("hljs-break-line");
});
$(document).on("click" , ".hljs-control-toggle-linenumber" , function(){
	let block = $(this).parent().parent();
	block.toggleClass("hljs-hide-linenumber");
});


/*Console*/
!function(){
	console.log('%cTheme: %cArgon%cBy solstice23', 'color: rgba(255,255,255,.6); background: #5e72e4; font-size: 15px;border-radius:5px 0 0 5px;padding:10px 0 10px 20px;','color: rgba(255,255,255,1); background: #5e72e4; font-size: 15px;border-radius:0;padding:10px 15px 10px 0px;','color: #fff; background: #92A1F4; font-size: 15px;border-radius:0 5px 5px 0;padding:10px 20px 10px 15px;');
	console.log('%cVersion%c' + $("meta[name='theme-version']").attr("content"), 'color:#fff; background: #5e72e4;font-size: 12px;border-radius:5px 0 0 5px;padding:3px 10px 3px 10px;','color:#fff; background: #92a1f4;font-size: 12px;border-radius:0 5px 5px 0;padding:3px 10px 3px 10px;');
	console.log('%chttps://github.com/solstice23/argon-theme', 'font-size: 12px;border-radius:5px;padding:3px 10px 3px 10px;border:1px solid #5e72e4;');
}();