var $ = window.$;
import { gotoHash } from './utils/go-to-hash';

//浮动按钮栏
document.addEventListener("DOMContentLoaded", () => {
	const $fabtns = $('#float_action_buttons');
	const $backToTopBtn = $('#fabtn_back_to_top');
	const $toggleSidesBtn = $('#fabtn_toggle_sides');
	const $toggleDarkmode = $('#fabtn_toggle_darkmode');
	const $toggleAmoledMode = $('#blog_setting_toggle_darkmode_and_amoledarkmode');
	const $toggleBlogSettings = $('#fabtn_toggle_blog_settings_popup');
	const $goToComment = $('#fabtn_go_to_comment');
	const $readingProgressBtn = $('#fabtn_reading_progress');
	const $readingProgressBar = $('#fabtn_reading_progress_bar');
	const $readingProgressDetails = $('#fabtn_reading_progress_details');
	$backToTopBtn.on("click" , function(){
		$("body,html").stop().animate({
			scrollTop: 0
		}, 800, 'easeOutExpo');
	});

	$toggleDarkmode.on("click" , function(){
		toggleDarkmode();
	});

	$toggleAmoledMode.on("click" , function(){
		toggleAmoledDarkMode();
	})

	if ($("#post_comment").length > 0){
		$("#fabtn_go_to_comment").removeClass("d-none");
	}else{
		$("#fabtn_go_to_comment").addClass("d-none");
	}
	$goToComment.on("click" , function(){
		gotoHash("#post_comment" , 600);
		$("#post_comment_content").focus();
	});

	if (localStorage['Argon_fabs_Floating_Status'] == "left"){
		$fabtns.addClass("fabtns-float-left");
	}
	$toggleSidesBtn.on("click" , function(){
		$fabtns.addClass("fabtns-unloaded");
		setTimeout(function(){
			$fabtns.toggleClass("fabtns-float-left");
			if ($fabtns.hasClass("fabtns-float-left")){
				localStorage['Argon_fabs_Floating_Status'] = "left";
			}else{
				localStorage['Argon_fabs_Floating_Status'] = "right";
			}
			$fabtns.removeClass("fabtns-unloaded");
		} , 300);
	});
	//博客设置
	$toggleBlogSettings.on("click" , function(){
		$("#float_action_buttons").toggleClass("blog_settings_opened");
	});
	$("#close_blog_settings").on("click" , function(){
		$("#float_action_buttons").removeClass("blog_settings_opened");
	});
	$("#blog_setting_darkmode_switch .custom-toggle-slider").on("click" , function(){
		toggleDarkmode();
	});
	//字体
	$("#blog_setting_font_sans_serif").on("click" , function(){
		$("html").removeClass("use-serif");
		localStorage['Argon_Use_Serif'] = "false";
	});
	$("#blog_setting_font_serif").on("click" , function(){
		$("html").addClass("use-serif");
		localStorage['Argon_Use_Serif'] = "true";
	});
	if (localStorage['Argon_Use_Serif'] == "true"){
		$("html").addClass("use-serif");
	}else if (localStorage['Argon_Use_Serif'] == "false"){
		$("html").removeClass("use-serif");
	}
	//阴影
	/*$("#blog_setting_shadow_small").on("click" , function(){
		$("html").removeClass("use-big-shadow");
		localStorage['Argon_Use_Big_Shadow'] = "false";
	});
	$("#blog_setting_shadow_big").on("click" , function(){
		$("html").addClass("use-big-shadow");
		localStorage['Argon_Use_Big_Shadow'] = "true";
	});
	if (localStorage['Argon_Use_Big_Shadow'] == "true"){
		$("html").addClass("use-big-shadow");
	}else if (localStorage['Argon_Use_Big_Shadow'] == "false"){
		$("html").removeClass("use-big-shadow");
	}*/
	//滤镜
	function setBlogFilter(name){
		if (name == undefined || name == ""){
			name = "off";
		}
		if (!$("html").hasClass("filter-" + name)){
			$("html").removeClass("filter-sunset filter-darkness filter-grayscale");
			if (name != "off"){
				$("html").addClass("filter-" + name);
			}
		}
		$("#blog_setting_filters .blog-setting-filter-btn").removeClass("active");
		$("#blog_setting_filters .blog-setting-filter-btn[filter-name='" + name + "']").addClass("active");
		localStorage['Argon_Filter'] = name;
	}
	setBlogFilter(localStorage['Argon_Filter']);
	$(".blog-setting-filter-btn").on("click" , function(){
		setBlogFilter(this.getAttribute("filter-name"));
	});

	let $window = $(window);

	function changefabtnDisplayStatus(){
		//阅读进度
		function hideReadingProgress(){
			$readingProgressBtn.addClass("fabtn-hidden");
		}
		function setReadingProgress(percent){
			$readingProgressBtn.removeClass("fabtn-hidden");
			$readingProgressDetails.html((percent * 100).toFixed(0) + "%");
			$readingProgressBar.css("width" , (percent * 100).toFixed(0) + "%");
		}
		if ($("article.post.post-full").length == 0){
			hideReadingProgress();
		}else{
			let a = $window.scrollTop() - ($("article.post.post-full").offset().top - 80);
			let b = $("article.post.post-full").outerHeight() + 50 - $window.height();
			if (b <= 0){
				hideReadingProgress();
			}else{
				readingProgress = a / b;
				if (isNaN(readingProgress) || readingProgress < 0 || readingProgress > 1){
					hideReadingProgress();
				}else{
					setReadingProgress(readingProgress);
				}
			}
		}
		//是否显示回顶
		if ($(window).scrollTop() >= 400){
			$backToTopBtn.removeClass("fabtn-hidden");
		}else{
			$backToTopBtn.addClass("fabtn-hidden");
		}
	}
	changefabtnDisplayStatus();
	$(window).scroll(function(){
		changefabtnDisplayStatus();
	});
	$(window).resize(function(){
		changefabtnDisplayStatus();
	});
	$fabtns.removeClass("fabtns-unloaded");
});

//import './blog-settings/card-radius'
import './blog-settings/theme-color'
