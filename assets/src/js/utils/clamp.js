import $clamp from 'clamp-js';
var $ = window.$;
export const clampInit = () => {
	$(".clamp").each(function(index, dom) {
		$clamp(dom, {clamp: dom.getAttribute("clamp-line")});
	});
}
clampInit();