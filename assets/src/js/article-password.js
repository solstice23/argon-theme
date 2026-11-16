var $ = window.$;
$(document).on("submit" , ".post-password-form" , function(){
	if (window.argonConfig.disable_pjax) return;
	$("input[type='submit']", this).attr("disabled", "disabled");
	let url = $(this).attr("action");
	$.pjax.form(this, {
		push: false,
		replace: false
	});
	return false;
});