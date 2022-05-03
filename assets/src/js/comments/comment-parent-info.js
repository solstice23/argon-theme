var $ = window.$;
$(document).on("mouseenter", ".comment-parent-info", function(){
	$("#comment-" + this.getAttribute("data-parent-id")).addClass("highlight");
});
$(document).on("mouseleave", ".comment-parent-info", function(){
	$("#comment-" + this.getAttribute("data-parent-id")).removeClass("highlight");
});