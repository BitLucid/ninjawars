<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT."control/lib_status.php"); // statuses for quickstats
require_once(LIB_ROOT."control/lib_player.php"); // Player info display pieces.
require_once(LIB_ROOT."control/Skill.php");


use \Player;
use \Skill;

/**
 * Handles both skill listing and displaying, and their usage
 */
class SkillController {
	const ALIVE = true;
	const PRIV  = true;

	/**
	 * Initialize with any external state if necessary
	 *
	 */
	public function __construct() {
		$this->player = Player::find(self_char_id());

	}

	/**
	 * Display the initial listing of skills for self-use
	 *
	 * @return Array
	 */
	public function index() {

		$skillsListObj = new Skill();

		$player = $this->player;
		$level = $player->level();
		$class = $player->class_display_name(); // Just to be displayed in the template.
		$starting_turns = $player->turns();
		$starting_ki = $player->ki();

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
		$wrath	= $skillsListObj->hasSkill('wrath');
		$can_harmonize			= $starting_ki;

		$parts = [
			'status_list'=>$status_list,
			'player'=>$player,
			'no_skills'=>$no_skills,
			'starting_turns'=>$starting_turns,
			'starting_ki'=>$starting_ki,
			'stealth'=>$stealth,
			'stealth_turn_cost'=>$stealth_turn_cost,
			'unstealth_turn_cost'=>$unstealth_turn_cost,
			'chi'=>$chi,
			'speed'=>$speed,
			'hidden_resurrect'=>$hidden_resurrect,
			'midnight_heal'=>$midnight_heal,
			'kampo_turn_cost'=>$kampo_turn_cost,
			'kampo'=>$kampo,
			'heal'=>$heal,
			'heal_turn_cost'=>$heal_turn_cost,
			'clone_kill'=>$clone_kill,
			'clone_kill_turn_cost'=>$clone_kill_turn_cost,
			'wrath'=>$wrath,
			'can_harmonize'=>$can_harmonize,
		];

		return [
			'title'=>'Your Skills',
			'template'=>'skills.tpl',
			'parts'=>$parts,
			'options'=>[
				'quickstat'=>'player'
				],
			];




	}

}

