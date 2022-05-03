import 'izitoast/dist/css/iziToast.min.css';
import iziToast from 'izitoast';
import __ from '../i18n';
import { calcHumanTimesOnPage } from '../utils/time-calculation';
import { cancelReply } from './reply-comment';

var $ = window.$;

//发送评论
function postComment(){
	let commentContent = $("#post_comment_content").val();
	let commentName = $("#post_comment_name").val();
	let commentEmail = $("#post_comment_email").val();
	let commentLink = $("#post_comment_link").val();
	let commentCaptcha = $("#post_comment_captcha").val();
	let useMarkdown = false;
	let privateMode = false;
	let mailNotice = false;
	if ($("#comment_post_use_markdown").length > 0){
		useMarkdown = $("#comment_post_use_markdown")[0].checked;
	}
	if ($("#comment_post_privatemode").length > 0){
		privateMode = $("#comment_post_privatemode")[0].checked;
	}
	if ($("#comment_post_mailnotice").length > 0){
		mailNotice = $("#comment_post_mailnotice")[0].checked;
	}

	let postID = $("#post_comment_post_id").val();
	let commentCaptchaSeed = $("#post_comment_captcha_seed").val();

	let isError = false;
	let errorMsg = "";

	//检查表单合法性
	if (commentContent.match(/^\s*$/)){
		isError = true;
		errorMsg += __("评论内容不能为空") + "</br>";
	}
	if (!$("#post_comment").hasClass("no-need-name-email")){
		if (commentName.match(/^\s*$/)){
			isError = true;
			errorMsg += __("昵称不能为空") + "</br>";
		}
		if ($("#post_comment").hasClass("enable-qq-avatar")){
			if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail) && !(/^[1-9][0-9]{4,10}$/).test(commentEmail)){
				isError = true;
				errorMsg += __("邮箱或 QQ 号格式错误") + "</br>";
			}
		}else{
			if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail)){
				isError = true;
				errorMsg += __("邮箱格式错误") + "</br>";
			}
		}
	}else{
		if (commentEmail.length || (document.getElementById("comment_post_mailnotice") != null && document.getElementById("comment_post_mailnotice").checked == true)){
			if ($("#post_comment").hasClass("enable-qq-avatar")){
				if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail) && !(/^[1-9][0-9]{4,10}$/).test(commentEmail)){
					isError = true;
					errorMsg += __("邮箱或 QQ 号格式错误") + "</br>";
				}
			}else{
				if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail)){
					isError = true;
					errorMsg += __("邮箱格式错误") + "</br>";
				}
			}
		}
	}
	if (commentLink != "" && !(/https?:\/\//).test(commentLink)){
		isError = true;
		errorMsg += __("网站格式错误 (不是 http(s):// 开头)") + "</br>";
	}
	if (!$("#post_comment").hasClass("no-need-captcha")){
		if (commentCaptcha == ""){
			isError = true;
			errorMsg += __("验证码未输入");
		}
		if (commentCaptcha != "" && !(/^[0-9]+$/).test(commentCaptcha)){
			isError = true;
			errorMsg += __("验证码格式错误");
		}
	}
	if (isError){
		iziToast.show({
			title: __("评论格式错误"),
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
	$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html(__("发送中"));

	iziToast.show({
		title: __("正在发送"),
		message: __("评论正在发送中..."),
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
		url: window.argonConfig.wp_path + "wp-admin/admin-ajax.php",
		dataType : "json",
		data: {
			action: "ajax_post_comment",
			comment: commentContent,
			author: commentName,
			email: commentEmail,
			url: commentLink,
			comment_post_ID: postID,
			comment_parent: window.replyID,
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
			$("#post_comment_send .btn-inner--icon.hide-on-comment-editing").html("<i class='fa fa-paper-plane'></i>");
			$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html(__("发送"));
			$("#post_comment").removeClass("show-extra-input post-comment-force-privatemode-on post-comment-force-privatemode-off");
			if (!result.isAdmin){
				$("#post_comment_captcha").removeAttr("disabled");
			}

			//判断是否有错误
			if (result.status == "failed"){
				iziToast.destroy();
				iziToast.show({
					title: __("评论发送失败"),
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
				title: __("发送成功"),
				message: __("您的评论已发送"),
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
			result.html = result.html.replace(/<img class='comment-sticker lazyload'(.*?)\/>/g, "").replace(/<(\/).noscript>/g, "");
			let parentID = result.parentID;
			if (parentID == "" || parentID == null){
				parentID = 0;
			}
			parentID = parseInt(parentID);
			if (parentID == 0){
				if ($("#comments > .card-body > ol.comment-list").length == 0){
					$("#comments > .card-body").html("<h2 class='comments-title'><i class='fa fa-comments'></i> " + __("评论") + "</h2><ol class='comment-list'></ol>");
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
			calcHumanTimesOnPage();
			//复位评论表单
			cancelReply();
			$("#post_comment_content").val("");
			$("#post_comment_captcha_seed").val(result.newCaptchaSeed);
			$("#post_comment_captcha + style").html(".post-comment-captcha-container:before{content: '" + result.newCaptcha + "';}");
			$("#post_comment_captcha").val(result.newCaptchaAnswer);
			$("body,html").animate({
				scrollTop: $("#comment-" + result.id).offset().top - 100
			}, 500, 'easeOutExpo');
		},
		error: function(result){
			$("#post_comment").removeClass("sending");
			$("#post_comment_content").removeAttr("disabled");
			$("#post_comment_name").removeAttr("disabled");
			$("#post_comment_email").removeAttr("disabled");
			$("#post_comment_link").removeAttr("disabled");
			$("#post_comment_send").removeAttr("disabled");
			$("#post_comment_reply_cancel").removeAttr("disabled");
			$("#post_comment_send .btn-inner--icon.hide-on-comment-editing").html("<i class='fa fa-paper-plane'></i>");
			$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html(__("发送"));
			$("#post_comment").removeClass("show-extra-input post-comment-force-privatemode-on post-comment-force-privatemode-off");
			if (!result.isAdmin){
				$("#post_comment_captcha").removeAttr("disabled");
			}

			iziToast.destroy();
			iziToast.show({
				title: __("评论发送失败"),
				message: __("未知原因"),
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
	let commentContent = $("#post_comment_content").val();
	let isError = false;
	let errorMsg = "";
	if (commentContent.match(/^\s*$/)){
		isError = true;
		errorMsg += __("评论内容不能为空") + "</br>";
	}
	if (isError){
		iziToast.show({
			title: __("评论格式错误"),
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
	$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html(__("编辑中"));

	iziToast.show({
		title: __("正在编辑"),
		message: __("评论正在编辑中..."),
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
		url: window.argonConfig.wp_path + "wp-admin/admin-ajax.php",
		dataType : "json",
		data: {
			action: "user_edit_comment",
			comment: commentContent,
			id: global.argon_editID
		},
		success: function(result){
			$("#post_comment_content").removeAttr("disabled");
			$("#post_comment_send").removeAttr("disabled");
			$("#post_comment_edit_cancel").removeAttr("disabled");
			$("#post_comment_send .btn-inner--icon.hide-on-comment-not-editing").html("<i class='fa fa-pencil'></i>");
			$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html(__("编辑"));

			//判断是否有错误
			if (result.status == "failed"){
				iziToast.destroy();
				iziToast.show({
					title: __("评论编辑失败"),
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
			result.new_comment = result.new_comment.replace(/<img class='comment-sticker lazyload'(.*?)\/>/g, "").replace(/<(\/).noscript>/g, "");		
			$("#comment-" + global.argon_editID + " .comment-item-text").html(result.new_comment);
			$("#comment-" + global.argon_editID + " .comment-item-source").html(result.new_comment_source);
			if ($("#comment-" + global.argon_editID + " .comment-info .comment-edited").length == 0){
				$("#comment-" + global.argon_editID + " .comment-info").prepend("<div class='comment-edited'><i class='fa fa-pencil' aria-hidden='true'></i>" + __("已编辑") + "</div>")
			}
			if (result.can_visit_edit_history){
				$("#comment-" + global.argon_editID + " .comment-info .comment-edited").addClass("comment-edithistory-accessible");
			}

			iziToast.destroy();
			iziToast.show({
				title: __("编辑成功"),
				message: __("您的评论已编辑"),
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
				scrollTop: $("#comment-" + global.argon_editID).offset().top - 100
			}, 500, 'easeOutExpo');
			editing = false;
			global.argon_editID = 0;
			$("#post_comment_content").val("");
			$('#post_comment').removeClass("editing post-comment-force-privatemode-on post-comment-force-privatemode-off");
			$("#post_comment_content").trigger("change");
		},
		error: function(result){
			$("#post_comment_content").removeAttr("disabled");
			$("#post_comment_send").removeAttr("disabled");
			$("#post_comment_edit_cancel").removeAttr("disabled");
			$("#post_comment_send .btn-inner--icon.hide-on-comment-not-editing").html("<i class='fa fa-pencil'></i>");
			$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html(__("编辑"));
			if (result.readyState != 4 || result.status == 0){
				iziToast.destroy();
				iziToast.show({
					title: __("评论编辑失败"),
					message: __("未知原因"),
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