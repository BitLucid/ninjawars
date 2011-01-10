<?php
require_once(LIB_ROOT.'control/lib_inventory.php');
$private   = false;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
require_once(LIB_ROOT."control/Skill.php");
require_once(DB_ROOT."SkillDAO.class.php");
require_once(LIB_ROOT."control/lib_clan.php");
require_once(LIB_ROOT."control/lib_player.php");

$target        = $player = first_value(in('ninja'), in('player'));
$target_id     = first_value(in('target_id'), in('player_id'), get_char_id($target)); // Find target_id if possible.
$target_player_obj = new Player($target_id);

$char_info = get_player_info();

if (!$target_player_obj || !$target_player_obj->id() || !$target_player_obj->isActive()) {
	$template = 'no-player.tpl';
	$parts    = array();
} else {
	$player_info = $target_player_obj->as_array(); // Pull the info out of the object.

	if (!$player_info) {
		$template = 'no-player.tpl';
		$parts    = array();
	} else {
		$viewing_player_obj = new Player(get_char_id());

		//$score = get_score_formula();

		$self        = (get_char_id() && get_char_id() == $player_info['player_id']); // Record whether this is a self-viewing.

		if ($viewing_player_obj && $viewing_player_obj->vo) {
			$char_id  = $viewing_player_obj->id();
			$username = $viewing_player_obj->name();
		}

		$message       = in('message');

		$player      = $target = $player_info['uname']; // reset the target and target_id vars.
		$target_id   = $player_info['player_id'];

    	$target_class_theme = char_class_theme($target_id);

		if ($message) {
		    send_message($char_id, $target_id, $message);
		    // "message sent" notice will be displayed by the template itself.
		}

		$viewers_clan       = ($viewing_player_obj instanceof Player && $viewing_player_obj->vo ? get_clan_by_player_id($viewing_player_obj->vo->player_id) : null);

		// Attack Legal section
		$params          = array('required_turns'=>0, 'ignores_stealth'=>true); // 0 for unstealth.
		$AttackLegal     = new AttackLegal($username, $target, $params);
		$attack_allowed  = $AttackLegal->check(false);
		$attack_error    = $AttackLegal->getError();

        $sel_rank_spot = "SELECT rank_id FROM rankings WHERE player_id = :char_id limit 1";
        $rank_spot = query_item($sel_rank_spot, array(':char_id'=>$player_info['player_id']));

		// Display the player info.
		$status_list          = get_status_list($player);
		$level_category       = level_category($player_info['level']);
		$gurl = $gravatar_url = generate_gravatar_url($target_player_obj);

		if ($char_id && !$attack_error && !$self) { // They're not dead or otherwise unattackable.
			// Attack or Duel

			$skillDAO = new SkillDAO();

			$combat_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->vo->_class_id, 'combat', $viewing_player_obj->vo->level)->fetchAll();
			$targeted_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->vo->_class_id, 'targeted', $viewing_player_obj->vo->level)->fetchAll();
		    // *** todo When Smarty3 is released, remove fetch all and change template to new foreach-as syntax ***

			$items = getInventory($char_id);
			$items = $items->fetchAll();
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
				$display_clan_options = ($username && !$self && $same_clan && is_clan_leader($viewing_player_obj->vo->player_id));
			} else {
				$same_clan = $display_clan_options = false;
			}
		}

		// Send the info to the template.

		$template = 'player.tpl';
		$parts = get_certain_vars(get_defined_vars(), array('char_info', 'combat_skills', 'targeted_skills', 'player_info', 'self', 'rank_spot', 'level_category', 'gravatar_url', 'status_list', 'clan', 'clan_members', 'items'));
	}
}

function getTurnCost($p_params, &$tpl) {
	$skillListObj = new Skill();
	return $skillListObj->getTurnCost($p_params['skillName']);
}

$template = prep_page(
	$template
	, 'Ninja'
	, $parts
	, array(
		'quickstat' => 'player'
	)
);

$template->register_function('getTurnCost', 'getTurnCost');

$template->fullDisplay();

}
?>
