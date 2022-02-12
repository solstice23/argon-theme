var $ = window.$;
function typeEffect($element, text, now, interval){
	if (now > text.length){
		setTimeout(function(){
			$element.removeClass("typing-effect");
		}, 1000);
		return;
	}
	$element[0].innerText = text.substring(0, now);
	setTimeout(function(){typeEffect($element, text, now + 1, interval)}, interval);
}
function startTypeEffect($element, text, interval){
	$element.addClass("typing-effect");
	$element.attr("style", "--animation-cnt: " + Math.ceil(text.length * interval / 1000));
	typeEffect($element, text, 1, interval);
}
!function(){
	if ($(".banner-title").data("interval") != undefined){
		let interval = $(".banner-title").data("interval");
		let $title = $(".banner-title-inner");
		let $subTitle = $(".banner-subtitle");
		startTypeEffect($title, $title.data("text"), interval);
		if (!$subTitle.length){
			return;
		}
		setTimeout(function(){startTypeEffect($subTitle, $subTitle.data("text"), interval);}, Math.ceil($title.data("text").length * interval / 1000) * 1000);
	}
}();