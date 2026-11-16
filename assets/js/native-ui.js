(function () {
	if (window.argonInstallPjaxLifecycle) return;

	// Preserve table semantics and give wide classic and block tables their own viewport.
	function argonPrepareReadingContent(root) {
		root.querySelectorAll('.post-content table').forEach(table => {
			if (table.closest('pre, .argon-table-scroll')) return;
			let viewport = table.closest('figure.wp-block-table');
			if (!viewport) {
				viewport = document.createElement('div');
				table.before(viewport);
				viewport.append(table);
			}
			viewport.classList.add('argon-table-scroll');
			viewport.tabIndex = 0;
			const label = table.getAttribute('aria-label') || (table.caption && table.caption.textContent.trim());
			if (label) {
				viewport.setAttribute('role', 'region');
				viewport.setAttribute('aria-label', label);
			}
		});
	}

	function argonInstallPjaxLifecycle($, hooks) {
		const replaced = new WeakSet();
		const scrollPositions = new Map();
		const isMain = event => event.target.id === 'primary';

		function prepareCatalog() {
			const catalog = document.getElementById('leftbar_catalog');
			if (!catalog) return;
			$(catalog).headIndex({
				articleWrapSelector: '#post_content',
				indexBoxSelector: '#leftbar_catalog',
				scrollWrap: catalog,
				subItemBoxClass: 'index-subItem-box',
				itemClass: 'index-item',
				linkClass: 'index-link',
				offset: 80,
			});
		}

		$(window).on('scroll.argonCatalog', () => $('#leftbar_catalog').triggerHandler('scroll'));
		prepareCatalog();

		function saveScroll() {
			const state = $.pjax.state;
			if (!state) return;
			scrollPositions.delete(state.id);
			scrollPositions.set(state.id, [window.scrollX, window.scrollY]);
			if (scrollPositions.size > 100) scrollPositions.delete(scrollPositions.keys().next().value);
		}

		$(document).on('pjax:popstate', event => {
			if (isMain(event)) saveScroll();
		}).on('pjax:start', (event, xhr, options) => {
			if (!isMain(event) || !xhr) return;
			if (!options.id && options.push) saveScroll();
			hooks.start();
		}).on('pjax:beforeReplace', (event, contents, options) => {
			if (!isMain(event)) return;
			replaced.add(options);
			const commentButton = document.getElementById('fabtn_go_to_comment');
			if (commentButton) commentButton.classList.toggle('d-none', !$('#post_comment', contents[0]).length);
		}).on('pjax:complete', (event, xhr, status, options) => {
			if (!isMain(event)) return;
			// GET fallback remains the responsibility of jquery-pjax. A failed POST
			// stays on the current page so a visitor can choose whether to retry.
			if (status !== 'success' && $(options.target).is('.post-password-form')){
				$(options.target).find('[type=submit]').prop('disabled', false);
			}
			hooks.complete();
		}).on('pjax:end', (event, xhr, options) => {
			if (!isMain(event) || !replaced.delete(options)) return;
			argonPrepareReadingContent(event.target);
			if (xhr) argonRenderMath(event.target);
			hooks.replace(event.target, !xhr);
			prepareCatalog();
			if (typeof window.pjaxLoaded === 'function'){
				try { window.pjaxLoaded(); } catch (error) { console.error(error); }
			}

			const position = scrollPositions.get(options.id);
			if (position){
				requestAnimationFrame(() => {
					if ($.pjax.state && $.pjax.state.id === options.id){
						$('html, body').stop(true);
						window.scrollTo(position[0], position[1]);
					}
				});
			}
		});
	}

	function argonRenderMath(root) {
		const math = window.MathJax;
		if (math && math.Hub) {
			math.Hub.Queue(['Typeset', math.Hub, root]);
		} else if (math && typeof math.typesetPromise === 'function') {
			math.typesetPromise([root]).catch(error => console.error(error));
		}
		if (typeof window.renderMathInElement === 'function') {
			window.renderMathInElement(root, {
				delimiters: [
					{left: '$$', right: '$$', display: true},
					{left: '\\[', right: '\\]', display: true},
					{left: '\\(', right: '\\)', display: false},
					{left: '$', right: '$', display: false},
				],
			});
		}
	}

	window.argonInstallPjaxLifecycle = argonInstallPjaxLifecycle;
	argonPrepareReadingContent(document);

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
