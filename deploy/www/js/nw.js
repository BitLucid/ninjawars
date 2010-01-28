/* Load all the js functionality here, mostly */

// Sections are, in order: SETTINGS | FUNCTIONS | READY

// Url Resources:
// http://www.jslint.com/
// http://yuiblog.com/blog/2007/06/12/module-pattern/
// http://www.javascripttoolbox.com/bestpractices/
// TODO: change generated vars to square bracket notation.

// TODO: Create a dummy con sole dot log functionality to avoid errors on live?


/*  GLOBAL SETTINGS & VARS */

function createNW(){
    var innerNW = {};
    innerNW.firstLoad = 1; 
    // Counter starts at 1 to indicate newly refreshed page, as opposed to ajax loads.
    return innerNW;
}

$ = typeof window.$ == 'object' ? window.$ : jQuery;
NW = typeof window.NW == 'object' ? window.NW : createNW();

// GLOBAL FUNCTIONS

// Not secure, just convenient.
function isLoggedIn(){
	return NW.loggedIn;
}

function setLoggedIn(){
	NW.loggedIn = 1;
}

function clearLoggedIn(){
	NW.loggedIn = 0;
}

// Returns true when debug bit set or localhost path used.
function debug(arg){
	if(NW.debug || !isLive()){
		if(console){console.log(arg);}
		return true;
	} 
	return false;
}

// Update the chat page without refresh.
//function updateChat(){
// store a latest chat id to check against the chat.
// Get the chat data.
// If the latest hasn't changed, just return nothing.
// If the latest has changed, return
// If the latest has changed, cycle through the data
//saving non-matches until you get back to the "latest" match.
// Update the chat's ui.
// update the "latest chat id" var.
//}


function writeLatestEvent(event){
	$('#recent-events', top.document).html("<div class='latest-event'><div id='latest-event-title'>Latest Event via <a href='player.php?player="+event.send_from+"' target='main'>"+event.sender+"</a>:</div><span class='latest-event-text "+(event.unread? "message-unread" : "")+"'>"+event.event.substr(0, 12)+"...</span></div>");
    // if unread, Add the unread class until next update.
    // Pull a message with a truncated length of 12.
}

function updateLatestEvent(){
	var updated = false;
	if(!NW.event){
		updated = true; // Try again shortly.
	}else if(NW.visibleEventId == NW.event.event_id){
		// Makes the event read every update interval.
        $('#recent-events .latest-event-text', top.document).removeClass('message-unread');
	} else {
		updated = true;
		NW.visibleEventId = NW.event.event_id;
        writeLatestEvent(event);
	}
	return updated;
}


function writeLatestMessage(message){
		// TODO: Transform the appended html into hidden html that gets fleshed out and marked visible by this function.
        // if unread, Add the unread class until next update.
        // Pull a message with a truncated length of 12.
		$('#recent-mail', top.document).html("<div class='latest-message'><div id='latest-message-title'>Latest Message:</div><a href='player.php?player="+message.send_from+"' target='main'>"+message.sender+"</a>: <span class='latest-message-text "+(message.unread? "message-unread" : "")+"'>"+message.message.substr(0, 12)+"...</span> </div>");
}


function updateLatestMessage(){
	var updated = false;
	if(!NW.message){
		updated = true; // Check for info again shortly.
	}else if(NW.visibleMessageId == NW.message.message_id){
        if(!message.unread){ // Only turn a message read if it actually has been in the message page.
			$('#recent-mail .latest-message-text', top.document).removeClass('message-unread');
		}
	} else {
		updated = true;
		NW.visibleMessageId = NW.message.message_id;
		writeLatestMessage(message);
	}
	return updated;
}



function updateHealthBar(health){
    // Should only update when a change occurs.
    if(health != NW.visibleHealth){
        var mess = health+' health';
        var span = $('#health-status', top.document);
        // Keep in mind the need to use window.parent when calling from an iframe.
        span.text(mess);
        if(health<100){
            span.addClass('injured');
        } else {
            span.removeClass('injured');
        }
        NW.visibleHealth = health;
        return true;
    }
    return false;
}


function getAndUpdateHealth(){
    var updated = false;
    NW.playerInfo.health = NW.playerInfo.health ? NW.playerInfo.health : null;
    if(NW.playerInfo.health !== null && NW.visibleHealth != NW.playerInfo.health){
    	updateHealthBar(NW.playerInfo.health);
    	updated = true;
    }
    return updated;
}



// Update display elements that live on the index page.
function updateIndex(){
    var messageUpdated = updateLatestMessage();
    var eventUpdated = updateLatestEvent();
    // update chat
    // health bar.
    var healthUpdated = getAndUpdateHealth();
    var res = (!!(messageUpdated || eventUpdated || healthUpdated));
    debug("Message Updated: "+messageUpdated);
    debug("Event Updated: "+eventUpdated);
    debug("Health Updated: "+healthUpdated);
    return res; // determines good or bad feedback.
}


