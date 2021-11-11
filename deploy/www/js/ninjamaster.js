/* eslint-disable func-names */
/* eslint-disable prefer-arrow-callback */
/* Functions for ninjamaster */
/* jshint browser: true, white: true, plusplus: true */
/* global $ */

// eslint-disable-next-line import/extensions
import api from './api.js';

$(function initializeNMPage() {
  // Handle the show/hide sections
  $('.show-hide-next').click(function showHideNext() {
    $(this).parent().next().slideToggle();
  }).html("<span class='slider'><span class='dot'></span></span>");
  $('.show-hide-next').parent().next().toggle();

  $('#start-deactivate').click(() => {
    $('#start-deactivate').hide();
    $('#deactivate-character').fadeIn('slow');
  });

  // So that we can turn off problematic characters
  $('#deactivate-character').click(function () {
    const charId = $(this).data('char-id');
    api.deactivateChar(charId).then(() => {
      // eslint-disable-next-line no-unused-expressions
      window && window.location.reload();
    });
  });

  // Turn back on So that we can turn off problematic characters
  $('#reactivate-character').click(function () {
    const charId = $(this).data('char-id');
    api.reactivateChar(charId).then(() => {
      // eslint-disable-next-line no-unused-expressions
      window && window.location.reload();
    });
  });
});
