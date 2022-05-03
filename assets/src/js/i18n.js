var translation = {};
translation['en_US'] = require('./i18n/en_US').default;
translation['ru_RU'] = require('./i18n/ru_RU').default;
translation['zh_TW'] = require('./i18n/zh_TW').default;
export default (text) => {
	let lang = window.argonConfig.language;
	if (typeof(translation[lang]) == "undefined"){
		return text;
	}
	if (typeof(translation[lang][text]) == "undefined"){
		return text;
	}
	return translation[lang][text];
}