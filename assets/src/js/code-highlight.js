import {randomString} from './utils/random';
import hljs from 'highlight.js/lib/common';
import ClipboardJS from 'clipboard';
import iziToast from 'izitoast';
import 'izitoast/dist/css/iziToast.css';
import __ from './i18n';
var $ = window.$;
window.hljs = hljs;
require('highlightjs-line-numbers.js');

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
export const highlightJsRender = () => {
	if (typeof(hljs) == "undefined"){
		return;
	}
	if (typeof(window.argonConfig.code_highlight.enable) == "undefined"){
		return;
	}
	if (!window.argonConfig.code_highlight.enable){
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

        let languageClass = $(block)[0].className.split(/\s+/).find(it => it.startsWith("language-"))
        let language = languageClass?.substring(9)
        if (language && language !== '' && hljs.getLanguage(language) === undefined) {
            $(block)[0].classList.remove(languageClass)
            $(block)[0].classList.add("language-plaintext")
            $(block).attr("data-original-language", language)
        }

		hljs.highlightElement(block);
		hljs.lineNumbersBlock(block, {singleLine: true});
		$(block).parent().addClass("hljs-codeblock");
		if (window.argonConfig.code_highlight.hide_linenumber){
			$(block).parent().addClass("hljs-hide-linenumber");
		}
		if (window.argonConfig.code_highlight.break_line){
			$(block).parent().addClass("hljs-break-line");
		}
		if (window.argonConfig.code_highlight.transparent_linenumber){
			$(block).parent().addClass("hljs-transparent-linenumber");
		}
		$(block).attr("hljs-codeblock-inner", "");
		let copyBtnID = "copy_btn_" + randomString();
		$(block).parent().append(`<div class="hljs-control hljs hljs-title">
				<div class="hljs-control-btn hljs-control-toggle-linenumber" tooltip-hide-linenumber="` + __("隐藏行号") + `" tooltip-show-linenumber="` + __("显示行号") + `">
					<i class="fa fa-list"></i>
				</div>
				<div class="hljs-control-btn hljs-control-toggle-break-line" tooltip-enable-breakline="` + __("开启折行") + `" tooltip-disable-breakline="` + __("关闭折行") + `">
					<i class="fa fa-align-left"></i>
				</div>
				<div class="hljs-control-btn hljs-control-copy" id="${copyBtnID}" tooltip="` + __("复制") + `">
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

document.addEventListener('DOMContentLoaded', function() {
	highlightJsRender();

	$(":root").css("--hljs-background-color", $(".hljs").css("background-color"))
});