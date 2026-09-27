(function () {
	'use strict';

	var desktop = window.matchMedia('(min-width: 901px)');
	var openNav = null;
	var openButton = null;

	document.documentElement.classList.remove('no-js');

	function setExpanded(button, expanded) {
		button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		if (expanded) {
			openNav = button.closest('.site-nav') || button.parentNode;
			openButton = button;
		} else {
			openNav = null;
			openButton = null;
		}
	}

	document.querySelectorAll('.site-nav-toggle').forEach(function (button) {
		if (button.tagName !== 'BUTTON') return;

		var menuId = button.getAttribute('aria-controls');
		var menu = menuId ? document.getElementById(menuId) : null;
		if (!menu) return;

		button.addEventListener('click', function () {
			var open = button.getAttribute('aria-expanded') === 'true';
			setExpanded(button, !open);
		});

		menu.addEventListener('click', function (event) {
			if (event.target.closest && event.target.closest('a')) {
				setExpanded(button, false);
			}
		});
	});

	document.addEventListener('click', function (event) {
		if (openNav && !openNav.contains(event.target)) {
			setExpanded(openButton, false);
		}
	}, true);

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && openButton) {
			var current = openButton;
			setExpanded(current, false);
			current.focus();
		}
	});

	function closeOnDesktop() {
		if (desktop.matches) {
			document.querySelectorAll('.site-nav-toggle').forEach(function (button) {
				if (button.getAttribute('aria-expanded') === 'true') setExpanded(button, false);
			});
		}
	}
	if (desktop.addEventListener) {
		desktop.addEventListener('change', closeOnDesktop);
	} else if (desktop.addListener) {
		desktop.addListener(closeOnDesktop);
	}
})();