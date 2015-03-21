function loadLastCommitMessage(){
    var owner = 'BitLucid';
    var repo = 'ninjawars';
    var oauthToken = ''; // TODO: Figure out how to store this info here.
    var access = oauthToken?'access_token='+oauthToken+'&':'';
    var githubUrl = 'https://api.github.com/repos/'+owner+'/'+repo+'/commits/HEAD?'+access+'callback=?';
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

    // https://api.github.com/repos/BitLucid/ninjawars/commits/HEAD
    $.getJSON(githubUrl, placeCommit);
}
