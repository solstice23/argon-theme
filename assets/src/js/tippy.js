var $ = window.$;
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light.css';
import 'tippy.js/themes/light-border.css';
import 'tippy.js/themes/material.css';
import 'tippy.js/themes/translucent.css';
export const tippyInit = (root = document) => {
	tippy(Array.from(root.querySelectorAll('sup.reference[data-content]')).filter(reference => !reference._tippy), {
		content: (reference) => reference.getAttribute('data-content'),
		allowHTML: true,
		interactive: true,theme: 'light scroll-y',
		delay: [100, 250],
		animation: 'fade'
	});
}
document.addEventListener('DOMContentLoaded', function() {
	tippyInit();
});