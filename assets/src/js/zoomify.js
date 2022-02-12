var $ = window.$;
import '../libs/zoomify/zoomify.css'
require('../libs/zoomify/zoomify.js');
export const zoomifyInit = () => {
	if (window.argonConfig.zoomify == false){
		return;
	}
	$("article img").zoomify(window.argonConfig.zoomify);
}
zoomifyInit();