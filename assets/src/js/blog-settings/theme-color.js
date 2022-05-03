import Pickr from '@simonwep/pickr'
import '../css/pickr/monolith.min.css';
import __ from '../i18n';
import * as colorCalc from '../utils/color-calculation';
import { getCookie, setCookie } from '../utils/cookies';
var $ = window.$;

document.addEventListener("DOMContentLoaded", () => {
if ($("meta[name='argon-enable-custom-theme-color']").attr("content") == 'true'){
	let themeColorPicker = new Pickr({
		el: '#theme-color-picker',
		container: 'body',
		theme: 'monolith',
		closeOnScroll: false,
		appClass: 'theme-color-picker-box',
		useAsButton: false,
		padding: 8,
		inline: false,
		autoReposition: true,
		sliders: 'h',
		disabled: false,
		lockOpacity: true,
		outputPrecision: 0,
		comparison: false,
		default: $("meta[name='theme-color']").attr("content"),
		swatches: ['#5e72e4', '#fa7298', '#009688', '#607d8b', '#2196f3', '#3f51b5', '#ff9700', '#109d58', '#dc4437', '#673bb7', '#212121', '#795547'],
		defaultRepresentation: 'HEX',
		showAlways: false,
		closeWithKey: 'Escape',
		position: 'top-start',
		adjustableNumbers: false,
		components: {
			palette: true,
			preview: true,
			opacity: false,
			hue: true,
			interaction: {
				hex: true,
				rgba: true,
				hsla: false,
				hsva: false,
				cmyk: false,
				input: true,
				clear: false,
				cancel: true,
				save: true
			}
		},
		strings: {
			'save': __('确定'),
			'clear': __('清除'),
			'cancel': __('恢复博客默认')
		}
	});
	themeColorPicker.on('change', instance => {
		updateThemeColor(pickrObjectToHEX(instance), true);
	})
	themeColorPicker.on('save', (color, instance) => {
		updateThemeColor(pickrObjectToHEX(instance._color), true);
		themeColorPicker.hide();
	})
	themeColorPicker.on('cancel', instance => {
		themeColorPicker.hide();
		themeColorPicker.setColor($("meta[name='theme-color-origin']").attr("content").toUpperCase());
		updateThemeColor($("meta[name='theme-color-origin']").attr("content").toUpperCase(), false);
		setCookie("argon_custom_theme_color", "", 0);
	});
}
});
function pickrObjectToHEX(color){
	let HEXA = color.toHEXA();
	return ("#" + HEXA[0] + HEXA[1] + HEXA[2]).toUpperCase();
}
function updateThemeColor(color, setcookie){
	let themecolor = color;
	let themecolor_rgbstr = colorCalc.hex2str(themecolor);
	let RGB = colorCalc.hex2rgb(themecolor);
	let HSL = colorCalc.rgb2hsl(RGB['R'], RGB['G'], RGB['B']);

	document.documentElement.style.setProperty('--themecolor', themecolor);
	document.documentElement.style.setProperty('--themecolor-R', RGB['R']);
	document.documentElement.style.setProperty('--themecolor-G', RGB['G']);
	document.documentElement.style.setProperty('--themecolor-B', RGB['B']);
	document.documentElement.style.setProperty('--themecolor-H', HSL['H']);
	document.documentElement.style.setProperty('--themecolor-S', HSL['S']);
	document.documentElement.style.setProperty('--themecolor-L', HSL['L']);


	if (colorCalc.rgb2gray(RGB['R'], RGB['G'], RGB['B']) < 50){
		$("html").addClass("themecolor-toodark");
	}else{
		$("html").removeClass("themecolor-toodark");
	}

	$("meta[name='theme-color']").attr("content", themecolor);
	$("meta[name='theme-color-rgb']").attr("content", themecolor_rgbstr);

	if (setcookie){
		setCookie("argon_custom_theme_color", themecolor, 365);
	}
}