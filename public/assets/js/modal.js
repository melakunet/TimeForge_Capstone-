(function () {
  'use strict';

  function openModal(modal) {
    if (!modal) return;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');

    var firstFocusable = modal.querySelector('input, select, textarea, button, a[href]');
    if (firstFocusable) firstFocusable.focus();
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  }

  function init() {
    var triggers = document.querySelectorAll('[data-modal-target]');

    for (var i = 0; i < triggers.length; i++) {
      (function (btn) {
        btn.addEventListener('click', function () {
          var id = btn.getAttribute('data-modal-target');
          if (!id) return;
          var modal = document.getElementById(id);
          openModal(modal);
        });
      })(triggers[i]);
    }

    // Close buttons + backdrop + ESC
    document.addEventListener('click', function (e) {
      var closeBtn = e.target.closest('[data-modal-close]');
      if (closeBtn) {
        var modal = closeBtn.closest('.modal');
        closeModal(modal);
        return;
      }

      // backdrop click
      var backdrop = e.target.classList && e.target.classList.contains('modal');
      if (backdrop) {
        closeModal(e.target);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      var open = document.querySelector('.modal.is-open');
      if (open) closeModal(open);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
