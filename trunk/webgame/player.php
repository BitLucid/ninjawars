<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php");
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

include "interface/header.php";
require_once(OBJ_ROOT."Skill.php");
$skillListObj = new Skill();
$target = $player = in('player');
$target_id = in('target_id');
$score = get_score_formula();

$linkbackpage = in('linkbackpage');
$viewing_player_obj = new Player(get_username());
$viewers_clan = $viewing_player_obj->vo->clan;

$target_player_obj = new Player(either($target_id, $target));

if(!$target_player_obj || !$target_player_obj->player_id){
	echo "<div class='error'>No such ninja.</div>";
	include "interface/footer.php";
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


// Display the player info.
if ($player_info) {
	echo "<div class='player-info'>";

	display_ranking_link($player_info, $linkbackpage, $sql);
	display_player_stats($player_info);

	echo "<table id='player-profile-table' align='center'>\n";
	echo "<tr>\n";
	
	if($attack_error){ // They're dead or otherwise unattackable.
		echo "<div class='ninja-error centered'>Cannot Attack: ".$attack_error."</div>";
	} else {
	    $class = getClass($username);

    $is_own_profile = ($username == $player_info['uname']? true : false);
    if($is_own_profile){
        echo "<div class='ninja-notice'>This is you.</div>";
    } else {
		// Attack or Duel
	    echo "<tr>\n";
	    echo "  <td colspan=\"2\">\n";
	    echo "  <table id='player-profile-attack' align=\"left\">\n";
	    echo "  <tr>\n";
	    echo "    <td style=\"border: thin solid clear;padding-left: 5;
	    	padding-right: 5;padding-top: 5;padding-bottom: 5;text-align: center;\">\n";
	    	// Attack.
	    echo "<form id=\"attack_player\" action=\"attack_mod.php\" method=\"post\" name=\"attack_player.php\">\n";
	    echo "<span style=\"border: thin solid clear;padding-top: 1;padding-bottom: 1;padding-left: 1;padding-right: 1;\">
	    	<label><a href=\"#\">Duel</a> <input id=\"duel\" type=\"checkbox\" name=\"duel\" /></label></span>\n";
	    if ($class == "Red") {
		  echo "<span style=\"border: thin solid clear;padding-top: 1;padding-bottom: 1;padding-left: 1;padding-right: 1;\">
		  	<label><a href=\"#\">Blaze</a><input id=\"blaze\" type=\"checkbox\" name=\"blaze\" /></label></span>\n";
		} else if ($class == "White") {
		  echo "<span style=\"border: thin solid clear;padding-top: 1;padding-bottom: 1;padding-left: 1;padding-right: 1;\">
		  	<label><a href=\"#\">Deflect</a><input id=\"deflect\" type=\"checkbox\" name=\"deflect\" /></label></span>\n";
		}
		assert($player == $target);
	    echo "    <input id=\"target\" type=\"hidden\" value=\"$target\" name=\"target\" />\n
	        <br /><label><a>Attack</a>
	        <input type='image' value='Attack' name='attack-player-shuriken' 
	        src='".IMAGE_ROOT."50pxShuriken.png' alt='Attack' title='Attack'>
	        </label>";
	    echo "    </form>\n";
	    echo "    </td>\n";
	    
	    
	    // Inventory Items
	    echo "    <td style=\"border: thin solid clear;padding-left: 5;padding-right: 
	    	    5;padding-top: 5;padding-bottom: 5;text-align: center;\">\n";
	    	    
	    echo render_item_use_on_another($target, $sql);
	    
	        
	        
	    echo "    </td>\n
	      </tr>\n
	      <tr>\n
	        <td style=\"border: thin solid clear;padding-left: 5;padding-right: 5;
	    	padding-top: 5;padding-bottom: 5;text-align: center;\">\n";

    		// Class skills.
    	if ($class == "Red") {
    	  echo "<form id=\"skill_use\" action=\"skills_mod.php\" method=\"post\" name=\"skill_use\">\n";
    	  echo "<input id=\"command\" type=\"submit\" value=\"Fire Bolt\" name=\"command\" class=\"formButton\" />\n";
    	  echo "<input id=\"target\" type=\"hidden\" value=\"$target\" name=\"target\" /><br />\n";
    	  echo "(".$skillListObj->getTurnCost('Fire Bolt')." Turns)\n";
    	  echo "</form>\n";
    	} else if ($class == "Black") {
    	  echo "<form id=\"skill_use\" action=\"skills_mod.php\" method=\"post\" name=\"skill_use\">\n";
    	  echo "<input id=\"command\" type=\"submit\" value=\"Poison Touch\" name=\"command\" class=\"formButton\" />\n";
    	  echo "<input id=\"target\" type=\"hidden\" value=\"$target\" name=\"target\" /><br />\n";
    	  echo "(".$skillListObj->getTurnCost('Poison Touch')." Turns)\n";
    	  echo "</form>\n";

    	  echo "<form id=\"skill_use\" action=\"skills_mod.php\" method=\"post\" name=\"skill_use\">\n";
    	  echo "<input id=\"command\" type=\"submit\" value=\"Steal\" name=\"command\" class=\"formButton\" />\n";
    	  echo "<input id=\"target\" type=\"hidden\" value=\"$target\" name=\"target\" />\n";
    	  echo "(".$skillListObj->getTurnCost('Steal')." Turns)\n";
    	  echo "</form>\n";
    	} else if ($class == "Blue") {
    	  echo "<form id=\"skill_use\" action=\"skills_mod.php\" method=\"post\" name=\"skill_use\">\n";
    	  echo "<input id=\"command\" type=\"submit\" value=\"Ice Bolt\" name=\"command\" class=\"formButton\" />\n";
    	  echo "<input id=\"target\" type=\"hidden\" value=\"$target\" name=\"target\" /><br />\n";
    	  echo "(".$skillListObj->getTurnCost('Ice Bolt')." Turns)\n";
    	  echo "</form>\n";
    	  echo "<form id=\"skill_use\" action=\"skills_mod.php\" method=\"post\" name=\"skill_use\">\n";
    	  echo "<input id=\"command\" type=\"submit\" value=\"Cold Steal\" name=\"command\" class=\"formButton\" />\n";
    	  echo "<input id=\"target\" type=\"hidden\" value=\"$target\" name=\"target\" /><br />\n";
    	  echo "(".$skillListObj->getTurnCost('Cold Steal')." Turns)<br />\n";
    	  echo "</form>";
    	} else if ($class == "White") {
    	  echo "<form id=\"skill_use\" action=\"skills_mod.php\" method=\"post\" name=\"skill_use\">\n";
    	  echo "<input id=\"command\" type=\"submit\" value=\"Sight\" name=\"command\" class=\"formButton\" />\n";
    	  echo "<input id=\"target\" type=\"hidden\" value=\"$target\" name=\"target\" /><br />\n";
    	  echo "(".$skillListObj->getTurnCost('Sight')." Turns)\n";
    	  echo "</form>\n";
    	}
	    echo "    </td>\n";
	} // End of the "viewing someone else's profile" section.	    
	    echo "  </tr>\n";

	    echo "  </table>\n";
	    echo "  </td>\n";
	    echo "</tr>\n";
	}

	echo "</table>\n";

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
	
	
	echo "</div><!-- End player-info -->";
}

include "interface/footer.php";
?>




