<?php
// Show the form for the clan joining, or perform the join.
function render_clan_join($process=null, $username, $clan_name){
   	$sql = new DBAccess();
    if ($process == 1) {
        $confirm = $sql->QueryItem("SELECT confirm FROM players WHERE uname = '$username'");
        $url = message_url("clan_confirm.php?clan_joiner=".rawurlencode($username)
            ."&confirm=$confirm&clan_name=".rawurlencode($clan_name), 'Confirm Request');
        $join_request_message = "CLAN JOIN REQUEST: $username has sent you a clan request.
            If you wish to allow this ninja into your clan click the following link:
            $url";
            
        send_message(get_user_id($username),get_user_id($clan_name),$join_request_message);
        echo "<div>***Your request to join this clan has been sent to $clan_name***</div>\n";
    } else {                                            
    
    //Clan Join list of available Clans
    //BUG: this code still needs fixing.
        $clan_leaders = $sql->FetchAll("SELECT uname,level,clan,clan_long_name FROM players
            WHERE lower(uname) = lower(clan) AND clan_long_name != '' AND confirmed = 1");
        echo "<p>Clans Available to Join</p>
        <p>To send a clan request click on that clan leader's name.</p>
        <ul>";
        foreach($clan_leaders as $leader){
            echo "<li><a href=\"clan.php?command=join&clan_name={$leader['clan']}&process=1\">
                    Join {$leader['clan_long_name']}</a>.
                    Its leader is <a href=\"player.php?player=".rawurlencode($leader['uname'])."\">
                    {$leader['uname']}</a>, level {$leader['level']}.
                    <a href=\"clan.php?command=view&clan_name={$leader['clan']}\">View This Clan</a>
                </li>\n";
        }
        echo "</ul>";
    }
}

// Functions for creating the clan ranking tag cloud.

/**
 * This determines how the clans get ranked and tagged, and how to only show non-empty clans.
**/
function clan_size()
{
	$res = array();
	$db = new DBAccess();

    // sum the levels of the players (minus days of inactivity) for each clan
	$sel = "select sum(level-3-round(days/5)) as sum, clan_name, clan_id from clan JOIN clan_player ON clan_id = _clan_id JOIN players ON _player_id = player_id where confirmed = 1 group by clan_id, clan_name order by sum desc";

	$counts = $db->FetchAll($sel);
	$largest = reset($counts);
	$max = $largest['sum'];

	foreach ($counts as $clan_info)
	{	// *** make percentage of highest, multiply by 10 and round to give a 1-10 size ***
		$res[$clan_info['clan_id']]['name'] = $clan_info['clan_name'];
		$res[$clan_info['clan_id']]['score'] = floor(( (($clan_info['sum'] - 1 < 1 ? 0 : $clan_info['sum'] - 1)) / $max) * 10) + 1;
	}

	return $res;
}

function render_clan_tags()
{
	$clans = clan_size();
	//$clans = @natsort2d($clans, 'level');
	$res = "<div id='clan-tags'>
                <h4 id='clan-tags-title'>
                    All Clans
                </h4>
            <ul>";

	foreach ($clans as $clan_id => $data)
	{
		$res .= "<li class='clan-tag size".$data['score']."'>
                <a href='?command=view&amp;clan_id=".urlencode($clan_id)."'>".$data['name']."</a>
            </li>";
	}

	$res .= "</ul>
            </div>";
	return $res;
}

// Not exactly just tags, but good enough for me.

/* Display the clan member name list or tag list */
function render_clan_view($p_clanID, $sql)
{
	if (!$p_clanID)
	{
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

	foreach ($members as $member)
	{
		$member['size'] = floor( ( ( ($member['level'] - $member['days'] < 1 ? 0 : $member['level'] - $member['days']) ) / $max) * 2) + 1;

		if ($member['player_id'] == $member['_creator_player_id'])
		{
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
