var $ = window.$;
$(document).on("click" , "#fabtn_open_sidebar, #open_sidebar" , function(){
	$("html").addClass("leftbar-opened");
});
$(document).on("click" , "#sidebar_mask" , function(){
	$("html").removeClass("leftbar-opened");
});
$(document).on("click" , "#leftbar a[href]:not([no-pjax]):not([href^='#'])" , function(){
	$("html").removeClass("leftbar-opened");
});
$(document).on("click" , "#navbar_global.show .navbar-nav a[href]:not([no-pjax]):not([href^='#'])" , function(){
	$("#navbar_global .navbar-toggler").click();
});
$(document).on("click" , "#navbar_global.show #navbar_search_btn_mobile" , function(){
	$("#navbar_global .navbar-toggler").click();
});
