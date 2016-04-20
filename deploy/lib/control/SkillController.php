<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\Filter;
use NinjaWars\core\data\Skill;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\Event;
use NinjaWars\core\data\CloneKill;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;

/**
 * Handles both skill listing and displaying, and their usage
 */
class SkillController extends AbstractController {
	const ALIVE = true;
	const PRIV  = true;

	const MIN_POISON_TOUCH = 1;

	public function maxPoisonTouch(){
		return (int)floor(2/3*Player::maxHealthByLevel(1));
	}

	public function fireBoltBaseDamage(Player $pc){
		return (int) (floor(Player::maxHealthByLevel($pc->level) / 3));
	}

	/**
	 * Technically max -additional damage off the base
	 */
	public function fireBoltMaxDamage(Player $pc){
		return (int) $pc->getStrength();
	}

	public function maxHarmonize(Player $pc){
		return $pc->getMaxHealth();
	}

	/**
	 * Initialize with any external state if necessary
	 *
	 */
	public function __construct() {
		$this->player = Player::find(SessionFactory::getSession()->get('player_id'));
	}

	/**
	 * Display the initial listing of skills for self-use
	 *
	 * @return Array
	 */
	public function index() {
		$error = in('error');
		$skillsListObj = new Skill();

		$player         = $this->player;
		$starting_turns = $player->turns;
		$starting_ki    = $player->ki;

		$status_list = Player::getStatusList();
		$no_skills   = true;
		$stealth     = $skillsListObj->hasSkill('Stealth', $player);

		if ($stealth) {
			$no_skills = false;
		}

		$stealth_turn_cost    = $skillsListObj->getTurnCost('Stealth');
		$unstealth_turn_cost  = $skillsListObj->getTurnCost('Unstealth');
		$chi                  = $skillsListObj->hasSkill('Chi', $player);
		$speed                = $skillsListObj->hasSkill('speed', $player);
		$hidden_resurrect     = $skillsListObj->hasSkill('hidden resurrect', $player);
		$midnight_heal        = $skillsListObj->hasSkill('midnight heal', $player);
		$kampo_turn_cost      = $skillsListObj->getTurnCost('Kampo');
		$kampo                = $skillsListObj->hasSkill('kampo', $player);
		$heal                 = $skillsListObj->hasSkill('heal', $player);
		$heal_turn_cost       = $skillsListObj->getTurnCost('heal');
		$clone_kill           = $skillsListObj->hasSkill('clone kill', $player);
		$clone_kill_turn_cost = $skillsListObj->getTurnCost('clone kill');
		$wrath                = $skillsListObj->hasSkill('wrath', $player);
		$can_harmonize        = $starting_ki;

		$parts = [
			'error'                => $error,
			'status_list'          => $status_list,
			'player'               => $player,
			'no_skills'            => $no_skills,
			'starting_turns'       => $starting_turns,
			'starting_ki'          => $starting_ki,
			'stealth'              => $stealth,
			'stealth_turn_cost'    => $stealth_turn_cost,
			'unstealth_turn_cost'  => $unstealth_turn_cost,
			'chi'                  => $chi,
			'speed'                => $speed,
			'hidden_resurrect'     => $hidden_resurrect,
			'midnight_heal'        => $midnight_heal,
			'kampo_turn_cost'      => $kampo_turn_cost,
			'kampo'                => $kampo,
			'heal'                 => $heal,
			'heal_turn_cost'       => $heal_turn_cost,
			'clone_kill'           => $clone_kill,
			'clone_kill_turn_cost' => $clone_kill_turn_cost,
			'wrath'                => $wrath,
			'can_harmonize'        => $can_harmonize,
		];

		return new StreamedViewResponse('Your Skills', 'skills.tpl', $parts, ['quickstat'=>'player']);
	}

	public function postUse() {
        $target = RequestWrapper::getPost('target');
        $target2 = RequestWrapper::getPost('target2');
        $act = RequestWrapper::getPost('act');
        $url = 'skill/use/'.rawurlencode($act).'/'.rawurlencode($target).'/'.($target2? rawurlencode($target2).'/' : '');

        // TODO: Need to double check that this doesn't allow for redirect injection
        return new RedirectResponse(WEB_ROOT.$url);
	}

