if (typeof(argonConfig) == "undefined"){
	var argonConfig = {};
}
if (typeof(argonConfig.wp_path) == "undefined"){
	argonConfig.wp_path = "/";
}
/*Cookies 操作*/
function setCookie(cname, cvalue, exdays) {
	let d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	let expires = "expires=" + d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
	let name = cname + "=";
	let decodedCookie = decodeURIComponent(document.cookie);
	let ca = decodedCookie.split(';');
	for (let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}

/*多语言支持*/
var translation = {};
translation['en_US'] = {
	"确定": "OK",
	"清除": "Clear",
	"恢复博客默认": "Set To Default",
	"评论内容不能为空": "Comment content cannot be empty",
	"昵称不能为空": "Name cannot be empty",
	"邮箱或 QQ 号格式错误": "Incorrect email or QQ format",
	"邮箱格式错误": "Incorrect email format",
	"网站格式错误 (不是 http(s):// 开头)": "Website URL format error",
	"验证码未输入": "CAPTCHA cannot be empty",
	"验证码格式错误": "Incorrect CAPTCHA format",
	"评论格式错误": "Comment format error",
	"发送中": "Sending",
	"正在发送": "Sending",
	"评论正在发送中...": "Comment is sending...",
	"发送": "Send",
	"评论发送失败": "Comment failed",
	"发送成功": "Success",
	"您的评论已发送": "Your comment has been sent",
	"评论": "Comments",
	"未知原因": "Unknown Error",
	"编辑中": "Editing",
	"正在编辑": "Editing",
	"评论正在编辑中...": "Comment is being edited...",
	"编辑": "Edit",
	"评论编辑失败": "Comment editing failed",
	"已编辑": "Edited",
	"编辑成功": "Success",
	"您的评论已编辑": "Your comment has been edited",
	"评论 #": "Comment #",
	"的编辑记录": "- Edit History",
	"加载失败": "Failed to load",
	"展开": "Show",
	"没有更多了": "No more comments",
	"找不到该 Repo": "Can't find the repository",
	"获取 Repo 信息失败": "Failed to get repository information",
	"点赞失败": "Vote failed",
	"Hitokoto 获取失败": "Failed to get Hitokoto",
	"复制成功": "Copied",
	"代码已复制到剪贴板": "Code has been copied to the clipboard",
	"复制失败": "Failed",
	"请手动复制代码": "Please copy the code manually",
	"刚刚": "Now",
	"分钟前": "minutes ago",
	"小时前": "hours ago",
	"昨天": "Yesterday",
	"前天": "The day before yesterday",
	"天前": "days ago",
	"隐藏行号": "Hide Line Numbers",
	"显示行号": "Show Line Numbers",
	"开启折行": "Enable Break Line",
	"关闭折行": "Disable Break Line",
	"复制": "Copy",
	"全屏": "Fullscreen",
	"退出全屏": "Exit Fullscreen",
};
translation['ru_RU'] = {
	"确定": "ОК",
	"清除": "Очистить",
	"恢复博客默认": "Восстановить по умолчанию",
	"评论内容不能为空": "Содержимое комментария не может быть пустым",
	"昵称不能为空": "Имя не может быть пустым",
	"邮箱或 QQ 号格式错误": "Неверный формат электронной почты или QQ",
	"邮箱格式错误": "Неправильный формат электронной почты",
	"网站格式错误 (不是 http(s):// 开头)": "Сайт ошибка формата URL-адреса ",
	"验证码未输入": "Вы не решили капчу",
	"验证码格式错误": "Ошибка проверки капчи",
	"评论格式错误": "Неправильный формат комментария",
	"发送中": "Отправка",
	"正在发送": "Отправка",
	"评论正在发送中...": "Комментарий отправляется...",
	"发送": "Отправить",
	"评论发送失败": "Не удалось отправить комментарий",
	"发送成功": "Комментарий отправлен",
	"您的评论已发送": "Ваш комментарий был отправлен",
	"评论": "Комментарии",
	"未知原因": "Неизвестная ошибка",
	"编辑中": "Редактируется",
	"正在编辑": "Редактируется",
	"评论正在编辑中...": "Комментарий редактируется",
	"编辑": "Редактировать",
	"评论编辑失败": "Не удалось отредактировать комментарий",
	"已编辑": "Изменено",
	"编辑成功": "Успешно",
	"您的评论已编辑": "Ваш комментарий был изменен",
	"评论 #": "Комментарий #",
	"的编辑记录": "- История изменений",
	"加载失败": "Ошибка загрузки",
	"展开": "Показать",
	"没有更多了": "Комментариев больше нет",
	"找不到该 Repo": "Невозможно найти репозиторий",
	"获取 Repo 信息失败": "Неудалось получить информацию репозитория",
	"点赞失败": "Ошибка голосования",
	"Hitokoto 获取失败": "Проблемы с вызовом Hitokoto",
	"复制成功": "Скопировано",
	"代码已复制到剪贴板": "Код скопирован в буфер обмена",
	"复制失败": "Неудалось",
	"请手动复制代码": "Скопируйте код вручную",
	"刚刚": "Сейчас",
	"分钟前": "минут назад",
	"小时前": "часов назад",
	"昨天": "Вчера",
	"前天": "Позавчера",
	"天前": "дней назад",
	"隐藏行号": "Скрыть номера строк",
	"显示行号": "Показать номера строк",
	"开启折行": "Включить перенос строк",
	"关闭折行": "Выключить перенос строк",
	"复制": "Скопировать",
	"全屏": "Полноэкранный режим",
	"退出全屏": "Выход из полноэкранного режима",
};
translation['zh_TW'] = {
	"确定": "確定",
	"清除": "清除",
	"恢复博客默认": "恢復博客默認",
	"评论内容不能为空": "評論內容不能為空",
	"昵称不能为空": "昵稱不能為空",
	"邮箱或 QQ 号格式错误": "郵箱或 QQ 號格式錯誤",
	"邮箱格式错误": "郵箱格式錯誤",
	"网站格式错误 (不是 http(s):// 开头)": "網站格式錯誤 (不是 http(s):// 開頭)",
	"验证码未输入": "驗證碼未輸入",
	"验证码格式错误": "驗證碼格式錯誤",
	"评论格式错误": "評論格式錯誤",
	"发送中": "發送中",
	"正在发送": "正在發送",
	"评论正在发送中...": "評論正在發送中...",
	"发送": "發送",
	"评论发送失败": "評論發送失敗",
	"发送成功": "發送成功",
	"您的评论已发送": "您的評論已發送",
	"评论": "評論",
	"未知原因": "未知原因",
	"编辑中": "編輯中",
	"正在编辑": "正在編輯",
	"评论正在编辑中...": "評論正在編輯中...",
	"编辑": "編輯",
	"评论编辑失败": "評論編輯失敗",
	"已编辑": "已編輯",
	"编辑成功": "編輯成功",
	"您的评论已编辑": "您的評論已編輯",
	"评论 #": "評論 #",
	"的编辑记录": "的編輯記錄",
	"加载失败": "加載失敗",
	"展开": "展開",
	"没有更多了": "沒有更多了",
	"找不到该 Repo": "找不到該 Repo",
	"获取 Repo 信息失败": "獲取 Repo 信息失敗",
	"点赞失败": "點贊失敗",
	"Hitokoto 获取失败": "Hitokoto 獲取失敗",
	"复制成功": "復制成功",
	"代码已复制到剪贴板": "代碼已復制到剪貼板",
	"复制失败": "復制失敗",
	"请手动复制代码": "請手動復制代碼",
	"刚刚": "剛剛",
	"分钟前": "分鐘前",
	"小时前": "小時前",
	"昨天": "昨天",
	"前天": "前天",
	"天前": "天前",
	"隐藏行号": "隱藏行號",
	"显示行号": "顯示行號",
	"开启折行": "開啟折行",
	"关闭折行": "關閉折行",
	"复制": "復制",
	"全屏": "全屏",
	"退出全屏": "退出全屏"
};
function __(text){
	let lang = argonConfig.language;
	if (typeof(translation[lang]) == "undefined"){
		return text;
	}
	if (typeof(translation[lang][text]) == "undefined"){
		return text;
	}
	return translation[lang][text];
}

/*根据滚动高度改变顶栏透明度*/
!function(){
	let toolbar = document.getElementById("navbar-main");
	let $bannerContainer = $("#banner_container");
	let $content = $("#content");

	let startTransitionHeight;
	let endTransitionHeight;
	let maxOpacity = 0.85;

	startTransitionHeight = $bannerContainer.offset().top - 75;
	endTransitionHeight = $content.offset().top - 75;

	$(window).resize(function(){
		startTransitionHeight = $bannerContainer.offset().top - 75;
		endTransitionHeight = $content.offset().top - 75;
	});

	function changeToolbarTransparency(){
		let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if (scrollTop < startTransitionHeight){
			toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), 0)', 'important');
			toolbar.style.setProperty('box-shadow', 'none');
			if (argonConfig.toolbar_blur){
				toolbar.style.setProperty('backdrop-filter', 'blur(0px)');
			}
			toolbar.classList.add("navbar-ontop");
			return;
		}
		if (scrollTop > endTransitionHeight){
			toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), ' + maxOpacity + ')', 'important');
			toolbar.style.setProperty('box-shadow', '');
			if (argonConfig.toolbar_blur){
				toolbar.style.setProperty('backdrop-filter', 'blur(16px)');
			}
			toolbar.classList.remove("navbar-ontop");
			return;
		}
		let transparency = (scrollTop - startTransitionHeight) / (endTransitionHeight - startTransitionHeight) * maxOpacity;
		toolbar.style.setProperty('background-color', 'rgba(var(--toolbar-color), ' + transparency, 'important');
		toolbar.style.setProperty('box-shadow', '');
		if (argonConfig.toolbar_blur){
			if ((scrollTop - startTransitionHeight) / (endTransitionHeight - startTransitionHeight) > 0.3){
				toolbar.style.setProperty('backdrop-filter', 'blur(16px)');
			}else{
				toolbar.style.setProperty('backdrop-filter', 'blur(0px)');
			}
		}
		toolbar.classList.remove("navbar-ontop");
	}
	function changeToolbarOnTopClass(){
		let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if (scrollTop < 30){
			toolbar.classList.add("navbar-no-blur");
		}else{
			toolbar.classList.remove("navbar-no-blur");
		}
	}
	if ($("html").hasClass("no-banner")) {
		changeToolbarOnTopClass();
		document.addEventListener("scroll", changeToolbarOnTopClass, {passive: true});
		return;
	}
	if (argonConfig.headroom == "absolute") {
		toolbar.classList.add("navbar-ontop");
		return;
	}
	if ($("html").hasClass("toolbar-blur")) {
		argonConfig.toolbar_blur = true;
		maxOpacity = 0.65;
	}else{
		argonConfig.toolbar_blur = false;
	}
	changeToolbarTransparency();
	document.addEventListener("scroll", changeToolbarTransparency, {passive: true});
}();

