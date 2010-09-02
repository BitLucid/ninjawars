/* The main javascript functionality of the site, apart from very page specific behaviors */


// Sections are, in order: SETTINGS | FUNCTIONS | READY

// Url Resources:
// http://www.jslint.com/
// http://yuiblog.com/blog/2007/06/12/module-pattern/
// http://www.javascripttoolbox.com/bestpractices/



// TODO: change generated vars to square bracket notation.

// TODO: Create a dummy console dot log functionality to avoid errors on live?

var NW = {};

g_isIndex = (window.location.pathname.substring(1) == 'index.php') || $('body').hasClass('main-body');

g_isLive = (window.location.host != 'localhost');

g_isRoot = (window.location.pathname == '/');

g_isSubpage = (!g_isIndex && !g_isRoot && (window.parent == window));

/*  GLOBAL SETTINGS & VARS */
if (parent.window != window) {
    // If the interior page of an iframe, use the already-defined globals from the index.
	$ = parent.$;
	NW = parent.NW;
} else {
    // If the page is standalone, define the objects as needed.
	$ = jQuery;
	NW = {};
	

	NW.datastore = {};
	NW.lastChatCheck = '';
	NW.chatLock = false;
	NW.manualChatLock = false;
	NW.manualChatLockTime = 3*1000;
	NW.maxMiniChats = 250;
	NW.currentMiniChats = 0;

	NW.chatLocked = function() {
		return NW.chatLock;
	};

	NW.lockChat = function() {
		NW.chatLock = true;
	};

	NW.unlockChat = function() {
		NW.chatLock = false;
	};

	NW.sendChat = function(p_inputField) {
		p_inputField.value = '';

		return true;
	};

	// For refreshing quickstats from inside main.
	NW.refreshQuickstats = function(typeOfView) {
		var self = this;
		self.getAndUpdateHealth();

		// Accounts for ajax section.
		if (!typeOfView) {
			typeOfView = '';
		}

		var url = 'quickstats.php?command='+typeOfView;

		if (!this.quickDiv) {
			this.quickDiv = document.getElementById('quickstats-frame-container');
		}

		if (this.quickDiv) {
			if (typeOfView == 'viewinv') {
				this.checkAPI(function() {self.quickDiv.replaceChild(self.renderInventoryQuickstats(), self.quickDiv.firstChild);});
			} else {
				this.checkAPI(function() {self.quickDiv.replaceChild(self.renderPlayerQuickstats(), self.quickDiv.firstChild);});
			}

			return true;
		} else if (parent.quickstats) {
			parent.quickstats.location = url;
			return true;
		} else {
			return false;
		}
	};

	NW.renderInventoryQuickstats = function() {
		var container, goldLabel, goldValue, itemLabel, itemValue;

		container = document.createElement('dl');
		container.className = "quickstats inventory";
		var items = this.datastore.inventory.items;

		for (i in items)
		{
			itemLabel = document.createElement('dt');
			itemLabel.appendChild(document.createTextNode(items[i].item+':'));
			container.appendChild(itemLabel);

			itemValue = document.createElement('dd');
			itemValue.appendChild(document.createTextNode(items[i].amount));
			container.appendChild(itemValue);
		}

		goldLabel = document.createElement('dt');
		goldLabel.appendChild(document.createTextNode('Gold:'));
		goldLabel.style.color = "gold";
		container.appendChild(goldLabel);

		goldValue = document.createElement('dd');
		goldValue.appendChild(document.createTextNode(this.datastore.playerInfo.gold));
		goldValue.style.color = "gold";
		container.appendChild(goldValue);

		return container;
	};

	NW.renderPlayerQuickstats = function() {
		var container, healthLabel, healthBar, expLabel, expBar, statusLabel, statusValue, turnsLabel, turnsValue, goldLabel, goldValue, bountyLabel, bountyValue;

        // Jesus dammit christmas!  We need to keep the html out of the js.

		container = document.createElement('dl');
		container.className = "quickstats player-stats";

		healthLabel = document.createElement('dt');
		healthLabel.appendChild(document.createTextNode('Health:'));
		expLabel = document.createElement('dt');
		expLabel.appendChild(document.createTextNode('Exp:'));
		statusLabel = document.createElement('dt');
		statusLabel.appendChild(document.createTextNode('Status:'));
		turnsLabel = document.createElement('dt');
		turnsLabel.appendChild(document.createTextNode('Turns:'));
		goldLabel = document.createElement('dt');
		goldLabel.appendChild(document.createTextNode('Gold:'));
		goldLabel.style.color = "gold";
		bountyLabel = document.createElement('dt');
		bountyLabel.appendChild(document.createTextNode('Bounty:'));

		healthBar = document.createElement('dd');
		var healthBarOuter = document.createElement('div');
		healthBarOuter.title = "HP: "+this.datastore.playerInfo.health;
		healthBarOuter.style.border = "1px solid rgb(238, 37, 32)";
		healthBarOuter.style.width = "100%";
		healthBarInner = document.createElement('div');
		healthBarInner.style.backgroundColor = "rgb(238, 37, 32)";
		healthBarInner.style.width = this.datastore.playerInfo.hp_percent+"%";
		healthBarInner.appendChild(document.createTextNode('\u00a0'));
		healthBarOuter.appendChild(healthBarInner);
		healthBar.appendChild(healthBarOuter);
		expBar = document.createElement('dd');
		var expBarOuter = document.createElement('div');
		expBarOuter.title = "Exp: "+this.datastore.playerInfo.exp_percent+"%";
		expBarOuter.style.border = "1px solid #6612ee";
		expBarOuter.style.width = "100%";
		expBarInner = document.createElement('div');
		expBarInner.style.backgroundColor = "#6612ee";
		expBarInner.style.width = this.datastore.playerInfo.exp_percent+"%";
		expBarInner.appendChild(document.createTextNode('\u00a0'));
		expBarOuter.appendChild(expBarInner);
		expBar.appendChild(expBarOuter);
		statusValue = document.createElement('dd');
		statusValue.appendChild(document.createTextNode(this.datastore.playerInfo.status_list));
		turnsValue = document.createElement('dd');
		turnsValue.appendChild(document.createTextNode(this.datastore.playerInfo.turns));
		goldValue = document.createElement('dd');
		goldValue.appendChild(document.createTextNode(this.datastore.playerInfo.gold));
		goldValue.style.color = "gold";
		bountyValue = document.createElement('dd');
		bountyValue.appendChild(document.createTextNode(this.datastore.playerInfo.bounty));

		container.appendChild(healthLabel);
		container.appendChild(healthBar);
		container.appendChild(expLabel);
		container.appendChild(expBar);
		container.appendChild(statusLabel);
		container.appendChild(statusValue);
		container.appendChild(turnsLabel);
		container.appendChild(turnsValue);
		container.appendChild(goldLabel);
		container.appendChild(goldValue);
		container.appendChild(bountyLabel);
		container.appendChild(bountyValue);

		return container;
	};

	// Returns true when debug bit set or localhost path used.
	NW.debug = function(arg) {
		if (this.debugging || !this.isLive) {
			//if (console) {console.log(arg);}
			return true;
		}

		return false;
	};

	// Display an event.
	NW.writeLatestEvent = function(event) {
		this.debug('Event display requested.');
		
		var recent = $('#recent-events', top.document)
		.find('#recent-event-attacked-by').text('You were recently in combat').end()
		.find('#view-event-char').text(event.sender).attr('href', 'player.php?player_id='+event.send_from).end();
		if(recent && recent.addClass){
    		if(event.unread){
    		    recent.addClass('message-unread');
        		// if unread, Add the unread class until next update.
    		} else {
    		    recent.removeClass('message-unread');
    		}
    		recent.toggle();
    	}
	}
	
	NW.eventsRead = function(){
		$('#recent-events', top.document).removeClass('message-unread').toggle(false);	    
	};

	// Pull the event from the data store and request it be displayed.
	NW.updateLatestEvent = function() {
		var feedback = false;
		var event = this.getEvent();

		if (!event) {
			this.feedbackSpeedUp(); // Make the interval to try again shorter.
			this.debug('No event data to use.');
		} else if (this.datastore.visibleEventId == event.event_id) {
			if (!this.datastore.visibleEventRead) {
				// Makes any unread event marked as read after a second update, even if it wasn't really read.
				NW.eventsRead();
				this.debug('Requested that latest event be marked as read.');
				this.datastore.visibleEventRead = true;
			}
		} else {
			this.debug('Request to write out the latest event.');
			feedback = true;
			this.datastore.visibleEventId = event.event_id;
			this.datastore.visibleEventRead = false;
			this.writeLatestEvent(event);
		}

		return feedback;
	};

    // Get the message count initially from the api datastore.
	NW.getMessageCount = function() {
		return this.pullArrayValue('unread_messages_count');
	};
	
	// Pull an unread message count from the new api storage, compare it to the stored value, and call the display function as necessary.
	NW.updateMessageCount = function () {
	    var updated = false;
	    var count = this.getMessageCount();
	    
	    if (this.storeArrayValue('unread_messages_count', count)){
	        updated = true;
	        this.unreadMessageCount(count); // Display a value if changed.
	    }
	    return updated;
	};


    // Update the number of unread messages, displayed on index. 
	NW.unreadMessageCount = function(messageCount) {        
		var recent = $('#messages', top.document).find('.unread-count').text(messageCount);
			// if unread, Add the unread class until next update.
		if(recent && recent.addClass){
    		if(messageCount>0){
    		  recent.addClass('message-unread');
    		} else {
    		  recent.removeClass('message-unread');
    		}
    	}
	};
	

	// Update the display of the health.
	NW.updateHealthBar = function(health) {
		// Should only update when a change occurs.
		if (health != this.datastore.visibleHealth) {
			var mess = health+' health';
			var span = $('#health-status', top.document);
			// Keep in mind the need to use window.parent when calling from an iframe.
			span.text(mess);

			if (health < 100) {
				span.addClass('injured');
			} else {
				span.removeClass('injured');
			}

			this.datastore.visibleHealth = health;

			return true;
		}

		return false;
	};

	// Update the displayed health from the javascript-stored current value.
	NW.getAndUpdateHealth = function() {
		var updated = false;

		if (this.datastore.playerInfo) {
			this.datastore.playerInfo.health = (this.datastore.playerInfo.health ? this.datastore.playerInfo.health : '0');

			if (this.datastore.visibleHealth != this.datastore.playerInfo.health) {
				this.updateHealthBar(this.datastore.playerInfo.health);
				updated = true;
			}
		}

		return updated;
	};

	NW.getEvent = function() {
		return this.pullFromDataStore('latestEvent');
	};

	NW.getPlayerInfo = function() {
		return this.pullFromDataStore('playerInfo');
	};

	// Update display elements that live on the index page.
	NW.updateIndex = function() {
		var messageUpdated = this.updateMessageCount();
		var eventUpdated = this.updateLatestEvent();
		// health bar.
		var healthUpdated = this.getAndUpdateHealth();

        // If any changes to data occurred, return true.		
		var res = (!!(messageUpdated || eventUpdated || healthUpdated));
		this.debug("Message Updated: "+messageUpdated);
		this.debug("Event Updated: "+eventUpdated);
		this.debug("Health Updated: "+healthUpdated);

		return res;
	};

	NW.feedbackSpeedUp = function() {
		this.feedbackValue = true;
	};

	// Get the feedback value.
	NW.feedback = function() {
		var res = (this.feedbackValue ? this.feedbackValue : false);
		this.feedbackValue = false; // Start slowing down after getting the value.
		return res;
	};

	NW.make_checkAPI_callback = function(p_additionalCallback) {
		var self = this;

		return function(data) {
			self.checkAPI_callback(data);

			if (p_additionalCallback) {
				p_additionalCallback();
			}
		}
	};
	
	// The checkAPI probably shouldn't delay display, display should happen whenever the api returns?
	// I guess the original objective was to decouple display calls and api data requests.

    // This pulls the data from api.php and stores the data, and then returns true if any of the data was different.
	NW.checkAPI_callback = function(data) {
		var updated = false;

		// Update global data stores if an update is needed.
		if (this.updateDataStore(data.player, 'player_id', 'playerInfo', 'hash')) {
			updated = true;
		}

		if (this.updateDataStore(data.inventory, 'inv', 'inventory', 'hash')) {
			updated = true;
		}

		if (this.updateDataStore(data.message, 'message_id', 'latestMessage', 'message_id')) {
			updated = true;
		}
		
		// Save the unread message count into an array.
		if (this.storeArrayValue('unread_messages_count', data.unread_messages_count)) {
			updated = true;
		}

		// Save the unread event count into an array.
		if (this.storeArrayValue('unread_events_count', data.unread_events_count)) {
			updated = true;
		}

		if (this.updateDataStore(data.event, 'event_id', 'latestEvent', 'event_id')) {
			updated = true;
		}

		this.debug('Update requested: '+updated);

		if (updated) {
			this.updateIndex(); // Always request a redisplay for any poll that has information updates.
			this.feedbackSpeedUp(updated);
		}
	};

	// Pull in the new info, update display only on changes.
	NW.checkAPI = function(p_additionalCallback) {
		// NOTE THAT THIS CALLBACK DOES NOT TRIGGER IMMEDIATELY.
		$.getJSON('api.php?type=index&jsoncallback=?', this.make_checkAPI_callback(p_additionalCallback));
	};

	// Saves an array of data to the global data storage, only works on array data, with an index.
	// Take in a new datum from the api, compare it's <property name> to the already-in-js-global-storage's property called <comparison_name>
	// This is comparing an old version to a new version, and storing any changes between the two.
	NW.updateDataStore = function(datum, property_name, global_store, comparison_name) {
		if (datum && datum[property_name]) {
			if (!this.datastore[global_store] || (this.datastore[global_store][comparison_name] != datum[property_name])) {
				// If the data isn't there, or doesn't match, update the store.
				this.datastore[global_store] = datum;
				this.debug(datum);
				return true;
			}
		}

		return false; // Input didn't contain the data, or the data hasn't changed.
	};
	
	// Return the most up-to-date value, which was stored prior.
	NW.pullFromDataStore = function(global_store, property_name){
	    if(this.datastore[global_store]){
	        if(property_name && typeof(this.datastore[global_store][property_name]) != 'undefined'){
	            // If a property_name was specified, return the value for that specific property (e.g. event.event_id)...
    	        return this.datastore[global_store][property_name];
    	    }
    	    // ...otherwise return the whole storage entity, (e.g. event).
    	    return this.datastore[global_store];
	    }
	    return null;
	};
	
	// Store any changes to the value, if any, and return true if changed, false if unchanged.
	NW.storeArrayValue = function(name, value){
	    if(!this.datastore['array']){
	        this.datastore['array'] = {}; // Verify there's a storage array.
	    }
	    // Check for a change to the value to store.
	    if((typeof(this.datastore['array'][name]) != 'undefined') 
	        || this.datastore['array'][name] == value){
	        // If it exists and differs, store the new one and return true.
	        this.datastore['array'][name] = value;
	        return true;
	    } else {
    	    return false;
    	}
	};
	
	// Get a stored hash if available.
	NW.pullArrayValue = function(name){
	    return (this.datastore['array'] && typeof(this.datastore['array'][name]) != 'undefined'?
	         this.datastore['array'][name] 
	         : null);
	};

	// Determines the update interval,
	//increases when feedback == false, rebaselines when feedback == true
	NW.getUpdateInterval = function(feedback) {
		var maxInt = 180;
		var min = 20; // Changes push the interval to this minimum.
		var first = 1;  // The very first interval to run the update for.
		var first_interval = false;

		if (!this.updateInterval) {
			first_interval = true;// Starting.
			this.updateInterval = min; // Default.
		} else if (feedback) {
			this.updateInterval = min; // Speed up to minimum.
		} else if (this.updateInterval >= maxInt) {
			this.updateInterval = maxInt; // Don't get any slower than max.
		} else {
			this.updateInterval++; // Slow down updates slightly.
		}

		return (first_interval ? first : this.updateInterval);
	};

	// JS Update Heartbeat
	NW.chainedUpdate = function(p_chainCounter) {
		var chainCounter = (!!p_chainCounter ? p_chainCounter : 1);
		// Skip the heartbeat the first time through, and skip it if not logged in.
		// TODO: skip heartbeat entirely when not logged in.
		if (this.loggedIn && chainCounter != 1) {
			this.checkAPI(); // Check for new information.
		}

		var furtherIntervals = this.getUpdateInterval(this.feedback());
		this.debug("Next Update will be in:"+furtherIntervals);
		this.debug("chainCounter: "+chainCounter);
		chainCounter++;

		var self = this;
		setTimeout(function() {
			self.chainedUpdate(chainCounter);
		}, furtherIntervals*1000); // Repeat once the interval has passed.
		// If we need to cancel the updating down the line for some reason, store the id that setTimeout returns.
	};

	// Adds a "click to hide another section" to any section, second param has default, but can be specified.
	NW.clickHidesTarget = function(ident, targetToHide) {
		$(ident).click(function() {
			$(targetToHide).toggle();
			return false;
		});
	};

	// Begin the cycle of refreshing the mini chat after the standard delay.
	NW.startRefreshingMinichat = function() {
		// TODO: Potentially make this use the timewatch pattern, so that the countdown can simply be zeroed instead of having multiple update threads.
		var secs = 30; // Chat checking frequency.
		var self = this;
		setTimeout(function() {
			self.checkForNewChats();
			self.startRefreshingMinichat(); // Loop the check for refresh.
		}, secs*1000);
	};

	NW.make_checkForNewChats_callback = function() {
		var self = this;
		return function(data) {
			// Update global data stores if an update is needed.
			if (self.updateDataStore(data.new_chats, 'new_count', 'new_chats', 'new_count')) {
				self.datastore.new_chats.new_count = 0;
				self.refreshMinichat(null, 50);
			}

			if (data.new_chats && data.new_chats.datetime) {
				NW.lastChatCheck = data.new_chats.datetime;
			}

			NW.unlockChat();
		}
	};

	// Check for the latest chat and update if it's different.
	NW.checkForNewChats = function() {
		// TODO: Eventually this should just pull the chats and load them into the dom.
		// Check whether the latest chat doesn't match the latest displayed chat.
		// NOTE THAT THIS CALLBACK DOES NOT TRIGGER IMMEDIATELY.
		if (!NW.chatLocked()) {
			NW.lockChat();
			$.getJSON('api.php?type=new_chats&since='+encodeURIComponent(NW.lastChatCheck)+'&jsoncallback=?', NW.make_checkForNewChats_callback());
		}
	};

	// Load the chat section, or if that's not available & nested iframe, refresh iframe
	NW.refreshMinichat = function() {
		if (this.datastore.new_chats) {
			var container = document.getElementById('mini-chat-display');
			var data;

			if (container) { // check that there is a new chat -to- load.
				var chats = this.datastore.new_chats.chats;
				var after = container.insertBefore(document.createTextNode(''), container.firstChild);
				for (chat_message in chats) {
				    // Jesus.
					after = container.insertBefore(this.renderChatAuthor(chats[chat_message]), after.nextSibling);
					after = container.insertBefore(this.renderChatMessage(chats[chat_message]), after.nextSibling);

					if (this.maxMiniChats <= this.currentMiniChats) {
						if (container.removeChild(container.lastChild).nodeType == 3) {
							container.removeChild(container.lastChild);
						}

						container.removeChild(container.lastChild);
					} else {
						++this.currentMiniChats;
					}
				}

				this.datastore.new_chats = null;
				return false;
			}
		}
	};

	NW.renderChatMessage = function(p_message) {
		var container = document.createElement('dd');
		container.className = "chat-message";
		container.appendChild(document.createTextNode(p_message.message));
		return container;
	};

	NW.renderChatAuthor = function(p_message) {
		var container = document.createElement('dt');
		container.className = "chat-author";
		container.appendChild(document.createTextNode('<'));
		var authorLink = document.createElement('a');
		authorLink.href = "player.php?player_id="+p_message.sender_id;
		authorLink.target = "main";
		authorLink.appendChild(document.createTextNode(p_message.uname));
		container.appendChild(authorLink);
		container.appendChild(document.createTextNode('>'));
		return container;
	};

	NW.chatRefreshClicked = function(button) {
		button.onclick = null;
		$(button).css({'cursor':'default'});
		//button.style.cursor = 'default';
		button.src = 'images/refresh_disabled.gif';
		setTimeout(function(){
		    button.onclick = function() { NW.chatRefreshClicked(this);};
		    button.src = 'images/refresh.gif';
		    $(button).css({'cursor':'pointer'});
            //button.style.cursor = 'pointer'; // This fails in chrome.
        }, this.manualChatLockTime);
		this.checkForNewChats();
	};


    // This locking mechanism should probably be migrated to a timewatch pattern instead.
    //So that 234 immediately consecutive spammings of enter to refresh the chat results in a delay until the last spamming comes through +3
    // Instead of preventing anything from happening for a time, only refresh the chat on the -last- request.
    
	NW.manualChatLocked = function() {
		return this.manualChatLock;
	};

	NW.lockManualChat = function() {
		this.manualChatLock = true;
		setTimeout(function() { NW.unlockManualChat(); }, this.manualChatLockTime);
	};

	NW.unlockManualChat = function() {
		this.manualChatLock = false;
	};

	// Send the contents of the chat form input box.
	NW.sendChatContents = function(p_form) {
		if (p_form.message && p_form.message.value.length > 0) {
			$.getJSON('api.php?type=send_chat&msg='+encodeURIComponent(p_form.message.value)+'&jsoncallback=?', NW.checkForNewChats);
			p_form.reset(); // Clear the chat form.
		} else if (!NW.manualChatLocked()) {
			this.lockManualChat();
			this.checkForNewChats();
		}

		return false;
	};

	// Just for fun.
	/*
	   function april1stCheck() {
	   if (g_isIndex) {
	   var currentTime = new Date();
	   var day = currentTime.getDate();
	   var month = currentTime.getMonth();
	   var randomnumber=Math.floor(Math.random()*(10+1));
	   var random2 = Math.floor(Math.random()*(10+1));
	   if (randomnumber == 10 && ((NW.debug() && random2 == 10) || (day == 0 && month == 3))) {
	   $('body').css({'-webkit-transform':'rotate(20deg)','-moz-transform':'rotate(20deg)', 'transform':'rotate(20deg)'});
	   }
	   }
	   }
	 */
}



