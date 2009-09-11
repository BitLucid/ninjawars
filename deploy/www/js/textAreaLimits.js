$(document).ready(function() {
    $(function(){
        $('#player-profile-area').keyup(function(){
            textAreaLimits('player-profile-area', 500, 'characters-left');
        })
    });
});


function textAreaLimits(textareaid, limit, infoid){
    var textArea = $('#'+textareaid);
    var text = textArea.val();
    var textlength = text.length;
    var info = $('#'+infoid);
    var newText = limit+" character limit, "+textlength+" characters used.";
    info.text(newText);
}
