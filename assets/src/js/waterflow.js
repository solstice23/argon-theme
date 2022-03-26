var $ = window.$;
export let waterflowInit = (keepInColumn = false) => {
	if (window.argonConfig.waterflow_columns == "1") {
		return;
	}
	$("#main.article-list img").each(function(index, ele){
		ele.onload = function(){
			waterflowInit();
		}
	});
	let columns;
	if (window.argonConfig.waterflow_columns == "2and3") {
		if ($("#main").outerWidth() > 1000) {
			columns = 3;
		} else {
			columns = 2;
		}
	}else{
		columns = parseInt(window.argonConfig.waterflow_columns);
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
			if (keepInColumn) {
				pos = parseInt($item.attr("waterflow-column"));
			}
			$item.css("top", heights[pos] + "px")
				.css("left", (pos * $item.outerWidth() + 10 * pos) + "px");
			heights[pos] += itemHeight;
			$item.css("z-index", pos)
				.attr("waterflow-column", pos);
		});
	}
	if ($(".waterflow-placeholder").length) {
		$(".waterflow-placeholder").css("height", getMaxHeight() + "px");
	}else{
		$container.prepend("<div class='waterflow-placeholder' style='height: " + getMaxHeight() +"px;'></div>");
	}
}
document.addEventListener('DOMContentLoaded', function() {
	waterflowInit();
	if (window.argonConfig.waterflow_columns != "1") {
		$(window).resize(function(){
			waterflowInit();
		});
		new MutationObserver(function(mutations, observer){
			waterflowInit();
		}).observe(document.querySelector("#primary"), {
			'childList': true
		});
	}
});