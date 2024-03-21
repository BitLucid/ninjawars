/* eslint-disable func-names */
/* eslint-disable prefer-arrow-callback */

// eslint-disable-next-line import/extensions
import api from './api.js';

// @ts-check

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.homepage = true;
const { debug } = console || { log: () => { /* noop */ }, debug: () => { /* noop */ } };

const pollCommunications = async () => {
  if (!(window.NW && window.NW.loggedIn)) return null;
  try {
    const comm = await api.unreadCommunications();
    const data = await comm.json();
    debug('Polling communications', data);
    return data;
  } catch (err) {
    debug('Error polling communications', err);
    return null;
  }
};

$(function indexBehaviors() {
  debug('Homepage behaviors loading');

  const horizontalClass = 'partial';
  $('#chat-toggle').on('click', function () {
    $('#main-column').toggleClass(horizontalClass);
    $('aside').fadeToggle('slow');
  });
  $('aside').hide();
  $('#main-column').removeClass(horizontalClass);
  // Fix for weird viewport sizing

  // if logged in, then pull the unread count
  let unreadTimer = null;
  debug('Logged in, checking unread count');
  setTimeout(async () => {
    // Running initial poll once
    const communications = await pollCommunications();
    if (communications) {
      $('#unread-count').text(communications.messages);
    }
  }, 1); // ms delay, so almost immediate the first time
  // eslint-disable-next-line no-unused-vars
  unreadTimer = setInterval(
    async () => {
      const communications = await pollCommunications();
      if (communications) {
        $('#unread-count').text(communications.messages);
      }
    },
    60 * 1000,
  );
});
