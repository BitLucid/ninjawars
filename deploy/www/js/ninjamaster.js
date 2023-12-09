/* eslint-disable func-names */
/* eslint-disable prefer-arrow-callback */
/* Functions for ninjamaster */

// eslint-disable-next-line import/extensions
import api from './api.js';

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.ninjamaster = true;

$(function initializeNMPage() {
  // Handle the show/hide sections
  $('.show-hide-next')
    .click(function showHideNext() {
      $(this).parent().next().slideToggle();
    })
    .html("<span class='slider'><span class='dot'></span></span>");
  $('.show-hide-next').parent().next().toggle();

  $('#start-deactivate').on('click', () => {
    $('#start-deactivate').hide();
    $('#deactivate-character').fadeIn('slow');
  });

  // So that we can turn off problematic characters
  $('#deactivate-character').on('click', function () {
    const charId = $(this).data('char-id');
    api.deactivateChar(charId).then(() => {
      // eslint-disable-next-line no-unused-expressions
      window?.location?.reload();
    });
  });

  // Batch deactivations
  $('button.deactivate-character').on('click', function () {
    const refer = $(this);
    const charId = $(this).data('char-id');
    api.deactivateChar(charId).then(() => {
      // eslint-disable-next-line no-unused-expressions
      refer.parent().hide();
    });
  });

  // Turn back on So that we can turn off problematic characters
  $('#reactivate-character').on('click', function () {
    const charId = $(this).data('char-id');
    api.reactivateChar(charId).then(() => {
      // eslint-disable-next-line no-unused-expressions
      window?.location?.reload();
    });
  });
});
