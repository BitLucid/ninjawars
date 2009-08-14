<?php
ob_start(null, 1); // File buffer output in chunks.
// General utility objects.
$filter = new Filter(); // *** Creates the filters for later use.
$sql = new DBAccess();
$section_only = in('section_only'); // Check whether it's an ajax section.

// ******************** Declared variables *****************************
$today = date("F j, Y, g:i a");  // Today var is only used for creating mails.

// Page viewing settings usually set before the header.
$private 	= (isset($private)? $private : NULL);
$quickstat 	= (isset($quickstat)? $quickstat : NULL);
$alive 		= (isset($alive)? $alive : NULL);
$page_title = (isset($page_title)? $page_title : "NinjaWars");
$error = null; // Logged in or alive error.

update_activity_info(); // *** Updates the activity of the page viewer in the database.

if(!is_logged_in()){
	if ($private) {
		$error = "<span class='notice'>You must log in to view this section.</span>";
		// Error triggers a die at the end of the header.
    }
} else { 
	// **************** Player information settings. *******************
	$username         = SESSION::get('username');
	
	$player = new Player($username); // Defaults to current session user.
		
	$players_id = $player->player_id;
	$player_id = $players_id; // Just two aliases for the player id.
	$players_email = $player->vo->email;

	// TODO: Turn this into a list extraction?
	// password and messages intentionally excluded.
	$players_turns    	= $player->vo->turns;
	$players_health   	= $player->vo->health;
	$players_bounty   	= $player->vo->bounty;
	$players_gold     	= $player->vo->gold;
	$players_level    	= $player->vo->level;
	$players_class    	= $player->vo->class;
	$players_strength 	= $player->vo->strength;
	$players_kills		= $player->vo->kills;

	$players_days		= $player->vo->days;
	$players_created_date = $player->vo->created_date;
	$players_last_started_attack = $player->vo->last_started_attack;
	$players_clan 		= $player->vo->clan_long_name;

	// TODO: not ready yet: $players_energy	= $player_data['energy'];
	// Also migrate the player_score to a true player object.
	// Also migrate the rank_id to a true player object.

	$players_status   = $player->getStatus();

	assert('isset($players_id) && isset($players_email) && isset($players_turns) && isset($players_health)
	&& isset($players_bounty) && isset($players_gold) && isset($players_level) && isset($players_class)
	&& isset($players_strength) && isset($players_kills) && isset($players_days) && isset($players_created_date)
	&& isset($players_last_started_attack) && isset($players_clan) && isset($players_status)');

	$error = render_error_if_dead($alive, $players_health, $players_status);
	// From lib_header.
}

if(!$section_only){
    write_html_for_header(); // ***** Display the html header
}

if ($error)
{
	echo $error;
	die();
}
?>
