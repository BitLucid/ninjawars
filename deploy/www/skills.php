<?php
$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
require_once(LIB_ROOT."control/lib_status.php"); // statuses for quickstats
require_once(LIB_ROOT."control/lib_player.php"); // Player info display pieces.
require_once(LIB_ROOT."control/Skill.php");

$skillsListObj = new Skill();

$player = new Player(get_char_id());
$level = $player->level();
$class = $player->class_display_name(); // Just to be displayed in the template.
$starting_turns = $player->turns();

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
$clone_kill 		= $skillsListObj->hasSkill('clone kill');
$clone_kill_turn_cost = $skillsListObj->getTurnCost('clone kill');


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
