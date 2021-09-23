/* Functions for ninjamaster */
/* jshint browser: true, white: true, plusplus: true */
/* global $ */
$(() => {
  $('.show-hide-next').click(function () {
    $(this).parent().next().slideToggle();
  }).text('... 👁');
  $('.show-hide-next').parent().next().toggle();
});

// Hit the api to deactivate the character
function deactivateChar(charId, callback) {
  // Deactivate a player character by id
  return $.getJSON(
    `/api?type=deactivate_char&data=${
      charId
    }&jsoncallback=?`,
    callback,
  );
}

// Reload the page afterwards
function afterDeactivation(data, callback) {
  return function () {
    console.log(data);
    window.location.reload();
    return callback && callback();
  };
}

// Check in the ui for an additional confirm of deactivation
function confirmDeactivation(e, charId, callback) {
  const message = 'Are you sure you want to deactivate this player character?';
  const result = window.confirm(message);
  if (result === true) {
    e.preventDefault();
    e.stopPropagation();
    deactivateChar(charId, () => {
      afterDeactivation(charId, callback);
    });
    return true;
  }
  e.preventDefault();
  e.stopPropagation();
  return false;
}

/* exported deactivateChar afterDeactivation confirmDeactivation */
