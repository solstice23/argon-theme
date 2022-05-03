import { gotoHash } from './utils/go-to-hash';
var $ = window.$;
const classInit = () => {
	if ($("#main").hasClass("article-list-home")){
		if (!$("html").hasClass("is-home")){
			$("html").addClass("is-home");
			$("html").trigger("resize");
		}
	}else{
		if ($("html").hasClass("is-home")){
			$("html").removeClass("is-home");
			$("html").trigger("resize");
		}
	}
}
const changeWidgetsDisplayStatus = () => {
	let scrollTop = $(window).scrollTop();
	if (scrollTop >= window.outerHeight * 0.2){
		$("#float_action_buttons").removeClass("hidden");
	}else{
		$("#float_action_buttons").addClass("hidden");
	}
	if (scrollTop >= window.outerHeight * 0.6){
		$(".cover-scroll-down").addClass("hidden");
	}else{
		$(".cover-scroll-down").removeClass("hidden");
	}
}
export const bannerCoverInit = () => {
	if ($("html").hasClass("banner-as-cover")){
		classInit();
		new MutationObserver(function(mutations, observer){
			classInit();
		}).observe(document.querySelector("#primary"), {
			'childList': true
		});
		$(".cover-scroll-down").on("click" , function(){
			gotoHash("#content", 600, 'easeOutCirc');
			$("#content").focus();
		});
		
		changeWidgetsDisplayStatus();
	
		$(window).scroll(function(){
			changeWidgetsDisplayStatus();
		});
		$(window).resize(function(){
			changeWidgetsDisplayStatus();
		});
	}
};
document.addEventListener("DOMContentLoaded", function(){
	bannerCoverInit();
});