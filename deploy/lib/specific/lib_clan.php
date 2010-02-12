<?php
// See also the older "clan functions" in commands.php
require_once(SERVER_ROOT."lib/specific/lib_player.php");

// Show the form for the clan joining, or perform the join.
function render_clan_join($process=null, $username, $clan_id) {
   	$sql = new DBAccess();

    if ($process == 1) {
        $clan        = get_clan($clan_id);
        $leader      = get_clan_leader_info($clan_id);
        $leader_id   = $leader['player_id'];
        $leader_name = $leader['uname'];

        $confirm = $sql->QueryItem("SELECT confirm FROM players WHERE uname = '$username'");

        $url = message_url("clan_confirm.php?clan_joiner=".get_user_id($username)."&confirm=$confirm&clan_id=".urlencode($clan_id), 'Confirm Request');

        $join_request_message = "CLAN JOIN REQUEST: $username has sent a request to join clan ".out($clan['clan_name']).".
            If you wish to allow this ninja into your clan click the following link:
            $url";
        send_message(get_user_id($username),$leader_id,$join_request_message);

        $res =  "<div id='clan-join-request-sent' class='ninja-notice'>Your request to join ".out($clan['clan_name'])." 
            has been sent to ".out($leader_name)."</div>\n";
    } else {                                            
        //Clan Join list of available Clans
        $leaders = get_clan_leaders(($clan_id ? $clan_id : null), ($clan_id ? false : true));
        $res = "<h2>Clans Available to Join</h2>
        <ul>";

        foreach ($leaders as $leader) {
            $info = get_player_info($leader['player_id']);
            $res .= "<li><a target='main' class='clan-join' href=\"clan.php?command=join&amp;clan_id={$leader['clan_id']}&amp;process=1\">
                    Join {$leader['clan_name']}</a>.
                    Its leader is <a href=\"player.php?player_id=".rawurlencode($leader['player_id'])."\">
                    {$leader['uname']}</a>, level {$info['level']}.
                    <a target='main' href=\"clan.php?command=view&amp;clan_id={$leader['clan_id']}\">View This Clan</a>
                </li>\n";
        }

        $res .= "</ul>";
    }

    return $res;
}

// Wrapper for getting a single clan's info.
function get_clan($clan_id) {
    $clans = get_clans($clan_id);
    return reset($clans);
}

// Just get the list of clans, plus the creator ids just for reference.
function get_clans($clan_id=null) {
    $clan_or_clans = ($clan_id ? 'WHERE clan_id = '.sql($clan_id) : 'ORDER BY clan_id');
    $sql = new DBAccess();
    $clans = $sql->fetchData("SELECT clan_id, clan_name, clan_created_date, _creator_player_id FROM clan $clan_or_clans");
    return $clans;
}

// Gets the clan founders, though they may be dead and unconfirmed now.
function get_clan_founders() {
    $sql = new DBAccess();
    $clan_founders = $sql->fetchData("SELECT uname, player_id, clan_name, confirmed 
        FROM clan JOIN clan_player ON clan_id = _clan_id JOIN player ON _creator_player_id = player_id AND player_id = _player_id
        ORDER BY clan_id");
    return $clan_founders;
}

// Return only the single clan leader and their information.
function get_clan_leader_info($clan_id) {
    $clans = get_clan_leaders($clan_id);
    return reset($clans);
}

// Return just the clan leader id for a clan.
function get_clan_leader_id($clan_id) {
    $leader_info = get_clan_leader_info($clan_id);
    return $leader_info['player_id'];
}

// Get the current clan leader or leaders.
function get_clan_leaders($clan_id=null, $all=false) {
    $limit = ($all ? '' : ' LIMIT 1 ');
    $clan_or_clans = ($clan_id ? " AND clan_id = ".sql($clan_id)." ORDER BY level " : ' ORDER BY clan_id, level ');
    $sql = new DBAccess();
    $clans = $sql->fetchData("SELECT clan_id, clan_name, _creator_player_id, player_id, uname 
        FROM clan JOIN clan_player ON clan_id = _clan_id JOIN players ON player_id=_player_id 
        WHERE confirmed = 1 AND member_level > 0 $clan_or_clans $limit");

    return $clans;
}

// Functions for creating the clan ranking tag cloud.

/**
 * This determines the criterial for how the clans get ranked and tagged, 
 *   and shows only non-empty clans.
**/
function clan_size() {
	$res = array();
	$db = new DBAccess();

    // sum the levels of the players (minus days of inactivity) for each clan
	$sel = "SELECT sum(level-3-round(days/5)) AS sum, clan_name, clan_id FROM clan JOIN clan_player ON clan_id = _clan_id JOIN players ON _player_id = player_id WHERE confirmed = 1 GROUP BY clan_id, clan_name ORDER BY sum DESC";

	$counts = $db->FetchAll($sel);
	$largest = reset($counts);
	$max = $largest['sum'];

	foreach ($counts as $clan_info) {
		// *** make percentage of highest, multiply by 10 and round to give a 1-10 size ***
		$res[$clan_info['clan_id']]['name'] = $clan_info['clan_name'];
		$res[$clan_info['clan_id']]['score'] = floor(( (($clan_info['sum'] - 1 < 1 ? 0 : $clan_info['sum'] - 1)) / $max) * 10) + 1;
	}

	return $res;
}

// Display the tag list of clans/clan links.
function render_clan_tags() {
	$clans = clan_size();
	//$clans = @natsort2d($clans, 'level');
	$res = "<div id='clan-tags'>
                <h4 id='clan-tags-title'>
                    All Clans
                </h4>
            <ul>";

	foreach ($clans as $clan_id => $data) {
		$res .= "<li class='clan-tag size".$data['score']."'>
                <a href='clan.php?command=view&amp;clan_id=".urlencode($clan_id)."'>".$data['name']."</a>
            </li>";
	}

	$res .= "</ul>
            </div>";

	return $res;
}

/* Display the clan member name list or tag list */
function render_clan_view($p_clan_id, $sql) {
	if (!$p_clan_id) {
		return ''; // No viewing criteria available.
	}

	$members = $sql->FetchAll (
        "SELECT uname, email, clan_name, level, days, _creator_player_id, player_id
            FROM clan
            JOIN clan_player ON _clan_id = $p_clan_id AND clan_id = _clan_id
            JOIN players ON player_id = _player_id AND confirmed = 1 ORDER BY health, level DESC");

	$max_list = $sql->FetchAll(
        "SELECT max(level) AS max 
        FROM clan
        JOIN clan_player ON _clan_id = $p_clan_id AND clan_id = _clan_id
        JOIN players ON player_id = _player_id AND confirmed = 1");

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

		$res .= "<li class='member-info'>
		        <span class='member size{$member['size']}'>
                <a href='player.php?player={$member['uname']}'>{$member['uname']}</a>
		        </span>";
        $res .= render_avatar_section_from_email($member['email']);
        $res .= "</li>";
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
