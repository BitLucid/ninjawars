<?php
require_once(OBJ_ROOT."Skill.php");
require_once(DB_ROOT."SkillDAO.class.php");
require_once(LIB_ROOT."specific/lib_clan.php");
require_once(LIB_ROOT."specific/lib_player.php");


$alive      = false;
$private    = false;
$page_title = 'Player Profile';
$quickstat  = 'player';
$buffer     = false;

include SERVER_ROOT."interface/header.php";

$target        = $player = in('player');
$target_id     = either(in('target_id'), either(in('player_id'), get_user_id($target)));
$target_player_obj = new Player(either($target_id, $target));

if (!$target_player_obj || !$target_player_obj->player_id) {
	transitional_display_full_template('no-player.tpl', get_certain_vars('quickstat'));
} else {
	$player_info = $target_player_obj->as_array(); // Pull the info out of the object.

	if (!$player_info) {
		transitional_display_full_template('no-player.tpl', get_certain_vars('quickstat'));
	} else {
		$score         = get_score_formula();
		$user_id       = get_user_id();
		$username      = get_username();
		$message       = in('message');

		$player      = $target = $player_info['uname']; // reset the target and target_id vars.
		$target_id   = $player_info['player_id'];
		$self        = (get_username() && get_username() == $player_info['uname']); // Record whether this is a self-viewing.
	
		if ($message) {
		    send_message($user_id, $target_id, $message);
		    // "message sent" notice will be displayed by the template itself.
		}
	
		$viewing_player_obj = new Player(get_username());
		$viewers_clan       = ($viewing_player_obj instanceof Player && $viewing_player_obj->vo ? get_clan_by_player_id($viewing_player_obj->vo->player_id) : null);
	
		// Attack Legal section
		$params          = array('required_turns'=>0, 'ignores_stealth'=>true); // 0 for unstealth.
		$AttackLegal     = new AttackLegal($username, $target, $params);
		$attack_allowed  = $AttackLegal->check();
		$attack_error    = $AttackLegal->getError();
	
		// TODO: Add the "player since" date to the player profile/info.
	
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("SELECT rank_id FROM rankings WHERE uname = :player");
		$statement->bindValue(':player', $player_info['uname']);
		$statement->execute();

		$rank_spot = $statement->fetchColumn();

		// Display the player info.
		$level_category          = level_category($player_info['level']);
		$status_section          = render_status_section($player_info['uname']);
		$gravatar_url            = generate_gravatar_url($target_player_obj);
		$player_activity_section = render_player_activity($player_info);
	
		if ($username && !$attack_error && !$self) { // They're not dead or otherwise unattackable.
			// Attack or Duel
	
			$skillDAO = new SkillDAO();
	
			$combat_skills     = $skillDAO->getSkillsByTypeAndClass(
			    $viewing_player_obj->vo->_class_id, 'combat', $viewing_player_obj->vo->level)->fetchAll(); 
			    // *** todo When Smarty3 is released, remove fetch all and change template to new foreach-as syntax ***
	
			$item_use_section  = render_item_use_on_another($target);
			$skill_use_section = render_skills($target, $viewing_player_obj);
		}	// End of the there-was-no-attack-error section
	
		$set_bounty_section     = '';
		$communication_section  = '';
		$clan_options_section   = '';
		$player_clan_section    = '';
	
		if ($username && !$self) {
			// Clan leader options on players in their clan.
			display_clan_options($player_info, $viewing_player_obj);
			$clan_options_section = ob_get_contents();
			ob_end_clean();
		}
	
		// Player clan and clan members
	
		$player_clan_section = render_player_clan($player_info, $viewers_clan);
	
		// Send the info to the template.
	
		$parts = get_certain_vars(get_defined_vars(), array('combat_skills', 'player_info', 'self', 'rank_spot', 'level_category', 'quickstat'));
	
		transitional_display_full_template('player.tpl', $parts);
	}
}
?>
