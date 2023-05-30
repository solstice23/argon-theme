var $ = window.$;

/*搜索*/
const searchPosts = (word) => {
	$.pjax({
		url: argonConfig.wp_path + "?s=" + encodeURI(word)
	});
}
/*顶栏搜索*/
$(document).on("click" , "#navbar_search_input_container" , function(){
	$(this).addClass("open");
	$("#navbar_search_input").focus();
});
$(document).on("blur" , "#navbar_search_input_container" , function(){
	$(this).removeClass("open");
});
$(document).on("keydown" , "#navbar_search_input_container #navbar_search_input" , function(e){
	if (e.keyCode != 13){
		return;
	}
	let word = $(this).val();
	if (word == ""){
		$("#navbar_search_input_container").blur();
		return;
	}
	let scrolltop = $(document).scrollTop();
	searchPosts(word);
});
/*顶栏搜索 (Mobile)*/
$(document).on("keydown" , "#navbar_search_input_mobile" , function(e){
	if (e.keyCode != 13){
		return;
	}
	let word = $(this).val();
	$("#navbar_global .collapse-close button").click();
	if (word == ""){
		return;
	}
	let scrolltop = $(document).scrollTop();
	searchPosts(word);
});
/*侧栏搜索*/
$(document).on("click" , "#leftbar_search_container" , function(){
	$(".leftbar-search-button").addClass("open");
	$("#leftbar_search_input").removeAttr("readonly").focus();
	$("#leftbar_search_input").focus();
	$("#leftbar_search_input").select();
	return false;
});
$(document).on("blur" , "#leftbar_search_container" , function(){
	$(".leftbar-search-button").removeClass("open");
	$("#leftbar_search_input").attr("readonly", "readonly");
});
$(document).on("keydown" , "#leftbar_search_input" , function(e){
	if (e.keyCode != 13){
		return;
	}
	let word = $(this).val();
	if (word == ""){
		$("#leftbar_search_container").blur();
		return;
	}
	$("html").removeClass("leftbar-opened");
	searchPosts(word);
});