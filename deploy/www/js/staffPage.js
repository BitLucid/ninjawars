function loadLastCommitMessage(){
    var login = 'tchalvak' // your username
        
    $.getJSON('http://github.com/api/v1/json/' + login + '/ninjawars/commit/master/?callback=?', function(data) {
    var unknown = $.grep(data.commit, function() {
        return true;
    })
        // Load latest commit message.
	$('#latest-commit').html(data.commit.message);
	$('#latest-commit').append("<div id='commit-author'>--"+data.commit.author.name+"</div>");
	$('#latest-commit-title').show();        
	$('#latest-commit').show();    
    });
}        


$(document).ready(function() {
	$('.developer-info').hide();
	$('.expand-link').click(function () {
		$('.developer-info').slideDown();
		$('.expand-link').hide();
	});

	$('#latest-commit').hide();
	$('#latest-commit-title').hide();

	loadLastCommitMessage();
});
