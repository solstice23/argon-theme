import __ from './i18n';
import {waterflowInit} from './waterflow';
var $ = window.$;
export const foldLongShuoshuo = () => {
	if (window.argonConfig.fold_long_shuoshuo == false){
		return;
	}
	$("#main .shuoshuo-foldable > .shuoshuo-content").each(function(){
		if ($(this).hasClass("shuoshuo-unfolded")){
			return;
		}
		if (this.clientHeight > 400){
			$(this).addClass("shuoshuo-folded");
			$(this).append("<div class='show-full-shuoshuo'><button class='btn btn-outline-primary'><i class='fa fa-angle-down' aria-hidden='true'></i> " + __("展开") + "</button></div>");
		}
	});
	waterflowInit();
}
document.addEventListener("DOMContentLoaded", () => {
	foldLongShuoshuo();
});
$(document).on("click" , ".show-full-shuoshuo" , function(){
	$(this).parent().removeClass("shuoshuo-folded").addClass("shuoshuo-unfolded");
	waterflowInit(true);
});
