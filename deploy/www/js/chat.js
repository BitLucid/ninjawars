if('undefined' !== typeof(NW) && 'undefined' !== typeof(NW.debug) && NW.debug){
	"use strict";
}

// Create chat object.
// Initialize maximum mini-chat listings...  it should be pretty damn high, actually.
// store the chat data locally, don't even have to cache this, it should pretty much be a js var
// allow sending of the chat from the input
// Set up a min/max refreshing cycle, theoretically with a typewatch kind of approach.
// Update the datastore with the latest chat info.
// Pass a chained callback using setTimeout


var Chat = Chat || {};

Chat.typewatch = (function(){
  var timer = 0;
  return function(callback, ms){
	clearTimeout (timer);
	timer = setTimeout(callback, ms);
  }  
})();

// Get all the initial chat messages and render them.
Chat.getExistingChatMessages = function(){
	console.log('Existing chat messages requested');
	var since = '1424019122';
	$.getJSON('api.php?type=new_chats&since='+encodeURIComponent(since)+'&jsoncallback=?', 
		function(data){
			console.log('Existing chats data:');
			console.log(data);
			window.storeChats = data;
			if(data && data.new_chats && data.new_chats.chats){
				console.log('Rendering pre-existing chat messages.');
				$.each(data.new_chats.chats, function(key, val){
					Chat.renderChatMessage(val);
				});
			}
		});
}


/* use the json data passed to add a new message to the mini-chat
Sample call:
Chat.renderChatMessage({'message':'Hi! I am chatting!','uname':'tchalvak','date':Date.now(),'sender_id':'128274'});
*/
Chat.renderChatMessage = function(p_data){
	if(!p_data.message){
		console.log('Error: Bad data sent in to renderChatMessage to be rendered');
		console.log(p_data);
		return false;
	}
	var fullLink = 'player.php?player_id='+p_data.sender_id;
	// Take the container. 
	var list = $('#mini-chat-display'); // The outer container.

	// clone the .chat-author template and .chat-message template
	var authorArea = list.find('.chat-author.template').clone();
	list.end();
	var messageArea = list.find('.chat-message.template').clone();
	list.end();
	if(!list.length || !authorArea.length || !messageArea.length){
		console.log('Chat list, author area or messageArea not found to place chats in!');
	}

	// put the new content into those areas.
	authorArea.removeClass('template').show().find('a').attr('href', fullLink).text(p_data.uname).end();
	messageArea.removeClass('template').show().text(p_data.message);
	list.prepend(authorArea, messageArea);	// Prepend each message and author of a new chat.
	return true;
};


// Send the contents of the chat form input box.
// Sample url: http://nw.local/api.php?type=send_chat&msg=test&jsoncallback=alert
Chat.sendChatContents = function(p_form) {
	if (p_form.message && p_form.message.value.length > 0) {
		message = p_form.message.value;
		// Send a new chat.
		$.getJSON('api.php?type=send_chat&msg='+encodeURIComponent(message)+'&jsoncallback=?', 
				function(echoed){ 
					// Place the chat in the interface on success.
					Chat.renderChatMessage(echoed);
					Chat.send(echoed);
					console.log('Chat send happened successfully');
				}).fail(function(){
					console.log('Error: Failed to send the chat to server.');
				});
		
		p_form.reset(); // Clear the chat form.
	}
	return false;
};

// Send a message, and allow for optional callbacks on success/fail.
Chat.send = function(messageData, successcall, failcall){
	messageData.timestamp = Date.now();
	messageData.userAgent = navigator.userAgent;
	var passfail = true;
	try{
		// Turn the data into a json object to pass.
		conn.send(JSON.stringify(messageData));
	} catch(ex){ // Maybe the connection send didn't work out.
		console.log(ex.message);
		if(failcall instanceof Function){
			failcall();
		}
		passfail = false;
	}
	if(passfail && successcall instanceof Function){
		successcall();
	}
	return passfail;
}

Chat.counter = 1;

// Once the chat is ready, initialize the ability to actually send chats.
function chatReady(){
	$('#post_msg_js, #mini-chat-display').show(); // Show the areas needed.
	console.log('Chat connected and read');
	var username = Chat.username || 'anon';
	Chat.counter = Chat.counter + 1;
}

var conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) {
    console.log("Websocket Connection established!");
    chatReady();
};

// Output information comes out here.
conn.onmessage = function(e) {
	if(e && 'undefined' !== typeof(e.data)){
		Chat.renderChatMessage(JSON.parse(e.data)); // Add the message to the interface when present!
		console.log('Chat entity from websocket was:');
		console.log(JSON.parse(e.data));
	}
};


$(function(){
	$('#chat-loading').show(); // Show the chat loading area.
	// Submit a chat message when the input box is used.
	$('#post_msg_js').hide().submit(function() {
		return Chat.sendChatContents(this);
	});
	Chat.getExistingChatMessages();
});


// Set up refreshing of the village chat board page (will pause refreshing while someone is writing
function refreshpagechat() {
	console.log('Village chat board refreshed');
	var messageInput = $('#message');
	if(!messageInput.length || false == messageInput.val()){ // Refresh only if text not being written.
		if(parent && parent.main && parent.main.location){
			parent.main.location.reload();
		} else {
			window.location.reload();
		}
	}
	console.log('chat not refreshed due to typed text');
};


