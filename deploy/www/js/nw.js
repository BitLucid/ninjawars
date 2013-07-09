/* The main javascript functionality of the site, apart from very page specific behaviors */

// Sections are, in order: SETTINGS | FUNCTIONS | READY

// Url Resources:
// http://www.jslint.com/
// http://yuiblog.com/blog/2007/06/12/module-pattern/
// http://www.javascripttoolbox.com/bestpractices/

// TODO: change generated vars to square bracket notation.
// TODO: Create a fade-out/fadein functionality like the google homepage, so that elements display only when needed/when the main element is moused-off-of.
"use strict"; // Testing out use of strict mode.

var NW = window.NW || {};

//var $ = jQuery; // jQuery sets itself to use the dollar sign shortcut by default.
// A different instance of jquery is currently used in the iframe and outside.

var g_isIndex = (window.location.pathname.substring(1) == 'index.php') || $('body').hasClass('main-body'); // This line requires and makes use of the $ jQuery var!

var g_isLive = (window.location.host != 'localhost');

var g_isRoot = (window.location.pathname == '/');

var g_isSubpage = (!g_isIndex && !g_isRoot && (window.parent == window));

// Guarantee that there is a console to prevent errors while debugging.
if (typeof(console) == 'undefined') { console = { log: function() { } }; }

// iepp v2.1pre @jon_neal & @aFarkas github.com/aFarkas/iepp
// html5shiv @rem remysharp.com/html5-enabling-script
// Dual licensed under the MIT or GPL Version 2 licenses
// What follows is an ie-only hack /*@ ... @*/
/*@cc_on(function(a,b){function r(a){var b=-1;while(++b<f)a.createElement(e[b])}if(!window.attachEvent||!b.createStyleSheet||!function(){var a=document.createElement("div");return a.innerHTML="<elem></elem>",a.childNodes.length!==1}())return;a.iepp=a.iepp||{};var c=a.iepp,d=c.html5elements||"abbr|article|aside|audio|canvas|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|subline|summary|time|video",e=d.split("|"),f=e.length,g=new RegExp("(^|\\s)("+d+")","gi"),h=new RegExp("<(/*)("+d+")","gi"),i=/^\s*[\{\}]\s*$/,j=new RegExp("(^|[^\\n]*?\\s)("+d+")([^\\n]*)({[\\n\\w\\W]*?})","gi"),k=b.createDocumentFragment(),l=b.documentElement,m=b.getElementsByTagName("script")[0].parentNode,n=b.createElement("body"),o=b.createElement("style"),p=/print|all/,q;c.getCSS=function(a,b){try{if(a+""===undefined)return""}catch(d){return""}var e=-1,f=a.length,g,h=[];while(++e<f){g=a[e];if(g.disabled)continue;b=g.media||b,p.test(b)&&h.push(c.getCSS(g.imports,b),g.cssText),b="all"}return h.join("")},c.parseCSS=function(a){var b=[],c;while((c=j.exec(a))!=null)b.push(((i.exec(c[1])?"\n":c[1])+c[2]+c[3]).replace(g,"$1.iepp-$2")+c[4]);return b.join("\n")},c.writeHTML=function(){var a=-1;q=q||b.body;while(++a<f){var c=b.getElementsByTagName(e[a]),d=c.length,g=-1;while(++g<d)c[g].className.indexOf("iepp-")<0&&(c[g].className+=" iepp-"+e[a])}k.appendChild(q),l.appendChild(n),n.className=q.className,n.id=q.id,n.innerHTML=q.innerHTML.replace(h,"<$1font")},c._beforePrint=function(){if(c.disablePP)return;o.styleSheet.cssText=c.parseCSS(c.getCSS(b.styleSheets,"all")),c.writeHTML()},c.restoreHTML=function(){if(c.disablePP)return;n.swapNode(q)},c._afterPrint=function(){c.restoreHTML(),o.styleSheet.cssText=""},r(b),r(k);if(c.disablePP)return;m.insertBefore(o,m.firstChild),o.media="print",o.className="iepp-printshim",a.attachEvent("onbeforeprint",c._beforePrint),a.attachEvent("onafterprint",c._afterPrint)})(this,document);@*/

