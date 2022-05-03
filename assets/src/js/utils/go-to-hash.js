var $ = window.$;
export const gotoHash = (hash, durtion, easing = 'easeOutExpo') => {
	if (hash.length == 0){
		return;
	}
	if ($(hash).length == 0){
		return;
	}
	if (durtion == null){
		durtion = 200;
	}
	$("body,html").stop().animate({
		scrollTop: $(hash).offset().top - 80
	}, durtion, easing);
}
export const getHash = (url) => {
	return url.substring(url.indexOf('#'));
}
$(window).on("hashchange" , function(){
	gotoHash(window.location.hash);
});
window.addEventListener('DomContentLoaded', function(){
	$(window).trigger("hashchange");
});