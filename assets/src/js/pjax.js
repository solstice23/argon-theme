import '../../js/native-ui.js';
import NProgress from 'nprogress';
import { lazyloadInit } from './lazyload';
import { zoomifyInit } from './zoomify';
import { highlightJsRender } from './code-highlight';
import { panguInit } from './pangu';
import { tippyInit } from './tippy';
import { clampInit } from './utils/clamp';
import { getGithubInfoCardContent } from './shortcodes/github-card';
import { showPostOutdateToast } from './post-outdated-toast';
import { calcHumanTimesOnPage } from './utils/time-calculation';
import { foldLongComments } from './comments/comment-fold';
import { foldLongShuoshuo } from './shuoshuo-fold';
import { shareInit } from './share';


var $ = window.$;
var pjaxScrollTop = 0;
window.pjaxLoading = false;
$.pjax.defaults.timeout = 10000;
$.pjax.defaults.container = ['#primary', '#leftbar_part1_menu', '#leftbar_part2_inner', '.page-information-card-container', '#rightbar', '#wpadminbar', '#argon_banner_cover'];
$.pjax.defaults.fragment = ['#primary', '#leftbar_part1_menu', '#leftbar_part2_inner', '.page-information-card-container', '#rightbar', '#wpadminbar', '#argon_banner_cover'];
$(document).pjax("a[href]:not([no-pjax]):not(.no-pjax):not([target='_blank']):not([download]):not(.reference-link):not(.reference-list-backlink)")
.on('pjax:click', function(e, f, g){
	if (window.argonConfig.disable_pjax == true){
		e.preventDefault();
		return;
	}
}).on('pjax:afterGetContainers', function(e, f, g) {
	if (g.is("#main article.post-preview a.post-title")){
		let $card = $(g.parents("article.post-preview")[0]);
		let waterflowOn = false;
		if ($("#main").hasClass("waterflow")){
			waterflowOn = true;
			$card.css("transition", "all .5s ease");
		}
		$card.append("<div class='loading-css-animation'><div class='loading-dot loading-dot-1' ></div><div class='loading-dot loading-dot-2' ></div><div class='loading-dot loading-dot-3' ></div><div class='loading-dot loading-dot-4' ></div><div class='loading-dot loading-dot-5' ></div><div class='loading-dot loading-dot-6' ></div><div class='loading-dot loading-dot-7' ></div><div class='loading-dot loading-dot-8' ></div></div></div>");
		$card.addClass("post-pjax-loading");
		$("#main").addClass("post-list-pjax-loading");
		let offsetTop = $($card).offset().top - $("#main").offset().top;
		if ($("html").hasClass("is-home") && $("html").hasClass("banner-as-cover")){
			offsetTop = $($card).offset().top - window.outerHeight * 0.418;
		}
		$card.css("transform" , "translateY(-" + offsetTop + "px)");
		if (waterflowOn){
			$card.css("left", "10px");
			$card.css("width", "calc(100% - 20px)");
			$card.css("z-index", "3");
		}
		$("body,html").animate({
			scrollTop: 0
		}, 450);
	}
	pjaxScrollTop = 0;
	if ($("html").hasClass("banner-as-cover")){
		if (g.is(".page-link")){
			pjaxScrollTop = $("#content").offset().top - 80;
		}
	}
	f.scrollTo = Math.max(0, pjaxScrollTop);
});

window.argonInstallPjaxLifecycle($, {
	start: function(){
		window.pjaxLoading = true;
		NProgress.start();
		NProgress.set(0.618);
	},
	complete: function(){
		window.pjaxLoading = false;
		NProgress.done();
	},
	replace: function(primary, cached){
		lazyloadInit(primary);
		zoomifyInit(primary);
		highlightJsRender(primary);
		if (!cached) panguInit(primary);
		clampInit(primary);
		tippyInit(primary);
		if (!cached) getGithubInfoCardContent(primary);
		showPostOutdateToast();
		calcHumanTimesOnPage(primary);
		foldLongComments(primary);
		foldLongShuoshuo(primary);
		shareInit();
		$("html").trigger("resize");
	},
});


$(document).on("click" , "#blog_tags .tag" , function(){
	$("#blog_tags button.close").trigger("click");
});
$(document).on("click" , "#blog_categories .tag" , function(){
	$("#blog_categories button.close").trigger("click");
});