/*搜索*/
function searchPosts(word){
	if ($(".search-result").length > 0){
		let url = new URL(window.location.href);
		url.searchParams.set("s", word);
		$.pjax({
			url: url.href
		});
	}else{
		$.pjax({
			url: argonConfig.wp_path + "?s=" + encodeURI(word)
		});
	}
}
/*顶栏搜索*/
$(document).on("click" , "#navbar_search_input_container" , function(){
	$(this).addClass("open");
	$("#navbar_search_input").focus();
});
$(document).on("blur" , "#navbar_search_input_container" , function(){
	$(this).removeClass("open");
});
$(document).on("keydown" , "#navbar_search_input_container #navbar_search_input" , function(e){
	if (e.keyCode != 13){
		return;
	}
	let word = $(this).val();
	if (word == ""){
		$("#navbar_search_input_container").blur();
		return;
	}
	let scrolltop = $(document).scrollTop();
	searchPosts(word);
});
/*顶栏搜索 (Mobile)*/
$(document).on("keydown" , "#navbar_search_input_mobile" , function(e){
	if (e.keyCode != 13){
		return;
	}
	let word = $(this).val();
	$("#navbar_global .collapse-close button").click();
	if (word == ""){
		return;
	}
	let scrolltop = $(document).scrollTop();
	searchPosts(word);
});
/*侧栏搜索*/
$(document).on("click" , "#leftbar_search_container" , function(){
	$(".leftbar-search-button").addClass("open");
	$("#leftbar_search_input").removeAttr("readonly").focus();
	$("#leftbar_search_input").focus();
	$("#leftbar_search_input").select();
	return false;
});
$(document).on("blur" , "#leftbar_search_container" , function(){
	$(".leftbar-search-button").removeClass("open");
	$("#leftbar_search_input").attr("readonly", "readonly");
});
$(document).on("keydown" , "#leftbar_search_input" , function(e){
	if (e.keyCode != 13){
		return;
	}
	let word = $(this).val();
	if (word == ""){
		$("#leftbar_search_container").blur();
		return;
	}
	$("html").removeClass("leftbar-opened");
	searchPosts(word);
});
/*搜索过滤器*/
$(document).on("change" , ".search-filter" , function(e){
	if (pjaxLoading){
		$(this).prop("checked", !$(this).prop("checked"));
		e.preventDefault();
		return;
	}
	pjaxLoading = true;
	let postTypes = [];
	$(".search-filter:checked").each(function(){
		postTypes.push($(this).attr("name"));
	});
	if (postTypes.length == 0){
		postTypes = ["none"];
	}
	let url = new URL(document.location.href);
	url.searchParams.set("post_type", postTypes.join(","));
	url.pathname = url.pathname.replace(/\/page\/\d+$/, '');
	$.pjax({
		url: url.href
	});
});

/*左侧栏随页面滚动浮动*/
!function(){
	if ($("#leftbar").length == 0){
		let contentOffsetTop = $('#content').offset().top;
		function changeLeftbarStickyStatusWithoutSidebar(){
			let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
			if( contentOffsetTop - 10 - scrollTop <= 20 ){
				document.body.classList.add('leftbar-can-headroom');
			}else{
				document.body.classList.remove('leftbar-can-headroom');
			}
		}
		changeLeftbarStickyStatusWithoutSidebar();
		document.addEventListener("scroll", changeLeftbarStickyStatusWithoutSidebar, {passive: true});
		$(window).resize(function(){
			contentOffsetTop = $('#content').offset().top;
			changeLeftbarStickyStatusWithoutSidebar();
		});
		return;
	}
	let $leftbarPart1 = $('#leftbar_part1');
	let $leftbarPart2 = $('#leftbar_part2');
	let leftbarPart1 = document.getElementById('leftbar_part1');
	let leftbarPart2 = document.getElementById('leftbar_part2');

	let part1OffsetTop = $('#leftbar_part1').offset().top;
	let part1OuterHeight = $('#leftbar_part1').outerHeight();

	function changeLeftbarStickyStatus(){
		let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if( part1OffsetTop + part1OuterHeight + 10 - scrollTop <= (argonConfig.headroom != "absolute" ? 90 : 18) ){
			//滚动条在页面中间浮动状态
			leftbarPart2.classList.add('sticky');
		}else{
			//滚动条在顶部 不浮动状态
			leftbarPart2.classList.remove('sticky');
		}
		if( part1OffsetTop + part1OuterHeight + 10 - scrollTop <= 20 ){//侧栏下部分是否可以随 Headroom 一起向上移动
			document.body.classList.add('leftbar-can-headroom');
		}else{
			document.body.classList.remove('leftbar-can-headroom');
		}
	}
	changeLeftbarStickyStatus();
	document.addEventListener("scroll", changeLeftbarStickyStatus, {passive: true});
	$(window).resize(function(){
		part1OffsetTop = $leftbarPart1.offset().top;
		part1OuterHeight = $leftbarPart1.outerHeight();
		changeLeftbarStickyStatus();
	});
	new MutationObserver(function(){
		part1OffsetTop = $leftbarPart1.offset().top;
		part1OuterHeight = $leftbarPart1.outerHeight();
		changeLeftbarStickyStatus();
	}).observe(leftbarPart1, {attributes: true, childList: true, subtree: true});
}();

