import './css/horizontal-scroll.scss';
document.addEventListener("wheel", function(e) {
    for (let target = e.target; target && target != this; target = target.parentNode) {
        if (target.matches(".horizontal-scroll")) {
			if (target.scrollWidth <= target.clientWidth) return;
			e.preventDefault();
			target.scrollLeft += e.deltaY;
            break;
        }
    }
}, { passive: false });