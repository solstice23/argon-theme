var $ = window.$;
import __ from './i18n';
import { Fancybox } from '@fancyapps/ui'
import "@fancyapps/ui/dist/fancybox.css";

Fancybox.defaults.infinite = false;
Fancybox.defaults.transitionEffect = "slide";
Fancybox.defaults.buttons = ["zoom", "fullScreen", "thumbs", "close"];
Fancybox.defaults.lang = window.argonConfig.language;
Fancybox.defaults.l10n = {
	CLOSE: __("关闭"),
	NEXT: __("下一张"),
	PREV: __("上一张"),
	ERROR: __("图片加载失败"),
	TOGGLE_SLIDESHOW: __("轮播模式"),
	TOGGLE_FULLSCREEN: __("全屏"),
	TOGGLE_THUMBS: __("缩略图"),
	TOGGLE_ZOOM: __("缩放")
};