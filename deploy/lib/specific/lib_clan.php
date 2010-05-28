<?php
// See also the older "clan functions" in commands.php
require_once(SERVER_ROOT."lib/specific/lib_player.php");


// Without checking for pre-existing clan and other errors, adds a player into a clan.
function add_player_to_clan($player_id, $clan_id){
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO clan_player (_clan_id, _player_id) VALUES (:clan, :player_id)");
	$statement->bindValue(':clan', $clan_id);
	$statement->bindValue(':player_id', $player_id);
	$statement->execute();
	// Add the player into the clan.

    $random      = rand(1001, 9990); // Semi-random confirmation number, and change the players confirmation number.
	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET confirm = :confirm WHERE player_id = :player_id");
	$statement->bindValue(':confirm', $random);
	$statement->bindValue(':player_id', $player_id);
	$statement->execute();
}


// Show the form for the clan joining, or perform the join.
function render_clan_join($process=null, $username, $clan_id) {
	DatabaseConnection::getInstance();

	if ($process == 1) {
		$clan        = get_clan($clan_id);
		$leader      = get_clan_leader_info($clan_id);
		$leader_id   = $leader['player_id'];
		$leader_name = $leader['uname'];

		$confirmStatement = DatabaseConnection::$pdo->prepare("SELECT confirm FROM players WHERE uname = :user");
		$confirmStatement->bindValue(':user', $username);
		$confirmStatement->execute();
		$confirm = $confirmStatement->fetchColumn();
		
		// These ampersands get encoded later.
		$url = message_url("clan_confirm.php?clan_joiner=".get_user_id($username)."&agree=1&confirm=$confirm&clan_id=".urlencode($clan_id), 'Confirm Request');

		$join_request_message = "CLAN JOIN REQUEST: ".htmlentities($username)." has sent a request to join your clan.
			If you wish to allow this ninja into your clan click the following link:
			$url";
		send_message(get_user_id($username), $leader_id, $join_request_message);

		$res =  "<div id='clan-join-request-sent' class='ninja-notice'>Your request to join ".htmlentities($clan['clan_name'])." 
			has been sent to ".htmlentities($leader_name)."</div>\n";
	} else {                                            
		//Clan Join list of available Clans
		$leaders = get_clan_leaders(($clan_id ? $clan_id : null), ($clan_id ? false : true));
		$res = "<h2>Clans Available to Join</h2>
		<ul>";

		foreach ($leaders as $leader) {
			$info = get_player_info($leader['player_id']);
			$res .= "<li><a target='main' class='clan-join' href=\"clan.php?command=join&amp;clan_id={$leader['clan_id']}&amp;process=1\">
					Join ".htmlentities($leader['clan_name'])."</a>.
					Its leader is <a href=\"player.php?player_id=".rawurlencode($leader['player_id'])."\">
					".htmlentities($leader['uname'])."</a>, level {$info['level']}.
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
	DatabaseConnection::getInstance();
	$clan_or_clans = ($clan_id ? 'WHERE clan_id = :clan' : 'ORDER BY clan_id');
	$clans = DatabaseConnection::$pdo->prepare("SELECT clan_id, clan_name, clan_created_date, clan_founder FROM clan $clan_or_clans");
	if ($clan_id) {
		$clans->bindValue(':clan', $clan_id);
	}

	$clans->execute();

	return $clans->fetchAll();
}

// Gets the clan founder, though they may be dead and unconfirmed now.
function get_clan_founders() {
	DatabaseConnection::getInstance();

	$founders_statement = DatabaseConnection::$pdo->query("SELECT clan_founder, clan_name, uname, player_id, confirmed 
		FROM clan LEFT JOIN players ON lower(clan_founder) = lower(uname)");
	return $founder_statement->fetchAll();
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
	$clan_or_clans = ($clan_id ? " AND clan_id = :clan ORDER BY level " : ' ORDER BY clan_id, level ');
	DatabaseConnection::getInstance();
	$clans = DatabaseConnection::$pdo->prepare("SELECT clan_id, clan_name, clan_founder, player_id, uname 
		FROM clan JOIN clan_player ON clan_id = _clan_id JOIN players ON player_id=_player_id 
		WHERE confirmed = 1 AND member_level > 0 $clan_or_clans $limit");

	if ($clan_id) {
		$clans->bindValue(':clan', $clan_id);
	}

	$clans->execute();

	return $clans->fetchAll();
}

// Functions for creating the clan ranking tag cloud.

/**
 * This determines the criterial for how the clans get ranked and tagged, 
 *   and shows only non-empty clans.
**/
function clan_size() {
	$res = array();

	// sum the levels of the players (minus days of inactivity) for each clan
	$counts = query("SELECT sum(round(((level+4)/5+8)-(days/3))) AS sum, clan_name, clan_id FROM clan JOIN clan_player ON clan_id = _clan_id JOIN players ON _player_id = player_id WHERE confirmed = 1 GROUP BY clan_id, clan_name ORDER BY sum DESC");

	if (!empty($counts)) {
		$largest = reset($counts);
		$max     = $largest['sum'];

		foreach ($counts as $clan_info) {
			// *** make percentage of highest, multiply by 10 and round to give a 1-10 size ***
			$res[$clan_info['clan_id']]['name'] = $clan_info['clan_name'];
			$res[$clan_info['clan_id']]['score'] = floor(( (($clan_info['sum'] - 1 < 1 ? 0 : $clan_info['sum'] - 1)) / $max) * 10) + 1;
		}
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
				<a href='clan.php?command=view&amp;clan_id=".urlencode($clan_id)."'>".htmlentities($data['name'])."</a>
			</li>";
	}

	$res .= "</ul>
			</div>";

	return $res;
}

/* Display the clan member name list or tag list */
function render_clan_view($p_clan_id) {
	if (!$p_clan_id) {
		return ''; // No viewing criteria available.
	}

    $members_resultset = query_resultset("SELECT uname, email, clan_name, level, days, clan_founder, player_id, member_level
			FROM clan
			JOIN clan_player ON _clan_id = :clan_id AND clan_id = _clan_id
			JOIN players ON player_id = _player_id AND confirmed = 1 ORDER BY health, level DESC", 
			array(':clan_id'=>$p_clan_id));

	
	$max = query_item("SELECT max(level) AS max 
		FROM clan
		JOIN clan_player ON _clan_id = :clan_id AND clan_id = _clan_id
		JOIN players ON player_id = _player_id AND confirmed = 1", 
		array(":clan_id"=>$p_clan_id));
	
	$clan = get_clan($p_clan_id); // Clan array.
	$clan_name = $clan['clan_name'];

	$res = "<div id='clan-members'>
			<h3 id='clan-members-title'>".htmlentities($clan_name)."</h3>
			<ul id='clan-members-list'>";
    $count = 0;
	foreach ($members_resultset as $member) {
		$member['size'] = floor( ( ($member['level'] - $member['days'] < 1 ? 0 : $member['level'] - $member['days']) / $max) * 2) + 1;

        $current_leader_class = null;
        // Calc the member display size based on their level relative to the max.
		if ($member['member_level'] == 1) {
		    $current_leader_class = 'original-creator';
			$member['size'] = $member['size'] + 2;
			$member['size'] = ($member['size'] > 2 ? 2 : $member['size']);
		}

		$res .= "<li class='member-info'>
                <a href='player.php?player=".urlencode($member['uname'])."'>
				<span class='member size{$member['size']} {$current_leader_class}'>".
				htmlentities($member['uname']).
                "</span>";
		$res .= render_template('gravatar.tpl', array('url' => generate_gravatar_url($member['player_id'])));
		$res .= "</a>";
		$res .= "</li>";
        $count++;
	}

	$res .= "</ul>
			</div>
			<div id='clan-members-count' style='clear:both;margin-top:1em;'>Clan Members: ".$count."</div>";

	return $res;
}

?>
