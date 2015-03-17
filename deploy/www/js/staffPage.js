function loadLastCommitMessage(){
    var owner = 'BitLucid';
    var repo = 'ninjawars';
    var githubUrl = 'https://api.github.com/repos/'+owner+'/'+repo+'/commits/HEAD?callback=?&limit=2';
    var placeCommit = function(data) {
        if(!data.data || !data.data.commit){
            console.log('No github commit api data');
            console.log(data);
            return;
        }
        // Load latest commit message.
        $('#latest-commit-section').find('#latest-commit')
            .html(data.data.commit.message)
            .append("<div id='commit-author'>--"+data.data.commit.author.name+"</div>")
            .show().end()
            .find('#latest-commit-title').show();        
    };

    function setHeader(xhr) {
        var oauthToken = ''; // Public url read access.
        xhr.setRequestHeader('Authorization', 'token '+oauthToken);
      };

    // https://api.github.com/repos/BitLucid/ninjawars/commits/HEAD
    $.ajax({
          url: githubUrl,
          type: 'GET',
          dataType: 'json',
          success: placeCommit,
          error: $.noop,
          beforeSend: setHeader
        }
    );
}
