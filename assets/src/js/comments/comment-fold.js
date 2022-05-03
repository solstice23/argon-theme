var $ = window.$;
export const foldLongComments = () => {
	if (window.argonConfig.fold_long_comments == false){
		return;
	}
	$(".comment-item-inner").each(function(){
		if ($(this).hasClass("comment-unfolded")){
			return;
		}
		if (this.clientHeight > 800){
			$(this).addClass("comment-folded");
			$(this).append("<div class='show-full-comment'><button><i class='fa fa-angle-down' aria-hidden='true'></i> " + __("展开") + "</button></div>");
		}
	});
}
foldLongComments();
$(document).on("click" , ".show-full-comment" , function(){
	$(this).parent().removeClass("comment-folded").addClass("comment-unfolded");
});