import * as $ from 'jquery';

/*根据滚动高度改变顶栏透明度*/
document.addEventListener("DOMContentLoaded", () => {
	let toolbar = document.getElementById("navbar-main");
	let $bannerContainer = $("#banner_container_main");
	let $content = $("#content");

	let startTransitionHeight;
	let endTransitionHeight;
	let maxOpacity = 0.85;

	startTransitionHeight = $bannerContainer.offset().top - 75;
	endTransitionHeight = $content.offset().top - 75;

	$(window).resize(function(){
		startTransitionHeight = $bannerContainer.offset().top - 75;
		endTransitionHeight = $content.offset().top - 75;
	});

	function changeToolbarTransparency(){
		let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if (scrollTop < startTransitionHeight){
			toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), 0)', 'important');
			toolbar.style.setProperty('box-shadow', 'none');
			if (argonConfig.toolbar_blur){
				toolbar.style.setProperty('backdrop-filter', 'blur(0px)');
			}
			toolbar.classList.add("navbar-ontop");
			return;
		}
		if (scrollTop > endTransitionHeight){
			toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), ' + maxOpacity + ')', 'important');
			toolbar.style.setProperty('box-shadow', '');
			if (argonConfig.toolbar_blur){
				toolbar.style.setProperty('backdrop-filter', 'blur(16px)');
			}
			toolbar.classList.remove("navbar-ontop");
			return;
		}
		let transparency = (scrollTop - startTransitionHeight) / (endTransitionHeight - startTransitionHeight) * maxOpacity;
		toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), ' + transparency, 'important');
		toolbar.style.setProperty('box-shadow', '');
		if (argonConfig.toolbar_blur){
			if ((scrollTop - startTransitionHeight) / (endTransitionHeight - startTransitionHeight) > 0.3){
				toolbar.style.setProperty('backdrop-filter', 'blur(16px)');
			}else{
				toolbar.style.setProperty('backdrop-filter', 'blur(0px)');
			}
		}
		toolbar.classList.remove("navbar-ontop");
	}
	function changeToolbarOnTopClass(){
		let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if (scrollTop < 30){
			toolbar.classList.add("navbar-no-blur");
		}else{
			toolbar.classList.remove("navbar-no-blur");
		}
	}
	if ($("html").hasClass("no-banner")) {
		changeToolbarOnTopClass();
		document.addEventListener("scroll", changeToolbarOnTopClass, {passive: true});
		return;
	}
	if (argonConfig.headroom == "absolute") {
		toolbar.classList.add("navbar-ontop");
		return;
	}
	if ($("html").hasClass("toolbar-blur")) {
		argonConfig.toolbar_blur = true;
		maxOpacity = 0.65;
	}else{
		argonConfig.toolbar_blur = false;
	}
	changeToolbarTransparency();
	document.addEventListener("scroll", changeToolbarTransparency, {passive: true});
});