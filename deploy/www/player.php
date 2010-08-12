<?php
$private   = false;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
require_once(OBJ_ROOT."Skill.php");
require_once(DB_ROOT."SkillDAO.class.php");
require_once(LIB_ROOT."specific/lib_clan.php");
require_once(LIB_ROOT."specific/lib_player.php");

$target        = $player = in('player');
$target_id     = first_value(in('target_id'), in('player_id'), get_user_id($target)); // Find target_id if possible.
$target_player_obj = new Player($target_id);

//debug($target_id);debug(in('target_id'));debug(in('player_id'));debug(get_user_id($target));debug($target_player_obj);

if (!$target_player_obj || !$target_player_obj->player_id || !$target_player_obj->isActive()) {
	$template = 'no-player.tpl';
	$parts    = array();
} else {
	$player_info = $target_player_obj->as_array(); // Pull the info out of the object.

	if (!$player_info) {
		$template = 'no-player.tpl';
		$parts    = array();
	} else {
		$viewing_player_obj = new Player(get_user_id());

		$score = get_score_formula();

		if ($viewing_player_obj && $viewing_player_obj->vo) {
			$user_id  = $viewing_player_obj->vo->player_id;
			$username = $viewing_player_obj->vo->uname;
			$self     = ($username && $username == $player_info['uname']); // Record whether this is a self-viewing.
		} else {
			$self = false;
		}

		$message       = in('message');

		$player      = $target = $player_info['uname']; // reset the target and target_id vars.
		$target_id   = $player_info['player_id'];

		if ($message) {
		    send_message($user_id, $target_id, $message);
		    // "message sent" notice will be displayed by the template itself.
		}

		$viewers_clan       = ($viewing_player_obj instanceof Player && $viewing_player_obj->vo ? get_clan_by_player_id($viewing_player_obj->vo->player_id) : null);

		// Attack Legal section
		$params          = array('required_turns'=>0, 'ignores_stealth'=>true); // 0 for unstealth.
		$AttackLegal     = new AttackLegal($username, $target, $params);
		$attack_allowed  = $AttackLegal->check(false);
		$attack_error    = $AttackLegal->getError();

		// TODO: Add the "player since" date to the player profile/info.

		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("SELECT rank_id FROM rankings WHERE uname = :player");
		$statement->bindValue(':player', $player_info['uname']);
		$statement->execute();

		$rank_spot = $statement->fetchColumn();

		// Display the player info.
		$status_list          = get_status_list($player);
		$player_activity_section = render_player_activity($player_info);

		$level_category          = level_category($player_info['level']);

		$gravatar_url            = generate_gravatar_url($target_player_obj);
		$gurl = $gravatar_url;

		if ($username && !$attack_error && !$self) { // They're not dead or otherwise unattackable.
			// Attack or Duel

			$skillDAO = new SkillDAO();

			$combat_skills = $skillDAO->getSkillsByTypeAndClass($viewing_player_obj->vo->_class_id, 'combat', $viewing_player_obj->vo->level)->fetchAll();
			    // *** todo When Smarty3 is released, remove fetch all and change template to new foreach-as syntax ***

			$item_use_section  = render_item_use_on_another($target);
			$skill_use_section = render_skills($target, $viewing_player_obj);
		}	// End of the there-was-no-attack-error section

		$set_bounty_section     = '';
		$communication_section  = '';
		$player_clan_section    = '';

		$clan = get_clan_by_player_id($player_info['player_id']);

		// Player clan and clan members

		if ($clan) {
			$viewer_clan  = (is_logged_in() ? get_clan_by_player_id($viewing_player_obj->vo->player_id) : null);
			$clan_members = render_clan_members($clan->getID());
			$clan_id      = $clan->getID();
			$clan_name    = $clan->getName();

			if ($viewer_clan) {
				$same_clan = ($clan->getID() == $viewer_clan->getID());
				$render_clan_options = ($username && !$self && $same_clan && is_clan_leader($viewing_player_obj->vo->player_id));
			} else {
				$same_clan = $render_clan_options = false;
			}
		}

		// Send the info to the template.

		$template = 'player.tpl';
		$parts = get_certain_vars(get_defined_vars(), array('combat_skills', 'player_info', 'self', 'rank_spot', 'level_category', 'gravatar_url', 'status_list', 'clan'));
	}
}

display_page(
	$template
	, 'Player Profile'
	, $parts
	, array(
		'quickstat' => 'player'
	)
);
}
?>
