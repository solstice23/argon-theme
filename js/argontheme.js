/*根据滚动高度改变顶栏透明度*/
!function(){
	let $toolbar = $("#navbar-main");
	let $bannerContainer = $("#banner_container");
	let $content = $("#content");

	let startTransitionHeight;
	let endTransitionHeight;
	function changeToolbarTransparency(){
		let toolbarRgb = "94, 114, 228";
		if ($("html").hasClass("darkmode")){
			toolbarRgb = "33, 33, 33";
		}
		let scrollTop = $(window).scrollTop();
		startTransitionHeight = $bannerContainer.offset().top - 75;
		endTransitionHeight = $content.offset().top - 75;
		if ($(window).scrollTop() < startTransitionHeight){
			$toolbar.css("cssText","background-color: rgba(" + toolbarRgb + ", 0) !important;");
			$toolbar.css("box-shadow","none");
			$toolbar.addClass("navbar-ontop");
			return;
		}
		if ($(window).scrollTop() > endTransitionHeight){
			$toolbar.css("cssText","background-color: rgba(" + toolbarRgb + ", 0.85) !important;");
			$toolbar.css("box-shadow","");
			$toolbar.removeClass("navbar-ontop");
			return;
		}
		let transparency = (scrollTop - startTransitionHeight) / (endTransitionHeight - startTransitionHeight) * 0.85;
		$toolbar.css("cssText","background-color: rgba(" + toolbarRgb + ", " + transparency + ") !important;");
		$toolbar.css("box-shadow","");
		$toolbar.removeClass("navbar-ontop");
	}
	changeToolbarTransparency();
	$(window).scroll(function(){
		changeToolbarTransparency();
	});
}();


/*左侧栏随页面滚动浮动*/
!function(){
	let $leftbarPart1 = $('#leftbar_part1');
	let $leftbarPart2 = $('#leftbar_part2');
	function changeLeftbarStickyStatus(){
		if( $('#leftbar_part1').offset().top + $('#leftbar_part1').outerHeight() + 10 - $(window).scrollTop() <= 90 ){
			//滚动条在页面中间浮动状态
			$leftbarPart2.addClass('sticky');
		}else{
			//滚动条在顶部 不浮动状态
			$leftbarPart2.removeClass('sticky');
		}
	}
	changeLeftbarStickyStatus();
	$(window).scroll(function(){
		changeLeftbarStickyStatus();
	});
	$(window).resize(function(){
		changeLeftbarStickyStatus();
	});
}();


/*浮动按钮栏相关 (回顶等)*/
!function(){
	let $fabs = $('#float_action_buttons');
	let $backToTopBtn = $('#fab_back_to_top');
	let $toggleSidesBtn = $('#fab_toggle_sides');
	let $toggleDarkmode = $('#fab_toggle_darkmode');

	let $readingProgressBar = $('#fab_reading_progress_bar');
	let $readingProgressDetails = $('#fab_reading_progress_details');

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

	function toggleDarkmode(){
		$("html").toggleClass("darkmode");
		if ($("html").hasClass("darkmode")){
			$('#fab_toggle_darkmode .btn-inner--icon').html("<i class='fa fa-lightbulb-o'></i>");
			localStorage['Argon_Enable_Dark_Mode'] = "true";
		}else{
			$('#fab_toggle_darkmode .btn-inner--icon').html("<i class='fa fa-moon-o'></i>");
			localStorage['Argon_Enable_Dark_Mode'] = "false";
		}
		$(window).trigger("scroll");
	}
	if (localStorage['Argon_Enable_Dark_Mode'] == "true"){
		toggleDarkmode();
	}
	$toggleDarkmode.on("click" , function(){
		toggleDarkmode();
	});
	
	if (localStorage['Argon_Fabs_Floating_Status'] == "left"){
		$fabs.addClass("fabs-float-left");
	}
	$toggleSidesBtn.on("click" , function(){
		$fabs.addClass("fabs-unloaded");
		setTimeout(function(){
			$fabs.toggleClass("fabs-float-left");
			if ($fabs.hasClass("fabs-float-left")){
				localStorage['Argon_Fabs_Floating_Status'] = "left";
			}else{
				localStorage['Argon_Fabs_Floating_Status'] = "right";
			}
			$fabs.removeClass("fabs-unloaded");
		} , 300);
	});
	function changeFabDisplayStatus(){
		//阅读进度
		let readingProgress = $(window).scrollTop() / ($(document).height() - $(window).height());
		$readingProgressDetails.html((readingProgress * 100).toFixed(0) + "%");
		$readingProgressBar.css("width" , (readingProgress * 100).toFixed(0) + "%");
		//是否显示回顶
		if ($(window).scrollTop() >= 400 || readingProgress >= 0.5){
			$backToTopBtn.removeClass("fab-hidden");
		}else{
			$backToTopBtn.addClass("fab-hidden");
		}
	}
	changeFabDisplayStatus();
	$(window).scroll(function(){
		changeFabDisplayStatus();
	});
	$fabs.removeClass("fabs-unloaded");
}();

