/* eslint-disable prefer-arrow-callback */
/* Functions for ninjamaster */
/* jshint browser: true, white: true, plusplus: true */
/* global $ */


// Hit the api to deactivate the character
function deactivateChar(charId, callback) {
  // Deactivate a player character by id
  return $.getJSON(
    // eslint-disable-next-line prefer-template
    '/api?type=deactivate_char&data='
    + charId
    + '&jsoncallback=?',
    callback
  );
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
  // eslint-disable-next-line no-var
  var message = 'Are you sure you want to deactivate this player character?';
  // eslint-disable-next-line no-var
  var result = window && window.confirm(message);
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