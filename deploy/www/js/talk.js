/* Assist sending of messages to clan or individuals */
/* jshint browser: true, white: true, plusplus: true */
/* global $, NW, refocus */

// eslint-disable-next-line no-var
// var presence = window.presence || {};
// presence.talk = true;

// eslint-disable-next-line no-var
var logger = console || { log: () => { /* no-op */ } };

function performTalk() {
  logger.debug('performTalk() run');
  // Cache the last messaged character after send
  if ($('#send-to').val() === '') {
    $('#send-to').val(NW.storage.appState.get('last_messaged', ''));
  }
  $('#message-form').submit(() => {
    NW.storage.appState.set('last_messaged', $('#send-to').val());
    return true;
  });

  // eslint-disable-next-line no-alert
  $('#delete-messages form').submit(() => (window && window.confirm('Delete all messages?'))); // *** boolean return value ***

  // If a refocus is requested, because a message was just sent, then refocus on the area.
  if (
    typeof refocus !== 'undefined'
        && refocus
        && typeof focus !== 'undefined'
        && focus
  ) {
    if (focus === 'clan') {
      $('input#message-clan').focus();
    } else {
      $('input#message-to-ninja').focus();
    }
  }
  return true;
}

$(() => {
  performTalk();
});
