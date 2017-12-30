/* Manipulate chats to and from the api */
/*jshint browser: true, white: true, plusplus: true*/
/*global $, NW, Chat */
(function ($) {
	'use strict';
	// Add shake plugin to jQuery
    $.fn.shake = function (options) {
        // defaults
        var settings = {
            'shakes': 2,
            'distance': 10,
            'duration': 400
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
                $this.animate({ left: settings.distance * -1 }, (settings.duration / settings.shakes) / 4)
                    .animate({ left: settings.distance }, (settings.duration / settings.shakes) / 2)
                    .animate({ left: 0 }, (settings.duration / settings.shakes) / 4);
            }
        });
    };
}(jQuery));

/**
 * Websockets tutorial: http://socketo.me/docs/hello-world
 * Run the chat server via: php bin/chat-server.php
 */

// Create chat object.
// Initialize maximum mini-chat listings...  it should be pretty damn high, actually.
// store the chat data locally, don't even have to cache this, it should pretty much be a js var
// allow sending of the chat from the input
// Set up a min/max refreshing cycle, theoretically with a typewatch kind of approach.
// Update the datastore with the latest chat info.
// Pass a chained callback using setTimeout

function getDomainName(hostName) {
    return hostName.substring(hostName.lastIndexOf(".", hostName.lastIndexOf(".") - 1) + 1);
}

var Chat = Chat || {};

// Add a typewatch IIFE
Chat.typewatch = (function() {
  var timer = 0;
  return function(callback, ms) {
	clearTimeout (timer);
	timer = setTimeout(callback, ms);
  };
})();

// Get all the initial chat messages and render them.
Chat.getExistingChatMessages = function() {
	'use strict'
	console.log('Existing chat messages requested');
	var since = '1424019122';

	$.getJSON(
		'/api?type=new_chats&since='+encodeURIComponent(since)+'&jsoncallback=?',
		function(data) {
			console.log('Existing chats data found:');
			console.log(data);
			window.storeChats = data;

			if (data && data.new_chats && data.new_chats.chats) {
				console.log('Rendering pre-existing chat messages.');

				$.each(data.new_chats.chats, function(key, val) {
					Chat.renderChatMessage(val);
				});

				Chat.displayMessages();
			}
		}
	);
};

// Display at least the messages area when there are some messages in it.
Chat.displayMessages = function() {
	'use strict'
	$('#mini-chat-display').show();
};

/* use the json data passed to add a new message to the mini-chat
 * Sample call:
 * Chat.renderChatMessage({'message':'Hi! I am chatting!',
 *							'uname':'tchalvak',
 *							'date':Date.now(),
 *							'sender_id':'128274'});
*/
Chat.renderChatMessage = function(p_data) {
	'use strict';
	if (!p_data.message) {
		console.log('Error: Bad data sent in to renderChatMessage to be rendered');
		console.log(p_data);
		return false;
	}

	var area = null;
	var fullLink = 'player.php?player_id='+p_data.sender_id;
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
		console.log('Chat '+area+' not found to place chats in!');
	}

	// put the new content into the author and message areas
	authorArea.removeClass('template').show().find('a').attr('href', fullLink).text(p_data.uname).end();
	messageArea.removeClass('template').show().text(p_data.message);
	list.prepend(authorArea, messageArea);	// Prepend each message and author of a new chat.

	return true;
};

// Send the contents of the chat form input box.
// Sample url: http://nw.local/api?type=send_chat&msg=test&jsoncallback=alert
Chat.sendChatContents = function(p_form) {
	'use strict'
	if (p_form.message && p_form.message.value.length > 0) {
		message = p_form.message.value;
		// Send a new chat.  // ASYNC
		$.getJSON('/api?type=send_chat&msg='+encodeURIComponent(message)+'&jsoncallback=?',
				function(echoed) {
					if (!echoed) {
						Chat.rejected();
						return false;

					}
					// Place the chat in the interface on success.
					Chat.renderChatMessage(echoed);
					success = Chat.send(echoed);
					p_form.reset(); // Clear the chat form.
				}
		).fail(
			function() {
				Chat.rejected();
				return false;
			}
		);
	}
};

// Notify the user when a chat send was rejected.
Chat.rejected = function() {
	'use strict'
	console.log('Error: Failed to send the chat to server.');
	Chat.submissionArea().shake(); // Shake the submission area to show a failed send of a chat.
};

// Send a messageData object to the websockets chat
Chat.send = function(messageData) {
	'use strict'
	if (!Chat.canSend()) {
		return false;
	}

	//messageData.userAgent = navigator.userAgent;

	var passfail = true;
	try {
		conn.send(JSON.stringify(messageData)); // Turn the data into a json object to pass.
		console.log('Chat message sent.');
	} catch(ex) { // Maybe the connection send didn't work out.
		console.log(ex.message);
		passfail = false;
	}

	return passfail;
};

// Get the area that handles chat submission.
Chat.submissionArea = function() {
	'use strict';
	return $('#post_msg_js');
};

// Once the chat is ready, initialize the ability to actually send chats.
Chat.chatReady = function() {
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
};

// Check whether logged in for chat sending
Chat.canSend = function() {
	'use strict';
	var $area = Chat.submissionArea();
	return Boolean($area.data('logged-in'));
};

// Get the dev domain if on .local, fallback to live chat
Chat.domain = function(url) {
	'use strict';
	var domain = getDomainName(url);

	if (domain.indexOf(".local") > -1 ) {
		return 'chatapi.'+domain;
	} else {
		return 'chatapi.ninjawars.net';
	}
};

var chatApiDomain = Chat.domain(window.location.host);

var config = {
	'server': chatApiDomain,
	'port':'8080'
};

// Try to connect to active websocket server, see README
$(function() {
	'use strict';
	if (window !== undefined && window.WebSocket !== undefined) { // Browser is compatible.
		var connectionString = 'ws://'+config.server+':'+config.port;
		console.log('Connecting to '+connectionString);

		window.conn = new WebSocket(connectionString);
		conn.onopen = function(e) {
		    console.log("Websocket Connection established!");
		    Chat.chatReady();
		};

		// Output information comes out here.
		conn.onmessage = function(e) {
			if (e && 'undefined' !== typeof(e.data)) {
				Chat.renderChatMessage(JSON.parse(e.data)); // Add the message to the interface when present!
			}
		};
	} else {
		console.log('Browser not compatible with websockets');
	}

	$('#chat-loading').show(); // Show the chat loading area.

	// Submit a chat message when the input box is used.
	var $submitArea = Chat.submissionArea();
	$submitArea.hide().submit(function(e) {
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
	console.log('Village chat board refreshed');
	var messageInput = $('#message');

	if (!messageInput.length || false == messageInput.val()) { // Refresh only if text not being written.
		if (parent && parent.main && parent.main.location) {
			parent.main.location.reload();
		} else {
			window.location.reload();
		}
	}

	console.log('chat not refreshed due to typed text');
}
