/* Load all the js scripts here, essentially */

// INIT
$(document).ready(function() {
   
   /* Collapse the following parts of the index */
    $("#links-menu").toggle();
    
    /* Expand the chat when it's clicked. */
    $("#expand-chat").click(function () {
        $("#mini-chat-frame-container").removeClass('chat-collapsed').addClass('chat-expanded');/*.height(780);*/
        $(this).hide();  // Hide the clicked section after expansion.
    });
    
    
    //$("a[target]").hide(); /* Hide all links using target */
    
    /*
    function addClickHandlers() {
        $("a.remote", this).click(function() {
            $("#target").load(this.href, addClickHandlers);
        });
    }
    $(document).ready(addClickHandlers);
    */
   
 });
 

// GLOBAL FUNCTIONS

function toggle_visibility(id) {
    var tog = $("#"+id);
    tog.toggle();
    return false;
}

function refreshMinichat(){
  parent.mini_chat.location="mini_chat.php";
}


function refreshQuickstats(quickView){
	if (!quickView){
		parent.quickstats.location="quickstats.php";
	} else {
		parent.quickstats.location='quickstats.php?command='+quickView;
	}
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


 
 
