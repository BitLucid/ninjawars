function loadLastCommitMessage(){
    var login = 'tchalvak' // your username
        
    $.getJSON('https://api.github.com/repos/tchalvak/ninjawars/commits/master?callback=?', 
    	function(data) {
    		if(!data.data){
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
		}
    );
}
