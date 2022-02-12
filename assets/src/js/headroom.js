import Headroom from "headroom.js";
document.addEventListener('DOMContentLoaded', () => {
	if (window.argonConfig.headroom == "true"){
		var headroom = new Headroom(document.querySelector("body"),{
			"tolerance" : {
				up : 0,
				down : 0
			},
			"offset": 0,
				"classes": {
				"initial": "with-headroom",
				"pinned": "headroom---pinned",
				"unpinned": "headroom---unpinned",
				"top": "headroom---top",
				"notTop": "headroom---not-top",
				"bottom": "headroom---bottom",
				"notBottom": "headroom---not-bottom",
				"frozen": "headroom---frozen"
			}
		}).init();
	}
});