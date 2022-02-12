var $ = window.$;
import iziToast from 'izitoast';
import __ from '../i18n';
function toggleCommentPin(commentID, pinned){
	$("#comment_pin_comfirm_dialog .modal-title").html(pinned ? __("取消置顶评论") : __("置顶评论"));
	$("#comment_pin_comfirm_dialog .modal-body").html(pinned ? __("是否要取消置顶评论 #") + commentID + "?" : __("是否要置顶评论 #") + commentID + "?");
	$("#comment_pin_comfirm_dialog .btn-comfirm").html(__("确认")).attr("disabled", false);
	$("#comment_pin_comfirm_dialog .btn-dismiss").html(__("取消")).attr("disabled", false);
	$("#comment_pin_comfirm_dialog .btn-comfirm").off("click").on("click", function(){
		$("#comment_pin_comfirm_dialog .btn-dismiss").attr("disabled", true)
		$("#comment_pin_comfirm_dialog .btn-comfirm").attr("disabled", true).prepend(__(`<span class="btn-inner--icon" style="margin-right: 10px;"><i class="fa fa-spinner fa-spin"></i></span>`));
		$.ajax({
			type: 'POST',
			url: window.argonConfig.wp_path + "wp-admin/admin-ajax.php",
			dataType : "json",
			data: {
				action: "pin_comment",
				id: commentID,
				pinned: pinned ? "false" : "true"
			},
			success: function(result){
				$("#comment_pin_comfirm_dialog").modal('hide');
				if (result.status == "success"){
					if (pinned){
						$("#comment-" + commentID + " .comment-name .badge-pinned").remove();
						$("#comment-" + commentID + " .comment-unpin").removeClass("comment-unpin").addClass("comment-pin").html(__("置顶"));
					}else{
						$("#comment-" + commentID + " .comment-name").append(`<span class="badge badge-danger badge-pinned">${__("置顶")}</span>`);
						$("#comment-" + commentID + " .comment-pin").removeClass("comment-pin").addClass("comment-unpin").html(__("取消置顶"));
					}
					iziToast.show({
						title: pinned ? __("取消置顶成功") : __("置顶成功"),
						message: pinned ? __("该评论已取消置顶") : __("该评论已置顶"),
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
				} else {
					iziToast.show({
						title: pinned ? __("取消置顶失败") : __("置顶失败"),
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
				}
			},
			error: function(result){
				$("#comment_pin_comfirm_dialog").modal('hide');
				iziToast.show({
					title: pinned ? __("取消置顶失败") : __("置顶失败"),
					message: __("未知错误"),
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
	$("#comment_pin_comfirm_dialog").modal(null);
}

$(document).on("click", ".comment-pin, .comment-unpin", function(){
	toggleCommentPin(this.getAttribute("data-id"), !this.classList.contains("comment-pin"));
});