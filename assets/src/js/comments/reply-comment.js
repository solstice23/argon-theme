import { cancelEdit } from './edit-comment';

var $ = window.$;
window.replying = false;
window.replyID = 0;
function reply(commentID){
	cancelEdit(false);
	window.replying = true;
	window.replyID = commentID;
	$("#post_comment_reply_name").html($("#comment-" + commentID + " .comment-item-title > .comment-name")[0].innerHTML);
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
	}, 500, 'easeOutCirc');
	$('#post_comment_reply_info').slideDown(500, 'easeOutCirc');
	setTimeout(function(){
		$("#post_comment_content").focus();
	}, 500);
}
export function cancelReply(){
	window.replying = false;
	window.replyID = 0;
	$('#post_comment_reply_info').slideUp(300, 'easeOutCirc');
	$("#post_comment").removeClass("post-comment-force-privatemode-on post-comment-force-privatemode-off");
}
$(document).on("click" , ".comment-reply" , function(){
	reply(this.getAttribute("data-id"));
});
$(document).on("click pjax:click" , "#post_comment_reply_cancel" , function(){
	cancelReply();
});
$(document).on("pjax:click" , function(){
	window.replying = false;
	window.replyID = 0;
	$('#post_comment_reply_info').css("display", "none");
	$("#post_comment").removeClass("post-comment-force-privatemode-on post-comment-force-privatemode-off");
});