/*Headroom*/
if (argonConfig.headroom == "true"){
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

/*瀑布流布局*/
function waterflowInit() {
	if (argonConfig.waterflow_columns == "1") {
		return;
	}
	$("#main.article-list img").each(function(index, ele){
		ele.onload = function(){
			waterflowInit();
		}
	});
	let columns;
	if (argonConfig.waterflow_columns == "2and3") {
		if ($("#main").outerWidth() > 1000) {
			columns = 3;
		} else {
			columns = 2;
		}
	}else{
		columns = parseInt(argonConfig.waterflow_columns);
	}
	if ($("#main").outerWidth() < 650 && columns == 2) {
		columns = 1;
	}else if ($("#main").outerWidth() < 800 && columns == 3) {
		columns = 1;
	}

	let heights = [0, 0, 0];
	function getMinHeightPosition(){
		let res = 0, minn = 2147483647;
		for (var i = 0; i < columns; i++) {
			if (heights[i] < minn) {
				minn = heights[i];
				res = i;
			}
		}
		return res;
	}
	function getMaxHeight(){
		let res = 0;
		for (let i in heights) {
			res = Math.max(res, heights[i]);
		}
		return res;
	}
	$("#primary").css("transition", "none")
		.addClass("waterflow");
	let $container = $("#main.article-list");
	if (!$container.length){
		return;
	}
	let $items = $container.find("article.post:not(.no-results), .shuoshuo-preview-container");
	columns = Math.max(Math.min(columns, $items.length), 1);
	if (columns == 1) {
		$container.removeClass("waterflow");
		$items.css("transition", "").css("position", "").css("width", "").css("top", "").css("left", "").css("margin", "");
		$(".waterflow-placeholder").remove();
	}else{
		$container.addClass("waterflow");
		$items.each(function(index, item) {
			let $item = $(item);
			$item.css("transition", "none")
				.css("position", "absolute")
				.css("width", "calc(" + (100 / columns) + "% - " + (10 * (columns - 1) / columns) + "px)").css("margin", 0);
			let itemHeight = $item.outerHeight() + 10;
			let pos = getMinHeightPosition();
			$item.css("top", heights[getMinHeightPosition()] + "px")
				.css("left", (pos * $item.outerWidth() + 10 * pos) + "px");
			heights[pos] += itemHeight;
		});
	}
	if ($(".waterflow-placeholder").length) {
		$(".waterflow-placeholder").css("height", getMaxHeight() + "px");
	}else{
		$container.prepend("<div class='waterflow-placeholder' style='height: " + getMaxHeight() +"px;'></div>");
	}
}
waterflowInit();
if (argonConfig.waterflow_columns != "1") {
	$(window).resize(function(){
		waterflowInit();
	});
	new MutationObserver(function(mutations, observer){
		waterflowInit();
	}).observe(document.querySelector("#primary"), {
		'childList': true
	});
}

/*浮动按钮栏相关 (回顶等)*/
!function(){
	let $fabtns = $('#float_action_buttons');
	let $backToTopBtn = $('#fabtn_back_to_top');
	let $toggleSidesBtn = $('#fabtn_toggle_sides');
	let $toggleDarkmode = $('#fabtn_toggle_darkmode');
	let $toggleAmoledMode = $('#blog_setting_toggle_darkmode_and_amoledarkmode');
	let $toggleBlogSettings = $('#fabtn_toggle_blog_settings_popup');
	let $goToComment = $('#fabtn_go_to_comment');

	let $readingProgressBtn = $('#fabtn_reading_progress');
	let $readingProgressBar = $('#fabtn_reading_progress_bar');
	let $readingProgressDetails = $('#fabtn_reading_progress_details');

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
	$("#blog_setting_shadow_small").on("click" , function(){
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
	}
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
}();

/*卡片圆角大小调整*/
!function(){
	function setCardRadius(radius, setcookie){
		document.documentElement.style.setProperty('--card-radius', radius + "px");
		if (setcookie){
			setCookie("argon_card_radius", radius, 365);
		}
	}
	let slider = document.getElementById('blog_setting_card_radius');
	noUiSlider.create(slider, {
		start: [$("meta[name='theme-card-radius']").attr("content")],
		step: 0.5,
		connect: [true, false],
		range: {
			'min': [0],
			'max': [30]
		}
	});
	slider.noUiSlider.on('update', function (values){
		let value = values[0];
		setCardRadius(value, false);
	});
	slider.noUiSlider.on('set', function (values){
		let value = values[0];
		setCardRadius(value, true);
	});
	$(document).on("click" , "#blog_setting_card_radius_to_default" , function(){
		slider.noUiSlider.set($("meta[name='theme-card-radius-origin']").attr("content"));
		setCardRadius($("meta[name='theme-card-radius-origin']").attr("content"), false);
		setCookie("argon_card_radius", $("meta[name='theme-card-radius-origin']").attr("content"), 0);
	});
}();

/*评论区 & 发送评论*/
!function(){
	//回复评论
	let replying = false , replyID = 0;
	function reply(commentID){
		cancelEdit(false);
		replying = true;
		replyID = commentID;
		$("#post_comment_reply_name").html($("#comment-" + commentID + " .comment-item-title > .comment-name")[0].innerHTML);
		let preview = $("#comment-" + commentID + " .comment-item-text")[0].innerHTML;
		if ($("#comment-" + commentID + " .comment-item-source")[0].innerHTML != ''){
			preview = $("#comment-" + commentID + " .comment-item-source")[0].innerHTML.replace(/\n/g, "</br>");
		}
		$("#post_comment_reply_preview").html(preview);
		if ($("#comment-" + commentID + " .comment-item-title .badge-private-comment").length > 0){
			$("#post_comment").addClass("post-comment-force-privatemode-on");
		}else{
			$("#post_comment").addClass("post-comment-force-privatemode-off");
		}
		$("body,html").animate({
			scrollTop: $('#post_comment').offset().top - 100
		}, 500, 'easeOutCirc');
		$('#post_comment_reply_info').slideDown(500, 'easeOutCirc');
		setTimeout(function(){
			$("#post_comment_content").focus();
		}, 500);
	}
	function cancelReply(){
		replying = false;
		replyID = 0;
		$('#post_comment_reply_info').slideUp(300, 'easeOutCirc');
		$("#post_comment").removeClass("post-comment-force-privatemode-on post-comment-force-privatemode-off");
	}
	$(document).on("click" , ".comment-reply" , function(){
		reply(this.getAttribute("data-id"));
	});
	$(document).on("click pjax:click" , "#post_comment_reply_cancel" , function(){
		cancelReply();
	});
	$(document).on("pjax:click" , function(){
		replying = false;
		replyID = 0;
		$('#post_comment_reply_info').css("display", "none");
		$("#post_comment").removeClass("post-comment-force-privatemode-on post-comment-force-privatemode-off");
	});
	//编辑评论
	let editing = false , editID = 0;
	function edit(commentID){
		cancelReply();
		editing = true;
		editID = commentID;
		$('#post_comment').addClass("editing");
		$("#post_comment_content").val($("#comment-" + editID + " .comment-item-source").text());
		$("#post_comment_content").trigger("change");
		if ($("#comment-" + editID).data("use-markdown") == true && document.getElementById("comment_post_use_markdown") != null){
			document.getElementById("comment_post_use_markdown").checked = true;
		}else{
			document.getElementById("comment_post_use_markdown").checked = false;
		}
		if ($("#comment-" + commentID + " .comment-item-title .badge-private-comment").length > 0){
			$("#post_comment").addClass("post-comment-force-privatemode-on");
		}else{
			$("#post_comment").addClass("post-comment-force-privatemode-off");
		}
		$("body,html").animate({
			scrollTop: $('#post_comment').offset().top - 100
		}, 500, 'easeOutCirc');
		$("#post_comment_content").focus();
	}
	function cancelEdit(clear){
		editing = false;
		editID = 0;
		$("#post_comment").removeClass("post-comment-force-privatemode-on post-comment-force-privatemode-off");
		if (clear == true) $("#post_comment_content").val("");
		$("#post_comment_content").trigger("change");
		$('#post_comment').removeClass("editing");
	}
	$(document).on("click" , ".comment-edit" , function(){
		edit(this.getAttribute("data-id"));
	});
	$(document).on("click" , "#post_comment_edit_cancel" , function(){
		$("body,html").animate({
			scrollTop: $("#comment-" + editID).offset().top - 100
		}, 400, 'easeOutCirc');
		cancelEdit(true);
	});
	$(document).on("pjax:click" , function(){
		cancelEdit(true);
	});

	//显示/隐藏额外输入框 (评论者网站)
	$(document).on("click" , "#post_comment_toggle_extra_input" , function(){
		$("#post_comment").toggleClass("show-extra-input");
		if ($("#post_comment").hasClass("show-extra-input")){
			$("#post_comment_extra_input").slideDown(300, 'easeOutCirc');
		}else{
			$("#post_comment_extra_input").slideUp(300, 'easeOutCirc');
		}
	});

	//输入框细节
	$(document).on("change input keydown keyup propertychange" , "#post_comment_content" , function(){
		$("#post_comment_content_hidden")[0].innerText = $("#post_comment_content").val() + "\n";
		$("#post_comment_content").css("height" , $("#post_comment_content_hidden").outerHeight());
	});
	$(document).on("focus" , "#post_comment_link" , function(){
		$(".post-comment-link-container").addClass("active");
	});
	$(document).on("blur" , "#post_comment_link" , function(){
		$(".post-comment-link-container").removeClass("active");
	});
	$(document).on("focus" , "#post_comment_captcha" , function(){
		$(".post-comment-captcha-container").addClass("active");
	});
	$(document).on("blur" , "#post_comment_captcha" , function(){
		$(".post-comment-captcha-container").removeClass("active");
	});

	//发送评论
	function postComment(){
		let commentContent = $("#post_comment_content").val();
		let commentName = $("#post_comment_name").val();
		let commentEmail = $("#post_comment_email").val();
		let commentLink = $("#post_comment_link").val();
		let commentCaptcha = $("#post_comment_captcha").val();
		let useMarkdown = false;
		let privateMode = false;
		let mailNotice = false;
		if ($("#comment_post_use_markdown").length > 0){
			useMarkdown = $("#comment_post_use_markdown")[0].checked;
		}
		if ($("#comment_post_privatemode").length > 0){
			privateMode = $("#comment_post_privatemode")[0].checked;
		}
		if ($("#comment_post_mailnotice").length > 0){
			mailNotice = $("#comment_post_mailnotice")[0].checked;
		}

		let postID = $("#post_comment_post_id").val();
		let commentCaptchaSeed = $("#post_comment_captcha_seed").val();

		let isError = false;
		let errorMsg = "";

		//检查表单合法性
		if (commentContent.match(/^\s*$/)){
			isError = true;
			errorMsg += __("评论内容不能为空") + "</br>";
		}
		if (!$("#post_comment").hasClass("no-need-name-email")){
			if (commentName.match(/^\s*$/)){
				isError = true;
				errorMsg += __("昵称不能为空") + "</br>";
			}
			if ($("#post_comment").hasClass("enable-qq-avatar")){
				if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail) && !(/^[1-9][0-9]{4,10}$/).test(commentEmail)){
					isError = true;
					errorMsg += __("邮箱或 QQ 号格式错误") + "</br>";
				}
			}else{
				if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail)){
					isError = true;
					errorMsg += __("邮箱格式错误") + "</br>";
				}
			}
		}else{
			if (document.getElementById("comment_post_mailnotice") != null){
				if (document.getElementById("comment_post_mailnotice").checked == true){
					if ($("#post_comment").hasClass("enable-qq-avatar")){
						if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail) && !(/^[1-9][0-9]{4,10}$/).test(commentEmail)){
							isError = true;
							errorMsg += __("邮箱或 QQ 号格式错误") + "</br>";
						}
					}else{
						if (!(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(commentEmail)){
							isError = true;
							errorMsg += __("邮箱格式错误") + "</br>";
						}
					}
				}
			}
		}
		if (commentLink != "" && !(/https?:\/\//).test(commentLink)){
			isError = true;
			errorMsg += __("网站格式错误 (不是 http(s):// 开头)") + "</br>";
		}
		if (!$("#post_comment").hasClass("no-need-captcha")){
			if (commentCaptcha == ""){
				isError = true;
				errorMsg += __("验证码未输入");
			}
			if (commentCaptcha != "" && !(/^[0-9]+$/).test(commentCaptcha)){
				isError = true;
				errorMsg += __("验证码格式错误");
			}
		}
		if (isError){
			iziToast.show({
				title: __("评论格式错误"),
				message: errorMsg,
				class: 'shadow-sm',
				position: 'topRight',
				backgroundColor: '#f5365c',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-close',
				timeout: 5000
			});
			return;
		}

		//增加 disabled 属性和其他的表单提示
		$("#post_comment").addClass("sending");
		$("#post_comment_content").attr("disabled","disabled");
		$("#post_comment_name").attr("disabled","disabled");
		$("#post_comment_email").attr("disabled","disabled");
		$("#post_comment_captcha").attr("disabled","disabled");
		$("#post_comment_link").attr("disabled","disabled");
		$("#post_comment_send").attr("disabled","disabled");
		$("#post_comment_reply_cancel").attr("disabled","disabled");
		$("#post_comment_send .btn-inner--icon.hide-on-comment-editing").html("<i class='fa fa-spinner fa-spin'></i>");
		$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html(__("发送中"));

		iziToast.show({
			title: __("正在发送"),
			message: __("评论正在发送中..."),
			class: 'shadow-sm iziToast-noprogressbar',
			position: 'topRight',
			backgroundColor: 'var(--themecolor)',
			titleColor: '#ffffff',
			messageColor: '#ffffff',
			iconColor: '#ffffff',
			progressBarColor: '#ffffff',
			icon: 'fa fa-spinner fa-spin',
			close: false,
			timeout: 999999999
		});

		$.ajax({
			type: 'POST',
			url: argonConfig.wp_path + "wp-admin/admin-ajax.php",
			dataType : "json",
			data: {
				action: "ajax_post_comment",
				comment: commentContent,
				author: commentName,
				email: commentEmail,
				url: commentLink,
				comment_post_ID: postID,
				comment_parent: replyID,
				comment_captcha_seed: commentCaptchaSeed,
				comment_captcha: commentCaptcha,
				"wp-comment-cookies-consent": "yes",
				use_markdown: useMarkdown,
				private_mode: privateMode,
				enable_mailnotice: mailNotice
			},
			success: function(result){
				$("#post_comment").removeClass("sending");
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_name").removeAttr("disabled");
				$("#post_comment_email").removeAttr("disabled");
				$("#post_comment_link").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_reply_cancel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon.hide-on-comment-editing").html("<i class='fa fa-send'></i>");
				$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html(__("发送"));
				$("#post_comment").removeClass("show-extra-input post-comment-force-privatemode-on post-comment-force-privatemode-off");
				if (!result.isAdmin){
					$("#post_comment_captcha").removeAttr("disabled");
				}

				//判断是否有错误
				if (result.status == "failed"){
					iziToast.destroy();
					iziToast.show({
						title: __("评论发送失败"),
						message: result.msg,
						class: 'shadow-sm',
						position: 'topRight',
						backgroundColor: '#f5365c',
						titleColor: '#ffffff',
						messageColor: '#ffffff',
						iconColor: '#ffffff',
						progressBarColor: '#ffffff',
						icon: 'fa fa-close',
						timeout: 5000
					});
					return;
				}

				//发送成功
				iziToast.destroy();
				iziToast.show({
					title: __("发送成功"),
					message: __("您的评论已发送"),
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#2dce89',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-check',
					timeout: 5000
				});
				//插入新评论
				result.html = result.html.replace(/<img class='comment-sticker lazyload'(.*?)\/>/g, "").replace(/<(\/).noscript>/g, "");
				let parentID = result.parentID;
				if (parentID == "" || parentID == null){
					parentID = 0;
				}
				parentID = parseInt(parentID);
				if (parentID == 0){
					if ($("#comments > .card-body > ol.comment-list").length == 0){
						$("#comments > .card-body").html("<h2 class='comments-title'><i class='fa fa-comments'></i> " + __("评论") + "</h2><ol class='comment-list'></ol>");
					}
					if (result.commentOrder == "asc"){
						$("#comments > .card-body > ol.comment-list").append(result.html);
					}else{
						$("#comments > .card-body > ol.comment-list").prepend(result.html);
					}
				}else{
					if ($("#comment-" + parentID + " + .comment-divider + li > ul.children").length > 0){
						$("#comment-" + parentID + " + .comment-divider + li > ul.children").append(result.html);
					}else{
						$("#comment-" + parentID + " + .comment-divider").after("<li><ul class='children'>" + result.html + "</ul></li>");
					}
				}
				calcHumanTimesOnPage();
				//复位评论表单
				cancelReply();
				$("#post_comment_content").val("");
				$("#post_comment_captcha_seed").val(result.newCaptchaSeed);
				$("#post_comment_captcha + style").html(".post-comment-captcha-container:before{content: '" + result.newCaptcha + "';}");
				$("#post_comment_captcha").val(result.newCaptchaAnswer);
				$("body,html").animate({
					scrollTop: $("#comment-" + result.id).offset().top - 100
				}, 500, 'easeOutExpo');
			},
			error: function(result){
				$("#post_comment").removeClass("sending");
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_name").removeAttr("disabled");
				$("#post_comment_email").removeAttr("disabled");
				$("#post_comment_link").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_reply_cancel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon.hide-on-comment-editing").html("<i class='fa fa-send'></i>");
				$("#post_comment_send .btn-inner--text.hide-on-comment-editing").html(__("发送"));
				$("#post_comment").removeClass("show-extra-input post-comment-force-privatemode-on post-comment-force-privatemode-off");
				if (!result.isAdmin){
					$("#post_comment_captcha").removeAttr("disabled");
				}

				iziToast.destroy();
				iziToast.show({
					title: __("评论发送失败"),
					message: __("未知原因"),
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#f5365c',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-close',
					timeout: 5000
				});
				return;
			}
		});
	}
	//编辑评论
	function editComment(){
		let commentContent = $("#post_comment_content").val();
		let isError = false;
		let errorMsg = "";
		if (commentContent.match(/^\s*$/)){
			isError = true;
			errorMsg += __("评论内容不能为空") + "</br>";
		}
		if (isError){
			iziToast.show({
				title: __("评论格式错误"),
				message: errorMsg,
				class: 'shadow-sm',
				position: 'topRight',
				backgroundColor: '#f5365c',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-close',
				timeout: 5000
			});
			return;
		}

		//增加 disabled 属性和其他的表单提示
		$("#post_comment_content").attr("disabled","disabled");
		$("#post_comment_send").attr("disabled","disabled");
		$("#post_comment_edit_cancel").attr("disabled","disabled");
		$("#post_comment_send .btn-inner--icon.hide-on-comment-not-editing").html("<i class='fa fa-spinner fa-spin'></i>");
		$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html(__("编辑中"));

		iziToast.show({
			title: __("正在编辑"),
			message: __("评论正在编辑中..."),
			class: 'shadow-sm iziToast-noprogressbar',
			position: 'topRight',
			backgroundColor: 'var(--themecolor)',
			titleColor: '#ffffff',
			messageColor: '#ffffff',
			iconColor: '#ffffff',
			progressBarColor: '#ffffff',
			icon: 'fa fa-spinner fa-spin',
			close: false,
			timeout: 999999999
		});

		$.ajax({
			type: 'POST',
			url: argonConfig.wp_path + "wp-admin/admin-ajax.php",
			dataType : "json",
			data: {
				action: "user_edit_comment",
				comment: commentContent,
				id: editID
			},
			success: function(result){
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_edit_cancel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon.hide-on-comment-not-editing").html("<i class='fa fa-pencil'></i>");
				$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html(__("编辑"));

				//判断是否有错误
				if (result.status == "failed"){
					iziToast.destroy();
					iziToast.show({
						title: __("评论编辑失败"),
						message: result.msg,
						class: 'shadow-sm',
						position: 'topRight',
						backgroundColor: '#f5365c',
						titleColor: '#ffffff',
						messageColor: '#ffffff',
						iconColor: '#ffffff',
						progressBarColor: '#ffffff',
						icon: 'fa fa-close',
						timeout: 5000
					});
					return;
				}

				//发送成功，替换原评论
				result.new_comment = result.new_comment.replace(/<img class='comment-sticker lazyload'(.*?)\/>/g, "").replace(/<(\/).noscript>/g, "");		
				$("#comment-" + editID + " .comment-item-text").html(result.new_comment);
				$("#comment-" + editID + " .comment-item-source").html(result.new_comment_source);
				if ($("#comment-" + editID + " .comment-info .comment-edited").length == 0){
					$("#comment-" + editID + " .comment-info").prepend("<div class='comment-edited'><i class='fa fa-pencil' aria-hidden='true'></i>" + __("已编辑") + "</div>")
				}
				if (result.can_visit_edit_history){
					$("#comment-" + editID + " .comment-info .comment-edited").addClass("comment-edithistory-accessible");
				}

				iziToast.destroy();
				iziToast.show({
					title: __("编辑成功"),
					message: __("您的评论已编辑"),
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#2dce89',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-check',
					timeout: 5000
				});
				$("body,html").animate({
					scrollTop: $("#comment-" + editID).offset().top - 100
				}, 500, 'easeOutExpo');
				editing = false;
				editID = 0;
				$("#post_comment_content").val("");
				$('#post_comment').removeClass("editing post-comment-force-privatemode-on post-comment-force-privatemode-off");
				$("#post_comment_content").trigger("change");
			},
			error: function(result){
				$("#post_comment_content").removeAttr("disabled");
				$("#post_comment_send").removeAttr("disabled");
				$("#post_comment_edit_cancel").removeAttr("disabled");
				$("#post_comment_send .btn-inner--icon.hide-on-comment-not-editing").html("<i class='fa fa-pencil'></i>");
				$("#post_comment_send .btn-inner--text.hide-on-comment-not-editing").html(__("编辑"));
				if (result.readyState != 4 || result.status == 0){
					iziToast.destroy();
					iziToast.show({
						title: __("评论编辑失败"),
						message: __("未知原因"),
						class: 'shadow-sm',
						position: 'topRight',
						backgroundColor: '#f5365c',
						titleColor: '#ffffff',
						messageColor: '#ffffff',
						iconColor: '#ffffff',
						progressBarColor: '#ffffff',
						icon: 'fa fa-close',
						timeout: 5000
					});
					return;
				}
			}
		});
	}
	$(document).on("click" , "#post_comment_send" , function(){
		if ($("#post_comment").hasClass("editing")){
			editComment();
		}else{
			postComment();
		}
	});
}();
/*评论点赞*/
$(document).on("click" , ".comment-upvote" , function(){
	$this = $(this);
	ID = $this.attr("data-id");
	$this.addClass("comment-upvoting");
	$.ajax({
		url : argonConfig.wp_path + "wp-admin/admin-ajax.php",
		type : "POST",
		dataType : "json",
		data : {
			action: "upvote_comment",
			comment_id : ID,
		},
		success : function(result){
			$this.removeClass("comment-upvoting");
			if (result.status == "success"){
				$(".comment-upvote-num" , $this).html(result.total_upvote);
				$this.addClass("upvoted");
			}else{
				$(".comment-upvote-num" , $this).html(result.total_upvote);
				iziToast.show({
					title: result.msg,
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#f5365c',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-close',
					timeout: 5000
				});
			}
		},
		error : function(xhr){
			$this.removeClass("comment-upvoting");
			iziToast.show({
				title: __("点赞失败"),
				class: 'shadow-sm',
				position: 'topRight',
				backgroundColor: '#f5365c',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-close',
				timeout: 5000
			});
		}
	});
});
/*评论表情面板*/
function lazyloadStickers(){
	$(".emotion-keyboard .emotion-group:not(d-none) .emotion-item > img.lazyload").lazyload({threshold: 500, effect: "fadeIn"}).removeClass("lazyload");
	$("html").trigger("scroll");
}
$(document).on("click" , "#comment_emotion_btn" , function(){
	$("#comment_emotion_btn").toggleClass("comment-emotion-keyboard-open");
	lazyloadStickers();
});
$(document).on("click" , ".emotion-keyboard .emotion-group-name" , function(){
	$(".emotion-keyboard .emotion-group-name.active").removeClass("active");
	$(this).addClass("active");
	$(".emotion-keyboard .emotion-group:not(d-none)").addClass("d-none");
	$(".emotion-keyboard .emotion-group[index='" + $(this).attr("index") + "']").removeClass("d-none");
	lazyloadStickers();
});
function inputInsertText(text, input){
	$(input).focus();
	let isSuccess = document.execCommand("insertText", false, text);
	if (!isSuccess) { //FF
		if (typeof input.setRangeText === "function"){
			const start = input.selectionStart;
			input.setRangeText(text);
			input.selectionStart = input.selectionEnd = start + input.length;
			const e = document.createEvent("UIEvent");
			e.initEvent("input", true, false);
			input.dispatchEvent(e);
		}else{
			let value = $(input).val();
			let startPos = input.selectionStart, endPos = input.selectionEnd;
			$(input).val(value.substring(0, startPos) + text + value.substring(endPos));
			input.selectionStart = startPos + text.length;
			input.selectionEnd = startPos + text.length;
		}
	}
	$(input).focus();
}
$(document).on("click" , ".emotion-keyboard .emotion-item" , function(){
	$("#comment_emotion_btn").removeClass("comment-emotion-keyboard-open");
	if ($(this).hasClass("emotion-item-sticker")){
		inputInsertText(" :" + $(this).attr("code") + ": ", document.getElementById("post_comment_content"));
	}else{
		inputInsertText($(this).attr("text"), document.getElementById("post_comment_content"));
	}
});
$(document).on("dragstart" , ".emotion-keyboard .emotion-item > img, .comment-sticker" , function(e){
	e.preventDefault();
});
document.addEventListener('click', (e) => {
	if (document.getElementById("comment_emotion_btn") == null){
		return;
	}
　　if(e.target.id != "comment_emotion_btn" && e.target.id != "emotion_keyboard" && !document.getElementById("comment_emotion_btn").contains(e.target) && !document.getElementById("emotion_keyboard").contains(e.target)){
		$("#comment_emotion_btn").removeClass("comment-emotion-keyboard-open");
　　}
})
/*查看评论编辑记录*/
function showCommentEditHistory(id){
	let requestID = parseInt(new Date().getTime());
	$("#comment_edit_history").data("request-id", requestID);
	$("#comment_edit_history .modal-title").html(__("评论 #") + id + " " + __("的编辑记录"));
	$("#comment_edit_history .modal-body").html("<div class='comment-history-loading'><span class='spinner-border text-primary'></span><span style='display: inline-block;transform: translateY(-4px);margin-left: 15px;font-size: 18px;'>加载中</span></div>");
	$("#comment_edit_history").modal(null);
	$.ajax({
		type: 'POST',
		url: argonConfig.wp_path + "wp-admin/admin-ajax.php",
		dataType : "json",
		data: {
			action: "get_comment_edit_history",
			id: id
		},
		success: function(result){
			if ($("#comment_edit_history").data("request-id") != requestID){
				return;
			}
			$("#comment_edit_history .modal-body").hide();
			$("#comment_edit_history .modal-body").html(result.history);
			$("#comment_edit_history .modal-body").fadeIn(300);
		},
		error: function(result){
			if ($("#comment_edit_history").data("request-id") != requestID){
				return;
			}
			$("#comment_edit_history .modal-body").hide();
			$("#comment_edit_history .modal-body").html(__("加载失败"));
			$("#comment_edit_history .modal-body").fadeIn(300);
		}
	});
}
$(document).on("click" , ".comment-edited.comment-edithistory-accessible" , function(){
	showCommentEditHistory($(this).parent().parent().parent().parent().data("id"));
});
/*过长评论折叠*/
function foldLongComments(){
	if (argonConfig.fold_long_comments == false){
		return;
	}
	$(".comment-item-inner").each(function(){
		if ($(this).hasClass("comment-unfolded")){
			return;
		}
		if (this.clientHeight > 800){
			$(this).addClass("comment-folded");
			$(this).append("<div class='show-full-comment'><button><i class='fa fa-angle-down' aria-hidden='true'></i> " + __("展开") + "</button></div>");
		}
	});
}
foldLongComments();
$(document).on("click" , ".show-full-comment" , function(){
	$(this).parent().removeClass("comment-folded").addClass("comment-unfolded");
});
/*评论文字头像*/
function generateCommentTextAvatar(img){
	let emailHash = '';
	try{
		emailHash = img.attr("src").match(/([a-f\d]{32}|[A-F\d]{32})/)[0];
	}catch{
		emailHash = img.parent().parent().parent().find(".comment-name").text().trim();
		if (emailHash == '' || emailHash == undefined){
			emailHash = img.parent().find("*['class'*='comment-author']").text().trim();
		}
	}
	let hash = 0;
	for (i in emailHash){
		hash = (hash * 233 + emailHash.charCodeAt(i)) % 16;
	}
	let colors = ['#e25f50', '#f25e90', '#bc67cb', '#9672cf', '#7984ce', '#5c96fa', '#7bdeeb', '#45d0e2', '#48b7ad', '#52bc89', '#9ace5f', '#d4e34a', '#f9d715', '#fac400', '#ffaa00', '#ff8b61', '#c2c2c2', '#8ea3af', '#a1877d', '#a3a3a3', '#b0b6e3', '#b49cde', '#c2c2c2', '#7bdeeb', '#bcaaa4', '#aed77f'];
	let text = $(".comment-name", img.parent().parent().parent()).text().trim()[0];
	if (text == '' || text == undefined){
		text = img.parent().find("*[class*='comment-author']").text().trim()[0];
	}
	let classList = img.attr('class') + " text-avatar";
	img.prop('outerHTML', '<div class="' + classList + '" style="background-color: ' + colors[hash] + ';">' + text + '</div>');
}
document.addEventListener("error", function(e){
	let img = $(e.target);
	if (!img.hasClass("avatar")){
		return;
	}
	generateCommentTextAvatar(img);
}, true);
function refreshCommentTextAvatar(){
	$(".comment-item-avatar > img.avatar").each(function(index, img){
		if (!img.complete){
			return;
		}
		if (img.naturalWidth !== 0){
			return false;
		}
		generateCommentTextAvatar($(img));
	});
}
refreshCommentTextAvatar();
$(window).on("load", function(){
	refreshCommentTextAvatar();
});
/*需要密码的文章加载*/
$(document).on("submit" , ".post-password-form" , function(){
	$("input[type='submit']", this).attr("disabled", "disabled");
	let url = $(this).attr("action");
	$.pjax.form(this, {
		push: false,
		replace: false
	});
	return false;
});
/*评论分页加载*/
!function(){
	$(document).on("click" , "#comments_navigation .page-item > div" , function(){
		$("#comments").addClass("comments-loading");
		NProgress.set(0.618);
		url = $(this).attr("href");
		$.ajax({
			type: 'POST',
			url: url,
			dataType : "html",
			success : function(result){
				NProgress.done();
				$vdom = $(result);
				$("#comments").html($("#comments", $vdom).html());
				$("#comments").removeClass("comments-loading");
				$("body,html").animate({
					scrollTop: $("#comments").offset().top - 100
				}, 500, 'easeOutExpo');
				foldLongComments();
				calcHumanTimesOnPage();
				panguInit();
				$(".comment-item-text .comment-sticker.lazyload").lazyload(argonConfig.lazyload).removeClass("lazyload");
			},
			error : function(){
				window.location.href = url;
			}
		});
	});
	$(document).on("click" , "#comments_more" , function(){
		$("#comments_more").attr("disabled", "disabled");
		NProgress.set(0.618);
		url = $(this).attr("href");
		$.ajax({
			type: 'POST',
			url: url,
			data: {
				no_post_view: 'true'
			},
			dataType : "html",
			success : function(result){
				NProgress.done();
				$vdom = $(result);
				$("#comments > .card-body > ol.comment-list").append($("#comments > .card-body > ol.comment-list", $vdom).html());
				if ($("#comments_more", $vdom).length == 0){
					$("#comments_more").remove();
					$(".comments-navigation-more").html("<div class='comments-navigation-nomore'>" + __("没有更多了") + "</div>");
				}else{
					$("#comments_more").attr("href", $("#comments_more", $vdom).attr("href"));
					$("#comments_more").removeAttr("disabled");
				}
				foldLongComments();
				calcHumanTimesOnPage();
				panguInit();
				$(".comment-item-text .comment-sticker.lazyload").lazyload(argonConfig.lazyload).removeClass("lazyload");
			},
			error : function(){
				window.location.href = url;
			}
		});
	});
}();

