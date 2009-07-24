/* Load all the js scripts here, essentially */

if(!$){
    var $ = jQuery;
}

if(!firstLoad){
    // Counter starts at 1 to indicate a newly refreshed page,
    // As opposed to subsequent loads of 
    var firstLoad = 1;
}

// INIT
$(document).ready(function() {

    // TODO: I need to specify whether this occurs in iframe windows. vs just outer window.
   
   /* Collapse the following parts of the index */
    $("#links-menu").toggle();
    
    /* Expand the chat when it's clicked. */
    $("#expand-chat").click(function () {
        $("#mini-chat-frame-container").removeClass('chat-collapsed').addClass('chat-expanded');/*.height(780);*/
        $(this).hide();  // Hide the clicked section after expansion.
    });
    
    
    quickstatsLinks = $("a[target='quickstats']");
    quickstatsLinks.css({'font-style':'italic'}); // Hide all links using target as a test.
    quickDiv =  $('div#quickstats-frame-container');
    //quickDiv.load('quickstats.php');
    // Add the click handlers for loading the quickstats frame.
    frameClickHandlers(quickstatsLinks, quickDiv);
    
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
    
   
 });
 

// GLOBAL FUNCTIONS

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


// Keep in mind the need to use window.parent when in iframe.
function updateHealthBar(health){
    // Should only update when a change occurs.
    mess = '| '+health+' health';
    span = $('#logged-in-bar-health', top.document);
    span.text(mess);
    if(health<100){
        span.css({'color' : 'red'});
    } else {
        span.css({'color' : ''});
    }
}

// For refreshing quickstats from inside main.
function refreshQuickstats(quickView){
    // Use parent to indicate the parent global variable.
    if(top.firstLoad > 1){
    	if (quickView){
    	    parent.quickstats.location='quickstats.php?command='+quickView;
    	} else {
    		parent.quickstats.location="quickstats.php";
    	}
    }
    top.firstLoad++;
}

/* Need to parse the 'this' php file/page so that refreshing to login auto-passes the appropriate page after */
function refreshToLogin(failed){
	if (top.location!= self.location) {
		top.location = 'index.php'
	}
	if(!failed){
		parent.location.href=parent.location.href;
	} else {
		parent.main.location="index.php?action=login";
	}
	return false;
}


 
 
