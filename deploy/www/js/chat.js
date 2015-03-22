

var g_isIndex = (window.location.pathname.substring(1) === 'index.php') || $('body').hasClass('main-body'); // This line requires and makes use of the $ jQuery var!

var g_isLive = (window.location.host !== 'localhost');

var g_isRoot = (window.location.pathname === '/');

var g_isSubpage = (!g_isIndex && !g_isRoot && (window.parent == window));

if (g_isIndex || g_isRoot) {

	var Chat = Chat || {};

	Chat.lastChatCheck = '';
	Chat.maxMiniChats = 250;
	Chat.currentMiniChats = 0;
	Chat.datastore = NW.datastore;

	Chat.typewatch = (function(){
	  var timer = 0;
	  return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	  }  
	})();

	Chat.sendChat = function(p_inputField) {
		p_inputField.value = '';

		return true;
	};

	// Begin the cycle of refreshing the mini chat after the standard delay.
	Chat.startRefreshingMinichat = function() {
		// TODO: Potentially make this use the timewatch pattern, so that the countdown can simply be zeroed instead of having multiple update threads.
		var secs = 30; // Chat checking frequency.
		var self = this;
		setTimeout(function() {
			self.checkForNewChats();
			self.startRefreshingMinichat(); // Loop the check for refresh.
		}, secs*1000);
	};

	// Wrapper for the NW updateDataStore functionality.
	Chat.updateDataStore = function(datum, property_name, global_store, comparison_name) {
		return NW.updateDataStore(datum, property_name, global_store, comparison_name);
	};

	// Returns chainable behavior to check for new chats.
	Chat.make_checkForNewChats_callback = function() {
		console.log('callback made to check for new chats on date/time: ['+new Date()+']');
		var self = this;
		return function(data) {
			// Update global data stores if an update is needed.
			if (Chat.updateDataStore(data.new_chats, 'new_count', 'new_chats', 'new_count')) {
				Chat.datastore.new_chats.new_count = 0;
				self.refreshMinichat(null, 50);
			}

			// Update the "since" time, only if new data is available.
			if (data.new_chats && data.new_chats.datetime) {
				Chat.lastChatCheck = data.new_chats.datetime;
				if(data.new_chats.length){ // Only update if something was actually available to update.
					Chat.lastChatsUpdated = data.new_chats.datetime;
				}
			}
		}
	};

	// Check for the latest chat and update if it's different.
	Chat.checkForNewChats = function(speed) {
		if(!speed){
			speed = 500;
		}
		// Check whether the latest chat doesn't match the latest displayed chat.
		// NOTE THAT THIS CALLBACK DOES NOT TRIGGER IMMEDIATELY.
		Chat.typewatch(function () {
    		$.getJSON('api.php?type=new_chats&since='+encodeURIComponent(Chat.lastChatsUpdated)+'&jsoncallback=?', Chat.make_checkForNewChats_callback());
  		}, speed); // some ms after the -last- call, perform the behavior.
	};

	// Update chats listed in the mini-chat
	Chat.refreshMinichat = function() {
		if (Chat.datastore.new_chats) { // Pull from NW object datastore.
			var chatContainer = $('#mini-chat-display');
			var data;

			if (chatContainer.length) { // check that there is a chat area to load into.
				var chats = Chat.datastore.new_chats.chats;
				if(chats){
					for (var chat_message in chats) {
						// TODO: Gotta lock down the for in here.
						Chat.addChatMessage(chatContainer, chats[chat_message]); // Add to the container, using the json data.
					}
				}
				chatContainer.show();

				Chat.datastore.new_chats = null;
				return false;
			}
		}
		console.log('mini-chat refreshed');
	};

	// use the json data passed to add a new message to the mini-chat
	Chat.addChatMessage = function(chatContainer, p_data){
		var author, message, timestamp, author_id, playerLink, fullLink;
		message = p_data.message;
		author = p_data.uname;
		timestamp = p_data.date;
		author_id = p_data.sender_id;
		playerLink = 'player.php?player_id=';
		fullLink = playerLink+author_id;


		
		// Take the container. 
		var list = $('#mini-chat-display');

		// clone the .chat-author template and .chat-message template
		var authorArea = list.find('.chat-author.template').clone();
		list.end();
		var messageArea = list.find('.chat-message.template').clone();
		list.end();

		// put the new content into those areas.
		authorArea.removeClass('template').show().find('a').attr('href', fullLink).text(author).end();
		messageArea.removeClass('template').show().text(message);
		// Prepend each entry of the chat 'em.
		list.prepend(authorArea, messageArea); 
	};

	// Chat requests should get a timewatch pattern instead of a lock
	//So that 234 immediately consecutive spammings of enter to refresh the chat results in a delay until the last spamming comes through +3
	// Instead of preventing anything from happening for a time, only refresh the chat on the -last- request.

	// Send a chat message to be saved.
	Chat.putChat = function(mess, callback){
		$.getJSON('api.php?type=send_chat&msg='+encodeURIComponent(mess)+'&jsoncallback=?', callback);
	};

	// Send the contents of the chat form input box.
	Chat.sendChatContents = function(p_form) {
		if (p_form.message && p_form.message.value.length > 0) {
			// Send a new chat.
			Chat.putChat(p_form.message.value, function(){ Chat.checkForNewChats(50); });
			
			p_form.reset(); // Clear the chat form.
		} else {
			this.checkForNewChats();
		}

		return false;
	};


}

if (g_isIndex || g_isRoot) {

	$(function(){ // DOM operations.
		$('#chat-loading').show();
		// Submit a chat message when the input box is used.
		$('#post_msg_js').submit(function() {return Chat.sendChatContents(this)});
	});

	$(window).load(function(){ // Delay load of mini-chat until all other assets have loaded.
			console.log('Mini-chat initialized after all other assets have loaded.');
			Chat.checkForNewChats();
			Chat.startRefreshingMinichat(); // Start refreshing the chat.
	});
} // Not index or root, so don't do anything relating to the chat.


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