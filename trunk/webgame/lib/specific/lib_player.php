<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php");
// lib_player.php

// Defines for avatar options.
define('GRAVATAR', 1);


/**
 * Pull out the url for the player's avatar
**/
function render_avatar($player, $size=null){
    // If the avatar_type is 0, return '';
    if(!$player->vo || !$player->vo->avatar_type || !$player->vo->email){
        return '';
    } else { // Otherwise, user the player info for creating a gravatar.
        $def = 'identicon'; // Default image or image class.
        $email = $player->vo->email;
        $avatar_type = $player->vo->avatar_type;
        $base = "http://www.gravatar.com/avatar/";
        $hash = md5(trim(strtolower($email)));
        $no_gravatar = "d=".urlencode($def);
        $size = either($size, 80);
        $rating = "r=x";
        $res = $base.$hash."?".implode("&", array($no_gravatar, $size, $rating));
        return $res;
    }
}

// Display the div for the avatar to live within.
function render_avatar_section($player, $img_size=null){
    $img_url = render_avatar($player, $img_size);
    //$img_url = IMAGE_ROOT."50pxShuriken.png";
    if(!$img_url){
        return '';
    } else {
        ob_start();
        ?>
        <div id='avatar'>
            <img alt='No Avatar' src='<?php echo $img_url; ?>'>
        </div>
        <?php
        $res = ob_get_contents();
        ob_end_clean();
        return $res;
    }
}




// The player's stats
function display_player_stats($player_info){
	$status = null;
	if (!$player_info['health']) {
	    $status = "Dead";
	} elseif($player_info['status'] == STEALTH) {
	    $status = "Stealthed";
	}
	$level = $player_info['level'];
	$level_and_cat = level_category($level);
	?>
		<div class='player-name'><?php echo $player_info['uname']; ?></div>
		<div class='player-titles centered'>
			<span class='player-class <?php echo $player_info['class']; ?>'>
				<img src='<?php echo WEB_ROOT;?>images/small<?php echo $player_info['class'];?>Shuriken.gif' alt=''>
				<?php echo $player_info['class']; ?>
			</span>
			<span class='player-level-category <?php echo $level_and_cat['css']; ?>'>
				<?php echo $level_and_cat['display']." [".$level."]"; ?>
			</span>
			<?php if($status){?><p class='player-status ninja-notice <?php echo $status;?>'><?php echo $status;?></p><?php }?>
		</div>
	<?php
}

// Player activity and events information.
function display_player_activity($player_info){
	$days = "Today";
	if($player_info['days']){
	    $days = $player_info['days']." days ago";
	}
	$bounty = $player_info['bounty'];
	?>
		<div class='player-stats centered'>
			<!-- Will display as floats horizontally -->
			<span class='player-last-active'>Last logged in <?php echo $days;?></span>
			<?php if($bounty){ ?> - <span class='player-bounty'><?php echo $bounty; ?> bounty</span><?php } ?>
		</div>
	<?php
}

// Display the clan name and members.
function display_player_clan($player_info, $viewers_clan=null){
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
			    <span class='subtitle'>Clan:</span> <a href='clan.php?command=view&clan_name=<?php echo $clan."'>".$clan_link; ?></a></p>
			<p class='clan-members centered'>
			    <?php display_clan_members($player_info['clan']); ?>
			</p>
		</div>
		<?php
	}
}

// Straight list of clan members
function display_clan_members($clan=null, $limit=30){
    if($clan){
        $where = "where clan = '$clan' and health>0 and confirmed=1";
        $sel = "select uname, player_id from players $where order by level desc limit $limit";
        $sql = new DBAccess();
        $res = $sql->QueryAssoc($sel);
        ?>
        <div class='clan-members'>
            <div class='subtitle'>Clan members</div>
            <ul>
                <?php
                foreach($res as $ninja){
                    echo "<li class='clan-member'>
                            <a href='player.php?target_id=".$ninja['player_id']."'>
                                ".$ninja['uname']."
                            </a>
                          </li>";
                } ?> 
            </ul>
        </div>
        <?php
    }
}

function display_player_profile($player_info){
    if($player_info['messages']){
	?>
	<div class='player-profile'>
		<p class='subtitle'>Message:<p>
		<p class='centered'><?php out($player_info['messages'], 'toMessage'); ?></p>
	</div>
	<?php
	}
}


function display_ranking_link($player_info, $linkbackpage, $sql){
	$rank_spot = $sql->QueryItem("SELECT rank_id FROM rankings WHERE uname = '".$player_info['uname']."'");
	echo "    <div class='player-ranking-linkback'>";
	echo "      <a href='list_all_players.php?rank_spot=$rank_spot&hide=dead&page=$linkbackpage'>&lt; Go to rank $rank_spot in the ninja list</a>\n";
	echo "    </div>";
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
    <input id=\"target\" type=\"hidden\" name=\"target\" value=\"$target\" />
    <input type=\"submit\" value=\"Use\" class=\"formButton\" />\n
    <select id=\"item\" name=\"item\">\n";
    $res .= render_inventory_options($username, $sql);
    $res .= "      </select>\n
        <input id=\"give\" type=\"submit\" value=\"Give\" name=\"give\" class=\"formButton\" />\n
    </form>\n";
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
        echo "<input id=\"kicked\" type=\"hidden\" value=\"{$player_info['uname']}\" name=\"kicked\" />\n";
        echo "<input id=\"command\" type=\"hidden\" value=\"kick\" name=\"command\" />\n";
        echo "<input type=\"submit\" value=\"Kick This Ninja From Your Clan\" class=\"formButton\" />\n";
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
    echo "    <input id=\"amount\" type=\"text\" size=\"4\" maxlength=\"5\" name=\"amount\" class=\"textField\" />\n";
    echo "    <input id=\"command\" type=\"submit\" value=\"Offer Bounty\" name=\"command\" class=\"formButton\" />\n";
    echo "    <input id=\"target\" type=\"hidden\" value=\"{$player_info['uname']}\" name=\"target\" />\n";
    echo "    </form>\n";
    echo "  </div>";
}
	
// Display the form to send mail to an individual.
function display_communication($target){
    echo "  <div class='player-communications centered'>";
    echo "    <form id=\"send_mail\" action=\"mail_send.php\" method=\"get\" name=\"send_mail\">\n";
    echo "    <input id=\"to\" type=\"hidden\" name=\"to\" value=\"$target\" />\n";
    echo "    <input type=\"submit\" value=\"Send Mail\" class=\"formButton\" />\n";
    echo "    <input id=\"messenger\" type=\"hidden\" value=\"1\" name=\"messenger\" /><br >\n";
    echo "    <textarea name=\"message\" cols=\"20\" rows=\"2\"></textarea>\n";
    echo "    </form>\n";
    echo "  </div>";
}



?>
