var $ = window.$;
$(document).on("click" , ".collapse-block .collapse-block-title" , function(){
	let block = $(this).parent();
	$(block).toggleClass("collapsed");
	let inner = $(".collapse-block-body", block);
	if (block.hasClass("collapsed")){
		inner.stop(true, false).slideUp(300, 'easeOutCirc');
	}else{
		inner.stop(true, false).slideDown(300, 'easeOutCirc');
	}
	$("html").trigger("scroll");
});
