import __ from './i18n'
import QRCode from 'qrcode';
import ClipboardJS from 'clipboard';
import iziToast from 'izitoast';
import 'izitoast/dist/css/iziToast.css';
import './css/share.scss'

const copyLinkInit = () => {
	const clipboard = new ClipboardJS("#share_copy_link", {
		text: function(trigger) {
			return shareInfo.url;
		}
	});
	clipboard.on('success', function(e) {
		iziToast.show({
			title: __("链接已复制"),
			message: __("链接已复制到剪贴板"),
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
			message: __("请手动复制链接"),
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
}
const QRCodeShareInit = () => {
	let QRBtn = document.getElementById("share_qrcode");
	QRBtn.parentNode.replaceChild(QRBtn.cloneNode(true), QRBtn);
	QRBtn = document.getElementById("share_qrcode");
	document.getElementById("share_qrcode").addEventListener("click", async () => {
		const img = await QRCode.toDataURL(shareInfo.url, {
			toSJISFunc: QRCode.toSJIS,
			color: {
				dark: getComputedStyle(document.documentElement).getPropertyValue('--themecolor').toUpperCase().trim(),
				light: '#FFFFFFFF'
			},
			width: 400
		});
		iziToast.show({
			class: 'qrcode-share-toast',
			message: `<img class="qrcode-share-img" src="${img}">`,
			zindex: 99999999,
			layout: 1,
			close: true,
			closeOnEscape: true,
			closeOnClick: false,
			displayMode: 'once',
			position: 'center',
			timeout: 9999999999,
			animateInside: false,
			drag: false,
			progressBar: false,
			overlay: true,
			overlayClose: false,
			overlayColor: 'rgba(0, 0, 0, 0.6)',
			transitionIn: 'fadeInUp',
			transitionOut: 'fadeOut',
			transitionInMobile: 'fadeInUp',
			transitionOutMobile: 'fadeOut'
		});
	});
}

export const shareInit = () => {
	if (!document.getElementById("share")){
		return;
	}
	var shareInfo = window.shareInfo;
	document.getElementById("share_show").addEventListener("click", () => {
		document.getElementById("share_container").classList.add("opened");
	});
	QRCodeShareInit();
	const url = shareInfo.url;
	const title = encodeURIComponent(shareInfo.title);
	const ogImgMeta = document.querySelector('meta[property="og:image"]');
	const image = ogImgMeta ? ogImgMeta.content : "";
	const source = shareInfo.source;
	const description = encodeURIComponent(shareInfo.description);
	(document.querySelector("#share .icon-telegram") ?? {}).href = `https://telegram.me/share/url?url=${url}&text=${title}`;
	(document.querySelector("#share .icon-twitter") ?? {}).href = `https://twitter.com/intent/tweet?text=${title}&url=${url}`;
	(document.querySelector("#share .icon-facebook") ?? {}).href = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
	(document.querySelector("#share .icon-weibo") ?? {}).href = `https://service.weibo.com/share/share.php?url=${url}&title=${title}`;
	(document.querySelector("#share .icon-qq") ?? {}).href = `https://connect.qq.com/widget/shareqq/index.html?url=${url}&title=${title}&source=${source}&desc=${description}&pics=${image}`;
	(document.querySelector("#share .icon-qzone") ?? {}).href = `http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=${url}&title=${title}&desc=${description}&summary=${description}&site=${source}&pics=${image}`;
	(document.querySelector("#share .icon-douban") ?? {}).href = `https://www.douban.com/sharebutton?image=${image}&href=${url}&name=${title}&text=${description}`;
}

document.addEventListener("DOMContentLoaded", () => {
	shareInit();
	copyLinkInit();
});