function updateIndexInfo(){
	var updated = false;
    $.getJSON('api.php?type=index&jsoncallback=?', function(data){
		if(data.player && data.player.player_id && !NW.playerInfo || !NW.playerInfo.health || data.player.health != NW.playerInfo.health || data.player.last_attacked != NW.playerInfo.last_attacked){
	    	NW.playerInfo = data.player;
	    	updated = true;
	    }
	    if(data.message && data.message.message_id && !NW.latestMessage || NW.latestMessage && NW.latestMessage.message_id != data.message.message_id){
	    	NW.latestMessage = data.message;
	    	updated = true;
	    }
	    if(data.event && data.event.event_id && !NW.latestEvent || NW.latestEvent.event_id != data.event.event_id){
	    	NW.latestEvent = data.event;
	    	updated = true;
	    }
	}); // End of getJSON function call.
    if(updated){
    	updateIndex(); // Always redisplay for any poll that has information updates.
    }
	return updated;
}


// Determines the update interval, 
//increases when feedback == false, rebaselines when feedback == true
function getUpdateInterval(feedback){
	var maxInt = 180;
	var min = 30;
	var first = 5;
	if(!NW.updateInterval){
		NW.updateInterval = first;
	} else if(feedback){
		NW.updateInterval = min;
	} else if(NW.updateInterval>=maxInt){
		NW.updateInterval = maxInt;
	} else {
		NW.updateInterval++;
	}
    return NW.updateInterval;
}

// JS Update Heartbeat
function chainedUpdate(chainCounter){
    var chainCounter = !!chainCounter ? chainCounter : 1;
    // Skip the update if not logged in.
    // Skip the heartbeat the first time through, and skip it if not logged in.
    var feedback = (isLoggedIn() && chainCounter != 1? updateIndexInfo() : true);
    // Update and get good or bad feedback to increase or decrease interval.
    var furtherIntervals = getUpdateInterval(feedback); 
    debug(furtherIntervals);
    debug("chainCounter: "+chainCounter);
    chainCounter++;
    setTimeout(function (){chainedUpdate(chainCounter);}, furtherIntervals*1000); // Repeat once the interval has passed.
    // If we need a to cancel the update down the line, store the id that setTimeout returns.
}


// When clicking frame links, load a section instead of the iframe.
function frameClickHandlers(links, div){
    links.click(function(){
        div.load(this.href, 'section_only=1');
        return false;
    });
}

function toggle_visibility(id) {
    var tog = $("#"+id);
    tog.toggle();
    return false;
}

function refreshMinichat(){
    // Add check for this location.
        parent.mini_chat.location="mini_chat.php?from_js=1";
}

// For refreshing quickstats from inside main.
function refreshQuickstats(quickView){
    // Accounts for ajax section.
    var url = 'quickstats.php?command='+quickView;
    if(top.window.NW.firstLoad > 1){
        if(top.window.NW.quickDiv){
            top.window.NW.quickDiv.load(url, 'section_only=1');
        } else {
            // Use parent to indicate the parent global variable.
            parent.quickstats.location=url;
        }
    }
    top.window.NW.firstLoad++;
}

function isIndex(){ // Return true if the index page.
	// Not great because it doesn't allow for pretty urls, down the line.  
	return (window.location.pathname.substr(-9,9) == 'index.php')  || $('body').hasClass('main-body');
}

function isLive(){
	return window.location.host  != 'localhost';
}

function isRoot(){
	return (window.location.pathname == '/');
}

function isSubpage(){
	return !isIndex() && !isRoot() && (window.parent == window);
}

/**
 * Add a class to the body of any solo pages, which other css can then key off of.
**/
function soloPage(){
	if(isSubpage()){
		$('body').addClass('solo-page'); // Added class to solo-page bodies.
	}
}

// Initial load of everything, run at the bottom to allow everything else to be defined beforehand.
$(document).ready(function() {
   
    // INDEX ONLY CHANGES 
    if(isIndex() || isRoot()){ 

		chainedUpdate(); // Start the periodic index update.
       	
       /* Collapse the following parts of the index */
        $("#links-menu").toggle();
        
        /* Expand the chat when it's clicked. */
        $("#expand-chat").click(function () {
            $("#mini-chat-frame-container").removeClass('chat-collapsed').addClass('chat-expanded');/*.height(780);*/
            $(this).hide();  // Hide the clicked section after expansion.
        });
        
        
        var quickstatsLinks = $("a[target='quickstats']");
        quickstatsLinks.css({'font-style':'italic'}); // Italicize 
        var quickDiv =  $('div#quickstats-frame-container');
        //quickDiv.load('quickstats.php');
        // Add the click handlers for loading the quickstats frame.
        frameClickHandlers(quickstatsLinks, quickDiv); // Load new contents into the div when clicked.
        NW.quickDiv = quickDiv;
        
        
    }
    
    /* THIS CODE RUNS FOR ALL SUBPAGES */
    soloPage(); // Append a link back to main page for any lone subpages not in iframes.
        
    // GOOGLE ANALYTICS
    /* There's a script include that goes with this, but I just put it in the head directly.*/
    try {
    var pageTracker = _gat._getTracker("UA-707264-2");
    pageTracker._trackPageview();
    } catch(err) {}

   
 });
