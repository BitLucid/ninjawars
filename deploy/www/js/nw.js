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

$ = window.$ ? window.$ : jQuery;
NW = window.NW ? window.NW : createNW();

// GLOBAL FUNCTIONS


// Determines the update interval, 
//increases when feedback == false, rebaselines when feedback == true
function getUpdateInterval(feedback){
    var interval = (feedback || !NW.updateInterval)? 30 : (NW.updateInterval<300 ? NW.updateInterval+1 : 180);
    // For now, put the interval at 30 until I have the increasing method set up.
    NW.updateInterval = interval; // Store the current value.
    return interval;
    // Start is about 10 sec, max is 3 minutes.
}

// JS Update Heartbeat
function chainedUpdate(chainCounter){
    chainCounter = chainCounter ? chainCounter : 1;
    var feedback = updateIndex(chainCounter); 
    // Update and get good or bad feedback to increase or decrease interval.
    var secondInterval = getUpdateInterval(feedback); 
    setTimeout(function (){chainedUpdate(chainCounter);}, secondInterval*1000); // Repeat once the interval has passed.
    // If we need a to cancel the update down the line, store the id that setTimeout returns.
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

function updateLatestMessage(){
    $.getJSON('api.php?type=latest_message&jsoncallback=?', function(data){
        var message = data.message
            if(NW.latest_message_id == message.message_id){
                // Don't need to update the text.
                if(!message.unread){
                    $('#recent-mail .latest-message-text').removeClass('message-unread');
                }
                return false;
            }

            NW.latest_message_id = message.message_id; // Store latest message.
            $('#recent-mail').html("<div class='latest-message'><div id='latest-message-title'>Latest Message:</div><a href='player.php?player="+message.send_from+"' target='main'>"+message.sender+"</a>: <span class='latest-message-text "+(message.unread? "message-unread" : "")+"'>"+message.message.substr(0, 12)+"...</span> </div>");
            // if unread, Add the unread class until next update.
            // Pull a message with a truncated length of 12.
        });
    return true;
}


function updateLatestEvent(){
    $.getJSON('api.php?type=latest_event&jsoncallback=?', function(data){
        var event = data.event;
        if(NW.latest_event_id == event.event_id){
            if(!event.unread){
                $('#recent-events .latest-event-text').removeClass('message-unread');
            }
            return false; // Nothing to update.
        }
        NW.latest_event_id = event.event_id; // Store latest event.
        // Add the unread class until next update.

        $('#recent-events').html("<div class='latest-event'><div id='latest-event-title'>Latest Event via <a href='player.php?player="+event.send_from+"' target='main'>"+event.sender+"</a>:</div><span class='latest-event-text "+(event.unread? "message-unread" : "")+"'>"+event.event.substr(0, 12)+"...</span></div>");
        // Pull a message with a truncated length of 12.
    });
    return true; // There was a valid update.
}


// Keep in mind the need to use window.parent when calling from an iframe.
function updateHealthBar(health){
    // Should only update when a change occurs.
    if(health != NW.currentHealth){
        var mess = health+' health';
        var span = $('#health-status', top.document);
        span.text(mess);
        if(health<100){
            span.addClass('injured');
        } else {
            span.removeClass('injured');
        }
        NW.currentHealth = health;
        return true;
    }
    return false;
}

//function checkForInfoChanges(){
    // Get everything all at once from the api.
    // If info isn't stored, store it.
    // Check if each info is changed.
    // If changed, run each update functionality.
//    $.getJSON('api.php?type=all&jsoncallback=?', function(data){
//    });
//}

function getAndUpdateHealth(){
    var res = false;
    NW.currentHealth = NW.currentHealth ? NW.currentHealth : null;
    $.getJSON('api.php?type=player&jsoncallback=?', function(data){
        if(data.player.health != NW.currentHealth){
            res = updateHealthBar(data.player.health);
            // That will also update the global currentHealth.
        }
    });
    return res; // Whether there was anything to update.
}

// Update display elements that live on the index page.
function updateIndex(){
    var messageUpdated = updateLatestMessage();
    var eventUpdated = updateLatestEvent();
    // update chat
    // health bar.
    var healthUpdated = getAndUpdateHealth();
    var res = (!!(messageUpdated || eventUpdated || healthUpdated));
	if(debug() && console){
    	console.log(messageUpdated);
    	console.log(eventUpdated);
    	console.log(healthUpdated);
    }
    return res; // determines good or bad feedback.
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
        parent.mini_chat.location="mini_chat.php";
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
	return (window.location.pathname.substr(-9,9) == 'index.php');
}

function isLive(){
	return window.location.host  != 'localhost';
}

// Returns true when debug bit set or localhost path used.
function debug(){
	return NW.debug || !isLive();
}

function isRoot(){
	return (window.location.pathname == '/');
}

function isSubpage(){
	return !isIndex() && !isRoot() && (window.parent == window);
}

// Not secure, just convenient.
function isLoggedIn(){
	return true;
}

/**
 * Add the logo as a back-link to any pages that are broken out of the iframe.
**/
function logoAppend(){
	if(isSubpage()){	
		$('#logo-appended').addClass('sub-page'); // Should make the image display on subpages.
	}
}

// Initial load of everything, run at the bottom to allow everything else to be defined beforehand.
$(document).ready(function() {
   
    // INDEX ONLY CHANGES 
    if(isIndex() || isRoot()){
    	// Not great because it doesn't allow for pretty urls, down the line.   

		if(isLoggedIn()){
	       	chainedUpdate(); // Start the periodic index update.
	    }
       	
       /* Collapse the following parts of the index */
        $("#links-menu").toggle();
        
        /* Expand the chat when it's clicked. */
        $("#expand-chat").click(function () {
            $("#mini-chat-frame-container").removeClass('chat-collapsed').addClass('chat-expanded');/*.height(780);*/
            $(this).hide();  // Hide the clicked section after expansion.
        });
        
        
        var quickstatsLinks = $("a[target='quickstats']");
        quickstatsLinks.css({'font-style':'italic'}); // Hide all links using target as a test.
        var quickDiv =  $('div#quickstats-frame-container');
        //quickDiv.load('quickstats.php');
        // Add the click handlers for loading the quickstats frame.
        frameClickHandlers(quickstatsLinks, quickDiv);
        NW.quickDiv = quickDiv;
        
        /*
        miniChatLinks = $("a[target='mini_chat']");
        miniChatLinks.css({'font-style':'italic'}); // Hide all links using target as a test.
        chatDiv = $('div#mini-chat-frame-container');
        
        frameClickHandlers(miniChatLinks, chatDiv);
        
        mainLinks = $("a[target='main']");
        mainLinks.css({'font-style':'italic'}); // Hide all links using target as a test.
        mainDiv = $('div#main-frame-container');
        mainDiv.hide();
        // The mainDiv handler would want to refresh quickstats when it loads if it's
        // footer gets excluded.
        */
    }    
    
    /* THIS CODE RUNS FOR ALL SUBPAGES */
    logoAppend(); // Append link back to main page for any lone subpages not in iframes.
    
    // TODO: Analyze whether it's good for this code to run in auto-loaded subpages in iframes, e.g. chat, quickstats.
        
    // GOOGLE ANALYTICS
    /* Original suggested header include, I made it nw specific with
    // http://www and just included the file directly <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    doscument.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script> */
    try {
    var pageTracker = _gat._getTracker("UA-707264-2");
    pageTracker._trackPageview();
    } catch(err) {}

   
 });
