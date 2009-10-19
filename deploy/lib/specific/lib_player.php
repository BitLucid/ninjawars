<?php
require_once(LIB_ROOT."specific/lib_status.php");
// lib_player.php

// Defines for avatar options.
define('GRAVATAR', 1);

// TODO: This is also begging for a template.
function render_skills($target, $skillListObj, $skillsListObj){
    $available_skills = $skillsListObj->hasSkills();
    if(empty($available_skills)){
        return '';
    }
    ob_start();
    echo "<form id=\"skill_use\" class='skill_use' action=\"skills_mod.php\" method=\"post\" name=\"skill_use\">\n";
    if ($skillsListObj->hasSkill('Fire Bolt')) {
      echo "<li>";
      echo "<input id=\"command\" class='command' type=\"submit\" value=\"Fire Bolt\" name=\"command\" class=\"formButton\">\n";
      echo "<input id=\"target\" class='target' type=\"hidden\" value=\"$target\" name=\"target\">\n";
      echo "(".$skillListObj->getTurnCost('Fire Bolt')." Turns)\n";
      echo "</li>";
    }
    if ($skillsListObj->hasSkill('Poison Touch')) {
      echo "<li>";
      echo "<input id=\"command\" class='command' type=\"submit\" value=\"Poison Touch\" name=\"command\" class=\"formButton\">\n";
      echo "<input id=\"target\" class='target' type=\"hidden\" value=\"$target\" name=\"target\">\n";
      echo "(".$skillListObj->getTurnCost('Poison Touch')." Turns)\n";
      echo "</li>";
    }
    if ($skillsListObj->hasSkill('Steal')) {
      echo "<li>";
      echo "<input id=\"command\" class='command' type=\"submit\" value=\"Steal\" name=\"command\" class=\"formButton\">\n";
      echo "<input id=\"target\" class='target' type=\"hidden\" value=\"$target\" name=\"target\">\n";
      echo "(".$skillListObj->getTurnCost('Steal')." Turns)\n";
      echo "</li>";
    }
    if ($skillsListObj->hasSkill('Ice Bolt')) {
      echo "<li>";
      echo "<input id=\"command\" class='command' type=\"submit\" value=\"Ice Bolt\" name=\"command\" class=\"formButton\">\n";
      echo "<input id=\"target\" class='target' type=\"hidden\" value=\"$target\" name=\"target\">\n";
      echo "(".$skillListObj->getTurnCost('Ice Bolt')." Turns)\n";
      echo "</li>";
    }
    if ($skillsListObj->hasSkill('Cold Steal')) {
      echo "<li>";
      echo "<input id=\"command\" class='command' type=\"submit\" value=\"Cold Steal\" name=\"command\" class=\"formButton\">\n";
      echo "<input id=\"target\" class='target' type=\"hidden\" value=\"$target\" name=\"target\">\n";
      echo "(".$skillListObj->getTurnCost('Cold Steal')." Turns)<br>\n";
      echo "</li>";
    }
    if ($skillsListObj->hasSkill('Sight')) {
      echo "<li>";
      echo "<input id=\"command\" class='command' type=\"submit\" value=\"Sight\" name=\"command\" class=\"formButton\">\n";
      echo "<input id=\"target\" class='target' type=\"hidden\" value=\"$target\" name=\"target\">\n";
      echo "(".$skillListObj->getTurnCost('Sight')." Turns)\n";
      echo "</li>";
    }
    echo "</form>\n";
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}


/**
 * Pull out the url for the player's avatar
**/
function render_avatar($player, $size=null){
	// If the avatar_type is 0, return '';
    if (!$player->vo || !$player->vo->avatar_type || !$player->vo->email){
        return '';
    } else {	// Otherwise, user the player info for creating a gravatar.
		$def = 'identicon'; // Default image or image class.
		// other options: wavatar , monsterid
		$email = $player->vo->email;
		$avatar_type = $player->vo->avatar_type;
		$base = "http://www.gravatar.com/avatar/";
		$hash = md5(trim(strtolower($email)));
		$no_gravatar = "d=".urlencode($def);
		$size = either($size, 80);
		$rating = "r=x";
		$res = $base.$hash."?".implode("&amp;", array($no_gravatar, $size, $rating));
		return $res;
	}
}

// Display the div for the avatar to live within.
function render_avatar_section($player, $img_size=null){
    if(!is_object($player)){
        $player = new Player($player);    
    }
	$img_url = (OFFLINE ? '' : render_avatar($player, $img_size));

	if (!$img_url){
		return '';
	}
    return "
    <div id='avatar'>
        <img alt='No Avatar' src='$img_url'>
    </div>";
}

