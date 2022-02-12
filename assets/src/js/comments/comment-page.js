var $ = window.$;
import __ from '../i18n';
import NProgress from 'nprogress';
import {foldLongComments} from './comment-fold';
import {calcHumanTimesOnPage} from '../utils/time-calculation';
import {panguInit} from '../pangu';
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
			$(".comment-item-text .comment-sticker.lazyload").lazyload(window.argonConfig.lazyload).removeClass("lazyload");
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
			$(".comment-item-text .comment-sticker.lazyload").lazyload(window.argonConfig.lazyload).removeClass("lazyload");
		},
		error : function(){
			window.location.href = url;
		}
	});
});