<?php
// File for creating the clan ranking tag cloud.

/**
 * This determines how the clans get ranked and tagged, and how to only show non-empty clans.
**/
function clan_size()
{
	$res = array();
	$db = new DBAccess();

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
