/* eslint-disable camelcase */
/* Manipulate chats to and from the api, run websockets server by sudo make run-chat */
/* jshint browser: true, white: true, plusplus: true */
/* global $, NW, Chat, jQuery, window.conn, window.parent, window.WebSocket, window.Chat */

// eslint-disable-next-line no-var
var logger = window.logger || console || {
  log: () => { /* noop */ },
  error: () => { /* noop */ },
  info: () => { /* noop */ },
  warn: () => { /* noop */ },
  dir: () => { /* noop */ },
};

// eslint-disable-next-line no-var
var Chat = window && typeof window.Chat !== 'undefined' ? window.Chat : {};

(function jQueryShakePluginAttach($) {
  // Add shake plugin to jQuery
  // eslint-disable-next-line no-param-reassign
  $.fn.shake = function fn7(options) {
    // defaults
    const settings = {
      shakes: 2,
      distance: 10,
      duration: 400,
    };

    // merge options
    if (options) {
      $.extend(settings, options);
    }

    // make it so
    let pos;

    return this.each(function fn8() {
      const $this = $(this);

      // position if necessary
      pos = $this.css('position');

      if (!pos || pos === 'static') {
        $this.css('position', 'relative');
      }

      // shake it
      // eslint-disable-next-line no-plusplus
      for (let x = 1; x <= settings.shakes; x++) {
        $this
          .animate(
            { left: settings.distance * -1 },
            settings.duration / settings.shakes / 4,
          )
          .animate(
            { left: settings.distance },
            settings.duration / settings.shakes / 2,
          )
          .animate(
            { left: 0 },
            settings.duration / settings.shakes / 4,
          );
      }
    });
  };
}(jQuery));

/**
 * Websockets tutorial: http://socketo.me/docs/hello-world
 * Run the chat server via: php bin/chat-server.php

 Create chat object.
 Initialize maximum mini-chat listings...  it should be a pretty damn high starting amount.
 store the chat data locally, don't even have to cache this, it should pretty much be a js var
 allow sending of the chat from the input
 Set up a min/max refreshing cycle, theoretically with a typewatch kind of approach.
 Update the datastore with the latest chat info.
 Pass a chained callback using setTimeout
 Note that the Chat var may pre-exist
*/

// Chat object gets defined at the top

// Get all the initial chat messages and render them.
Chat.getExistingChatMessages = function fnCh() {
  logger.log('Existing chat messages requested');
  const since = '1424019122';

  $.getJSON(
    `/api?type=new_chats&since=${encodeURIComponent(since)
    }&jsoncallback=?`,
    (data) => {
      logger.log('Existing chats data found:');
      logger.log(data);
      window.storeChats = data;

      if (data && data.new_chats && data.new_chats.chats) {
        logger.log('Rendering pre-existing chat messages.');

        $.each(data.new_chats.chats, (key, val) => {
          Chat.renderChatMessage(val);
        });

        Chat.displayMessages();
      }
    },
  );
};

// Display at least the messages area when there are some messages in it.
Chat.displayMessages = function () {
  $('#mini-chat-display').show();
};

/* use the json data passed to add a new message to the mini-chat
 * Sample call:
 * Chat.renderChatMessage({'message':'Hi! I am chatting!',
 *              'uname':'tchalvak',
 *              'date':Date.now(),
 *              'sender_id':'128274'});
 */
Chat.renderChatMessage = function (p_data) {
  if (!p_data.message) {
    logger.error(
      'Error: Bad data sent in to renderChatMessage to be rendered',
    );
    logger.log(p_data);
    return false;
  }

  const fullLink = `player.php?player_id=${p_data.sender_id}`;
  const list = $('#mini-chat-display'); // The outer container.

  // clone the .chat-author template and .chat-message template
  const authorArea = list.find('.chat-author.template').clone();
  list.end();
  const messageArea = list.find('.chat-message.template').clone();
  list.end();

  const missingArea = (!list.length && 'list') || (!authorArea.length && 'authorArea') || (!messageArea.length && 'messageArea');

  if (missingArea) {
    logger.error(`Chat ${missingArea} not found to place chats in!`);
    return false;
  }

  // put the new content into the author and message areas
  authorArea
    .removeClass('template')
    .show()
    .find('a')
    .attr('href', fullLink)
    .text(p_data.uname)
    .end();
  messageArea.removeClass('template').show().text(p_data.message);
  list.prepend(authorArea, messageArea); // Prepend each message and author of a new chat.

  return true;
};

