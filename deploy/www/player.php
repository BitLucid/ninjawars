<?php
require_once(OBJ_ROOT."Skill.php");
require_once(LIB_ROOT."specific/lib_player.php"); //


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
$target_id = either(in('target_id'), in('player_id'));
$score = get_score_formula();

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

// Attack Legal section
$attacker = get_username();
$params = array('required_turns'=>0, 'ignores_stealth'=>true); // 0 for unstealth.
$AttackLegal = new AttackLegal($attacker, $target, $params);
$attack_allowed = $AttackLegal->check();
$attack_error = $AttackLegal->getError();

// TODO: Add the "player since" date to the player profile/info.


// Display the player info.
if ($player_info) {
	echo "<div class='player-info'>";

	display_ranking_link($player_info, $linkbackpage, $sql);
	display_player_stats($player_info);

	echo "<table id='player-profile-table' align='center'>\n";
    echo "  <tr>\n";

	if ($attack_error)
	{	// They're dead or otherwise unattackable.
		echo "<td><div class='ninja-error centered'>Cannot Attack: ".$attack_error."</div></td>";
	}
	else
	{
	    $class = getClass($username);

		$is_own_profile = ($username == $player_info['uname']? true : false);
		if ($is_own_profile)
		{
			echo "<td><div class='ninja-notice'>This is you.</div></td>";
		}
		else
		{
			// Attack or Duel
		    echo "<td colspan=\"2\">\n";
		    echo "  <table id='player-profile-attack' align=\"left\">\n";
		    echo "    <tr>\n";
		    echo "      <td style=\"border: thin solid clear;padding-left: 5; padding-right: 5;padding-top: 5;padding-bottom: 5;text-align: center;\">\n";
			// Attack.
			echo "        <form id=\"attack_player\" action=\"attack_mod.php\" method=\"post\" name=\"attack_player.php\">\n";
			echo "          <span style=\"border: thin solid clear;padding: 1px;\">
                              <label><a href=\"#\">Duel</a> <input id=\"duel\" type=\"checkbox\" name=\"duel\"></label>
                            </span>\n";

			if ($skillsListObj->hasSkill('Blaze'))
			{
				echo "      <span style=\"border: thin solid clear;padding: 1px;\">
                              <label><a href=\"#\">Blaze</a><input id=\"blaze\" type=\"checkbox\" name=\"blaze\"></label>
                            </span>\n";
			}

			if ($skillsListObj->hasSkill('Deflect'))
			{
				echo "      <span style=\"border: thin solid clear;padding: 1px;\">
                              <label><a href=\"#\">Deflect</a><input id=\"deflect\" type=\"checkbox\" name=\"deflect\"></label>
                            </span>\n";
			}

			assert($player == $target);

			echo "          <input id=\"target\" type=\"hidden\" value=\"$target\" name=\"target\">\n
                            <label class='attack-player-trigger'>
                              <input class='attack-player-image' type='image' value='Attack' name='attack-player-shuriken' src='".IMAGE_ROOT."50pxShuriken.png' alt='Attack' title='Attack'>
                              <a>Attack</a>
                            </label>";
			echo "        </form>\n";
			echo "      </td>\n";

			// Inventory Items
			echo "      <td style=\"border: thin solid clear;padding: 5px;text-align: center;\">\n";

			echo render_item_use_on_another($target, $sql);

			echo "      </td>\n
                      </tr>\n
                      <tr>\n
                        <td style=\"border: thin solid clear;padding: 5px;text-align: center;\">\n";

			echo render_skills($target, $skillListObj, $skillsListObj);

			echo "      </td>\n";
			echo "    </tr>\n";
		    echo "  </table>\n";
		    echo "</td>\n";
		} // End of the "viewing someone else's profile" section.
	}

	echo "  </tr>\n";

	echo "</table>\n";

	// Alive or dead
	display_player_activity($player_info);

	if($player_info['uname'] != get_username()){
    	// Allows the viewer to set bounty on a player.
        display_set_bounty($player_info); // TODO: Move this functionality to the doshin.

    	// Send 'em mail
    	display_communication($player_info['uname']);
	}

	if($player_info['uname'] != get_username()){
        // Clan leader options on players in their clan.
    	display_clan_options($player_info, $viewing_player_obj);
    }
	// Player clan and clan members

	display_player_clan($player_info, $viewers_clan);

	// Player profile message

	display_player_profile($player_info);

	echo render_avatar_section($target_player_obj);

	echo "</div><!-- End player-info -->";
}

include SERVER_ROOT."interface/footer.php";
?>
