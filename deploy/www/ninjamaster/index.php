<?php
// Secure the script
$char_id = get_char_id();
$self = null;
if($char_id){
	$self = new Player($char_id);
}
if($self && $self->isAdmin()){
// Admin possibilities start here.

function high_rollers(){
	// Select first few max kills from players.
	// Max turns.
	// Max gold.
	// Max kills
	// etc.
	$res = array();
	$res['gold'] = query_array('select player_id, uname, gold from players order by gold desc limit 10');
	$res['turns'] = query_array('select player_id, uname, turns from players order by turns desc limit 10');
	$res['kills'] = query_array('select player_id, uname, kills from players order by kills desc limit 10');
	$res['health'] = query_array('select player_id, uname, health from players order by health desc limit 10');
	$res['ki'] = query_array('select player_id, uname, ki from players order by ki desc limit 10');
	return $res;
}


function duped_ips(){
	return query_array('select uname, player_id, ip from players where ip in (SELECT ip FROM players WHERE active = 1 GROUP  BY ip HAVING count(*) > 1 ORDER BY count(*) ASC limit 30) order by ip');
}

// Return one or many
function split_char_infos($ids){
	if(is_numeric($ids)){
		return array(char_info($ids));
	} else {
		$res = array();
		$ids = explode(',', $ids);
		foreach($ids as $id){
			$res[$id] = char_info($id);
		}
		return $res;
	}
}

	$dupes = duped_ips();

	$stats = high_rollers();

	// If a request is made to view a character's info, show it.
	$view_char = in('view');
	$char_infos = null;
	if($view_char){
		$char_infos = split_char_infos($view_char);
	}


	display_page(
		'ninjamaster.tpl'	// *** Main Template ***
		, 'Admin Actions' // *** Page Title ***
		, array('stats'=>$stats, 'char_infos'=>$char_infos, 'dupes'=>$dupes) // *** Page Variables ***
	);


} else {
	// Redirect to the root site.
	redirect('/');
}




?>
