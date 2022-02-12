var $ = window.$;
$(document).on("click", ".reference-link , .reference-list-backlink" , function(e){
	e.preventDefault();
	$target = $($(this).attr("href"));
	$("body,html").animate({
		scrollTop: $target.offset().top - document.body.clientHeight / 2 - 75
	}, 500, 'easeOutExpo')
	setTimeout(function(){
		if ($target.is("li")){
			$(".space", $target).focus();
		}else{
			$target.focus();
		}
	}, 1);
});