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

$level = getLevel($username);
$class = getClass($username);

$status_output_list = render_status_section();
$no_skills = true;
$stealth   = $skillsListObj->hasSkill('Stealth');

if ($stealth) {
	$no_skills = false;
}

$stealth_turn_cost   = $skillsListObj->getTurnCost('Stealth');
$unstealth_turn_cost = $skillsListObj->getTurnCost('Unstealth');
$kampo_turn_cost     = $skillsListObj->getTurnCost('Kampo');
$chi                 = $skillsListObj->hasSkill('Chi');
$speed               = $skillsListObj->hasSkill('Chi');
$hidden_resurrect    = $skillsListObj->hasSkill('hidden resurrect');
$midnight_heal       = $skillsListObj->hasSkill('midnight heal');
$kampo               = $skillsListObj->hasSkill('kampo');
// TODO:  Midnight Heal currently isn't in play, needs fixing in the deity_nightly script.

display_page(
	'skills.tpl'	// *** Main Template ***
	, 'Your Skills'	// *** Page Title ***
	, get_certain_vars(get_defined_vars(), array())	// *** Page Variables ***
	, array(	// *** Page Options ***
		'quickstat' => 'player'
	)
); 
}
?>
