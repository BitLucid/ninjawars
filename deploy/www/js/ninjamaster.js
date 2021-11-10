/* eslint-disable prefer-arrow-callback */
/* Functions for ninjamaster */
/* jshint browser: true, white: true, plusplus: true */
/* global $ */

import { api } from './api';

$(function initializeNMPage() {
  $('.show-hide-next').click(function showHideNext() {
    $(this).parent().next().slideToggle();
  }).html("<span class='slider'><span class='dot'></span></span>");
  $('.show-hide-next').parent().next().toggle();
});

// Hit the api to deactivate the character
function deactivateChar(charId, callback) {
  api.deactivateChar(charId, callback);
}

// Reload the page afterwards
function afterDeactivation(data, callback) {
  return function reloadThenCallback() {
    console.log(data);
    // eslint-disable-next-line no-unused-expressions
    window && window.location.reload();
    return callback && callback();
  };
}

// Check in the ui for an additional confirm of deactivation
// eslint-disable-next-line no-unused-vars
function confirmDeactivation(e, charId, callback) {
  const message = 'Are you sure you want to deactivate this player character?';
  const result = (window && window.confirm(message)) || false;
  if (result === true) {
    e.preventDefault();
    e.stopPropagation();
    deactivateChar(charId, function callForAfterDeactivation() {
      afterDeactivation(charId, callback);
    });
    return true;
  }
  e.preventDefault();
  e.stopPropagation();
  return false;
}

/* exported deactivateChar afterDeactivation confirmDeactivation */