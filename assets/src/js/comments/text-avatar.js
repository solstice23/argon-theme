var $ = window.$;
function generateCommentTextAvatar(img){
	let emailHash = '';
	try{
		emailHash = img.attr("src").match(/([a-f\d]{32}|[A-F\d]{32})/)[0];
	}catch{
		emailHash = img.parent().parent().parent().find(".comment-name").text().trim();
		if (emailHash == '' || emailHash == undefined){
			emailHash = img.parent().find("*[class*='comment-author']").text().trim();
		}
	}
	let hash = 0;
	for (i in emailHash){
		hash = (hash * 233 + emailHash.charCodeAt(i)) % 16;
	}
	let colors = ['#e25f50', '#f25e90', '#bc67cb', '#9672cf', '#7984ce', '#5c96fa', '#7bdeeb', '#45d0e2', '#48b7ad', '#52bc89', '#9ace5f', '#d4e34a', '#f9d715', '#fac400', '#ffaa00', '#ff8b61', '#c2c2c2', '#8ea3af', '#a1877d', '#a3a3a3', '#b0b6e3', '#b49cde', '#c2c2c2', '#7bdeeb', '#bcaaa4', '#aed77f'];
	let text = $(".comment-name", img.parent().parent().parent()).text().trim()[0];
	if (text == '' || text == undefined){
		text = img.parent().find("*[class*='comment-author']").text().trim()[0];
	}
	let classList = img.attr('class') + " text-avatar";
	img.prop('outerHTML', '<div class="' + classList + '" style="background-color: ' + colors[hash] + ';">' + text + '</div>');
}
document.addEventListener("error", function(e){
	let img = $(e.target);
	if (!img.hasClass("avatar")){
		return;
	}
	generateCommentTextAvatar(img);
}, true);
export const refreshCommentTextAvatar = () => {
	$(".comment-item-avatar > img.avatar").each(function(index, img){
		if (!img.complete){
			return;
		}
		if (img.naturalWidth !== 0){
			return false;
		}
		generateCommentTextAvatar($(img));
	});
}
refreshCommentTextAvatar();
$(window).on("load", function(){
	refreshCommentTextAvatar();
});