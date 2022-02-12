var $ = window.$;

//显示/隐藏额外输入框 (评论者网站)
$(document).on("click" , "#post_comment_toggle_extra_input" , function(){
	$("#post_comment").toggleClass("show-extra-input");
	if ($("#post_comment").hasClass("show-extra-input")){
		$("#post_comment_extra_input").slideDown(300, 'easeOutCirc');
	}else{
		$("#post_comment_extra_input").slideUp(300, 'easeOutCirc');
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