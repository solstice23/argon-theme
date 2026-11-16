var $ = window.$;
import '../libs/zoomify/zoomify.css'
require('../libs/zoomify/zoomify.js');
export const zoomifyInit = (root = document) => {
	if (window.argonConfig.zoomify == false){
		return;
	}
	$("article img", root).zoomify(window.argonConfig.zoomify);
}
zoomifyInit();