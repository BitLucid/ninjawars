/* Store the shop settings */
/* jshint browser: true, white: true, plusplus: true */
/* global $, NW, loggedIn */
$(() => {
  const auth = typeof loggedIn !== 'undefined' ? loggedIn : false;

  const quantity = NW.storage.appState.get('quantity', 1);

  $('#quantity').val(quantity);

  $('#shop_form').submit(() => {
    if (!auth) {
      return false;
    }
    NW.storage.appState.set('quantity', $('#quantity').val());
    return true;
  });
});