/***************************** Execution of code, run at the end to allow all definitions to exist beforehand. ******/
$(document).ready(function() {
	// INDEX ONLY CHANGES
	if (g_isIndex || g_isRoot) {
		NW.quickDiv = null;
		NW.miniChatContainer = null;

		NW.quickDiv = document.getElementById('quickstats-frame-container');

		$('#chat-loading').show();

		NW.chainedUpdate(); // Start the periodic index update.


		var quickstatsLinks = $("a[target='quickstats']");
		quickstatsLinks.css({'font-style':'italic'}); // Italicize

		NW.miniChatContainer = document.getElementById('mini-chat-display');

		// Update the mini chat section for the first time.
		NW.checkForNewChats();

		// Start refreshing the chat.
		NW.startRefreshingMinichat(); // Start refreshing the chat.

		$('#post_msg_js').submit(function() {return NW.sendChatContents(this)});
		// When chat form is submitted, send the message, load() the chat section and then clear the textbox text.

		// Add click handlers to certain sections.
		NW.clickHidesTarget('#show-hide-chat', '#chat-and-switch');
		NW.clickHidesTarget('#show-hide-quickstats', '#quickstats-and-switch-stats');
		NW.clickHidesTarget('#show-hide-actions-menu', '#actions-menu');
		
		// Display the chat refresh image and toggle it if it is clicked.
		$('#chat-refresh-image').toggle().click(NW.chatRefreshClicked(this));
		
		
	} else if (g_isSubpage) {
		$('body').addClass('solo-page'); // Add class to solo-page bodies.
		// Displays the link back to main page for any lone subpages not in iframes.
	}

	//april1stCheck();
});
