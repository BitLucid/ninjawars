/* Manipulate chats to and from the api, run websockets server by sudo make run-chat */
/*jshint browser: true, white: true, plusplus: true*/
/*global $, NW, Chat, jQuery, console, window.conn, window.parent, window.WebSocket, window.Chat*/
(function ($) {
    'use strict';
    // Add shake plugin to jQuery
    $.fn.shake = function (options) {
        // defaults
        var settings = {
            shakes: 2,
            distance: 10,
            duration: 400,
        };

        // merge options
        if (options) {
            $.extend(settings, options);
        }

        // make it so
        var pos;

        return this.each(function () {
            var $this = $(this);

            // position if necessary
            pos = $this.css('position');

            if (!pos || pos === 'static') {
                $this.css('position', 'relative');
            }

            // shake it
            for (var x = 1; x <= settings.shakes; x++) {
                $this
                    .animate(
                        { left: settings.distance * -1 },
                        settings.duration / settings.shakes / 4
                    )
                    .animate(
                        { left: settings.distance },
                        settings.duration / settings.shakes / 2
                    )
                    .animate(
                        { left: 0 },
                        settings.duration / settings.shakes / 4
                    );
            }
        });
    };
})(jQuery);

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

var Chat = undefined !== window.Chat ? window.Chat : {};

// Get all the initial chat messages and render them.
Chat.getExistingChatMessages = function () {
    'use strict';
    console.log('Existing chat messages requested');
    var since = '1424019122';

    $.getJSON(
        '/api?type=new_chats&since=' +
            encodeURIComponent(since) +
            '&jsoncallback=?',
        function (data) {
            console.log('Existing chats data found:');
            console.log(data);
            window.storeChats = data;

            if (data && data.new_chats && data.new_chats.chats) {
                console.log('Rendering pre-existing chat messages.');

                $.each(data.new_chats.chats, function (key, val) {
                    Chat.renderChatMessage(val);
                });

                Chat.displayMessages();
            }
        }
    );
};

// Display at least the messages area when there are some messages in it.
Chat.displayMessages = function () {
    'use strict';
    $('#mini-chat-display').show();
};

/* use the json data passed to add a new message to the mini-chat
 * Sample call:
 * Chat.renderChatMessage({'message':'Hi! I am chatting!',
 *							'uname':'tchalvak',
 *							'date':Date.now(),
 *							'sender_id':'128274'});
 */