/*URL 中 # 根据 ID 定位*/
function gotoHash(hash, durtion, easing = 'easeOutExpo'){
	if (hash.length == 0){
		return;
	}
	if ($(hash).length == 0){
		return;
	}
	if (durtion == null){
		durtion = 200;
	}
	$("body,html").stop().animate({
		scrollTop: $(hash).offset().top - 80
	}, durtion, easing);
}
function getHash(url){
	return url.substring(url.indexOf('#'));
}
!function(){
	$(window).on("hashchange" , function(){
		gotoHash(window.location.hash);
	});
	$(window).trigger("hashchange");
}();

/*显示文章过时信息 Toast*/
function showPostOutdateToast(){
	if ($("#primary #post_outdate_toast").length > 0){
		iziToast.show({
			title: '',
			message: $("#primary #post_outdate_toast").data("text"),
			class: 'shadow-sm',
			position: 'topRight',
			backgroundColor: 'var(--themecolor)',
			titleColor: '#ffffff',
			messageColor: '#ffffff',
			iconColor: '#ffffff',
			progressBarColor: '#ffffff',
			icon: 'fa fa-info',
			close: false,
			timeout: 8000
		});
		$("#primary #post_outdate_toast").remove();
	}
}
showPostOutdateToast();

/*Zoomify*/
function zoomifyInit(){
	if (argonConfig.zoomify == false){
		return;
	}
	$("article img").zoomify(argonConfig.zoomify);
}
zoomifyInit();

