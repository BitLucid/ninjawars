<?php
require_once(dirname(__DIR__.'/../../').'/core/base.inc.php');
require_once(LIB_ROOT.'control/lib_inventory.php');

use NinjaWars\core\data\Message;
use NinjaWars\core\control\AttackLegal;

$private   = false;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
require_once(LIB_ROOT."control/Skill.php");
require_once(DB_ROOT."SkillDAO.class.php");
require_once(LIB_ROOT."control/lib_clan.php");
require_once(LIB_ROOT."control/lib_player.php");

$target        = $player = first_value(in('ninja'), in('player'), in('find'), in('target'));
$target_id     = first_value(in('target_id'), in('player_id'), get_char_id($target)); // Find target_id if possible.
$target_player_obj = new Player($target_id);
$viewed_name_for_title = null;
if ($target_player_obj && $target_player_obj->name()) {
	$viewed_name_for_title = $target_player_obj->name();
}

$char_info = self_info();

if (!$target_player_obj || !$target_player_obj->id() || !$target_player_obj->isActive()) {
	$template = 'no-player.tpl';
	$parts    = array();
} else {
	$player_info = $target_player_obj->as_array(); // Pull the info out of the object.

	if (!$player_info) {
		$template = 'no-player.tpl';
		$parts    = array();
	} else {
		$viewing_player_obj = new Player(self_char_id());

		$self        = (self_char_id() && self_char_id() == $player_info['player_id']); // Record whether this is a self-viewing.

		if ($viewing_player_obj && $viewing_player_obj->vo) {
			$char_id  = $viewing_player_obj->id();
			$username = $viewing_player_obj->name();
		}

		$message       = in('message');

		$player      = $target = $player_info['uname']; // reset the target and target_id vars.
		$target_id   = $player_info['player_id'];

    	$target_class_theme = char_class_theme($target_id);

		if ($message) {
			Message::create(['send_from'=>$char_id, 'send_to'=>$target_id, 'message'=>$message, 'type'=>0]);
		    // "message sent" notice will be displayed by the template itself.
		}

		// Get the player's kills for this date.
		$kills_today = query_item('select sum(killpoints) from levelling_log where _player_id = :player_id and killsdate = CURRENT_DATE and killpoints > 0', array(':player_id'=>$target_id));

		$viewers_clan       = ($viewing_player_obj instanceof Player && $viewing_player_obj->vo ? get_clan_by_player_id($viewing_player_obj->vo->player_id) : null);

		// Attack Legal section
		$params          = array('required_turns'=>0, 'ignores_stealth'=>true); // 0 for unstealth.
		$AttackLegal     = new AttackLegal($viewing_player_obj, $target_player_obj, $params);
		$attack_allowed  = $AttackLegal->check(false);
		$attack_error    = $AttackLegal->getError();

        $sel_rank_spot = "SELECT rank_id FROM rankings WHERE player_id = :char_id limit 1";
        $rank_spot = query_item($sel_rank_spot, array(':char_id'=>$player_info['player_id']));

		// Display the player info.
		$status_list          = get_status_list($player);
		$level_category       = level_category($player_info['level']);
		$gurl = $gravatar_url = generate_gravatar_url($target_player_obj);

		if ($viewing_player_obj->id() && !$attack_error && !$self) { // They're not dead or otherwise unattackable.
			// Attack or Duel

			$skillDAO = new SkillDAO();

			$combat_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->vo->_class_id, 'combat', $viewing_player_obj->vo->level, $viewing_player_obj->isAdmin());
			$targeted_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->vo->_class_id, 'targeted', $viewing_player_obj->vo->level, $viewing_player_obj->isAdmin());
		    // *** todo When Smarty3 is released, remove fetch all and change template to new foreach-as syntax ***

			// Pull the items and some necessary data about them.
			$items = inventory_counts($char_id);

			$valid_items = rco($items);// row count

		}	// End of the there-was-no-attack-error section

		$set_bounty_section     = '';
		$communication_section  = '';
		$player_clan_section    = '';

		$clan = get_clan_by_player_id($player_info['player_id']);
		$same_clan = false;

		$player_info = format_health_percent($player_info);

		// Player clan and clan members

		if ($clan) {
			$viewer_clan  = (is_logged_in() ? get_clan_by_player_id($viewing_player_obj->vo->player_id) : null);
			$clan_members = get_clan_members($clan->getID())->fetchAll(); // TODO - When we switch to Smarty 3, remove fetchAll for foreach
			$clan_id      = $clan->getID();
			$clan_name    = $clan->getName();

			if ($viewer_clan) {
				$same_clan = ($clan->getID() == $viewer_clan->getID());
				$display_clan_options = ($viewing_player_obj->name() && !$self && $same_clan && is_clan_leader($viewing_player_obj->vo->player_id));
			} else {
				$same_clan = $display_clan_options = false;
			}
		}

		// Send the info to the template.

		$template = 'player.tpl';
		$parts = get_certain_vars(get_defined_vars(), array('char_info', 'viewing_player_obj', 'target_player_obj', 'combat_skills',
			'targeted_skills', 'player_info', 'self', 'rank_spot', 'kills_today', 'level_category',
			'gravatar_url', 'status_list', 'clan', 'clan_members', 'items'));
	}
}

function getTurnCost($p_params, &$tpl) {
	$skillListObj = new Skill();
	return $skillListObj->getTurnCost($p_params['skillName']);
}

$template = prep_page(
	$template
	, 'Ninja'.($viewed_name_for_title? ": $viewed_name_for_title" : ' Profile')
	, $parts
	, array(
		'quickstat' => 'player'
	)
);

$template->registerPlugin("function","getTurnCost", "getTurnCost");


$template->fullDisplay();

} // End of no display_error area.
