/* Simple defaults for the casino page */
/* jshint browser: true, white: true, plusplus: true */
/* jslint browser: true, white: true, plusplus: true */
/* global $, NW */
$(() => {
  // Get stored bet
  $('#bet').val(NW.storage.appState.get('bet', 1));
  // Store latest bet
  $('#coin_flip').submit(() => {
    NW.storage.appState.set('bet', $('#bet').val());
    return true;
  });
});
