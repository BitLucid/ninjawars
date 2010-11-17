<?php
$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
require_once(LIB_ROOT."specific/lib_status.php"); // statuses for quickstats
require_once(LIB_ROOT."specific/lib_player.php"); // Player info display pieces.
include(OBJ_ROOT."Skill.php");

// TODO: Consider more skills along the lines of: disguise, escape, concealment, archery, medicine, explosives, and poisons.
// TODO: Also consider "packageable" classes.

$skillsListObj = new Skill();

$player = new Player(get_char_id());
$level = $player->vo->level;
$class = getClass($username); // *** double-check player values before replacing this line ***

$status_list = get_status_list();
$no_skills = true;
$stealth   = $skillsListObj->hasSkill('Stealth');

if ($stealth) {
	$no_skills = false;
}

$stealth_turn_cost   = $skillsListObj->getTurnCost('Stealth');
$unstealth_turn_cost = $skillsListObj->getTurnCost('Unstealth');
$chi                 = $skillsListObj->hasSkill('Chi');
$speed               = $skillsListObj->hasSkill('speed');
$hidden_resurrect    = $skillsListObj->hasSkill('hidden resurrect');
$midnight_heal       = $skillsListObj->hasSkill('midnight heal');
$kampo_turn_cost     = $skillsListObj->getTurnCost('Kampo');
$kampo               = $skillsListObj->hasSkill('kampo');
$heal             = $skillsListObj->hasSkill('heal');
$heal_turn_cost     = $skillsListObj->getTurnCost('heal');

display_page(
	'skills.tpl'	// *** Main Template ***
	, 'Your Skills'	// *** Page Title ***
	, get_certain_vars(get_defined_vars(), array('status_list'))	// *** Page Variables ***
	, array(	// *** Page Options ***
		'quickstat' => 'player'
	)
); 
}
?>
