/* TailWell — progressive-enhancement navigation.
   The primary menu lives inside a <details> element. A closed <details>
   hides its content no matter what CSS says, so on desktop (where the
   summary is visually hidden) the menu would disappear. This script keeps
   the menu open at desktop widths and leaves native toggle behaviour on
   mobile. If JS is unavailable the menu stays collapsed on small screens
   but the summary remains a real, keyboard-operable control. */
(function () {
  'use strict';

  var details = document.querySelector('.nav-toggle');
  if (!details) return;

  var mq = window.matchMedia('(min-width: 901px)');

  function sync() {
    details.open = mq.matches;
  }

  function close() {
    if (details.open) details.open = false;
  }

  sync();

  if (typeof mq.addEventListener === 'function') {
    mq.addEventListener('change', sync);
  } else if (typeof mq.addListener === 'function') {
    mq.addListener(sync);
  }

  details.addEventListener('click', function (event) {
    if (!mq.matches && event.target.closest('.main-nav a')) close();
  });

  document.addEventListener('click', function (event) {
    if (!mq.matches && details.open && !details.contains(event.target)) close();
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !mq.matches && details.open) {
      close();
      var summary = details.querySelector('summary');
      if (summary) summary.focus();
    }
  });
})();