Chat.renderChatMessage = function (p_data) {
    'use strict';
    if (!p_data.message) {
        console.log(
            'Error: Bad data sent in to renderChatMessage to be rendered'
        );
        console.log(p_data);
        return false;
    }

    var area = null;
    var fullLink = 'player.php?player_id=' + p_data.sender_id;
    var list = $('#mini-chat-display'); // The outer container.

    // clone the .chat-author template and .chat-message template
    var authorArea = list.find('.chat-author.template').clone();
    list.end();
    var messageArea = list.find('.chat-message.template').clone();
    list.end();

    if (!list.length) {
        area = 'list';
    } else if (!authorArea.length) {
        area = 'authorArea';
    } else if (!messageArea.length) {
        area = 'messageArea';
    }

    if (area) {
        console.log('Chat ' + area + ' not found to place chats in!');
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
Chat.sendChatContents = function (p_form) {
    'use strict';
    if (p_form.message && p_form.message.value.length > 0) {
        var message = p_form.message.value;
        // Send a new chat.  // ASYNC
        $.getJSON(
            '/api?type=send_chat&msg=' +
                encodeURIComponent(message) +
                '&jsoncallback=?',
            function (echoed) {
                if (!echoed) {
                    Chat.rejected();
                    return false;
                }
                // Place the chat in the interface on success.
                Chat.renderChatMessage(echoed);
                var success = Chat.send(echoed);
                if (success) {
                    p_form.reset(); // Clear the chat form.
                }
                return success;
            }
        ).fail(function () {
            Chat.rejected();
            return false;
        });
    }
};

// Notify the user when a chat send was rejected.
Chat.rejected = function () {
    'use strict';
    console.error('Error: Failed to send the chat to server.');
    Chat.submissionArea().shake(); // Shake the submission area to show a failed send of a chat.
    return false;
};

// Send a messageData object to the websockets chat
Chat.send = function (messageData) {
    'use strict';
    if (!Chat.canSend()) {
        return false;
    }
    var passfail = true;
    try {
        window.conn.send(JSON.stringify(messageData)); // Turn the data into a json object to pass.
        console.log('Chat message sent.');
    } catch (ex) {
        // Maybe the connection send didn't work out.
        console.log(ex.message);
        passfail = false;
    }

    return passfail;
};

// Get the area that handles chat submission.
Chat.submissionArea = function () {
    'use strict';
    return $('#post_msg_js');
};

// Once the chat is ready, initialize the ability to actually send chats.
Chat.chatReady = function () {
    'use strict';
    Chat.displayMessages(); // Will display the whole messages area.
    var $submitter = Chat.submissionArea();

    if (Chat.canSend()) {
        $submitter.show();
    } else {
        $submitter.hide();
        console.log('Warning: Not logged in to be able to send messages.');
    }

    console.log('Chat connected and ready');
    return true;
};

// Check whether logged in for chat sending
Chat.canSend = function () {
    'use strict';
    var $area = Chat.submissionArea();
    return Boolean($area.data('logged-in'));
};

// Get the dev domain if on .local, fallback to live chat
Chat.domain = function (url) {
    'use strict';
    // Use document element link processing to get url parts
    var link = document.createElement('a');
    link.setAttribute('href', url);
    var host = link.hostname;
    console.log(host);

    if (host.indexOf('.local') > -1) {
        return 'chatapi.' + host;
    } else if (host.indexOf('localhost') > -1) {
        return host;
    } else {
        return 'chatapi.ninjawars.net';
    }
};

// Add a typewatch IIFE
Chat.typewatch = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

// Try to connect to active websocket server, see README
$(function () {
    'use strict';
    // Set up initial config.
    Chat.config = {
        server: Chat.domain(window.location.host),
        port: '8080',
        protocol: window.location.protocol === 'https:' ? 'wss' : 'ws',
    };
    if (window.WebSocket !== undefined) {
        // Browser is compatible.
        var connectionString =
            Chat.config.protocol +
            '://' +
            Chat.config.server +
            ':' +
            Chat.config.port;
        console.log('Connecting to ' + connectionString);

        window.conn = new WebSocket(connectionString);
        /*eslint no-unused-vars: 0 */
        window.conn.onopen = function (e) {
            console.log('Websocket Connection established!');
            Chat.chatReady();
        };

        // Output information comes out here.
        window.conn.onmessage = function (e) {
            if (e && 'undefined' !== typeof e.data) {
                Chat.renderChatMessage(JSON.parse(e.data)); // Add the message to the interface when present!
            }
        };
    } else {
        console.log('Browser not compatible with websockets');
    }

    $('#chat-loading').show(); // Show the chat loading area.

    // Submit a chat message when the input box is used.
    var $submitArea = Chat.submissionArea();
    $submitArea.hide().submit(function (e) {
        e.preventDefault();
        var success = Chat.sendChatContents(this);
        if (!success) {
            ///TODO handle failure
        }
    });

    Chat.getExistingChatMessages();
});

// Set up refreshing of the village chat board page (will pause refreshing while someone is writing
function refreshpagechat() {
    'use strict';
    var messageInput = $('#message');
    if (!messageInput.length || false == messageInput.val()) {
        // Refresh only if text not being written.
        if (
            window.parent &&
            window.parent.main &&
            window.parent.main.location
        ) {
            window.parent.main.location.reload();
        } else {
            window.location.reload();
        }
        return true;
    }
    return false;
}
