import pangu from 'pangu';
export const panguInit = () => {
	if (window.argonConfig.pangu.indexOf("article") >= 0){
		pangu.spacingElementByClassName('post-content');
	}
	if (window.argonConfig.pangu.indexOf("comment") >= 0){
		pangu.spacingElementById('comments');
	}
	if (window.argonConfig.pangu.indexOf("shuoshuo") >= 0){
		pangu.spacingElementByClassName('shuoshuo-content');
	}
}
panguInit();
