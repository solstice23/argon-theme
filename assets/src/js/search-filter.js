var $ = window.$;
$(document).on("change" , ".search-filter" , function(e){
	if (window.pjaxLoading){
		$(this).prop("checked", !$(this).prop("checked"));
		e.preventDefault();
		return;
	}
	window.pjaxLoading = true;
	let postTypes = [];
	$(".search-filter:checked").each(function(){
		postTypes.push($(this).attr("name"));
	});
	if (postTypes.length == 0){
		postTypes = ["none"];
	}
	let url = new URL(document.location.href);
	url.searchParams.set("post_type", postTypes.join(","));
	console.log(url.pathname);
	url.pathname = url.pathname.replace(/\/page\/\d+\/?$/, '');
	$.pjax({
		url: url.href
	});
});