/* Show current character limits on profile text */
/*jshint browser: true, white: true, plusplus: true*/
/*global $ */
function charSuggest(textareaid, limit, infoid){
    $('#'+textareaid).keyup(function(){
        var textlength = $('#'+textareaid).val().length;
        var newText = limit+" character limit, "+textlength+" characters used.";
        $('#'+infoid).text(newText);
    });

}
$(function() {
	'use strict';
    charSuggest('player-profile-area', 500, 'characters-left');
});
