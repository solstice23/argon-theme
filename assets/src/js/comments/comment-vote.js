var $ = window.$;
import __ from '../i18n';
import iziToast from 'izitoast';
import 'izitoast/dist/css/iziToast.min.css';
$(document).on("click" , ".comment-upvote" , function(){
	$this = $(this);
	ID = $this.attr("data-id");
	$this.addClass("comment-upvoting");
	$.ajax({
		url : window.argonConfig.wp_path + "wp-admin/admin-ajax.php",
		type : "POST",
		dataType : "json",
		data : {
			action: "upvote_comment",
			comment_id : ID,
		},
		success : function(result){
			$this.removeClass("comment-upvoting");
			if (result.status == "success"){
				$(".comment-upvote-num" , $this).html(result.total_upvote);
				$this.addClass("upvoted");
			}else{
				$(".comment-upvote-num" , $this).html(result.total_upvote);
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
			$this.removeClass("comment-upvoting");
			iziToast.show({
				title: __("点赞失败"),
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