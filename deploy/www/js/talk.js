$(document).ready(function () {
	$('#delete-messages form').submit(function() {
		return confirm('Delete all messages?'); // *** boolean return value ***
	});

	// If a refocus is requested, because a message was just sent, then refocus on the area.
	if(refocus && focus){
	  if(focus == 'clan'){
	    $('input#message-clan').focus();
	  } else {
	    $('input#message-to-ninja').focus();
	  }
	}
});
