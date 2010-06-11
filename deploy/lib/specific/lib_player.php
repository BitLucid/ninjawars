<?php
require_once(LIB_ROOT."specific/lib_status.php");
require_once(LIB_ROOT."common/lib_accounts.php");
// lib_player.php

// Defines for avatar options.
define('GRAVATAR', 1);

// TODO: This is also begging for a template.
function render_skills($target, $player) {
	$skillDAO = new SkillDAO();
	$skillListObj = new Skill();
	$available_skills = $skillDAO->getSkillsByTypeAndClass($player->vo->_class_id, 'targeted', $player->vo->level);

	$skill = $available_skills->fetch();

	if (empty($skill)) {
		return '';
	} else {
		ob_start();

		echo "<form id=\"skill_use\" class='skill_use' action=\"skills_mod.php\" method=\"post\" name=\"skill_use\">\n";
		echo "<ul>";

		do {
			echo "<li>";
			echo "<input id=\"command\" class='command' type=\"submit\" value=\"".$skill['skill_display_name']."\" name=\"command\" class=\"formButton\">\n";
			echo "<input id=\"target\" class='target' type=\"hidden\" value=\"$target\" name=\"target\">\n";
			echo "(".$skillListObj->getTurnCost($skill['skill_display_name'])." Turns)\n";
			echo "</li>";
		} while ($skill = $available_skills->fetch());

		echo "</ul>\n";
		echo "</form>\n";

		$res = ob_get_contents();
		ob_end_clean();

		return $res;
	}
}

/**
 * Pull out the url for the player's avatar
**/
function render_avatar($player, $size=null) {
	// If the avatar_type is 0, return '';
    if (!$player->vo || !$player->vo->avatar_type || !$player->vo->email) {
        return '';
    } else {	// Otherwise, user the player info for creating a gravatar.
		$email       = $player->vo->email;
		$avatar_type = $player->vo->avatar_type;
		return render_avatar_from_email($email, $avatar_type, $size);
	}
}

function generate_gravatar_url($player) {
	if (!is_object($player)) {
		$player = new Player($player);
	}

	return (OFFLINE ? IMAGE_ROOT.'default_avatar.png' : render_avatar($player));
}

// Use the email information to return the gravatar image url.
function render_avatar_from_email($email, $avatar_type=null, $size=null){
	$def         = 'monsterid'; // Default image or image class.
	// other options: wavatar (polygonal creature) , monsterid, identicon (random shape)
	$base        = "http://www.gravatar.com/avatar/";
	$hash        = md5(trim(strtolower($email)));
	$no_gravatar = "d=".urlencode($def);
	$size        = either($size, 80);
	$rating      = "r=x";
	$res         = $base.$hash."?".implode('&', array($no_gravatar, $size, $rating));

	return $res;    
}

// Player activity and events information.
function render_player_activity($player_info) {
	$days = "Today";

	if ($player_info['days']) {
	    $days = $player_info['days']." days ago";
	}

	$bounty = $player_info['bounty'];
	$bounty_section = ($bounty ? " - <span class='player-bounty'>$bounty bounty</span>" : '');
	$res = <<<HEREDOC
		<div class='player-stats centered'>
			<!-- Will display as floats horizontally -->
			<span class='player-last-active'>Last logged in $days</span>
			$bounty_section
		</div>
HEREDOC;
	return $res;
}

// Display the clan name and members.
function render_player_clan($player_info, $viewers_clan=null) {
	ob_start();
	// Display a message if they're the same clan.
	$same_clan = false;

	$clan = get_clan_by_player_id($player_info['player_id']);

	if ($player_info['uname'] != get_username()
	    && $viewers_clan && $clan && $clan->getID() == $viewers_clan->getID()) {
	    $same_clan = $player_info['uname']; // puts the username in same_clan
	}

	if ($clan) {
		$clan_link = $clan_long_name = $clan->getName();
?>

		<div class='player-clan'>
<?php
		if ($same_clan) {
?>
            <p class='ninja-notice'><?php echo htmlentities($same_clan);?> is part of your clan.</p>
<?php
		}
?>
			<p class='clan-link centered'>
			    <span class='subtitle'>Clan:</span>
			    <a href='clan.php?command=view&amp;clan_id=<?php echo $clan->getID();?>'><?php echo $clan_link;?></a>
			</p>
			<div class='clan-members centered'>
			    <?php echo render_clan_members($clan->getID());?>
			</div>
		</div>
<?php
	}

	$res = ob_get_contents();
	ob_end_clean();
	return $res;
}

