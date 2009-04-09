function init_at_end() {
	if(document.getElementById && document.createTextNode) {
		/* Compatibility check */

		/* Scripts to run when the page loads */
		/* Collapse the following parts of the index. */
	    toggle_visibility('links-menu');
	    
	    /* Collapse mini-chat to tolerable height via js */
	    expand_collapse_mini_chat();
    
	    /* Add in unobtrusive javascript onclick creation to the appropriate element ids. */

/* Unobtrusive js example
window.onload = function(){ //Wait for the page to load.
    var inputs = document.getElementsByTagName('input'), input;
    for(var i=0,l=inputs.length;i<l;i++){ 
        input = inputs[i];
        if(input.name && input.name=='date'){ 
            input.onchange = function(){ 
                validateDate();
            }
        }
    }
};
*/


	}/* End compatibility check */
}

window.onload = init_at_end;
