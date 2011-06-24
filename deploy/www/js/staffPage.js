function loadLastCommitMessage(){
    var login = 'tchalvak' // your username
        
    $.getJSON('http://github.com/api/v1/json/' + login + '/ninjawars/commit/master/?callback=?', function(data) {
    var unknown = $.grep(data.commit, function() {
        return true;
    })
        // Load latest commit message.
    $('#latest-commit-section').find('#latest-commit')
    .html(data.commit.message)
    .append("<div id='commit-author'>--"+data.commit.author.name+"</div>")
    .show().end()
	.find('#latest-commit-title').show();        
    });
}
