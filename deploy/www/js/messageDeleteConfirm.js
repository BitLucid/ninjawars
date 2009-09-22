$('#delete-messages').click(function(){
    $(this).css({'color':'red'});
  var answer = confirm('Delete all messages?');
  return answer // answer is a boolean
}); 

    $(document).ready(function(){
        $('#delete-message').css({'color':'red','background-color':'teal'});
    });
/*
    $('#delete-messages a').click(function () {
        var answer = confirm("Delete all messsages?");
        alert('was here'+answer);
        return answer;
    });
    */
