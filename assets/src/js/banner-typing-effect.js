const typeEffect = (element, text, now, interval) => {
	if (now > text.length){
		setTimeout(function(){
			element.classList.remove("typing-effect");
		}, 1000);
		return;
	}
	element.innerText = text.substring(0, now);
	setTimeout(() => {typeEffect(element, text, now + 1, interval)}, interval);
}
const startTypeEffect = (element, text, interval) => {
	element.classList.add("typing-effect");
	element.setAttribute("style", "--animation-cnt: " + Math.ceil(text.length * interval / 1000));
	typeEffect(element, text, 1, interval);
}
document.addEventListener("DOMContentLoaded", () => {
	if (document.getElementsByClassName("banner-title")[0].hasAttribute("data-interval")){
		let interval = document.getElementsByClassName("banner-title")[0].getAttribute("data-interval");
		let title = document.getElementsByClassName("banner-title-inner")[0];
		let subTitle = document.getElementsByClassName("banner-subtitle")[0];
		startTypeEffect(title, title.getAttribute("data-text"), interval);
		if (!subTitle){
			return;
		}
		setTimeout(
			() => {
				startTypeEffect(subTitle, subTitle.getAttribute("data-text"), interval);
			},
			Math.ceil(title.getAttribute("data-text").length * interval / 1000) * 1000
		);
	}
});