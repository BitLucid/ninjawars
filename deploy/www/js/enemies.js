/**
 * who/what/why/where
 * Show enemy matches when searching and similar cases
 *
**/
$(document).ready(function(){

	// Function to display the matches.
	NW.displayMatches = function(json_matches){
		var sample = $('#sample-enemy-match').detach();
		var moreMatches = $('#more-matches');
		//NW.debug(json_matches);
		if(typeof(json_matches.char_matches) !== 'undefined'){
			// Remove all li's not preceded by an li.
			$('#ninja-matches li+li').remove();
			// Take the matches, extract them into individuals.
			var inc = 0;
			for(var i in json_matches.char_matches){
				if(inc>9){
					break;
				}
				var clone = sample.clone().attr('id', 'enemy-match-'+i);
				if(i%2 === 1){ // Classify the even entries (here 0, 2, 4, etc)
					clone.addClass('even');
				}
				var match = json_matches.char_matches[i];
				//NW.debug(match);
				var link = clone.find('a');
				//NW.debug(sample);
				// For each individual, extend the default link to make an attack link.
				var newlink = link.attr('href')+match.uname;
				// Add the new ones back on after the sample.
				sample.after(link.attr('href',newlink).text(match.uname).end().show());
				inc++;
			}
			if(json_matches.char_matches.length > 9){
				moreMatches.show(); // Show the "with more matches" section.
			} else {
				moreMatches.hide();
			}
		}
	};

	// Only show the matches section when needed.
	$ninjaMatches = $('#ninja-matches').hide();
	$('#enemy-add input').focus(function(){
		NW.debug('Enemy form focused');
		$ninjaMatches.show();
	});
	
	
	var searchbox = $('#enemy-match');
	searchbox.keyup(function () {
		NW.typewatch(function () {
			// executed only 500 ms after the last keyup event.
			var term = $('#enemy-match').val();
			var limit = 11; // Limit to 11, and only display 10.
			if(term && term.length>2){
				// Only search after a few characters are typed out
				NW.charMatch(term, limit, NW.displayMatches);
			}
		}, 500);
	});
	
});