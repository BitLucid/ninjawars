<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\Filter;
use NinjaWars\core\data\Skill;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Character;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\Event;
use NinjaWars\core\data\CloneKill;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use Pimple\Container;

/**
 * Handles both skill listing and displaying, and their usage
 */
class SkillController extends AbstractController {
	const ALIVE = true;
	const PRIV  = true;
	public $update_timer;

	const MIN_POISON_TOUCH = 1;

	protected function maxPoisonTouch(){
		return (int)floor(2/3*Player::maxHealthByLevel(1));
	}

	protected function fireBoltBaseDamage(Player $pc){
		return (int) (floor(Player::maxHealthByLevel($pc->level) / 3));
	}

	/**
	 * Technically max -additional damage off the base
	 */
	protected function fireBoltMaxDamage(Player $pc){
		return (int) $pc->getStrength();
	}

	protected function maxHarmonize(Player $pc){
		return $pc->getMaxHealth();
	}

	/**
	 * Display the initial listing of skills for self-use
	 *
	 * @return StreamedViewResponse
	 */
	public function index(Container $p_dependencies) {
		$error = RequestWrapper::getPostOrGet('error');
		$skillsListObj = new Skill();

		$player         = $p_dependencies['current_player'];
		$starting_turns = $player->turns;
		$starting_ki    = $player->ki;

		$status_list = Player::getStatusList($player->id());
		$no_skills   = true;
		$stealth     = $skillsListObj->hasSkill('Stealth', $player);

		if ($stealth) {
			$no_skills = false;
		}

		$stealth_turn_cost    = $skillsListObj->getTurnCost('Stealth');
		$unstealth_turn_cost  = $skillsListObj->getTurnCost('Unstealth');
		$stalk                = $skillsListObj->hasSkill('Stalk', $player);
		$stalk_turn_cost      = $skillsListObj->getTurnCost('Stalk');
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
			'stalk'                => $stalk,
			'stalk_turn_cost'      => $stalk_turn_cost,
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

	/**
	 * Take the skill form elements and translate the post into the equivalent get requests.
	 *
	 */
	public function postUse(Container $p_dependencies) {
        $request = RequestWrapper::$request;
        $target = $request->get('target');
        $target2 = $request->get('target2');
        $act = $request->get('act');
        $url = 'skill/use/'.rawurlencode($act).'/'.rawurlencode($target).'/'.($target2? rawurlencode($target2).'/' : '');
        return new RedirectResponse(WEB_ROOT.$url); // default 302 redirect
	}

	/**
	 * Translate the self-use requests into their get equivalent
	 */
    public function postSelfUse(Container $p_dependencies) {
        $act = RequestWrapper::getPost('act');
        $url = 'skill/self_use/'.rawurlencode($act).'/';
        return new RedirectResponse(WEB_ROOT.$url); // default 302 redirect
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
	public function selfUse(Container $p_dependencies){
		return $this->useSkill($p_dependencies, true);
	}

	/**
	 * Attempt to resolve a target from a string slug
	 * @return Player|null
	 */
	private function findTarget($dirty_target){
        // If the target is a string instead of an id already, see if it can be translated into a ninja
        $targetObj = null;
        $filtered_id = Filter::toNonNegativeInt($dirty_target);
        if($filtered_id){
        	$targetObj = Player::find($filtered_id);
        } else { // No valid integer, so try filtering by name.
        	if($dirty_target !== null){
        		$targetObj = Player::findByName($dirty_target);
        	}
        }
		return $targetObj;
	}

	/**
	 * Use, the skills_mod equivalent
     *
	 * @note Test with urls like:
	 * http://nw.local/skill/use/Fire%20Bolt/10
	 * http://nw.local/skill/self_use/Unstealth/
	 * http://nw.local/skill/self_use/Heal/
	 * @todo Refactor: Extract these individual skills into individual skill classes
	 */
	public function useSkill(Container $p_dependencies, $self_use=false) {
		// Template vars.
		$display_sight_table = $generic_skill_result_message = $generic_state_change = $killed_target =
			$loot = $added_bounty = $bounty = $suicided = $destealthed = null;
		$error = null;

		$player  = $p_dependencies['current_player'];
		$targetObj = null;
		$char_id = $player->id();
        $path    = RequestWrapper::getPathInfo();
        $slugs   = $this->parseSlugs($path);
        // (fullpath0) /skill1/use2/Fire%20Bolt3/target_james4/(target_beagle5/)
        $requested_use_type = (isset($slugs[2]) ? $slugs[2] : null);
        $act     = (isset($slugs[3]) ? $slugs[3] : null);
        $target_identity  = (isset($slugs[4]) ? $slugs[4] : null);
        $target2 = (isset($slugs[5]) ? $slugs[5] : null);

        // If the target is a string instead of an id already, see if it can be translated into a ninja
        $target_id = null;
        if($self_use === true){
			$target_id = $char_id;
        } else{ // Do a find
        	$targetObj = $this->findTarget($target_identity);
        	$target_id = ($targetObj instanceof Character ? $targetObj->id() : null);
		}

		if ($target2 && $target2 !== null) {
			$target2Obj = $this->findTarget($target_identity);
			$target2 = ($target2Obj instanceof Character ? $target2Obj->id() : null);
		}

		$skillListObj    = new Skill();
		// *** Before level-based addition.
		$turn_cost       = $skillListObj->getTurnCost(strtolower($act));
		$ignores_stealth = $skillListObj->getIgnoreStealth($act);
		$self_usable     = $skillListObj->getSelfUse($act);
		$use_on_target   = $skillListObj->getUsableOnTarget($act);
		$ki_cost 		 = 0; // Ki taken during use.
		$reuse 			 = true;  // Able to reuse the skill.

		// Check whether the user actually has the needed skill.
		$has_skill = $skillListObj->hasSkill($act, $player);

		assert($turn_cost>=0);
		$return_to_target = true;
		$sight_data = null;

		if ($self_use === true) {
			// Use the skill on self.
			$return_to_target = false;
			$targetObj    = $player;
			$target_id = $player->id();
		} else {
			if(0<$target_id && $target_id !== $player->id()){
				$return_to_target = true;
			}

			if(!($targetObj instanceof Character)) {
				// For target that doesn't exist, e.g. http://nw.local/skill/use/Sight/zigzlklkj
				error_log('Info: Attempt to use a skill on a target ['.rawurlencode($target_identity).'] that did not exist.');
				return new RedirectResponse(WEB_ROOT.'skill/?error='.rawurlencode('Invalid target ['.$target_identity.'] for skill ['.rawurldecode($act).'].'));
			}
		}

		$covert           = false;
		$victim_alive     = true;
		$attacker_id      = $player->name();
		$attacker_char_id = $player->id();
		$starting_turns   = $player->turns;

		$level_check  = $player->level - $targetObj->level;

		if ($player->hasStatus(STEALTH)) {
			$attacker_id = 'A Stealthed Ninja';
		}

		if ($act == 'Clone Kill' || $act == 'Harmonize') {
			$has_skill        = true;
			$attack_error     = null;
			$covert           = true;
		} else {
			// *** Checks the skill use legality, as long as the target isn't self.
		    $params = [
		        'required_turns'  => $turn_cost,
		        'ignores_stealth' => $ignores_stealth,
		        'self_use'        => $self_use
		    ];

			$AttackLegal    = new AttackLegal($player, $targetObj, $params);
			$update_timer = isset($this->update_timer)? $this->update_timer : true;
			$AttackLegal->check($update_timer);
			$attack_error   = $AttackLegal->getError();
		}

		if ($attack_error === null) { // Only bother to check for other errors if there aren't some already.
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

			if ($act == 'Sight') {
				$covert = true;

				$sight_data = $this->pullSightData($targetObj);

				$display_sight_table = true;
			} elseif ($act == 'Steal') {
				$covert = true;

				$gold_decrease = min($targetObj->gold, rand(5, 50));

				$player->setGold($player->gold + $gold_decrease);
                $player->save();

                $targetObj->setGold($targetObj->gold - $gold_decrease);
                $targetObj->save();

				$msg = "$attacker_id stole $gold_decrease gold from you.";
				Event::create($attacker_char_id, $targetObj->id(), $msg);

				$generic_skill_result_message = "You have stolen $gold_decrease gold from __TARGET__!";
			} elseif ($act == 'Unstealth') {
				$state = 'unstealthed';

				if ($targetObj->hasStatus(STEALTH)) {
					$targetObj->subtractStatus(STEALTH);
					$generic_state_change = "You are now $state.";
				} else {
					$turn_cost = 0;
					$generic_state_change = "__TARGET__ is already $state.";
				}
			} elseif ($act == 'Stealth') {
				$covert     = true;
				$state      = 'stealthed';

				if (!$targetObj->hasStatus(STEALTH)) {
					$targetObj->addStatus(STEALTH);
					$targetObj->subtractStatus(STALKING);
					$generic_state_change = "__TARGET__ is now $state.";
				} else {
					$turn_cost = 0;
					$generic_state_change = "__TARGET__ is already $state.";
				}
			} elseif ($act == 'Stalk') {
				$state      = 'stalking';
				if(!$targetObj->hasStatus(STALKING)) {
					$targetObj->addStatus(STALKING);
					$targetObj->subtractStatus(STEALTH);
					$generic_state_change = "__TARGET__ is now $state.";
				} else {
					$turn_cost = 0;
					$generic_state_change = "__TARGET__ is already $state.";
				}
			} elseif ($act == 'Kampo') {
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
			} elseif ($act == 'Poison Touch') {
				$covert = true;

				$targetObj->addStatus(POISON);
				$targetObj->addStatus(WEAKENED); // Weakness kills strength.

				$target_damage = rand(self::MIN_POISON_TOUCH, $this->maxPoisonTouch());

				$victim_alive = $targetObj->harm($target_damage);
				$generic_state_change = "__TARGET__ has been poisoned!";
				$generic_skill_result_message = "__TARGET__ has taken $target_damage damage!";

				$msg = "You have been poisoned by $attacker_id";
				Event::create($attacker_char_id, $targetObj->id(), $msg);
			} elseif ($act == 'Fire Bolt') {
				$target_damage = $this->fireBoltBaseDamage($player) + rand(1, $this->fireBoltMaxDamage($player));


				$generic_skill_result_message = "__TARGET__ has taken $target_damage damage!";

				$victim_alive = $targetObj->harm($target_damage);

				$msg = "You have had fire bolt cast on you by ".$player->name();
				Event::create($player->id(), $targetObj->id(), $msg);
			} elseif ($act == 'Heal' || $act == 'Harmonize') {
				// This is the starting template for self-use commands, eventually it'll be all refactored.
				$harmonize = false;

				if ($act == 'Harmonize') {
					$harmonize = true;
				}

			    $hurt = $targetObj->is_hurt_by(); // Check how much the TARGET is hurt (not the originator, necessarily).
			    // Check that the target is not already status healing.
			    if ($targetObj->hasStatus(HEALING) && !$player->isAdmin()) {
			        $turn_cost = 0;
			        $generic_state_change = '__TARGET__ is already under a healing aura.';
				} elseif ($hurt < 1) {
					$turn_cost = 0;
					$generic_skill_result_message = '__TARGET__ is already fully healed.';
				} else {
					if(!$harmonize){
						$original_health = $targetObj->health;
						$heal_points = $player->getStamina()+1;
						$new_health = $targetObj->heal($heal_points); // Won't heal more than possible
						$healed_by = $new_health - $original_health;
					} else {
						$start_health = $player->health;
						// Harmonize those chakra!
						$player = $this->harmonizeChakra($player);
						$healed_by = $player->health - $start_health;
						$ki_cost = $healed_by;
					}

				    $targetObj->addStatus(HEALING);
				    $generic_skill_result_message = "__TARGET__ healed by $healed_by to ".$targetObj->health.".";

				    if ($targetObj->id() != $player->id())  {
						Event::create($attacker_char_id, $targetObj->id(), "You have been healed by $attacker_id for $healed_by.");
					}
				}
			} elseif ($act == 'Ice Bolt') {
				if ($targetObj->hasStatus(SLOW)) {
					$turn_cost = 0;
					$generic_skill_result_message = '__TARGET__ is already iced.';
				} else {
					if ($targetObj->turns >= 10) {
						$turns_decrease = rand(1, 5);
						$targetObj->turns = $targetObj->turns - $turns_decrease;
						// Changed ice bolt to kill stealth.
						$targetObj->subtractStatus(STEALTH);
						$targetObj->subtractStatus(STALKING);
						$targetObj->addStatus(SLOW);

						$msg = "Ice bolt cast on you by $attacker_id, your turns have been reduced by $turns_decrease.";
						Event::create($attacker_char_id, $targetObj->id(), $msg);

						$generic_skill_result_message = "__TARGET__'s turns reduced by $turns_decrease!";
					} else {
						$turn_cost = 0;
						$generic_skill_result_message = "__TARGET__ does not have enough turns for you to take.";
					}
				}
			} elseif ($act == 'Cold Steal') {
				if ($targetObj->hasStatus(SLOW)) {
					$turn_cost = 0;
					$generic_skill_result_message = '__TARGET__ is already iced.';
				} else {
					$critical_failure = rand(1, 100);

					if ($critical_failure > 7) {// *** If the critical failure rate wasn't hit.
						if ($targetObj->turns >= 10) {
							$turns_diff = rand(2, 7);

							$targetObj->turns = $targetObj->turns - $turns_diff;
							$player->turns = $player->turns + $turns_diff; // Stolen
							$targetObj->addStatus(SLOW);

							$msg = "You have had Cold Steal cast on you for $turns_diff by $attacker_id";
							Event::create($attacker_char_id, $targetObj->id(), $msg);

							$generic_skill_result_message = "You cast Cold Steal on __TARGET__ and take $turns_diff turns.";
						} else {
							$turn_cost = 0;
							$generic_skill_result_message = '__TARGET__ did not have enough turns to give you.';
						}
					} else { // *** CRITICAL FAILURE !!
						$player->addStatus(FROZEN);

						$unfreeze_time = date('F j, Y, g:i a', mktime(date('G')+1, 0, 0, date('m'), date('d'), date('Y')));

						$failure_msg = "You have experienced a critical failure while using Cold Steal. You will be unfrozen on $unfreeze_time";
						Event::create((int)0, $player->id(), $failure_msg);
						$generic_skill_result_message = "Cold Steal has backfired! You are frozen until $unfreeze_time!";
					}
				}
			} elseif ($act == 'Clone Kill') {
				// Obliterates the turns and the health of similar accounts that get clone killed.
				$generic_skill_result_message = $this->prepareCloneKill($player, $targetObj, $target2);
				$reuse = false; // Don't give a reuse link.
			}

			// ************************** Section applies to all skills ******************************

			if (!$victim_alive) { // Someone died.
				if ($targetObj->player_id == $player->player_id) { // Attacker killed themself.
					$loot = 0;
					$suicided = true;
				} else { // Attacker killed someone else.
					$killed_target = true;
					$gold_mod = 0.15;
					$loot     = floor($gold_mod * $targetObj->gold);
					$player->setGold($player->gold+$loot);
					$targetObj->setGold($targetObj->gold-$loot);

					$player->addKills(1);

					$added_bounty = floor($level_check / 5);

					if ($added_bounty > 0) {
						$player->setBounty($player->bounty+($added_bounty * 25));
					} elseif ($targetObj->bounty > 0 && $targetObj->id() !== $player->id()) {
						 // No suicide bounty, No bounty when your bounty getting ++ed.
						$player->setGold($player->gold+$targetObj->bounty); // Reward the bounty
						$targetObj->setBounty(0); // Wipe the bounty
                    }

					$target_message = "$attacker_id has killed you with $act and taken $loot gold.";
					Event::create($attacker_char_id, $targetObj->id(), $target_message);

					$attacker_message = "You have killed ".$targetObj->name()." with ".$act." and taken ".$loot." gold.";
					Event::create($targetObj->id(), $player->id(), $attacker_message);
				}
			}

			if (!$covert && $player->hasStatus(STEALTH)) {
				$player->subtractStatus(STEALTH);
				$destealthed = true;
			}

			$targetObj->save();
		} // End of the skill use SUCCESS block.

        $player->turns = $player->turns - max(0, $turn_cost); // Take the skill use cost.
        $player->save();

        $ending_turns         = $player->turns;
		$parts = [
			'attack_error'=>$attack_error,
			'error'=>$attack_error,
			'targetObj'=>$targetObj,
			'display_sight_table'=>$display_sight_table,
			'sight_data'=>$sight_data,
			'generic_skill_result_message'=>$generic_skill_result_message,
			'generic_state_change'=>$generic_state_change,
			'killed_target'=>$killed_target,
			'act'=>$act,
			'loot'=>$loot,
			'added_bounty'=>$added_bounty,
			'bounty'=>$bounty,
			'suicided'=>$suicided,
			'destealthed'=>$destealthed,
			'turn_cost'=>$turn_cost,
			'ki_cost'=>$ki_cost,
			'reuse'=>$reuse,
			'self_use'=>$self_use,
			'return_to_target'=>$return_to_target,
		];
		$options = [
			'quickstat'=>'player'
			];

		return new StreamedViewResponse('Skill Effect', 'skills_mod.tpl', $parts, $options);
	}


	/**
	 * Search for a character by object/string/id
	 * @param Player|string|int|null $search
	 * @return Player|null
	 */
	private function searchForChar($search){
		if($search instanceof Player && (0 < $search->id())){
			return $search; // Already a player object, so return fast
		}
		if((int)$search == $search){
			return Player::find($search);
		} elseif(is_string($search)){
			return Player::findByName($search);
		} else {
			return null;
		}
	}

	/**
	 * Check for clone kill-ability of two characters
	 * @param Player|string|int|null $search1
	 * @param Player|string|int|null $search2
	 */
	private function prepareCloneKill(Player $self_char, $search1, $search2){
		// Consider moving these error conditions in to CloneKill itself.

		$clone1 = $this->searchForChar($search1);
		$clone2 = $this->searchForChar($search2);

		if (!$clone1 || !$clone2) {
			if (!$clone2) {
				$not_a_ninja = $search2;
			} else {
				$not_a_ninja = $search1;
			}
			$generic_skill_result_message = 'There is no such ninja as ['.$not_a_ninja.']';
		} elseif ($clone1->id() == $self_char->id() || $clone2->id() == $self_char->id()) {
			$generic_skill_result_message = 'You cannot clone kill yourself.';
		} elseif ($clone1->id() == $clone2->id()){
			$generic_skill_result_message = 'The same ninja twice is not the same as a clone.';
		} else {
			// The two potential clones will be obliterated immediately if the criteria are met in CloneKill.
			$kill_or_fail = CloneKill::kill($self_char, $clone1, $clone2);

			if ($kill_or_fail !== false) {
				$generic_skill_result_message = $kill_or_fail;
			} else {
				$generic_skill_result_message = "Those two ninja don't seem to be clones.";
			}
		}
		return $generic_skill_result_message;
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
			$char->setKi($char->ki - $heal_for);
			$char->save();
		}

		return $char;
	}
}
