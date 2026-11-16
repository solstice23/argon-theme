(function () {
	const sidebar = document.getElementById('leftbar');
	if (!sidebar) return;

	const root = document.documentElement;
	const mobile = window.matchMedia('(max-width: 900px)');
	const triggers = document.querySelectorAll('#open_sidebar, #fabtn_open_sidebar');
	let opener = null;
	let wasOpen = false;

	function syncSidebar() {
		const open = mobile.matches && root.classList.contains('leftbar-opened');
		if (open === wasOpen) return;
		triggers.forEach(button => button.setAttribute('aria-expanded', String(open)));
		if (open) sidebar.focus({preventScroll: true});
		wasOpen = open;
	}

	triggers.forEach(button => {
		button.addEventListener('click', () => { opener = button; });
	});

	function dismissSidebar() {
		root.classList.remove('leftbar-opened');
		if (opener) opener.focus({preventScroll: true});
	}

	document.addEventListener('keydown', event => {
		if (event.key === 'Escape' && !event.defaultPrevented && wasOpen) {
			event.preventDefault();
			dismissSidebar();
		}
	});

	document.getElementById('sidebar_mask').addEventListener('click', dismissSidebar);
	// Search and PJAX can also close the drawer through the existing class.
	new MutationObserver(syncSidebar).observe(root, {attributes: true, attributeFilter: ['class']});
	window.addEventListener('resize', () => {
		if (!mobile.matches) root.classList.remove('leftbar-opened');
	});
	syncSidebar();
}());
