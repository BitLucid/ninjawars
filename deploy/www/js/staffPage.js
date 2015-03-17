function loadLastCommitMessage(){
    var owner = 'BitLucid';
    var repo = 'ninjawars';
    var oauthToken = '76392ebba585c4be3f63e8a4b7d2704ca00e71bf'; // Public url read access.
    var githubUrl = 'https://api.github.com/repos/'+owner+'/'+repo+'/commits/HEAD?access_token='+oauthToken+'&callback=?';
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
