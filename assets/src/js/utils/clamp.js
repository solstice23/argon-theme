import $clamp from 'clamp-js';
var $ = window.$;
export const clampInit = (root = document) => {
	$(".clamp", root).each(function(index, dom) {
		$clamp(dom, {clamp: dom.getAttribute("clamp-line")});
	});
}
clampInit();