/*Fancybox*/
$.fancybox.defaults.transitionEffect = "slide";
$.fancybox.defaults.buttons = ["zoom", "fullScreen", "thumbs", "close"];
$.fancybox.defaults.lang = argonConfig.language;
$.fancybox.defaults.i18n = {
	en_US: {
		CLOSE: "Close",
		NEXT: "Next",
		PREV: "Previous",
		ERROR: "The requested content cannot be loaded. <br/> Please try again later.",
		PLAY_START: "Start slideshow",
		PLAY_STOP: "Pause slideshow",
		FULL_SCREEN: "Full screen",
		THUMBS: "Thumbnails",
		DOWNLOAD: "Download",
		SHARE: "Share",
		ZOOM: "Zoom"
	},
	zh_CN: {
		CLOSE: "关闭",
		NEXT: "下一张",
		PREV: "上一张",
		ERROR: "图片加载失败",
		PLAY_START: "开始幻灯片展示",
		PLAY_STOP: "暂停幻灯片展示",
		FULL_SCREEN: "全屏",
		THUMBS: "缩略图",
		DOWNLOAD: "下载",
		SHARE: "分享",
		ZOOM: "缩放"
	}
};

/*Lazyload*/
function lazyloadInit(){
	if (argonConfig.lazyload == false){
		return;
	}
	if (argonConfig.lazyload.effect == "none"){
		delete argonConfig.lazyload.effect;
	}
	$("article img.lazyload:not(.lazyload-loaded) , .related-post-thumbnail.lazyload:not(.lazyload-loaded) , .shuoshuo-preview-container img.lazyload:not(.lazyload-loaded)").lazyload(
		Object.assign(argonConfig.lazyload, {
			load: function () {
				$(this).addClass("lazyload-loaded");
				$(this).parent().removeClass("lazyload-container-unload");
			}
		})
	);
	$(".post-thumbnail.lazyload:not(.lazyload-loaded)").lazyload(
		Object.assign({threshold: argonConfig.lazyload.threshold}, {
			load: function () {
				$(this).addClass("lazyload-loaded");
				$(this).parent().removeClass("lazyload-container-unload");
				waterflowInit();
			}
		})
	);
	$(".comment-item-text .comment-sticker.lazyload").lazyload(Object.assign(argonConfig.lazyload, {load: function(){$(this).removeClass("lazyload")}}));
}
lazyloadInit();

