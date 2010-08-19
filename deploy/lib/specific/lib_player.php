<?php
require_once(LIB_ROOT."specific/lib_status.php");
require_once(LIB_ROOT."common/lib_accounts.php");
// lib_player.php

// Defines for avatar options.
define('GRAVATAR', 1);

/***********************   Refactor these class functions from commands.php  ********************************/


// ************************************
// ********** CLASS FUNCTIONS *********
// ************************************

// Wrapper functions for the old usages.
// DEPRECATED
function setClass($who, $new_class){
    $char_id = get_char_id($who);
    return set_class($char_id, $new_class);
}

// Wrapper functions for the old usages.
// DEPRECATED
function getClass($who){
    $char_id = get_char_id($who);
    return char_class_identity($char_id);
    // Note that classes now have identity/name(for display)/theme, so this function should be deprecated.
}


    // ************************************
    // ************************************	


// Centralized holding for the maximum level available in the game.
function maximum_level(){
    return 250;
}

// The number of kills needed to level up to the next level.
function required_kills_to_level($current_level){
    $levelling_cost_multiplier = 5; // 5 more kills in cost for every level you go up.
    $required_kills = ($current_level+1)*$levelling_cost_multiplier;
    return $required_kills;
    
}


// ******** Leveling up Function *************************
// Incorporate this into the kill system to cause auto-levelling.
function level_up_if_possible($char_id){
    // Setup values: 
	$max_level = maximum_level();
    $health_to_add = 100;
    $turns_to_give = 50;
    $strength_to_add = 5;
    
    
        
    $username = get_char_name($char_id);
    $char_level = getLevel($username);
    $char_kills = getKills($username);


    // Check required values:
	$nextLevel  = $char_level + 1;
	$required_kills = required_kills_to_level($char_level);
	// Have to be under the max level and have enough kills.
	$level_up_possible = ( 
	    ($nextLevel < $max_level) && 
	    ($char_kills >= $required_kills) );
	    
	    
	if ($level_up_possible) {
	    // ****** Perform the level up actions ****** //
		$userKills = subtractKills($username, $required_kills);
		$userLevel = addLevel($username, 1);
		addStrength($username, $strength_to_add);
		addHealth($username, $health_to_add);
		addTurns($username, $turns_to_give);
		return true;
	} else {
	    return false;
	}
}



// Check that a class matches against the class identities available in the database.
function is_valid_class($potential_class_identity){
    $sel = "select identity from class";
    $classes = query_array($sel);
    foreach($classes as $l_class){
        if($l_class['identity'] == $potential_class_identity){
            return true;
        }
    }
    return false;
}

// Set the character's class.
function set_class($char_id, $new_class) {
    if(!is_valid_class($new_class)){
        return "That class was not an option to change into.";
    } else {
    	$up = "UPDATE players SET _class_id = (select class_id FROM class WHERE class.identity = :class) WHERE player_id = :char_id";
    	query($up, array(':class'=>$new_class, ':char_id'=>$char_id));

    	return null;
    }
}


// Get the character class information.
function char_class_identity($char_id) {
    return query_item("SELECT class.identity FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id", 
        array(':char_id'=>$char_id));
}


// Get the character class theme string.
function char_class_theme($char_id) {
    return query_item("SELECT class.theme FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id", 
        array(':char_id'=>$char_id));
}



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
	$size        = whichever($size, 80);
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