// Send the contents of the chat form input box.
// Sample url: https://nw.local/api?type=send_chat&msg=test&jsoncallback=alert
Chat.sendChatContents = function fnChSendCont(p_form) {
  if (p_form.message && p_form.message.value.length > 0) {
    const message = p_form.message.value;
    // Send a new chat.  // ASYNC
    $.getJSON(
      `/api?type=send_chat&msg=${encodeURIComponent(message)
      }&jsoncallback=?`,
      (echoed) => {
        if (!echoed) {
          Chat.rejected();
          return false;
        }
        // Place the chat in the interface on success.
        Chat.renderChatMessage(echoed);
        const success = Chat.send(echoed);
        if (success) {
          p_form.reset(); // Clear the chat form.
        }
        return success;
      },
    ).fail(() => {
      Chat.rejected();
      return false;
    });
  }
};

// Notify the user when a chat send was rejected.
Chat.rejected = function () {
  logger.error('Error: Failed to send the chat to server.');
  Chat.submissionArea().shake(); // Shake the submission area to show a failed send of a chat.
  return false;
};

// Send a messageData object to the websockets chat
Chat.send = function fnS77(messageData) {
  if (!Chat.canSend()) {
    return false;
  }
  let passfail = true;
  try {
    window.conn.send(JSON.stringify(messageData)); // Turn the data into a json object to pass.
    logger.info('Chat message sent.');
  } catch (ex) {
    // Maybe the connection send didn't work out.
    logger.warn(`Chat connection failed with: ${ex.message}`);
    passfail = false;
  }

  return passfail;
};

// Get the area that handles chat submission.
Chat.submissionArea = function () {
  return $('#post_msg_js');
};

// Once the chat is ready, initialize the ability to actually send chats.
Chat.chatReady = function () {
  Chat.displayMessages(); // Will display the whole messages area.
  const $submitter = Chat.submissionArea();

  if (Chat.canSend()) {
    $submitter.show();
  } else {
    $submitter.hide();
    logger.warn('Warning: Not logged in to be able to send messages.');
  }

  logger.info('Chat connected and ready');
  return true;
};

// Check whether logged in for chat sending
Chat.canSend = function () {
  const $area = Chat.submissionArea();
  return Boolean($area.data('logged-in'));
};

// Get the dev domain if on .local, fallback to live chat
Chat.domain = function fnChDomain(url) {
  // Use document element link processing to get url parts
  const link = document.createElement('a');
  link.setAttribute('href', url);
  const host = link.hostname;
  logger.log(`Connecting to chat api host: ${host}`);

  if (host.indexOf('.local') > -1) {
    return `chatapi.${host}`;
  } if (host.indexOf('localhost') > -1) {
    return host;
  }
  return 'chatapi.ninjawars.net';
};

// Add a typewatch IIFE
Chat.typewatch = (function fnChTypewatch() {
  let timer = 0;
  return function fnTypewatchAnon(callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
}());

// Try to connect to active websocket server, see README
$(() => {
  // Set up initial config.
  Chat.config = {
    server: Chat.domain(window.location.host),
    port: '8080',
    protocol: window.location.protocol === 'https:' ? 'wss' : 'ws',
  };
  if (window.WebSocket !== undefined) {
    // Browser is compatible.
    const connectionString = `${Chat.config.protocol}://${Chat.config.server}:${Chat.config.port}`;
    logger.log(`Connecting to ${connectionString}`);

    window.conn = new WebSocket(connectionString);
    /* eslint no-unused-vars: 0 */
    window.conn.onopen = function fnWebsocketConn(e) {
      logger.log('Websocket Connection established!');
      Chat.chatReady();
    };

    // Output information comes out here.
    window.conn.onmessage = function fnWebsocketMess(e) {
      if (e && typeof e.data !== 'undefined') {
        Chat.renderChatMessage(JSON.parse(e.data)); // Add msg to UI when available!
      }
    };
  } else {
    logger.log('Browser not compatible with websockets');
  }

  $('#chat-loading').show(); // Show the chat loading area.

  // Submit a chat message when the input box is used.
  const $submitArea = Chat.submissionArea();
  $submitArea.hide().submit(function fnSubmitChat(e) {
    e.preventDefault();
    const success = Chat.sendChatContents(this);
    if (!success) {
      /// TODO handle failure
    }
  });

  Chat.getExistingChatMessages();
});

// Set up refreshing of the village chat board page (will pause refreshing while someone is writing
function refreshpagechat() {
  const messageInput = $('#message');
  // Refresh only if text not being written.
  if (!messageInput.length || messageInput.val() === false || messageInput.val() === '') {
    if (
      window.parent
      && window.parent.main
      && window.parent.main.location
    ) {
      window.parent.main.location.reload();
    } else {
      window.location.reload();
    }
    return true;
  }
  return false;
}