/*Pangu.js*/
function panguInit(){
	if (argonConfig.pangu.indexOf("article") >= 0){
		pangu.spacingElementByClassName('post-content');
	}
	if (argonConfig.pangu.indexOf("comment") >= 0){
		pangu.spacingElementById('comments');
	}
	if (argonConfig.pangu.indexOf("shuoshuo") >= 0){
		pangu.spacingElementByClassName('shuoshuo-content');
	}
}
panguInit();

/*Clamp.js*/
function clampInit(){
	$(".clamp").each(function(index, dom) {
		$clamp(dom, {clamp: dom.getAttribute("clamp-line")});
	});
}
clampInit();

/*Tippy.js*/
function tippyInit(){
	//Reference Popover
	tippy('sup.reference[data-content]:not(.tippy-initialized)', {
		content: (reference) => reference.getAttribute('data-content'),
		allowHTML: true,
		interactive: true,theme: 'light scroll-y',
		delay: [100, 250],
		animation: 'fade'
	});
	$("sup.reference[data-content]:not(.tippy-initialized)").addClass("tippy-initialized");
}
tippyInit();

/*Banner 全屏封面相关*/
if ($("html").hasClass("banner-as-cover")){
	function classInit(){
		if ($("#main").hasClass("article-list-home")){
			if (!$("html").hasClass("is-home")){
				$("html").addClass("is-home");
				$("html").trigger("resize");
			}
		}else{
			if ($("html").hasClass("is-home")){
				$("html").removeClass("is-home");
				$("html").trigger("resize");
			}
		}
	}
	classInit();
	new MutationObserver(function(mutations, observer){
		classInit();
	}).observe(document.querySelector("#primary"), {
		'childList': true
	});
	$(".cover-scroll-down").on("click" , function(){
		gotoHash("#content", 600, 'easeOutCirc');
		$("#content").focus();
	});
	$fabs = $("#float_action_buttons");
	$coverScrollDownBtn = $(".cover-scroll-down");
	function changeWidgetsDisplayStatus(){
		let scrollTop = $(window).scrollTop();
		if (scrollTop >= window.outerHeight * 0.2){
			$fabs.removeClass("hidden");
		}else{
			$fabs.addClass("hidden");
		}
		if (scrollTop >= window.outerHeight * 0.6){
			$coverScrollDownBtn.addClass("hidden");
		}else{
			$coverScrollDownBtn.removeClass("hidden");
		}
	}
	changeWidgetsDisplayStatus();
	$(window).scroll(function(){
		changeWidgetsDisplayStatus();
	});
	$(window).resize(function(){
		changeWidgetsDisplayStatus();
	});
}

/*Pjax*/
var pjaxScrollTop = 0, pjaxLoading = false;
$.pjax.defaults.timeout = 10000;
$.pjax.defaults.container = ['#primary', '#leftbar_part1_menu', '#leftbar_part2_inner', '.page-information-card-container', '#rightbar', '#wpadminbar'];
$.pjax.defaults.fragment = ['#primary', '#leftbar_part1_menu', '#leftbar_part2_inner', '.page-information-card-container', '#rightbar', '#wpadminbar'];
$(document).pjax("a[href]:not([no-pjax]):not(.no-pjax):not([target='_blank']):not([download]):not(.reference-link):not(.reference-list-backlink)")
.on('pjax:click', function(e, f, g){
	if (argonConfig.disable_pjax == true){
		e.preventDefault();
		return;
	}
	NProgress.remove();
	NProgress.start();
	pjaxLoading = true;
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
}).on('pjax:send', function() {
	NProgress.set(0.618);
}).on('pjax:beforeReplace', function(e, dom) {
	if ($("#post_comment", dom[0]).length > 0){
		$("#fabtn_go_to_comment").removeClass("d-none");
	}else{
		$("#fabtn_go_to_comment").addClass("d-none");
	}
	if ($("html").hasClass("banner-as-cover")){
		if (!$("#main").hasClass("article-list-home")){
			pjaxScrollTop = 0;
		}
	}
}).on('pjax:complete', function() {
	pjaxLoading = false;
	NProgress.inc();
	try{
		if (MathJax != undefined){
			if (MathJax.Hub != undefined){
				MathJax.Hub.Typeset();
			}else{
				MathJax.typeset();
			}
		}
	}catch (err){}
	try{
		if (renderMathInElement != undefined){
			renderMathInElement(document.body,{
				delimiters: [
					{left: "$$", right: "$$", display: true},
					{left: "$", right: "$", display: false},
					{left: "\\(", right: "\\)", display: false}
				]
			});
		}
	}catch (err){}

	waterflowInit();
	lazyloadInit();
	zoomifyInit();
	highlightJsRender();
	panguInit();
	clampInit();
	tippyInit();
	getGithubInfoCardContent();
	showPostOutdateToast();
	calcHumanTimesOnPage();
	foldLongComments();
	foldLongShuoshuo();
	$("html").trigger("resize");

	if (typeof(window.pjaxLoaded) == "function"){
		try{
			window.pjaxLoaded();
		}catch (err){
			console.error(err);
		}
	}

	NProgress.done();
}).on('pjax:end', function() {
	waterflowInit();
	lazyloadInit();
});

/*Reference 跳转*/
$(document).on("click", ".reference-link , .reference-list-backlink" , function(e){
	e.preventDefault();
	$target = $($(this).attr("href"));
	$("body,html").animate({
		scrollTop: $target.offset().top - document.body.clientHeight / 2 - 75
	}, 500, 'easeOutExpo')
	setTimeout(function(){
		if ($target.is("li")){
			$(".space", $target).focus();
		}else{
			$target.focus();
		}
	}, 1);
});

/*Tags Dialog pjax 加载后自动关闭*/
$(document).on("click" , "#blog_tags .tag" , function(){
	$("#blog_tags button.close").trigger("click");
});
$(document).on("click" , "#blog_categories .tag" , function(){
	$("#blog_categories button.close").trigger("click");
});

/*侧栏 & 顶栏菜单手机适配*/
!function(){
	$(document).on("click" , "#fabtn_open_sidebar, #open_sidebar" , function(){
		$("html").addClass("leftbar-opened");
	});
	$(document).on("click" , "#sidebar_mask" , function(){
		$("html").removeClass("leftbar-opened");
	});
	$(document).on("click" , "#leftbar a[href]:not([no-pjax]):not([href^='#'])" , function(){
		$("html").removeClass("leftbar-opened");
	});
	$(document).on("click" , "#navbar_global.show .navbar-nav a[href]:not([no-pjax]):not([href^='#'])" , function(){
		$("#navbar_global .navbar-toggler").click();
	});
	$(document).on("click" , "#navbar_global.show #navbar_search_btn_mobile" , function(){
		$("#navbar_global .navbar-toggler").click();
	});
}();

/*折叠区块小工具*/
$(document).on("click" , ".collapse-block .collapse-block-title" , function(){
	let block = $(this).parent();
	$(block).toggleClass("collapsed");
	let inner = $(".collapse-block-body", block);
	if (block.hasClass("collapsed")){
		inner.stop(true, false).slideUp(300, 'easeOutCirc');
	}else{
		inner.stop(true, false).slideDown(300, 'easeOutCirc');
	}
	$("html").trigger("scroll");
});

