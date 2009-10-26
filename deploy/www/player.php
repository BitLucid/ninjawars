<?php
require_once(OBJ_ROOT."Skill.php");
require_once(LIB_ROOT."specific/lib_player.php");


/**
 * Displays a players' available skills and allows their use.
 *
 * @package combat
 * @subpackage skill
**/
$alive      = false;
$private    = true;
$quickstat  = "player";
$page_title = "Player Detail";

include SERVER_ROOT."interface/header.php";

$skillListObj = new Skill();
$skillsListObj = $skillListObj;
$target = $player = in('player');
$target_id = either(in('target_id'), either(in('player_id'), get_user_id($target)));
$user_id = get_user_id();
$score = get_score_formula();

$message = in('message');
if($message){
    send_message($user_id, $target_id, $message);
    echo "<div id='message-sent' class='ninja-notice'>Message sent</div>";
}


$linkbackpage = in('linkbackpage');
$viewing_player_obj = new Player(get_username());
$viewers_clan = $viewing_player_obj->vo->clan;

$target_player_obj = new Player(either($target_id, $target));

if(!$target_player_obj || !$target_player_obj->player_id){
	echo "<div class='error'>No such ninja.</div>";
	echo render_list_link();
	include SERVER_ROOT."interface/footer.php";
	die();
}
$player_info = $target_player_obj->as_array(); // Pull the info out of the object.
$player = $target = $player_info['uname']; // reset the target and target_id vars.
$target_id = $player_info['player_id'];
$self = (get_username() == $player_info['uname']); // Recorde whether this is a self-viewing.

// Attack Legal section
$attacker = get_username();
$params = array('required_turns'=>0, 'ignores_stealth'=>true); // 0 for unstealth.
$AttackLegal = new AttackLegal($attacker, $target, $params);
$attack_allowed = $AttackLegal->check();
$attack_error = $AttackLegal->getError();

// TODO: Add the "player since" date to the player profile/info.


// Display the player info.
if(!$player_info){
    echo "<div class='error'>No such ninja</div>";
    echo render_list_link();
	include SERVER_ROOT."interface/footer.php";
	die();
} else {
    $ranking_link_section = render_ranking_link($player_info, $linkbackpage, $sql);

    
    $class_section = render_class_section($player_info['class']);
	$level_and_category = render_level_and_category($player_info['level']);
	$status_section = render_status_section($player_info['uname']);
    
	$avatar_section = render_avatar_section($target_player_obj);

	if(!$attack_error && !$self){ // They're not dead or otherwise unattackable.
    	// Attack or Duel

        $skills_available = $skillsListObj->hasSkills();

        $item_use_section = render_item_use_on_another($target, $sql);


    	$skill_use_section = render_skills($target, $skillListObj, $skillsListObj);
	} // End of the there-was-no-attack-error section

	$player_activity_section = render_player_activity($player_info);

    $set_bounty_section = '';
    $communication_section = '';
    $clan_options_section = '';
    $player_clan_section = '';
    $player_profile_message = '';
	if(!$self){
    	// Allows the viewer to set bounty on a player.
    	ob_start();
        display_set_bounty($player_info); // TODO: Move this functionality to the doshin.
        $set_bounty_section = ob_get_contents();
        ob_end_clean();

    	$communication_section = render_communication($player_info['uname']);

    	ob_start();
        // Clan leader options on players in their clan.
    	display_clan_options($player_info, $viewing_player_obj);
    	$clan_options_section = ob_get_contents();
    	ob_end_clean();
    	
    }
	// Player clan and clan members

	$player_clan_section = render_player_clan($player_info, $viewers_clan);

	// Player profile message
    ob_start();
	display_player_profile($player_info);
	$player_profile_message = ob_get_contents();
	ob_end_clean();
}

// Send the info to the template.

$parts = get_certain_vars(get_defined_vars(), array('skills_available'));

echo render_template('player.tpl', $parts);

include SERVER_ROOT."interface/footer.php";
?>