function render_class_section($class){
    $IMAGE_ROOT = IMAGE_ROOT;
    return "<span class='player-class $class'>
        <img src='{$IMAGE_ROOT}small{$class}Shuriken.gif' alt=''>
        $class
    </span>";
}


function render_level_and_category($level){
    $res = '';
    $level_and_cat = level_category($level);
    $res .= "<span class='player-level-category {$level_and_cat['css']}'>
		{$level_and_cat['display']} [{$level}]
	</span>";
	return $res;
}

// Player activity and events information.
function render_player_activity($player_info){
	$days = "Today";
	if($player_info['days']){
	    $days = $player_info['days']." days ago";
	}
	$bounty = $player_info['bounty'];
	$bounty_section = $bounty? " - <span class='player-bounty'>$bounty bounty</span>" : '';
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
function render_player_clan($player_info, $viewers_clan=null){
    ob_start();
	// Display a message if they're the same clan.
	$same_clan = false;
	if ( $player_info['uname'] != get_username()
	    && $viewers_clan && $player_info['clan'] == $viewers_clan){
	    $same_clan = $player_info['uname']; // puts the username in same_clan
    }
	$clan = $player_info['clan'];
	if($clan){
		$clan_long_name = $player_info['clan_long_name'];
		if($player_info['clan_long_name']){
			$clan_link = $player_info['clan_long_name'];
		} else {
			$clan_link = $player_info['clan']."'s Clan";
		}
		?>

		<div class='player-clan'>
            <?php if($same_clan){?>
            <p class='ninja-notice'><?=$same_clan;?> is part of your clan.</p>
            <?php } ?>
			<p class='clan-link centered'>
			    <span class='subtitle'>Clan:</span> 
			    <a href='clan.php?command=view&amp;clan_name=<?php echo $clan;?>'><?php echo$clan_link;?></a>
			</p>
			<div class='clan-members centered'>
			    <?php echo render_clan_members($player_info['clan']); ?>
			</div>
		</div>
		<?php
	}
	$res = ob_get_contents();
	ob_end_clean();
	return $res;
}

// Straight list of clan members
function render_clan_members($clan=null, $limit=30){
    ob_start();
    if($clan){
        $where = "where clan = '$clan' and confirmed=1";
        $sel = "select uname, player_id, health from players $where order by health desc, level desc limit $limit";
        $sql = new DBAccess();
        $ninjas = $sql->QueryAssoc($sel);
        ?>
        <div class='clan-members'>
            <div class='subtitle'>Clan members</div>
                <?php
                if(!empty($ninjas)){
                    $display_ul = true;
                }
                if($display_ul){
                    echo "<ul>";
                }
                foreach($ninjas as $ninja){
                    $added_class = '';
                    if($ninja['health']<1){
                        $added_class = ' injured';
                    }
                    echo "<li class='clan-member$added_class'>
                            <a href='player.php?target_id=".$ninja['player_id']."'>
                                ".$ninja['uname']."
                            </a>
                          </li>";
                }
                if($display_ul){
                    echo "</ul>";
                }
                 ?>
        </div>
        <?php
    }
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

function display_player_profile($player_info){
    if($player_info['messages']){
	?>
	<div class='player-profile'>
		<p class='centered'><span class='subtitle'>Message:</span> <?php echo out($player_info['messages']); ?></p>
	</div>
	<?php
	}
}


function render_ranking_link($player_info, $linkbackpage, $sql){
	$rank_spot = $sql->QueryItem("SELECT rank_id FROM rankings WHERE uname = '".sql($player_info['uname'])."'");
	$res = "    <div class='player-ranking-linkback'>
              <a href='list_all_players.php?rank_spot=$rank_spot&amp;hide=dead&amp;page=$linkbackpage'>&lt; Go to rank $rank_spot in the ninja list</a>
        </div>";
    return $res;
}


function render_list_link(){
    $res = "<div class='player-list-link'>
                <a href='list_all_players.php'>Go back to the ninja list</a>
            </div>";
    return $res;
}


/**
 * Create the item options for the inventory dropdown.
**/
function render_inventory_options($username, $sql){
    $res = '';
    $selected = "selected='selected'";// Mark first option as selected.
    $loop_items = $sql->QueryAssoc(
        "SELECT owner, item, item_id, amount
        FROM inventory WHERE owner = '$username'
        AND amount>0 order by item");
    if (empty($loop_items)){
        $res = "          <option value=\"\" selected=\"selected\">No Items</option>\n";
    } else { // Some items available.
        // Set shuriken at highest precedence.
        $items_indexed = array();
        foreach($loop_items as $litem){
            $items_indexed[$litem['item']] = $litem; // indexed by item name.
        }
        if(isset($items_indexed['Shuriken'])){
            // Set shuriken as first dropdown entry.
            $shuriken_entry = $items_indexed['Shuriken'];
            unset($items_indexed['Shuriken']);
            $items_indexed['Shuriken'] = $shuriken_entry;
            $items_indexed = array_reverse($items_indexed);
        }
	    foreach($items_indexed AS $loopItem) {
			$res .= "      <option $selected value='{$loopItem['item']}'>{$loopItem['amount']} {$loopItem['item']}</option>\n";
			$selected = '';
		}
	}
	return $res;
}

/**
 * Display the full form for item use/dropdowns/give/
**/
function render_item_use_on_another($target, $sql){
    $username = get_username();
    $res = "<form id=\"inventory_form\" action=\"inventory_mod.php\" method=\"post\" name=\"inventory_form\">\n
    <input id=\"target\" type=\"hidden\" name=\"target\" value=\"$target\">
    <input type=\"submit\" value=\"Use\" class=\"formButton\">\n
    <select id=\"item\" name=\"item\">\n";
    $res .= render_inventory_options($username, $sql);
    $res .= "</select>";
    $targets_clan = getClan($target);
    if($targets_clan && $targets_clan == getClan($username)){
        // Only allow giving items within the same clan.
        $res .= "
            <input id=\"give\" type=\"submit\" value=\"Give\" name=\"give\" class=\"formButton\">\n";
    }
    $res .= "</form>\n";
    return $res;
}


function display_attack_options(){
	// Attack Duel deflect or blaze
	// Use [Item List] Give (only if in same clan)
	// Extra skills (sight, pickpocket)
	// Make Attacks central, secondary options up against left and right sides.
}


// Display the in-clan options for clan leaders.
function display_clan_options($player_info, $viewing_player_obj){
    if ($player_info['clan'] && $viewing_player_obj->vo->clan
        && $player_info['clan'] == $viewing_player_obj->vo->clan
        && is_clan_leader($viewing_player_obj)){
        echo "<div class='clan-leader-options centered'>";
        echo "<form id=\"kick_form\" action=\"clan.php\" method=\"get\" name=\"kick_form\">\n";
        echo "<input id=\"kicked\" type=\"hidden\" value=\"{$player_info['uname']}\" name=\"kicked\">\n";
        echo "<input id=\"command\" type=\"hidden\" value=\"kick\" name=\"command\">\n";
        echo "<input type=\"submit\" value=\"Kick This Ninja From Your Clan\" class=\"formButton\">\n";
        echo "</form>\n";
        echo "</div>";
    } else {
        return;
    }
}

// Check whether the player is the leader of their clan.
function is_clan_leader($player){
    if (strtolower($player->vo->clan) == strtolower($player->vo->uname)){
        return true;
    } else {
        return false;
    }

}

// display the form to set bounty on a player.
function display_set_bounty($player_info){
    echo "  <div class='set-bounty centered'>";
    echo "    <form id=\"set_bounty\" action=\"doshin_office.php\" method=\"post\" name=\"set_bounty\">\n";
    echo "    <input id=\"amount\" type=\"text\" size=\"4\" maxlength=\"5\" name=\"amount\" class=\"textField\">\n";
    echo "    <input id=\"command\" type=\"submit\" value=\"Offer Bounty\" name=\"command\" class=\"formButton\">\n";
    echo "    <input id=\"target\" type=\"hidden\" value=\"{$player_info['uname']}\" name=\"target\">\n";
    echo "    </form>\n";
    echo "  </div>";
}

// Display the form to send mail to an individual.
function render_communication($target){
    $target_id = get_user_id($target);
    $res = "<div class='player-communications centered'>
        <form id='send_mail' action='player.php' method='get' name='send_mail'>
        <input type='hidden' name='target_id' value='$target_id'>
        <input id='messenger' type='hidden' value='1' name='messenger'><br >
        <textarea name='message' cols='20' rows='2'></textarea>
        <input type='submit' value='Send Mail' class='formButton'>
        </form>
      </div>";
      return $res;
}


function get_rank($username, $sql=null){
    if(!$sql){
        $sql = new DBAccess();
    }
    $rank         = $sql->QueryItem("SELECT rank_id FROM rankings WHERE uname = '".$username."'");
    $rank         = ($rank > 0 ? $rank : 1); // Make rank default to 1 if no valid ones are found.
    return $rank;
}


?>
