/**
 * m4p_userlocation
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

(function () {
  'use strict';

  // The submit button lives in a <noscript>, so here there is only the select.
  document.addEventListener('DOMContentLoaded', function () {
    Array.prototype.forEach.call(document.querySelectorAll('[data-m4p-userlocation-select]'), function (select) {
      select.addEventListener('change', function () {
        select.form.submit();
      });
    });
  });
})();