/*获得 Github Repo Shortcode 信息卡内容*/
function getGithubInfoCardContent(){
	$(".github-info-card").each(function(){
		(function($this){
			if ($this.attr("data-getdata") == "backend"){
				$(".github-info-card-description" , $this).html($this.attr("data-description"));
				$(".github-info-card-stars" , $this).html($this.attr("data-stars"));
				$(".github-info-card-forks" , $this).html($this.attr("data-forks"));
				return;
			}
			$(".github-info-card-description" , $this).html("Loading...");
			$(".github-info-card-stars" , $this).html("-");
			$(".github-info-card-forks" , $this).html("-");
			author = $this.attr("data-author");
			project = $this.attr("data-project");
			$.ajax({
				url : "https://api.github.com/repos/" + author + "/" + project,
				type : "GET",
				dataType : "json",
				success : function(result){
					description = result.description;
					if (result.homepage != "" && result.homepage != null){
						description += " <a href='" + result.homepage + "' target='_blank' no-pjax>" + result.homepage + "</a>"
					}
					$(".github-info-card-description" , $this).html(description);
					$(".github-info-card-stars" , $this).html(result.stargazers_count);
					$(".github-info-card-forks" , $this).html(result.forks_count);
				},
				error : function(xhr){
					if (xhr.status == 404){
						$(".github-info-card-description" , $this).html(__("找不到该 Repo"));
					}else{
						$(".github-info-card-description" , $this).html(__("获取 Repo 信息失败"));
					}
				}
			});
		})($(this));
	});
}
getGithubInfoCardContent();

/*说说点赞*/
$(document).on("click" , ".shuoshuo-upvote" , function(){
	$this = $(this);
	ID = $this.attr("data-id");
	$this.addClass("shuoshuo-upvoting");
	$.ajax({
		url : argonConfig.wp_path + "wp-admin/admin-ajax.php",
		type : "POST",
		dataType : "json",
		data : {
			action: "upvote_shuoshuo",
			shuoshuo_id : ID,
		},
		success : function(result){
			$this.removeClass("shuoshuo-upvoting");
			if (result.status == "success"){
				$(".shuoshuo-upvote-num" , $this).html(result.total_upvote);
				$("i.fa-thumbs-o-up" , $this).addClass("fa-thumbs-up").removeClass("fa-thumbs-o-up");
				$this.addClass("upvoted");
				$this.addClass("shuoshuo-upvoted-animation");
				iziToast.show({
					title: result.msg,
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#2dce89',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-check',
					timeout: 5000
				});
			}else{
				$(".shuoshuo-upvote-num" , $this).html(result.total_upvote);
				iziToast.show({
					title: result.msg,
					class: 'shadow-sm',
					position: 'topRight',
					backgroundColor: '#f5365c',
					titleColor: '#ffffff',
					messageColor: '#ffffff',
					iconColor: '#ffffff',
					progressBarColor: '#ffffff',
					icon: 'fa fa-close',
					timeout: 5000
				});
			}
		},
		error : function(xhr){
			$this.removeClass("shuoshuo-upvoting");
			iziToast.show({
				title: __("点赞失败"),
				class: 'shadow-sm',
				position: 'topRight',
				backgroundColor: '#f5365c',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-close',
				timeout: 5000
			});
		}
	});
});
//折叠长说说
function foldLongShuoshuo(){
	if (argonConfig.fold_long_shuoshuo == false){
		return;
	}
	$("#main .shuoshuo-foldable > .shuoshuo-content").each(function(){
		if ($(this).hasClass("shuoshuo-unfolded")){
			return;
		}
		if (this.clientHeight > 400){
			$(this).addClass("shuoshuo-folded");
			$(this).append("<div class='show-full-shuoshuo'><button class='btn btn-outline-primary'><i class='fa fa-angle-down' aria-hidden='true'></i> " + __("展开") + "</button></div>");
		}
	});
}
foldLongShuoshuo();
$(document).on("click" , ".show-full-shuoshuo" , function(){
	$(this).parent().removeClass("shuoshuo-folded").addClass("shuoshuo-unfolded");
});

