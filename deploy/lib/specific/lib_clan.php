<?php
// Show the form for the clan joining, or perform the join.
function render_clan_join($process=null, $username, $clan_id){
   	$sql = new DBAccess();
    if ($process == 1) {
        $clan = get_clan($clan_id);
        $confirm = $sql->QueryItem("SELECT confirm FROM players WHERE uname = '$username'");
        $url = message_url("clan_confirm.php?clan_joiner=".rawurlencode($username)
            ."&confirm=$confirm&clan_id=".url($clan_id), 'Confirm Request');
        $join_request_message = "CLAN JOIN REQUEST: $username has sent a request to join clan ".out($clan['clan_name']).".
            If you wish to allow this ninja into your clan click the following link:
            $url";
        $leader = get_clan_leader($clan_id);
        $leader_id = $leader['player_id'];
        $leader_name = $leader['uname'];
        send_message(get_user_id($username),$leader_id,$join_request_message);
        $res =  "<div id='clan-join-request-sent'>Your request to join ".out($clan['clan_name'])." 
            has been sent to ".out($leader_name)."</div>\n";
    } else {                                            
        //Clan Join list of available Clans
        $leaders = get_clan_leaders();
        $res = "<p>Clans Available to Join</p>
        <p>To send a clan request click on that clan leader's name.</p>
        <ul>";
        foreach($leaders as $leader){
            $res .= "<li><a href=\"clan.php?command=join&clan_id={$leader['clan_id']}&process=1\">
                    Join {$leader['clan_name']}</a>.
                    Its leader is <a href=\"player.php?player=".rawurlencode($leader['player_id'])."\">
                    {$leader['uname']}</a>, level {$leader['level']}.
                    <a href=\"clan.php?command=view&clan_id={$leader['clan_id']}\">View This Clan</a>
                </li>\n";
        }
        $res .= "</ul>";
    }
    return $res;
}

// Wrapper for getting a single clan's info.
function get_clan($clan_id){
    $clans = get_clans($clan_id);
    return reset($clans);
}

// Just get the list of clans, plus the creator ids just for reference.
function get_clans($clan_id=null){
    $clan_or_clans = $clan_id? 'where clan_id = '.sql($clan_id) : 'order by clan_id';
    $sql = new DBAccess();
    $clans = $sql->fetchData("select clan_id, clan_name, clan_created_date, _creator_player_id 
        from clan $clan_or_clans");
    return $clans;
}

// Gets the clan founders, though they may be dead and unconfirmed now.
function get_clan_founders(){
    $sql = new DBAccess();
    $clan_founders = $sql->fetchData("select uname, player_id, clan_name, confirmed 
        from players join clan_player on player_id=_player_id join clan on clan_id=_clan_id 
        where _creator_player_id = player_id order by clan_id");
    return $clan_founders;
}

// Return only the single clan leader and their information.
function get_clan_leader($clan_id){
    $clans = get_clan_leaders($clan_id);
    return reset($clans);
}

// Get the current clan leader or leaders.
function get_clan_leaders($clan_id=null, $all=false){
    $limit = $all? '' : ' limit 1';
    $clan_or_clans = $clan_id? 'and clan_id = '.sql($clan_id) : 'order by clan_id';
    $sql = new DBAccess();
    $clans = $sql->FetchAll("select clan_id, clan_name, _creator_player_id, player_id, uname 
        from players join clan_player on player_id=_player_id join clan on _clan_id=clan_id 
        where confirmed=1 and member_level>0 $clan_or_clans order by clan_id, level $limit");
    return $clans;
}

// Functions for creating the clan ranking tag cloud.

/**
 * This determines the criterial for how the clans get ranked and tagged, 
 *   and shows only non-empty clans.
**/
function clan_size(){
	$res = array();
	$db = new DBAccess();

    // sum the levels of the players (minus days of inactivity) for each clan
	$sel = "select sum(level-3-round(days/5)) as sum, clan_name, clan_id from clan JOIN clan_player ON clan_id = _clan_id JOIN players ON _player_id = player_id where confirmed = 1 group by clan_id, clan_name order by sum desc";

	$counts = $db->FetchAll($sel);
	$largest = reset($counts);
	$max = $largest['sum'];

	foreach ($counts as $clan_info){
		// *** make percentage of highest, multiply by 10 and round to give a 1-10 size ***
		$res[$clan_info['clan_id']]['name'] = $clan_info['clan_name'];
		$res[$clan_info['clan_id']]['score'] = floor(( (($clan_info['sum'] - 1 < 1 ? 0 : $clan_info['sum'] - 1)) / $max) * 10) + 1;
	}

	return $res;
}

// Display the tag list of clans/clan links.
function render_clan_tags(){
	$clans = clan_size();
	//$clans = @natsort2d($clans, 'level');
	$res = "<div id='clan-tags'>
                <h4 id='clan-tags-title'>
                    All Clans
                </h4>
            <ul>";

	foreach ($clans as $clan_id => $data){
		$res .= "<li class='clan-tag size".$data['score']."'>
                <a href='?command=view&amp;clan_id=".urlencode($clan_id)."'>".$data['name']."</a>
            </li>";
	}

	$res .= "</ul>
            </div>";
	return $res;
}

/* Display the clan member name list or tag list */
function render_clan_view($p_clanID, $sql){
	if (!$p_clanID){
		return ''; // No viewing criteria available.
	}

	$members = $sql->FetchAll(
        "SELECT uname, clan_name, level, days, _creator_player_id, player_id
            FROM clan
            JOIN clan_player ON _clan_id = $p_clanID AND clan_id = _clan_id
            JOIN players ON player_id = _player_id AND confirmed = 1 order by health, level desc");

	$max_list = $sql->FetchAll(
        "SELECT max(level) as max 
        FROM clan
        JOIN clan_player ON _clan_id = $p_clanID AND clan_id = _clan_id
        JOIN players on player_id = _player_id AND confirmed = 1");

	$max_array = reset($max_list);
	$max = $max_array['max'];
	//$members = @natsort2d($members, 'days');

	$res = "<div id='clan-members'>
            <h3 id='clan-members-title'>".$members[0]['clan_name']."</h3>
            <div id='clan-members-count'>Clans Members: ".count($members)."</div>
            <ul id='clan-members-list'>";

	foreach ($members as $member){
		$member['size'] = floor( ( ( ($member['level'] - $member['days'] < 1 ? 0 : $member['level'] - $member['days']) ) / $max) * 2) + 1;

		if ($member['player_id'] == $member['_creator_player_id']){
			$member['size'] = $member['size'] + 2;
			$member['size'] = ($member['size'] > 2 ? 2 : $member['size']);
		}

		$res .= "<li class='member size{$member['size']}'>
                <a href='player.php?player={$member['uname']}'>{$member['uname']}</a>
            </li>";
	}

	$res .= "</ul>
            </div>";
	return $res;
}


// Helper function.
/*
function natsort2d($arrIn, $index = null) {
    $arrTemp = array();
    $arrOut = array();
    foreach ( $arrIn as $key=>$value ) {
        reset($value);
        $arrTemp[$key] = is_null($index)
                            ? current($value)
                            : $value[$index];
    }
    natsort($arrTemp);
    foreach ( $arrTemp as $key=>$value ) {
        $arrOut[$key] = $arrIn[$key];
    }
    $arrIn = $arrOut;
    return $arrIn;
}*/



?>
