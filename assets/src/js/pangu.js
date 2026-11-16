import pangu from 'pangu';
export const panguInit = (root = document) => {
	const groups = {article: ".post-content", comment: "#comments", shuoshuo: ".shuoshuo-content"};
	Object.keys(groups).forEach(group => {
		if (window.argonConfig.pangu.indexOf(group) >= 0){
			root.querySelectorAll(groups[group]).forEach(node => pangu.spacingNode(node));
		}
	});
}
panguInit();
