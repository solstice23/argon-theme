import { gotoHash } from './utils/go-to-hash';
var $ = window.$;
if ($("html").hasClass("banner-as-cover")){
	function classInit(){
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
	$fabs = $("#float_action_buttons");
	$coverScrollDownBtn = $(".cover-scroll-down");
	function changeWidgetsDisplayStatus(){
		let scrollTop = $(window).scrollTop();
		if (scrollTop >= window.outerHeight * 0.2){
			$fabs.removeClass("hidden");
		}else{
			$fabs.addClass("hidden");
		}
		if (scrollTop >= window.outerHeight * 0.6){
			$coverScrollDownBtn.addClass("hidden");
		}else{
			$coverScrollDownBtn.removeClass("hidden");
		}
	}
	changeWidgetsDisplayStatus();
	$(window).scroll(function(){
		changeWidgetsDisplayStatus();
	});
	$(window).resize(function(){
		changeWidgetsDisplayStatus();
	});
}