import __ from '../i18n'
var $ = window.$;
function showCommentEditHistory(id){
	let requestID = parseInt(new Date().getTime());
	$("#comment_edit_history").data("request-id", requestID);
	$("#comment_edit_history .modal-title").html(__("评论 #") + id + " " + __("的编辑记录"));
	$("#comment_edit_history .modal-body").html("<div class='comment-history-loading'><span class='spinner-border text-primary'></span><span style='display: inline-block;transform: translateY(-4px);margin-left: 15px;font-size: 18px;'>加载中</span></div>");
	$("#comment_edit_history").modal(null);
	$.ajax({
		type: 'POST',
		url: window.argonConfig.wp_path + "wp-admin/admin-ajax.php",
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
			$("#comment_edit_history .modal-body").html(__("加载失败"));
			$("#comment_edit_history .modal-body").fadeIn(300);
		}
	});
}
$(document).on("click" , ".comment-edited.comment-edithistory-accessible" , function(){
	showCommentEditHistory($(this).parent().parent().parent().parent().data("id"));
});