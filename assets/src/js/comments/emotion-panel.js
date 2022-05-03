import '../css/comment-emotion.scss'
var $ = window.$;
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