//颜色计算
function rgb2hsl(R,G,B){
	let r = R / 255;
	let g = G / 255;
	let b = B / 255;

	let var_Min = Math.min(r, g, b);
	let var_Max = Math.max(r, g, b);
	let del_Max = var_Max - var_Min;

	let H, S, L = (var_Max + var_Min) / 2;

	if (del_Max == 0){
		H = 0;
		S = 0;
	}else{
		if (L < 0.5){
			S = del_Max / (var_Max + var_Min);
		}else{
			S = del_Max / (2 - var_Max - var_Min);
		}

		del_R = (((var_Max - r) / 6) + (del_Max / 2)) / del_Max;
		del_G = (((var_Max - g) / 6) + (del_Max / 2)) / del_Max;
		del_B = (((var_Max - b) / 6) + (del_Max / 2)) / del_Max;

		if (r == var_Max){
			H = del_B - del_G;
		}
		else if (g == var_Max){
			H = (1 / 3) + del_R - del_B;
		}
		else if (b == var_Max){
			H = (2 / 3) + del_G - del_R;
		}
		if (H < 0) H += 1;
		if (H > 1) H -= 1;
	}
	return {
		'h': H,//0~1
		's': S,
		'l': L
	};
}
function Hue_2_RGB(v1,v2,vH){
	if (vH < 0) vH += 1;
	if (vH > 1) vH -= 1;
	if ((6 * vH) < 1) return (v1 + (v2 - v1) * 6 * vH);
	if ((2 * vH) < 1) return v2;
	if ((3 * vH) < 2) return (v1 + (v2 - v1) * ((2 / 3) - vH) * 6);
	return v1;
}
function hsl2rgb(h,s,l){
	let r, g, b, var_1, var_2;
	if (s == 0){
		r = l;
		g = l;
		b = l;
	}
	else{
		if (l < 0.5){
			var_2 = l * (1 + s);
		}
		else{
			var_2 = (l + s) - (s * l);
		}
		var_1 = 2 * l - var_2;
		r = Hue_2_RGB(var_1, var_2, h + (1 / 3));
		g = Hue_2_RGB(var_1, var_2, h);
		b = Hue_2_RGB(var_1, var_2, h - (1 / 3));
	}
	return {
		'R': Math.round(r * 255),//0~255
		'G': Math.round(g * 255),
		'B': Math.round(b * 255),
		'r': r,//0~1
		'g': g,
		'b': b
	};
}
function rgb2hex(r,g,b){
	let hex = new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
	let rh, gh, bh;
	rh = "", gh ="", bh="";
	while (rh.length < 2){
		rh = hex[r%16] + rh;
		r = Math.floor(r / 16);
	}
	while (gh.length < 2){
		gh = hex[g%16] + gh;
		g = Math.floor(g / 16);
	}
	while (bh.length < 2){
		bh = hex[b%16] + bh;
		b = Math.floor(b / 16);
	}
	return "#" + rh + gh + bh;
}
function hex2rgb(hex){
	//hex: #XXXXXX
	let dec = {
		'0': 0, '1': 1, '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9, 'A': 10, 'B': 11, 'C': 12, 'D': 13, 'E': 14, 'F': 15
	};
	return {
		'R': (dec[hex.substr(1,1)] * 16 + dec[hex.substr(2,1)]),//0~255
		'G': (dec[hex.substr(3,1)] * 16 + dec[hex.substr(4,1)]),
		'B': (dec[hex.substr(5,1)] * 16 + dec[hex.substr(6,1)]),
		'r': (dec[hex.substr(1,1)] * 16 + dec[hex.substr(2,1)]) / 255,//0~1
		'g': (dec[hex.substr(3,1)] * 16 + dec[hex.substr(4,1)]) / 255,
		'b': (dec[hex.substr(5,1)] * 16 + dec[hex.substr(6,1)]) / 255
	};
}
function rgb2gray(R,G,B){
	return Math.round(R * 0.299 + G * 0.587 + B * 0.114);
}
function hex2gray(hex){
	let rgb_array = hex2rgb(hex);
	return hex2gray(rgb_array['R'], rgb_array['G'], rgb_array['B']);
}
function rgb2str(rgb){
	return rgb['R'] + "," + rgb['G'] + "," + rgb['B'];
}
function hex2str(hex){
	return rgb2str(hex2rgb(hex));
}
//颜色选择器 & 切换主题色
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
			save: __('确定'),
			clear: __('清除'),
			cancel: __('恢复博客默认')
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
function pickrObjectToHEX(color){
	let HEXA = color.toHEXA();
	return ("#" + HEXA[0] + HEXA[1] + HEXA[2]).toUpperCase();
}
function updateThemeColor(color, setcookie){
	let themecolor = color;
	let themecolor_rgbstr = hex2str(themecolor);
	let RGB = hex2rgb(themecolor);
	let HSL = rgb2hsl(RGB['R'], RGB['G'], RGB['B']);

	let RGB_dark0 = hsl2rgb(HSL['h'], HSL['s'], Math.max(HSL['l'] - 0.025, 0));
	let themecolor_dark0 = rgb2hex(RGB_dark0['R'],RGB_dark0['G'],RGB_dark0['B']);

	let RGB_dark = hsl2rgb(HSL['h'], HSL['s'], Math.max(HSL['l'] - 0.05, 0));
	let themecolor_dark = rgb2hex(RGB_dark['R'], RGB_dark['G'], RGB_dark['B']);

	let RGB_dark2 = hsl2rgb(HSL['h'], HSL['s'], Math.max(HSL['l'] - 0.1, 0));
	let themecolor_dark2 = rgb2hex(RGB_dark2['R'],RGB_dark2['G'],RGB_dark2['B']);

	let RGB_dark3 = hsl2rgb(HSL['h'], HSL['s'], Math.max(HSL['l'] - 0.15, 0));
	let themecolor_dark3 = rgb2hex(RGB_dark3['R'],RGB_dark3['G'],RGB_dark3['B']);

	let RGB_light = hsl2rgb(HSL['h'], HSL['s'], Math.min(HSL['l'] + 0.1, 1));
	let themecolor_light = rgb2hex(RGB_light['R'],RGB_light['G'],RGB_light['B']);

	document.documentElement.style.setProperty('--themecolor', themecolor);
	document.documentElement.style.setProperty('--themecolor-dark0', themecolor_dark0);
	document.documentElement.style.setProperty('--themecolor-dark', themecolor_dark);
	document.documentElement.style.setProperty('--themecolor-dark2', themecolor_dark2);
	document.documentElement.style.setProperty('--themecolor-dark3', themecolor_dark3);
	document.documentElement.style.setProperty('--themecolor-light', themecolor_light);
	document.documentElement.style.setProperty('--themecolor-rgbstr', themecolor_rgbstr);

	if (rgb2gray(RGB['R'], RGB['G'], RGB['B']) < 50){
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

/*打字效果*/
function typeEffect($element, text, now, interval){
	if (now > text.length){
		setTimeout(function(){
			$element.removeClass("typing-effect");
		}, 1000);
		return;
	}
	$element[0].innerText = text.substring(0, now);
	setTimeout(function(){typeEffect($element, text, now + 1, interval)}, interval);
}
function startTypeEffect($element, text, interval){
	$element.addClass("typing-effect");
	$element.attr("style", "--animation-cnt: " + Math.ceil(text.length * interval / 1000));
	typeEffect($element, text, 1, interval);
}
!function(){
	if ($(".banner-title").data("interval") != undefined){
		let interval = $(".banner-title").data("interval");
		let $title = $(".banner-title-inner");
		let $subTitle = $(".banner-subtitle");
		startTypeEffect($title, $title.data("text"), interval);
		if (!$subTitle.length){
			return;
		}
		setTimeout(function(){startTypeEffect($subTitle, $subTitle.data("text"), interval);}, Math.ceil($title.data("text").length * interval / 1000) * 1000);
	}
}();

/*一言*/
if ($(".hitokoto").length > 0){
	$.ajax({
		type: 'GET',
		url: "https://v1.hitokoto.cn",
		success: function(result){
			$(".hitokoto").text(result.hitokoto);
		},
		error: function(result){
			$(".hitokoto").text(__("Hitokoto 获取失败"));
		}
	});
}

/*Highlight.js*/
function randomString(len) {
	len = len || 32;
	let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	let res = "";
	for (let i = 0; i < len; i++) {
		res += chars.charAt(Math.floor(Math.random() * chars.length));
	}
	res[0] = chars.charAt(Math.floor(Math.random() * (chars.length - 10)));
	return res;
}
var codeOfBlocks = {};
function getCodeFromBlock(block){
	if (codeOfBlocks[block.id] != undefined){
		return codeOfBlocks[block.id];
	}
	let lines = $(".hljs-ln-code", block);
	let res = "";
	for (let i = 0; i < lines.length - 1; i++){
		res += lines[i].innerText;
		res += "\n";
	}
	res += lines[lines.length - 1].innerText;
	codeOfBlocks[block.id] = res;
	return res;
}
function highlightJsRender(){
	if (typeof(hljs) == "undefined"){
		return;
	}
	if (typeof(argonConfig.code_highlight.enable) == "undefined"){
		return;
	}
	if (!argonConfig.code_highlight.enable){
		return;
	}
	$("article pre.code").each(function(index, block) {
		if ($(block).hasClass("no-hljs")){
			return;
		}
		$(block).html("<code>" + $(block).html() + "</code>");
	});
	$("article pre > code").each(function(index, block) {
		if ($(block).hasClass("no-hljs")){
			return;
		}
		$(block).parent().attr("id", randomString());
		hljs.highlightBlock(block);
		hljs.lineNumbersBlock(block, {singleLine: true});
		$(block).parent().addClass("hljs-codeblock");
		if (argonConfig.code_highlight.hide_linenumber){
			$(block).parent().addClass("hljs-hide-linenumber");
		}
		if (argonConfig.code_highlight.break_line){
			$(block).parent().addClass("hljs-break-line");
		}
		$(block).attr("hljs-codeblock-inner", "");
		let copyBtnID = "copy_btn_" + randomString();
		$(block).parent().append(`<div class="hljs-control hljs-title">
				<div class="hljs-control-btn hljs-control-toggle-linenumber" tooltip-hide-linenumber="` + __("隐藏行号") + `" tooltip-show-linenumber="` + __("显示行号") + `">
					<i class="fa fa-list"></i>
				</div>
				<div class="hljs-control-btn hljs-control-toggle-break-line" tooltip-enable-breakline="` + __("开启折行") + `" tooltip-disable-breakline="` + __("关闭折行") + `">
					<i class="fa fa-align-left"></i>
				</div>
				<div class="hljs-control-btn hljs-control-copy" id=` + copyBtnID + ` tooltip="` + __("复制") + `">
					<i class="fa fa-clipboard"></i>
				</div>
				<div class="hljs-control-btn hljs-control-fullscreen" tooltip-fullscreen="` + __("全屏") + `" tooltip-exit-fullscreen="` + __("退出全屏") + `">
					<i class="fa fa-arrows-alt"></i>
				</div>
			</div>`);
		let clipboard = new ClipboardJS("#" + copyBtnID, {
			text: function(trigger) {
				return getCodeFromBlock($(block).parent()[0]);
			}
		});
		clipboard.on('success', function(e) {
			iziToast.show({
				title: __("复制成功"),
				message: __("代码已复制到剪贴板"),
				class: 'shadow',
				position: 'topRight',
				backgroundColor: '#2dce89',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-check',
				timeout: 5000
			});
		});
		clipboard.on('error', function(e) {
			iziToast.show({
				title: __("复制失败"),
				message: __("请手动复制代码"),
				class: 'shadow',
				position: 'topRight',
				backgroundColor: '#f5365c',
				titleColor: '#ffffff',
				messageColor: '#ffffff',
				iconColor: '#ffffff',
				progressBarColor: '#ffffff',
				icon: 'fa fa-close',
				timeout: 5000
			});
		});
	});
}
$(document).ready(function(){
	highlightJsRender();
	waterflowInit();
});
$(document).on("click" , ".hljs-control-fullscreen" , function(){
	let block = $(this).parent().parent();
	block.toggleClass("hljs-codeblock-fullscreen");
	if (block.hasClass("hljs-codeblock-fullscreen")){
		$("html").addClass("noscroll codeblock-fullscreen");
	}else{
		$("html").removeClass("noscroll codeblock-fullscreen");
	}
});
$(document).on("click" , ".hljs-control-toggle-break-line" , function(){
	let block = $(this).parent().parent();
	block.toggleClass("hljs-break-line");
});
$(document).on("click" , ".hljs-control-toggle-linenumber" , function(){
	let block = $(this).parent().parent();
	block.toggleClass("hljs-hide-linenumber");
});

/*时间差计算*/
function addPreZero(num, n) {
	var len = num.toString().length;
	while(len < n) {
		num = "0" + num;
		len++;
	}
	return num;
}
function humanTimeDiff(time){
	let now = new Date();
	time = new Date(time);
	let delta = now - time;
	if (delta < 0){
		delta = 0;
	}
	if (delta < 1000 * 60){
		return __("刚刚");
	}
	if (delta < 1000 * 60 * 60){
		return parseInt(delta / (1000 * 60)) + " " + __("分钟前");
	}
	if (delta < 1000 * 60 * 60 * 24){
		return parseInt(delta / (1000 * 60 * 60)) + " " + __("小时前");
	}
	let yesterday = new Date(now - 1000 * 60 * 60 * 24);
	yesterday.setHours(0);
	yesterday.setMinutes(0);
	yesterday.setSeconds(0);
	yesterday.setMilliseconds(0);
	if (time > yesterday){
		return __("昨天") + " " + time.getHours() + ":" + addPreZero(time.getMinutes(), 2);
	}
	let theDayBeforeYesterday = new Date(now - 1000 * 60 * 60 * 24 * 2);
	theDayBeforeYesterday.setHours(0);
	theDayBeforeYesterday.setMinutes(0);
	theDayBeforeYesterday.setSeconds(0);
	theDayBeforeYesterday.setMilliseconds(0);
	if (time > theDayBeforeYesterday && argonConfig.language.indexOf("zh") == 0){
		return __("前天") + " " + time.getHours() + ":" + addPreZero(time.getMinutes(), 2);
	}
	if (delta < 1000 * 60 * 60 * 24 * 30){
		return parseInt(delta / (1000 * 60 * 60 * 24)) + " " + __("天前");
	}
	let theFirstDayOfThisYear = new Date(now);
	theFirstDayOfThisYear.setMonth(0);
	theFirstDayOfThisYear.setDate(1);
	theFirstDayOfThisYear.setHours(0);
	theFirstDayOfThisYear.setMinutes(0);
	theFirstDayOfThisYear.setSeconds(0);
	theFirstDayOfThisYear.setMilliseconds(0);
	if (time > theFirstDayOfThisYear){
		if (argonConfig.dateFormat == "YMD" || argonConfig.dateFormat == "MDY"){
			return (time.getMonth() + 1) + "-" + time.getDate();
		}else{
			return time.getDate() + "-" + (time.getMonth() + 1);
		}
	}
	if (argonConfig.dateFormat == "YMD"){
		return time.getFullYear() + "-" + (time.getMonth() + 1) + "-" + time.getDate();
	}else if (argonConfig.dateFormat == "MDY"){
		return time.getDate() + "-" + (time.getMonth() + 1) + "-" + time.getFullYear();
	}else if (argonConfig.dateFormat == "DMY"){
		return time.getDate() + "-" + (time.getMonth() + 1) + "-" + time.getFullYear();
	}
}
function calcHumanTimesOnPage(){
	$(".human-time").each(function(){
		$(this).text(humanTimeDiff(parseInt($(this).data("time")) * 1000));
	});
}
calcHumanTimesOnPage();
setInterval(function(){
	calcHumanTimesOnPage()
}, 15000);


/*Console*/
!function(){
	console.log('%cTheme: %cArgon%cBy solstice23', 'color: rgba(255,255,255,.6); background: #5e72e4; font-size: 15px;border-radius:5px 0 0 5px;padding:10px 0 10px 20px;','color: rgba(255,255,255,1); background: #5e72e4; font-size: 15px;border-radius:0;padding:10px 15px 10px 0px;','color: #fff; background: #92A1F4; font-size: 15px;border-radius:0 5px 5px 0;padding:10px 20px 10px 15px;');
	console.log('%cVersion%c' + $("meta[name='theme-version']").attr("content"), 'color:#fff; background: #5e72e4;font-size: 12px;border-radius:5px 0 0 5px;padding:3px 10px 3px 10px;','color:#fff; background: #92a1f4;font-size: 12px;border-radius:0 5px 5px 0;padding:3px 10px 3px 10px;');
	console.log('%chttps://github.com/solstice23/argon-theme', 'font-size: 12px;border-radius:5px;padding:3px 10px 3px 10px;border:1px solid #5e72e4;');
}();
