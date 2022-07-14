var $ = window.$;
require('../libs/jquery-lazyload/lazyload')
export const lazyloadInit = () => {
	$(".comment-item-text .comment-sticker.lazyload").lazyload(Object.assign(window.argonConfig.lazyload, {load: function(){$(this).removeClass("lazyload")}}));
	if (window.argonConfig.lazyload == false){
		return;
	}
	if (window.argonConfig.lazyload.effect == "none"){
		delete window.argonConfig.lazyload.effect;
	}
	$("article img.lazyload:not(.lazyload-loaded) , .related-post-thumbnail.lazyload:not(.lazyload-loaded) , .shuoshuo-preview-container img.lazyload:not(.lazyload-loaded)").lazyload(
		Object.assign(window.argonConfig.lazyload, {
			load: function () {
				$(this).addClass("lazyload-loaded");
				$(this).parent().removeClass("lazyload-container-unload");
			}
		})
	);
	$(".post-thumbnail.lazyload:not(.lazyload-loaded)").lazyload(
		Object.assign({threshold: window.argonConfig.lazyload.threshold}, {
			load: function () {
				$(this).addClass("lazyload-loaded");
				$(this).parent().removeClass("lazyload-container-unload");
				window.waterflowInit();
			}
		})
	);
}
document.addEventListener("DOMContentLoaded", lazyloadInit);