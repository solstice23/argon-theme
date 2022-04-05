var $ = window.$;
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light.css';
import 'tippy.js/themes/light-border.css';
import 'tippy.js/themes/material.css';
import 'tippy.js/themes/translucent.css';
export const tippyInit = () => {
	tippy('sup.reference[data-content]:not(.tippy-initialized)', {
		content: (reference) => reference.getAttribute('data-content'),
		allowHTML: true,
		interactive: true,theme: 'light scroll-y',
		delay: [100, 250],
		animation: 'fade'
	});
	//$("sup.reference[data-content]:not(.tippy-initialized)").addClass("tippy-initialized");
}
document.addEventListener('DOMContentLoaded', function() {
	tippyInit();
});