    public function postSelfUse() {
        $act = RequestWrapper::getPost('act');
        $url = 'skill/self_use/'.rawurlencode($act).'/';

        // TODO: Need to double check that this doesn't allow for redirect injection
        return new RedirectResponse(WEB_ROOT.$url);
    }

	/**
	 * Get the slugs from the path, eventually should be a controller standard.
	 */
	private function parseSlugs($path){
		$slugs = explode('/', trim(urldecode($path), '/'));
		array_unshift($slugs, $path); // First element is full path
		return $slugs;
	}

	/**
	 * Use an item only on self
	 */
	public function selfUse(){
		return $this->useSkill(true);
	}

	/**
	 * Use, the skills_mod equivalent
     *
	 * @note Test with urls like:
	 * http://nw.local/skill/use/Fire%20Bolt/10
	 * http://nw.local/skill/self_use/Unstealth/
	 * http://nw.local/skill/self_use/Heal/
	 */
	public function useSkill($self_use=false) {
		// Template vars.
		$display_sight_table = $generic_skill_result_message = $generic_state_change = $killed_target =
			$loot = $added_bounty = $bounty = $suicided = $destealthed = null;
		$error = null;

		$char_id = SessionFactory::getSession()->get('player_id');
		$player  = Player::find($char_id);
        $path    = RequestWrapper::getPathInfo();
        $slugs   = $this->parseSlugs($path);
        // (fullpath0) /skill1/use2/Fire%20Bolt3/tchalvak4/(beagle5/)
        $act     = (isset($slugs[3]) ? $slugs[3] : null);
        $target  = (isset($slugs[4]) ? $slugs[4] : null);
        $target2 = (isset($slugs[5]) ? $slugs[5] : null);

		if (!Filter::toNonNegativeInt($target)) {
			if ($self_use) {
				$target = $char_id;
			} else {
				if ($target !== null) {
					$targetObj = Player::findByName($target);
					$target = ($targetObj ? $targetObj->id() : null);
				} else {
					$target = null;
				}
			}
		}

		if ($target2 && !Filter::toNonNegativeInt($target2)) {
			$target2Obj = Player::findByName($target2);
			$target2 = ($target2Obj ? $target2Obj->id() : null);
		}

		$skillListObj    = new Skill();
		// *** Before level-based addition.
		$poisonTurnCost  = $skillListObj->getTurnCost('poison touch'); // wut
		$turn_cost       = $skillListObj->getTurnCost(strtolower($act));
		$ignores_stealth = $skillListObj->getIgnoreStealth($act);
		$self_usable     = $skillListObj->getSelfUse($act);
		$use_on_target   = $skillListObj->getUsableOnTarget($act);
		$ki_cost 		 = 0; // Ki taken during use.
		$reuse 			 = true;  // Able to reuse the skill.
		$today           = date("F j, Y, g:i a");

		// Check whether the user actually has the needed skill.
		$has_skill = $skillListObj->hasSkill($act, $player);

		$starting_turn_cost = $turn_cost;
		assert($turn_cost>=0);
		$turns_to_take = null;  // *** Even on failure take at least one turn.

		if ($self_use) {
			// Use the skill on himself.
			$return_to_target = false;
			$target    = $player;
			$target_id = null;
		} else if ($target != '' && $target != $player->player_id) {
			$target = Player::find($target);
			$target_id = $target->id();
			$return_to_target = true;
		} else {
			// For target that doesn't exist, e.g. http://nw.local/skill/use/Sight/zigzlklkj
			error_log('Info: Attempt to use a skill on a target that did not exist.');
			return new RedirectResponse(WEB_ROOT.'skill/?error='.rawurlencode('Invalid target for skill ['.rawurlencode($act).'].'));
		}

		$covert           = false;
		$victim_alive     = true;
		$attacker_id      = $player->name();
		$attacker_char_id = $player->id();
		$starting_turns   = $player->vo->turns;

		$level_check  = $player->vo->level - $target->vo->level;

		if ($player->hasStatus(STEALTH)) {
			$attacker_id = 'A Stealthed Ninja';
		}

		$use_attack_legal = true;

		if ($act == 'Clone Kill' || $act == 'Harmonize') {
			$has_skill        = true;
			$use_attack_legal = false;
			$attack_allowed   = true;
			$attack_error     = null;
			$covert           = true;
		} else {
			// *** Checks the skill use legality, as long as the target isn't self.
		    $params = [
		        'required_turns'  => $turn_cost,
		        'ignores_stealth' => $ignores_stealth,
		        'self_use'        => $self_use
		    ];

			$AttackLegal    = new AttackLegal($player, $target, $params);
			$attack_allowed = $AttackLegal->check();
			$attack_error   = $AttackLegal->getError();
		}

		if (!$attack_error) { // Only bother to check for other errors if there aren't some already.
			if (!$has_skill || $act == '') {
				// Set the attack error to display that that skill wasn't available.
				$attack_error = 'You do not have the requested skill.';
			} elseif ($starting_turns < $turn_cost) {
				$turn_cost = 0;
				$attack_error = "You do not have enough turns to use $act.";
			}
        }

		if (!$attack_error) { // Nothing to prevent the attack from happening.
			// Initial attack conditions are alright.
			$result = '';

			if ($act == 'Sight') {
				$covert = true;

				$sight_data = $this->pullSightData($target);

				$display_sight_table = true;
			} elseif ($act == 'Steal') {
				$covert = true;

				$gold_decrease = min($target->gold, rand(5, 50));

				$player->set_gold($player->gold + $gold_decrease);
                $player->save();

                $target->set_gold($target->gold - $gold_decrease);
                $target->save();

				$msg = "$attacker_id stole $gold_decrease gold from you.";
				Event::create($attacker_char_id, $target->id(), $msg);

				$generic_skill_result_message = "You have stolen $gold_decrease gold from __TARGET__!";
			} else if ($act == 'Unstealth') {
				$state = 'unstealthed';

				if ($target->hasStatus(STEALTH)) {
					$target->subtractStatus(STEALTH);
					$generic_state_change = "You are now $state.";
				} else {
					$turn_cost = 0;
					$generic_state_change = "__TARGET__ is already $state.";
				}
			} else if ($act == 'Stealth') {
				$covert     = true;
				$state      = 'stealthed';

				if (!$target->hasStatus(STEALTH)) {
					$target->addStatus(STEALTH);
					$generic_state_change = "__TARGET__ is now $state.";
				} else {
					$turn_cost = 0;
					$generic_state_change = "__TARGET__ is already $state.";
				}
			} else if ($act == 'Kampo') {
				$covert = true;

				// *** Get Special Items From Inventory ***
				$user_id = $player->id();
				$root_item_type = 7;
                $itemCount = query_item(
                    'SELECT sum(amount) AS c FROM inventory WHERE owner = :owner AND item_type = :type GROUP BY item_type',
                    [':owner'=>$user_id, ':type'=>$root_item_type]
                );
		        $turn_cost = min($itemCount, $starting_turns-1, 2); // Costs 1 or two depending on the number of items.
				if ($turn_cost && $itemCount > 0) {	// *** If special item count > 0 ***
                    $inventory = new Inventory($player);
                    $inventory->remove('ginsengroot', $itemCount);
                    $inventory->add('tigersalve', $itemCount);

					$generic_skill_result_message = 'With intense focus you grind the '.$itemCount.' roots into potent formulas.';
				} else { // *** no special items, give error message ***
					$turn_cost = 0;
					$generic_skill_result_message = 'You do not have the necessary ginsengroots or energy to create any Kampo formulas.';
				}
			} else if ($act == 'Poison Touch') {
				$covert = true;

				$target->addStatus(POISON);
				$target->addStatus(WEAKENED); // Weakness kills strength.

				$target_damage = rand(self::MIN_POISON_TOUCH, $this->maxPoisonTouch());

				$victim_alive = $target->harm($target_damage);
				$generic_state_change = "__TARGET__ has been poisoned!";
				$generic_skill_result_message = "__TARGET__ has taken $target_damage damage!";

				$msg = "You have been poisoned by $attacker_id";
				Event::create($attacker_char_id, $target->id(), $msg);
			} elseif ($act == 'Fire Bolt') {
				$target_damage = $this->fireBoltBaseDamage($player) + rand(1, $this->fireBoltMaxDamage($player));


				$generic_skill_result_message = "__TARGET__ has taken $target_damage damage!";

				$victim_alive = $target->harm($target_damage);

				$msg = "You have had fire bolt cast on you by ".$player->name();
				Event::create($player->id(), $target->id(), $msg);
			} else if ($act == 'Heal' || $act == 'Harmonize') {
				// This is the starting template for self-use commands, eventually it'll be all refactored.
				$harmonize = false;

				if ($act == 'Harmonize') {
					$harmonize = true;
				}

			    $hurt = $target->is_hurt_by(); // Check how much the TARGET is hurt (not the originator, necessarily).
			    // Check that the target is not already status healing.
			    if ($target->hasStatus(HEALING) && !$player->isAdmin()) {
			        $turn_cost = 0;
			        $generic_state_change = '__TARGET__ is already under a healing aura.';
				} elseif ($hurt < 1) {
					$turn_cost = 0;
					$generic_skill_result_message = '__TARGET__ is already fully healed.';
				} else {
					if(!$harmonize){
						$original_health = $target->health;
						$heal_points = $player->stamina()+1;
						$new_health = $target->heal($heal_points); // Won't heal more than possible
						$healed_by = $new_health - $original_health;
					} else {
						$start_health = $player->health;
						// Harmonize those chakra!
						$player = $this->harmonizeChakra($player);
						$healed_by = $player->health - $start_health;
						$ki_cost = $healed_by;
					}

				    $target->addStatus(HEALING);
				    $generic_skill_result_message = "__TARGET__ healed by $healed_by to ".$target->health.".";

				    if ($target->id() != $player->id())  {
						Event::create($attacker_char_id, $target->id(), "You have been healed by $attacker_id for $healed_by.");
					}
				}
			} else if ($act == 'Ice Bolt') {
				if (!$target->hasStatus(SLOW)) {
					if ($target->vo->turns >= 10) {
						$turns_decrease = rand(1, 5);
						$target->changeTurns(-1*$turns_decrease);
						// Changed ice bolt to kill stealth.
						$target->subtractStatus(STEALTH);
						$target->addStatus(SLOW);

						$msg = "Ice bolt cast on you by $attacker_id, your turns have been reduced by $turns_decrease.";
						Event::create($attacker_char_id, $target->id(), $msg);

						$generic_skill_result_message = "__TARGET__'s turns reduced by $turns_decrease!";
					} else {
						$turn_cost = 0;
						$generic_skill_result_message = "__TARGET__ does not have enough turns for you to take.";
					}
				} else {
					$turn_cost = 0;
					$generic_skill_result_message = '__TARGET__ is already iced.';
				}
			} else if ($act == 'Cold Steal') {
				if (!$target->hasStatus(SLOW)) {
					$critical_failure = rand(1, 100);

					if ($critical_failure > 7) {// *** If the critical failure rate wasn't hit.
						if ($target->vo->turns >= 10) {
							$turns_decrease = rand(2, 7);

							$target->changeTurns(-1*$turns_decrease);
							$target->addStatus(SLOW);
							$player->changeTurns($turns_decrease);

							$msg = "You have had Cold Steal cast on you for $turns_decrease by $attacker_id";
							Event::create($attacker_char_id, $target->id(), $msg);

							$generic_skill_result_message = "You cast Cold Steal on __TARGET__ and take $turns_decrease turns.";
						} else {
							$turn_cost = 0;
							$generic_skill_result_message = '__TARGET__ did not have enough turns to give you.';
						}
					} else { // *** CRITICAL FAILURE !!
						$player->addStatus(FROZEN);

						$unfreeze_time = date('F j, Y, g:i a', mktime(date('G')+1, 0, 0, date('m'), date('d'), date('Y')));

						$failure_msg = "You have experienced a critical failure while using Cold Steal. You will be unfrozen on $unfreeze_time";
						Event::create((int)"SysMsg", $player->id(), $failure_msg);
						$generic_skill_result_message = "Cold Steal has backfired! You are frozen until $unfreeze_time!";
					}
				} else {
					$turn_cost = 0;
					$generic_skill_result_message = '__TARGET__ is already iced.';
				}
			} else if ($act == 'Clone Kill') {
				// Obliterates the turns and the health of similar accounts that get clone killed.
				$reuse = false; // Don't give a reuse link.

				$clone1 = Player::findByName($target);
				$clone2 = Player::findByName($target2);

				if (!$clone1 || !$clone2) {
					$not_a_ninja = $target;

					if (!$clone2) {
						$not_a_ninja = $target2;
					}

					$generic_skill_result_message = "There is no such ninja as $not_a_ninja.";
				} elseif ($clone1->id() == $clone2->id()) {
					$generic_skill_result_message = '__TARGET__ is just the same ninja, so not the same thing as a clone at all.';
				} elseif ($clone1->id() == $char_id || $clone2->id() == $char_id) {
					$generic_skill_result_message = 'You cannot clone kill yourself.';
				} else {
					// The two potential clones will be obliterated immediately if the criteria are met in CloneKill.
					$kill_or_fail = CloneKill::kill($player, $clone1, $clone2);

					if ($kill_or_fail !== false) {
						$generic_skill_result_message = $kill_or_fail;
					} else {
						$generic_skill_result_message = "Those two ninja don't seem to be clones.";
					}
				}
			}

			// ************************** Section applies to all skills ******************************

			if (!$victim_alive) { // Someone died.
				if ($target->player_id == $player->player_id) { // Attacker killed themself.
					$loot = 0;
					$suicided = true;
				} else { // Attacker killed someone else.
					$killed_target = true;
					$gold_mod = 0.15;
					$loot     = floor($gold_mod * $target->gold);
					$player->set_gold($player->gold+$loot);
					$target->set_gold($target->gold-$loot);

					$player->addKills(1);

					$added_bounty = floor($level_check / 5);

					if ($added_bounty > 0) {
						$player->set_bounty($player->bounty+($added_bounty * 25));
					} else if ($target->bounty > 0 && $target->id() !== $player->id()) {
						 // No suicide bounty, No bounty when your bounty getting ++ed.
						$player->set_gold($player->gold+$target->bounty); // Reward the bounty
						$target->set_bounty(0); // Wipe the bounty
                    }

					$target_message = "$attacker_id has killed you with $act and taken $loot gold.";
					Event::create($attacker_char_id, $target->id(), $target_message);

					$attacker_message = "You have killed $target with $act and taken $loot gold.";
					Event::create($target->id(), $player->id(), $attacker_message);
				}
			}

			$turns_to_take = $turns_to_take - $turn_cost;
			$player->save();
			$target->save();

			if (!$covert && $player->hasStatus(STEALTH)) {
				$player->subtractStatus(STEALTH);
				$destealthed = true;
			}
		} // End of the skill use SUCCESS block.

		$ending_turns         = $player->changeTurns($turns_to_take); // Take the skill use cost.
		$target_ending_health = $target->health;
		$target_name          = $target->name();
		$parts                = get_defined_vars();
		$options              = ['quickstat'=>'player'];

		return new StreamedViewResponse('Skill Effect', 'skills_mod.tpl', $parts, $options);
	}

	/**
	 * Pull a stripped down set of player data to display to the skill user.
	 */
	private function pullSightData(Player $target){
		$data = $target->data();
		// Strip all fields but those allowed.
		$allowed = [
		    'Name'     => 'uname',
		    'Class'    => 'class_name',
		    'Level'    => 'level',
		    'Turns'    => 'turns',
		    'Strength' => 'strength',
		    'Speed'    => 'speed',
		    'Stamina'  => 'stamina',
		    'Ki'       => 'ki',
		    'Gold'     => 'gold',
		    'Kills'    => 'kills',
		];

		$res = array();

		foreach ($allowed as $header => $field) {
			$res[$header] = $data[$field];
		}

		return $res;
	}

	/**
	 * Use up some ki to heal yourself.
	 */
	private function harmonizeChakra(Player $char){
		// Heal at most 100 or ki available or hurt by AND at least 0
		$heal_for = (int) max(0, min($this->maxHarmonize($char), $char->is_hurt_by(), $char->ki));
		if ($heal_for > 0) {
			// If there's anything to heal, try.
			// Subtract the ki used for healing.
			$char->heal($heal_for);
			$char->set_ki($char->ki - $heal_for);
			$char->save();
		}

		return $char;
	}
}
