$(document).ready(function() {
    charSuggest('player-profile-area', 500, 'characters-left');
});

function charSuggest(textareaid, limit, infoid){
    $('#'+textareaid).keyup(function(){
        var textArea = $('#'+textareaid);
        var text = textArea.val();
        var textlength = text.length;
        var info = $('#'+infoid);
        var newText = limit+" character limit, "+textlength+" characters used.";
        info.text(newText);
    });

}
