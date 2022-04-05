var $ = window.$;
import tippy, {animateFill} from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light.css';
//import 'tippy.js/themes/light-border.css';
//import 'tippy.js/themes/material.css';
//import 'tippy.js/themes/translucent.css';
import 'tippy.js/dist/backdrop.css';
import './css/tippy.js/backdrop-override.scss';
import 'tippy.js/animations/shift-away.css';

const isJson = (str) => {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

const tooltips = () => {
	tippy('[tooltip]', {
		content: (dom) => {
			const attr = dom.getAttribute('tooltip').replace(/\'/g, '"');
			if (isJson(attr)){
				// [[className, content], ...]
				return JSON.parse(attr).map((item) => {
					return `<span class="${item[0]}">${item[1]}</span>`;
				}).join('');
			}
			return dom.getAttribute('tooltip')
		},
		appendTo: 'parent',
		offset: [0, 5],
		allowHTML: true,
		interactive: false,
		animateFill: true,
		animation: 'fade',
		plugins: [animateFill],
	});
}

const footnotes = () => {
	tippy('sup.reference[data-content]', {
		content: (reference) => reference.getAttribute('data-content'),
		allowHTML: true,
		interactive: true,
		theme: 'light scroll-y',
		delay: [100, 250],
		animation: 'fade'
	});
}

export const tippyInit = () => {
	tooltips();
	footnotes();
}

document.addEventListener('DOMContentLoaded', function() {
	tippyInit();
});

export const removeAllTippies = () => {
	[...document.querySelectorAll("*[data-tippy-root]")].forEach(node => {
		node.parentElement.removeChild(node);
	});
}