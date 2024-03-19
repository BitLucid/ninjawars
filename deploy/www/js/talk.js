/* Assist sending of messages to clan or individuals */
/* global NW, refocus, focusArea */

import api from './api.js';

// eslint-disable-next-line no-var
// var presence = window.presence || {};
// presence.talk = true;

// eslint-disable-next-line no-var, no-unused-vars
var logger = console || {
  log: () => {
    /* no-op */
  },
  debug: () => {
    /* no-op */
  },
};

function performTalk() {
  // Cache the last messaged character after send
  if ($('#send-to').val() === '') {
    $('#send-to').val(NW.storage.appState.get('last_messaged', ''));
  }
  $('#message-form').on('submit', () => {
    NW.storage.appState.set('last_messaged', $('#send-to').val());
    return true;
  });

  let timer = null;

  // Hit the api to send out communications
  $('#email-messages').on('click', () => {
    if ($('#email-messages').prop('disabled')) return;
    // Disable the button to prevent double sending
    $('#email-messages').prop('disabled', true);
    // if it's disabled, ignore the click
    // eslint-disable-next-line no-unused-vars
    const resu = api.sendCommunications();
    // re-enable the button after a delay
    if (timer) clearTimeout(timer);
    timer = setTimeout(() => {
      $('#email-messages').prop('disabled', false);
    }, 5000);
  });

  // eslint-disable-next-line no-alert
  $('#delete-messages form').on(
    'submit',
    () => window && window.confirm('Delete all messages?'),
  ); // *** boolean return value ***

  // If a refocus is requested, because a message was just sent, then refocus on the area.
  if (
    typeof refocus !== 'undefined'
        && refocus
    && typeof focusArea !== 'undefined'
    && focusArea
  ) {
    if (focusArea === 'clan') {
      $('input#message-clan').trigger('focus');
    } else {
      $('input#message-to-ninja').trigger('focus');
    }
  }
  return true;
}

$(() => {
  performTalk();
});
