/* Assist sending of messages to clan or individuals */
/* global NW, refocus, focusArea */

/* eslint max-lines-per-function: ["error", 100] */

import api from './api.js';

// eslint-disable-next-line no-var
// var presence = window.presence || {};
// presence.talk = true;

// eslint-disable-next-line no-var, no-unused-vars
const { debug } = console || {
  log: () => {
    /* no-op */
  },
  debug: () => {
    /* no-op */
  },
};

/**
 * On the messages page, caches last messaged
 * and provides interaction functionality for
 * js actions on the page
 * @returns {boolean}
 */
const performTalk = () => {
  // Cache the last messaged character after send
  if ($('#send-to').val() === '') {
    $('#send-to').val(NW.storage.appState.get('last_messaged', ''));
  }
  $('#message-form').on('submit', () => {
    NW.storage.appState.set('last_messaged', $('#send-to').val());
    return true;
  });

  // Hit the api to send out communications
  $('#email-messages').on('click', () => {
    if ($('#email-messages').prop('disabled')) return;
    // Disable the button to prevent double sending
    $('#email-messages').prop('disabled', true);
    // Change the button content to a spinner
    $('#email-messages').children().first().html('<i class="fa fa-spinner fa-spin"></i>');
    // if it's disabled, ignore the click

    // eslint-disable-next-line no-unused-vars
    const resu = api.sendCommunications().then((res) => res.json()).then((data) => {
      if (data && data.events) {
        $('#email-messages').children().first().html('<i class="fa fa-check"></i>');
      } else {
        debug('Failed to email messages');
      }
    }).catch((err) => {
      debug('Failed to email messages', err);
      $('#email-messages').prop('disabled', false);
      $('#email-messages').children().first().html('<i class="fa fa-times"></i>');
    });
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
};

window.performTalk = performTalk;

$(() => {
  performTalk();
});