/*  GLOBAL SETTINGS & VARS */
if (parent.window != window) {
	// If the interior page of an iframe, use the already-defined globals from the index.
	//$ = parent.$;
	NW = parent.NW;
} else {
	// If the page is standalone, define the objects as needed.
	//$ = jQuery;
	NW = {};

	NW.datastore = {};
	NW.lastChatCheck = '';
	NW.chatLock = false;
	NW.manualChatLock = false;
	NW.manualChatLockTime = 3*1000;
	NW.maxMiniChats = 250;
	NW.currentMiniChats = 0;

	// Create the typewatch var and store it for later.
	NW.typewatch = (function(){
	  var timer = 0;
	  return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	  }  
	})();
	
	// Accept a json data array of matches and house them in the interface.
	NW.displayMatches = function(json_matches){
		
	}
	
	// Get the chars/ids matching a term and then have the callback run.
	NW.charMatch = function(term, limit, callback) {
		$.getJSON('api.php?type=char_search&term='+term+'&limit='+limit+'&jsoncallback=?', callback);
	};

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
	
	
	// Update the barstats visuals with incoming data.
	NW.updateBarstats = function (barstats) {
		//alert('update of barstats visuals reached! Barstats are:'+barstats.health+' '+barstats.max_health+' '+barstats.kills+' '+barstats.next_level+' '+barstats.turns+' '+barstats.kills_percent);
		// Find the barstats container.
		var barstatsContainer = $('#barstats');
		// Find the bars inside that.
		// Change the number floating over the bars.
		barstatsContainer.find('#health').find('.bar-number').text(barstats.health).end().find('.bar').css({'width':barstats.health_percent+'%'}).end()
		.end().find('#kills').find('.bar-number').text(barstats.kills).end().find('.bar').css({'width':barstats.kills_percent+'%'}).end()
		.end().find('#turns').find('.bar-number').text(barstats.turns).end().find('.bar').css({'width':barstats.turns_percent+'%'}).end()
		.end();
		// Change the percentage of the background bar.
	}
	
	
	NW.displayBarstats = function() {
		$('#barstats').show();
	}
	
	NW.refreshStats = function(playerInfo) {
		// Pull health, turns, and kills.
		var updated = false;
		if(typeof(NW.barstats) == 'undefined'){
			// Create the barstats data container if it doesn't already exist.
			NW.barstats = {};
			NW.barstats.health = null;
			NW.barstats.turns = null;
			NW.barstats.kills = null;
		}
		if(playerInfo && typeof(playerInfo.health) != 'undefined'){
			this.datastore.playerInfo = playerInfo;
		}
		if (this.datastore.playerInfo) {
			if (NW.barstats.health != this.datastore.playerInfo.health ||
				NW.barstats.turns != this.datastore.playerInfo.turns ||
				NW.barstats.kills != this.datastore.playerInfo.kills) {
				// Save the data to the barstats temp variable.
				NW.barstats.health = this.datastore.playerInfo.health;
				NW.barstats.max_health = this.datastore.playerInfo.max_health;
				// Save all the percentages as well.
				NW.barstats.health_percent = this.datastore.playerInfo.hp_percent;
				NW.barstats.turns = this.datastore.playerInfo.turns;
				NW.barstats.max_turns = this.datastore.playerInfo.max_turns;
				NW.barstats.turns_percent = this.datastore.playerInfo.turns_percent;
				NW.barstats.kills = this.datastore.playerInfo.kills;
				NW.barstats.next_level = this.datastore.playerInfo.next_level;
				NW.barstats.kills_percent = this.datastore.playerInfo.exp_percent;
				NW.updateBarstats(this.barstats);
				updated = true;
			}
		}

		return updated;
	}

	// For refreshing quickstats (now barstats) from inside the main iframe.
	NW.refreshQuickstats = function(typeOfView) {
		NW.refreshStats(); // Just call the function to refresh stats.
	};


	// Returns true when debug bit set or localhost path used.
	NW.debug = function(arg) {
		if (this.debugging || !this.isLive) {
			if (console) {console.log(arg);}
			return true;
		}
		return false;
	};

	// Display an event.
	NW.writeLatestEvent = function(event) {

		var recent = $('#recent-events', top.document)
		.find('#recent-event-attacked-by').text('You were recently in combat').end()
		.find('#view-event-char').text(event.sender).attr('href', 'player.php?player_id='+event.send_from).end();
		if (recent && recent.addClass) {
			if (event.unread) {
				recent.addClass('message-unread');
				// if unread, Add the unread class until next update.
			} else {
				recent.removeClass('message-unread');
			}

			recent.show().click(NW.eventsHide);
		}
	}

	NW.eventsRead = function() {
		$('#recent-events', top.document).removeClass('message-unread');
	};
	
	NW.eventsHide = function() {
		$('#recent-events', top.document).hide();
	};
	
	NW.eventsShow = function() {
		$('#recent-events', top.document).show();
	}

	// Pull the event from the data store and request it be displayed.
	NW.updateLatestEvent = function() {
		var hideEventsAfter = 10;
		var feedback = false;
		var event = this.getEvent();

		if (!event) {
			this.feedbackSpeedUp(); // Make the interval to try again shorter.
		} else if (this.datastore.visibleEventId == event.event_id) {
			// If the stored data is the same as the latest pulled event...
			this.datastore.eventUpdateCount = (typeof this.datastore.eventUpdateCount === 'undefined'? 
					this.datastore.eventUpdateCount = 1 : 
					this.datastore.eventUpdateCount + 1 ) ;
			if(this.datastore.eventUpdateCount > hideEventsAfter){
				NW.eventsHide();
			}
			if (!this.datastore.visibleEventRead) {
				// Makes any unread event marked as read after a second update, even if it wasn't really read.
				NW.eventsRead();
				this.datastore.visibleEventRead = true;
			}
		} else {
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
	NW.updateMessageCount = function() {
		var updated = false;
		var count = this.getMessageCount();

		if (this.storeArrayValue('unread_messages_count', count)) {
			updated = true;
			this.unreadMessageCount(count); // Display a value if changed.
		}

		return updated;
	};

	NW.updateMemberCounts = function() {
		var activeCount = this.pullFromDataStore('member_counts', 'active');
		var totalCount = this.pullFromDataStore('member_counts', 'total');

		NW.activeMembersContainer.textContent = activeCount;
		NW.totalMembersContainer.textContent = totalCount;

		return true;
	};

	// Update the number of unread messages, displayed on index.
	NW.unreadMessageCount = function(messageCount) {
		var recent = $('#messages', top.document).find('.unread-count').text(messageCount);
		// if unread, Add the unread class until next update.
		if (recent && recent.addClass) {
			if (messageCount>0) {
				recent.addClass('message-unread');
			} else {
				recent.removeClass('message-unread');
			}
		}
	};

	NW.getEvent = function() {
		return this.pullFromDataStore('latestEvent');
	};

	NW.getPlayerInfo = function() {
		return this.pullFromDataStore('playerInfo');
	};

	// Update display elements that live on the index page.
	NW.updateIndex = function() {
		var messageUpdated      = this.updateMessageCount();
		var eventUpdated        = this.updateLatestEvent();
		//var healthUpdated       = this.getAndUpdateHealth(); // health bar.
		var memberCountsUpdated = this.updateMemberCounts(); // Active member count over chat

		// If any changes to data occurred, return true.
		var res = (!!(messageUpdated || eventUpdated || healthUpdated));

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

		if (this.updateDataStore(data.member_counts, 'active', 'member_counts', 'active')) {
			updated = true;
		}

		if (this.updateDataStore(data.event, 'event_id', 'latestEvent', 'event_id')) {
			updated = true;
		}

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
	
	// No-one knows what the fuck this does.
	NW.make_checkAPI_callback = function(p_additionalCallback) {
		var self = this;
		return function(data) {
			self.checkAPI_callback(data);
			if (p_additionalCallback) {
				p_additionalCallback();
			}
		}
	};

	// Saves an array of data to the global data storage, only works on array data, with an index.
	// Take in a new datum from the api, compare it's <property name> to the already-in-js-global-storage's property called <comparison_name>
	// This is comparing an old version to a new version, and storing any changes between the two.
	NW.updateDataStore = function(datum, property_name, global_store, comparison_name) {
		if (datum && datum[property_name]) {
			if (!this.datastore[global_store] || (this.datastore[global_store][comparison_name] != datum[property_name])) {
				// If the data isn't there, or doesn't match, update the store.
				this.datastore[global_store] = datum;
				return true;
			}
		}

		return false; // Input didn't contain the data, or the data hasn't changed.
	};

	// Return the most up-to-date value, which was stored prior.
	NW.pullFromDataStore = function(global_store, property_name) {
		if (this.datastore[global_store]) {
			if (property_name && typeof(this.datastore[global_store][property_name]) != 'undefined') {
				// If a property_name was specified, return the value for that specific property (e.g. event.event_id)...
				return this.datastore[global_store][property_name];
			}

			// ...otherwise return the whole storage entity, (e.g. event).
			return this.datastore[global_store];
		}

		return null;
	};

	// Store any changes to the value, if any, and return true if changed, false if unchanged.
	NW.storeArrayValue = function(name, value) {
		if (!this.datastore['array']) {
			this.datastore['array'] = {}; // Verify there's a storage array.
		}

		// Check for a change to the value to store.
		if ((typeof(this.datastore['array'][name]) != 'undefined') || this.datastore['array'][name] == value) {
			// If it exists and differs, store the new one and return true.
			this.datastore['array'][name] = value;
			return true;
		} else {
			return false;
		}
	};

	// Get a stored hash if available.
	NW.pullArrayValue = function(name) {
		return (this.datastore['array'] && typeof(this.datastore['array'][name]) != 'undefined' ? this.datastore['array'][name] : null);
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
				if(chats){
					for (var chat_message in chats) {
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
				}

				this.datastore.new_chats = null;
				return false;
			}
		}
	};
	
	NW.addChat = function(author, message, timestamp, author_id){
		
	};

	NW.renderChatMessage = function(p_message) {
		var container = document.createElement('dd');
		container.className = "chat-message";
		container.appendChild(document.createTextNode(p_message.message));
		return container;
	};

	/**
	* Takes in a chat message and returns an HTML node that contains the display for the author of the chat message
	*/
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

	/**
	* Event handler for when the chat refresh button is clicked
	*/
	NW.chatRefreshClicked = function(button) {
		button.onclick = null;
		$(button).css({'cursor':'default'});
		//button.style.cursor = 'default'; // This fails in chrome.
		button.src = 'images/refresh_disabled.gif';
		setTimeout(function() {
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

	/**
	* Returns whether or not manual chat refresh (as opposed to automatic chat refresh) is currently locked (because a manual refresh is ongoing)
	*/
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

/***************************** Execution of code, run at the end to allow all function definitions to exist beforehand. ******/
if(g_isIndex || g_isRoot){
// This has to be outside of domready for some reason.
	if (parent.frames.length != 0) { // If there is a double-nested index...
		location.href = "main.php"; // ...Display the main page instead.
		// This function must be outside of domready, for some reason.
	}
}

$(function() {

	$('html').removeClass('no-js'); // Remove no-js class when js present.
	$('time.timeago').timeago(); // Set time since whatever areas
		
	// INDEX ONLY CHANGES
	if (g_isIndex || g_isRoot) {
		var hash = window.location.hash;
		if(hash && hash.indexOf(".php") > 0){ // If a hash exists.
			var page = hash.substring(1); // Create a page from the hash by removing the #.
			$('iframe#main').attr('src', page); // Change the iframe src to use the hash page.
		}
		$('#donation-button').hide().delay('3000').slideDown('slow').delay('20000').slideUp('slow');
		// Hide, show, and then eventually hide the donation button.
		var isTouchDevice = 'ontouchstart' in document.documentElement;
		if(!isTouchDevice){
			// Hide the self and map subcategories initially, leaving the combat subcategory visible
			var subcats = $('#self-subcategory, #map-subcategory').hide();
			//delay('2000').slideUp('slow');
			// Find the trigger areas and show the appropriate subcategory.
			var triggers = $('#category-bar').find('.combat, .self, .map');
			if(triggers){
				triggers.mouseenter(function(){
					var trigger = $(this);
					var triggeredSubcat = $("#"+trigger.attr('class')+'-subcategory').show().siblings().hide();
					// When a different trigger area is hovered, hide the other subcats.
				});
			}
		}
		// For all pages, if a link with a target of the main iframe is clicked
		// make iframe links record in the hash.
		$('a[target=main]').click(function(){
			var target = $(this).attr('href');
			var winToChange = window.parent != window? window.parent : window;
			winToChange.location.hash = target;
			// Then update the hash to the source for that link.
			return true;
		});
	
		NW.quickDiv = document.getElementById('quickstats-frame-container');
		$('#chat-loading').show();
		NW.chainedUpdate(); // Start the periodic index update.
		$('#skip-to-bottom').click(function(){
			$(this).hide();
		});
		var quickstatsLinks = $("a[target='quickstats']");
		quickstatsLinks.css({'font-style':'italic'}); // Italicize
        NW.displayBarstats(); // Display the barstats already fleshed out by php.
		// Update the mini chat section for the first time.
		NW.checkForNewChats();
		NW.startRefreshingMinichat(); // Start refreshing the chat.

		$('#post_msg_js').submit(function() {return NW.sendChatContents(this)});
		// When chat form is submitted, send the message, load() the chat section and then clear the textbox text.

		NW.activeMembersContainer = document.getElementById('active-members-display');
		NW.totalMembersContainer = document.getElementById('total-members-display');
	} else if (g_isSubpage) {
		$('body').addClass('solo-page'); // Add class to solo-page bodies.
		// Displays the link back to main page for any lone subpages not in iframes.
	}

	//april1stCheck();
});
