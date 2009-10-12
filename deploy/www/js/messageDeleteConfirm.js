$(document).ready(function (){
    $('#delete-messages a').click(function(){
      var answer = confirm('Delete all messages?');
      return answer // answer is a boolean
    }).hover(function () {$(this).css({'background-color':'red'})}, function () {$(this).css({'background-color':'inherit'})}); 
});