// Straight list of clan members
function render_clan_members($clan_id = 0, $limit = 30) {
	ob_start();

	if ($clan_id) {
		$sel = "SELECT uname, player_id, health FROM clan_player JOIN players ON player_id = _player_id AND _clan_id = :clanID AND confirmed = 1 ORDER BY health DESC, level DESC LIMIT :limit";
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare($sel);
		$statement->bindValue(':clanID', $clan_id);
		$statement->bindValue(':limit', $limit);
		$statement->execute();
?>
        <div class='clan-members'>
            <h3 class='clan-members-header'>Clan members</h3>
<?php
		if ($ninja = $statement->fetch()) {
			$display_ul = true;
			echo "<ul>";

			do {
				$added_class = '';

				if ($ninja['health'] < 1) {
					$added_class = ' injured';
				}

				echo "<li class='clan-member$added_class'>
                            <a href='player.php?target_id=", htmlentities(urlencode($ninja['player_id'])), "'>", htmlentities($ninja['uname']), "</a>
                          </li>";
			} while ($ninja = $statement->fetch());

			echo "</ul>\n";
		}
?>
        </div>
<?php
	}

	$res = ob_get_contents();
	ob_end_clean();

	return $res;
}

/**
 * Create the item options for the inventory dropdown.
**/
function render_inventory_options($username) {
	DatabaseConnection::getInstance();

	$user_id = get_user_id($username);
	$res = '';
	$selected = "selected='selected'";// Mark first option as selected.
	$loop_items = DatabaseConnection::$pdo->prepare(
        "SELECT owner, item, item_id, amount
        FROM inventory WHERE owner = :owner
        AND amount > 0 ORDER BY item");
	$loop_items->bindValue(':owner', $user_id);
	$loop_items->execute();

	if ($litem = $loop_items->fetch()) {
		// Set shuriken at highest precedence.
		$items_indexed = array();

		do {
			$items_indexed[$litem['item']] = $litem; // indexed by item name.
		} while ($litem = $loop_items->fetch());

		if (isset($items_indexed['Shuriken'])) {
			// Set shuriken as first dropdown entry.
			$shuriken_entry = $items_indexed['Shuriken'];
			unset($items_indexed['Shuriken']);
			$items_indexed['Shuriken'] = $shuriken_entry;
			$items_indexed = array_reverse($items_indexed);
		}

		foreach ($items_indexed AS $loopItem) {
			$res .= "      <option $selected value='{$loopItem['item']}'>".htmlentities($loopItem['item'])." ({$loopItem['amount']})</option>\n";
			$selected = '';
		}
	} else { // Some items available.
		$res = "          <option value=\"\" selected=\"selected\">You Have No Items</option>\n";
	}

	return $res;
}

/**
 * Display the full form for item use/dropdowns/give/
**/
function render_item_use_on_another($target) {
	$username = get_username();
	$res = "<form id=\"inventory_form\" action=\"inventory_mod.php\" method=\"post\" name=\"inventory_form\">\n
    <input id=\"target\" type=\"hidden\" name=\"target\" value=\"$target\">
    <input type=\"submit\" value=\"Use\" class=\"formButton\">\n
    <select id=\"item\" name=\"item\">\n";

	$res .= render_inventory_options($username);
	$res .= "</select>";

	$target_id   = get_user_id($target);
	$target_clan = get_clan_by_player_id($target_id);

	if ($target_clan && ($user_clan = get_clan_by_player_id(get_user_id($username))) && $target_clan->getID() == $user_clan->getID()) {
		// Only allow giving items within the same clan.
		$res .= "<input id=\"give\" type=\"submit\" value=\"Give\" name=\"give\" class=\"formButton\">\n";
	}

	$res .= "</form>\n";
	return $res;
}

// Display the in-clan options for clan leaders.
function display_clan_options($player_info, $viewing_player_obj) {
	$clan        = get_clan_by_player_id($player_info['player_id']);
	$viewer_clan = get_clan_by_player_id($viewing_player_obj->vo->player_id);

	if ($clan && $viewer_clan
		&& $clan->getID() == $viewer_clan->getID()
		&& is_clan_leader($viewing_player_obj->vo->player_id)) {
		echo "<div class='clan-leader-options centered'>";
		echo "<form id=\"kick_form\" action=\"clan.php\" method=\"get\" name=\"kick_form\">\n";
		echo "<input id=\"kicked\" type=\"hidden\" value=\"", htmlentities($player_info['player_id']), "\" name=\"kicked\">\n";
		echo "<input id=\"command\" type=\"hidden\" value=\"kick\" name=\"command\">\n";
		echo "<input type=\"submit\" value=\"Kick This Ninja From Your Clan\" class=\"formButton\">\n";
		echo "</form>\n";
		echo "</div>";
	} else {
		return;
	}
}

function render_player_link($username) {
    return "<a href='player.php?player=".htmlentities(urlencode($username))."'>".htmlentities($username)."</a>";
}

// Check whether the player is the leader of their clan.
function is_clan_leader($player_id) {
	return (($clan = get_clan_by_player_id($player_id)) && $player_id == get_clan_leader_id($clan->getID()));
}

function get_rank($username) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT rank_id FROM rankings WHERE uname = :player");
	$statement->bindValue(':player', $username);
	$statement->execute();

	$rank = $statement->fetchColumn();

	return ($rank > 0 ? $rank : 1); // Make rank default to 1 if no valid ones are found.
}
?>
