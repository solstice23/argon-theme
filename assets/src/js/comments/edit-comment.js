import { cancelReply } from './reply-comment';

var $ = window.$;

global.argon_editing = false;
global.argon_editID = 0;
function edit(commentID){
	cancelReply();
	global.argon_editing = true;
	global.argon_editID = commentID;
	$('#post_comment').addClass("editing");
	$("#post_comment_content").val($("#comment-" + global.argon_editID + " .comment-item-source").text());
	$("#post_comment_content").trigger("change");
	if ($("#comment-" + global.argon_editID).data("use-markdown") == true && document.getElementById("comment_post_use_markdown") != null){
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
	}, 500, 'easeOutCirc');
	$("#post_comment_content").focus();
}
export function cancelEdit(clear){
	global.argon_editing = false;
	global.argon_editID = 0;
	$("#post_comment").removeClass("post-comment-force-privatemode-on post-comment-force-privatemode-off");
	if (clear == true) $("#post_comment_content").val("");
	$("#post_comment_content").trigger("change");
	$('#post_comment').removeClass("editing");
}
$(document).on("click", ".comment-edit", function(){
	edit(this.getAttribute("data-id"));
});
$(document).on("click", "#post_comment_edit_cancel", function(){
	$("body,html").animate({
		scrollTop: $("#comment-" + global.argon_editID).offset().top - 100
	}, 400, 'easeOutCirc');
	cancelEdit(true);
});
$(document).on("pjax:click", function(){
	cancelEdit(true);
});