/*评论区 & 发送评论*/
!function(){
	//回复评论
	replying = false , replyID = 0;
	function reply(commentID){
		replying = true;
		replyID = commentID;
		$("#post_comment_reply_name").html($("#comment-" + commentID + " .comment-item-title")[0].innerHTML);
		$("#post_comment_reply_preview").html($("#comment-" + commentID + " .comment-item-text")[0].innerHTML);
		$("body,html").animate({
			scrollTop: $('#post_comment').offset().top - 100
		}, 300);
		$('#post_comment_reply_info').slideDown(600);
	}
	function cencelReply(){
		replying = false;
		replyID = 0;
		$('#post_comment_reply_info').slideUp(300);
	}
	$(document).on("click" , ".comment-reply" , function(){
		reply(this.getAttribute("data-id"));
	});
	$(document).on("click" , "#post_comment_reply_cencel" , function(){
		cencelReply();
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
	$(document).on("click" , "#post_comment_send" , function(){
		commentContent = $("#post_comment_content").val();
		commentName = $("#post_comment_name").val();
		commentEmail = $("#post_comment_email").val();
		commentLink = $("#post_comment_link").val();
		commentCaptcha = $("#post_comment_captcha").val();

		postID = $("#post_comment_post_id").val();
		commentCaptchaSeed = $("#post_comment_captcha_seed").val();

		isError = false;
		errorMsg = "";

		//检查表单合法性
		if (commentContent.match(/^\s*$/)){
			isError = true;
			errorMsg += "评论内容不能为空</br>";
		}
		if (commentName.match(/^\s*$/)){
			isError = true;
			errorMsg += "昵称不能为空</br>";
		}
		if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail)){
			isError = true;
			errorMsg += "邮箱格式错误</br>";
		}
		if (commentLink != "" && !(/https?:\/\//).test(commentLink)){
			isError = true;
			errorMsg += "网站格式错误 (不是 http(s):// 开头)</br>";
		}
		if (commentCaptcha == ""){
			isError = true;
			errorMsg += "验证码未输入";
		}
		if (commentCaptcha != "" && !(/^[0-9]+$/).test(commentCaptcha)){
			isError = true;
			errorMsg += "验证码格式错误";
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
		$("#post_comment_name").attr("disabled","disabled");
		$("#post_comment_email").attr("disabled","disabled");
		$("#post_comment_captcha").attr("disabled","disabled");
		$("#post_comment_link").attr("disabled","disabled");
		$("#post_comment_send").attr("disabled","disabled");
		$("#post_comment_reply_cencel").attr("disabled","disabled");
		$("#post_comment_send .btn-inner--icon").html("<i class='fa fa-spinner fa-spin'></i>");
		$("#post_comment_send .btn-inner--text").html("发送中");


		iziToast.show({
			title: '正在发送',
			message: "评论正在发送中...",
			class: 'shadow-sm',
			position: 'topRight',
			backgroundColor: '#5e72e4',
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
			url: "/wp-comments-post.php",
			data: {
				comment: commentContent,
				author: encodeURI(commentName),
				email: encodeURI(commentEmail),
				url: encodeURI(commentLink),
				comment_post_ID: postID,
				comment_parent: replyID,
				comment_captcha_seed: commentCaptchaSeed,
				comment_captcha: commentCaptcha
			},
			success: function(result){
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_name").removeAttr("disabled");
				$("#post_comment_email").removeAttr("disabled");
				$("#post_comment_captcha").removeAttr("disabled");
				$("#post_comment_link").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_reply_cencel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon").html("<i class='fa fa-send'></i>");
				$("#post_comment_send .btn-inner--text").html("发送");
				let vdom = document.createElement('html');
				vdom.innerHTML = result;
				let $vdom = $('<div></div>');
				$vdom.html(result);

				//判断是否有错误
				if (vdom.getElementsByTagName('body')[0].getAttribute("id") == "error-page"){
					$vbody = $('<div></div>');
					$vbody.html("<div id='body'>" + vdom.getElementsByTagName('body')[0].innerHTML + "</div>");
					$("a" , $vbody).remove();
					iziToast.destroy();
					iziToast.show({
						title: '评论发送失败',
						message: $.trim($("#body" , $vbody)[0].innerText),
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

				//发送成功，替换评论区
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
				replying = false;
				replyID = 0;
				$("#comments").html($("#comments" , $vdom)[0].innerHTML);
				$("#post_comment").html($("#post_comment" , $vdom)[0].innerHTML);
			},
			error: function(result){
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_name").removeAttr("disabled");
				$("#post_comment_email").removeAttr("disabled");
				$("#post_comment_captcha").removeAttr("disabled");
				$("#post_comment_link").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_reply_cencel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon").html("<i class='fa fa-send'></i>");
				$("#post_comment_send .btn-inner--text").html("发送");
				if (result.readyState != 4 || result.status == 0){
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
				let vdom = document.createElement('html');
				vdom.innerHTML = result.responseText;
				let $vdom = $('<div></div>');
				$vdom.html(result.responseText);
				if (vdom.getElementsByTagName('body')[0].getAttribute("id") == "error-page"){
					$vbody = $('<div></div>');
					$vbody.html("<div id='body'>" + vdom.getElementsByTagName('body')[0].innerHTML + "</div>");
					$("a" , $vbody).remove();
					iziToast.destroy();
					iziToast.show({
						title: '评论发送失败',
						message: $.trim($("#body" , $vbody)[0].innerText),
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
				}else{
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
			}
		});
	});
}();

/*URL 中 # 根据 ID 定位*/
!function(){
	$(window).on("hashchange" , function(){
		hash = window.location.hash;
		if (hash.length == 0){
			return;
		}
		if ($(hash).length == 0){
			return;
		}
		$("body,html").animate({
			scrollTop: $(hash).offset().top + 100
		}, 200);
	});
	$(window).trigger("hashchange");
}();

/*Pjax*/
var pjaxUrlChanged , pjaxLoading = false;
function pjaxLoadUrl(url , pushstate){
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
						throw "Different Agreements";
					}
				}
			}
			NProgress.set(0.618);
			$.ajax({
				url : url,
				type : "GET",
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
						$("title").html($("title" , $vdom)[0].innerHTML);
						$("#leftbar_part2_inner").html($("#leftbar_part2_inner" , $vdom)[0].innerHTML);
						$("#primary").html($("#primary" , $vdom)[0].innerHTML);
						$("#leftbar_part1_menu").html($("#leftbar_part1_menu" , $vdom)[0].innerHTML);
						$("#wpadminbar").html($("#wpadminbar" , $vdom).html());

						$("#content .page-infomation-card").remove();
						if ($(".page-infomation-card" , $vdom).length > 0){
							$("#content").prepend($(".page-infomation-card" , $vdom)[0].outerHTML);
						}

						
						NProgress.inc();
						
						if (pushstate == true){
							window.history.pushState({} , '' , url);
						}
						pjaxLoading = false;
						pjaxUrlChanged = true;
						
						if ($("script#mathjax_script" , $vdom).length > 0){
							MathJax.Hub.Typeset();
						}

						getGithubInfoCardContent();
						
						let scripts = $("#content script:not([no-pjax]):not(.no-pjax)" , $vdom);
						for (let script of scripts){
							eval(script.innerHTML);
						}

						NProgress.done();

						$(window).trigger("hashchange");
						$(window).trigger("scroll");

						if (typeof(window.pjaxLoaded) == "function"){
							window.pjaxLoaded();
						}
					}catch (err){
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
			});
		}catch(err){
			NProgress.done();
			pjaxLoading = false;
			pjaxUrlChanged = true;
			window.location.href = url;
		}
	}
}
$(document).ready(function(){
	$(document).on("click" , "a[href]:not([no-pjax]):not(.no-pjax):not([href^='#']):not([target='_blink'])" , function(){
		let url = this.getAttribute("href");
		$("body,html").animate({
			scrollTop: 0
		}, 600)
		pjaxLoadUrl(url , true);
		return false;
	});
	$(window).on("popstate" , function(){
		try{
			$("article img.zoomify.zoomed").zoomify('zoomOut');
		}catch(err){}
		setTimeout(function(){
			pjaxLoadUrl(document.location , false);
		},1);
		return false;
	});
});

/*Tags Dialog pjax 加载后自动关闭*/
$(document).on("click" , "#blog_tags .tag" , function(){
	$("#blog_tags button.close").trigger("click");
});
$(document).on("click" , "#blog_categories .tag" , function(){
	$("#blog_categories button.close").trigger("click");
});

/*侧栏手机适配*/
!function(){
	$(document).on("click" , "#fab_open_sidebar" , function(){
		$("html").addClass("leftbar-opened");
	});
	$(document).on("click" , "#sidebar_mask" , function(){
		$("html").removeClass("leftbar-opened");
	});
	$(document).on("click" , "#leftbar a[href]:not([no-pjax]):not([href^='#'])" , function(){
		$("html").removeClass("leftbar-opened");
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
					console.log(result);
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
		url : "/wp-admin/admin-ajax.php",
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