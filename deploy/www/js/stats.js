/**
 * General behaviors for the stats page.
 */
/* global $ */

// eslint-disable-next-line import/extensions
import { debounce, logger } from './utils.js';

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.stats = true;

/**
 * Update the Description area text on the fly
 */
(() => {
  $('#description').on(
    'input',
    debounce((e) => {
      logger().log('Field change registered.');
      $('#ninja-description-append').text($(e.target).val());
    }, 50),
  );
})();
