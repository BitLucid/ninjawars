<?php
require_once(LIB_ROOT.'control/lib_player.php'); // Player info display pieces.
require_once(LIB_ROOT.'control/lib_status.php'); // Status alterations.

$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
	die();
}

$changedetails = in('changedetails');
$newprofile    = trim(in('newprofile', null, null)); // Unfiltered input.
$description = in('description');
$goals = in('goals');
$instincts = in('instincts');
$beliefs = in('beliefs');
$traits = in('traits');

$dev = (bool) DEBUG;


// Update a player's OOC profile
function update_profile($char_id, $new_profile){
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare('UPDATE players SET messages = :profile WHERE player_id = :char_id');
	$statement->bindValue(':profile', $new_profile);
	$statement->bindValue(':char_id', $char_id);
	$statement->execute();
	return true;
}


$username = self_name();
$char_id  = self_char_id();

// Password and email changing systems exist in account.php (& account.tpl).

$char         = new Player($char_id);

$profile_changed    = false;
$profile_max_length = 500; // Should match the limit in limitStatChars.js - ajv: No, limitStatChars.js should be dynamically generated with this number from a common location -

$successMessage = null;
if ($changedetails == 1) {
    // Limit the profile length.
	if ($newprofile != '') {
		$profile_changed = update_profile($char_id, $newprofile);
	} else {
		$error = 'Cannot enter a blank profile.';
	}
}

if($changedetails){
	$changed = false;
	// Check that the text features don't differ
	foreach(array('description', 'goals', 'instincts', 'beliefs', 'traits') as $type){
		if($$type && isset($char->vo)){
			$char->vo->$type = $$type; // Set the various values.
			$changed = true;
		}
		$$type = $char->vo->$type; // Default to current values.
	}

	/*
	//TODO: Implement player object saving.
	if($changed){
		$player_factory = new PlayerDAO();
		$player_factory->save($char->vo);
	}*/
}
if(DEBUG){
	$description = 'This is a description here and all';
	$goals = 'Kill ninja of the ramen clan';
	$beliefs = 'I believe in a one true ninja god';
	$instincts = 'When I hear whistling, I duck';
	$traits = 'Hardy, nervous, meaty, silent';
}
$player           = self_info();
//$player['created_date']=$player['created_date']? date("c", strtotime($player['created_date'])) : null;
$class_theme      = class_theme($char->class_identity());
$level_category   = level_category($player['level']);
$status_list      = get_status_list();
$gravatar_url     = generate_gravatar_url($player['player_id']);
$gurl             = $gravatar_url;
$rank_display     = get_rank($char_id); // rank display.
$profile_editable = $player['messages'];

$parts = get_certain_vars(get_defined_vars(), array('player', 'level_category', 'status_list', 'description', 'goals', 'beliefs', 'instincts', 'traits', 'dev'));

// Set the parts array's player clan if any is found.
if ($parts['player_clan'] = get_clan_by_player_id($char_id)) {
    // Set the char clan name and id for later usage.
	$parts['clan_name'] = $parts['player_clan']->getName();
	$parts['clan_id']   = $parts['player_clan']->getID();
}

display_page(
	'stats.tpl'
	, 'Ninja Stats'
	, $parts
	, array(
		'quickstat' => 'player'
	)
);