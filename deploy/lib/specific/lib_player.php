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

// Display the profile message.
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
    echo "
        <div class='set-bounty centered'>
        <form id=\"set_bounty\" action=\"doshin_office.php\" method=\"post\" name=\"set_bounty\">
        <input id=\"amount\" type=\"text\" size=\"4\" maxlength=\"5\" name=\"amount\" class=\"textField\">
        <input id=\"command\" type=\"submit\" value=\"Offer Bounty\" name=\"command\" class=\"formButton\">
        <input id=\"target\" type=\"hidden\" value=\"{$player_info['uname']}\" name=\"target\">
        </form>
      </div>";
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



// Gives the blacklisted emails, should eventually be from a table.
function get_blacklisted_emails(){
	return array('@hotmail.com', '@hotmail.co.uk', '@msn.com', '@live.com', '@aol.com', '@aim.com', '@yahoo.com', '@yahoo.co.uk');
}

// Gives whitelisted emails, make a table eventually.
function get_whitelisted_emails() {
	return array('@gmail.com');
}

// Return 1 if the email is a blacklisted email, 0 otherwise.
function preconfirm_some_emails($email) {
	// Made the default be to auto-confirm players.
	$res = 1;
	$blacklisted_by = get_blacklisted_emails();
	$whitelisted_by = get_whitelisted_emails();

	foreach ($blacklisted_by AS $loop_domain) {
		if (strpos(strtolower($email), $loop_domain)) {
			return 1;
		}
	}

	foreach ($whitelisted_by AS $loop_domain) {
		if (strpos(strtolower($email), $loop_domain)) {
			return 0;
		}
	}

	return $res;
}

function render_class_select($current) {
    ob_start();
?>
	  <select id="send_class" name="send_class">
	    <option value="">Pick Ninja Color</option>
	    <option value="Red" id='red-class-select' <?php if($current=='Red') { echo 'selected="selected"'; } ?>>Red - Strength</option>
	    <option value="Blue" id='blue-class-select' <?php if($current=='Blue') { echo 'selected="selected"'; } ?>>Blue - Speed</option>
	    <option value="White" id='white-class-select' <?php if($current=='White') { echo 'selected="selected"'; } ?>>White - Healing</option>
	    <option value="Black" id='black-class-select' <?php if($current=='Black') { echo 'selected="selected"'; } ?>>Black - Poison</option>
	  </select>
<?php
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

function display_signup_form($enteredName, $enteredEmail, $enteredClass, $enteredReferral) {
	// ************************* START OF SIGNUP FORM ********************************
	$class_select = render_class_select($enteredClass);
    echo render_template('signup.tpl', array(
        'enteredName'=>$enteredName, 
        'enteredEmail'=>$enteredEmail, 
        'enteredClass'=>$enteredClass, 
        'enteredReferral'=>$enteredReferral, 
        'class_select'=>$class_select, 
        'SYSTEM_MESSENGER_EMAIL'=>SYSTEM_MESSENGER_EMAIL));
} // *** End of function display_signup_form().


function create_player($send_name, $params=array()){
    $sql = new DBAccess();
    $send_email = $params['send_email'];
    $send_pass = $params['send_pass'];
    $send_class = $params['send_class'];
    $preconfirm = (int) $params['preconfirm'];
    $confirm = (int) $params['confirm'];
    $referred_by = $params['referred_by'];
    
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: ".SYSTEM_MESSENGER_NAME." <".SYSTEM_MESSENGER_EMAIL.">\r\n";
	$headers .= "Reply-To: ".SUPPORT_EMAIL_FORMAL_NAME." <".SUPPORT_EMAIL.">\r\n";
	// Create the initial player row.
	$playerCreationQuery= "INSERT INTO players
		 (uname, pname, health, strength, gold, messages, kills, turns, confirm, confirmed,
		  email, class, level,  status, member, days, ip, bounty, clan, clan_long_name, created_date)
		 VALUES
		 ('$send_name','$send_pass','150','5','100','','0','80','$confirm','$preconfirm',
		 '$send_email','$send_class','1','1','0','0','','0','','', now())";
	//  ***  Inserts the choices and defaults into the player table. Status defaults to stealthed. ***
	$sql->Insert($playerCreationQuery);
	
	//  ***  Sends out the confirmation email to the chosen email address.  ***
	$_to = "$send_email";
	$_subject = "NinjaWars Account Sign Up";
	$_body = render_template('signup_email_body.tpl', array(
	        'send_name'=>$send_name, 
	        'confirm'=>$confirm,
	        'send_class'=>$send_class,
	        'WEB_ROOT'=>WEB_ROOT,
	        'SUPPORT_EMAIL'=>SUPPORT_EMAIL));
	$_from = "$headers";
	// *** Create message object.
	$message = new Nmail($_to, $_subject, $_body, $_from);
    if(DEBUG){$message->dump = true; }
	$sent = false; // By default, assume failure.
	$sent = $message->send();
	
	// TODO: Need an in-game error logging.
    return $sent;
}

function confirm_player($player_name, $confirmation=0, $autoconfirm=false){
    $sql = new DBAccess();
	// Preconfirmed or the email didn't send, so automatically confirm the player.
	$require_confirm = $autoconfirm? '' : "and confirm = '$confirmation'";
	$up = "update players set confirmed = 1, confirm='55555' where uname = '".$player_name."' "
	    .$require_confirm;
	$up_res = $sql->Update($up);
    $res = $autoconfirm? true : !!$up_res;
	return $res;
}



function validate_signup($enteredName, $enteredEmail, $enteredClass, $enteredReferral, $enteredPass) {
	$successful = false;
	$sql = $GLOBALS['sql'];
	assert($sql);
	$send_name  = $enteredName;
	$send_pass  = $enteredPass;
	$send_class = $enteredClass;
	$send_email = $enteredEmail;
	$referred_by = $enteredReferral;

	echo "Your responses:<br> Name - $send_name,<br>
		 Password - ".(isset($send_pass)? "***yourpassword***" : "NO PASSWORD").",<br>
		 Class - $send_class,<br>
		 Email - $send_email,<br>
		 Site Referred By - $referred_by<br><br>\n";

	//  *** Requirement checking Section  ***

	if ($send_name != "" && $send_pass != "" && $send_email != "" && $send_class != "") {  //When everything is non-blank.
		$check_name = 0;
		$check_email = 0;
		$sql->QueryItem("SELECT uname FROM players WHERE uname = '$send_name'");
		$check_name  = $sql->getRowCount();
		$sql->QueryItem("SELECT email FROM players WHERE email = '$send_email'");
		$check_email = $sql->getRowCount();
		
        // Validate the username symbols!
		$username_error = validate_username($send_name);
		
		if ($username_error) {
			echo $username_error;
		} else {  //when all the name requirement errors didn't trigger.
			$send_name = trim($send_name);  // Just cuts off any white space at the end.
			$filter = new Filter();
			$send_name = $filter->toUsername($send_name); // Filter any un-whitelisted characters.
			echo "Phase 1 Complete: Name passes requirements.<hr>\n";

			// Validate the password!
			$password_error = validate_password($send_pass);

			if ($password_error) {
				echo $password_error;
			} else {
				$send_pass = trim($send_pass); // *** Trims any extra space off of the password.
				$send_pass = $filter->toPassword($send_pass); // Filter any un-whitelisted characters.
				echo "Phase 2 Complete: Password passes requirements.<hr>\n";

				if (FALSE) { // CURRENTLY NO BLOCKED EMAIL SERVICES strstr($send_email, "@") == "@aol.com" || strstr($send_email, "@") == "@netscape.com" || strstr($send_email, "@") == "@aim.com"
				    //Throws error if email from blocked domain.
					echo "Phase 3 Incomplete: We cannot currently accept @aol.com, @netscape.com, or @aim.com email addresses.";
				} elseif (!eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", trim($send_email))) {
					echo "Phase 3 Incomplete: The email address (".htmlentities($send_email).") 
					    must contain an @ symbol and a domain name to be valid.";
				} else if ($check_name == 0 && $check_email == 0 && $send_name != "SysMsg" && $send_name != "NewUserList") {
				    //Uses previous query to make sure name and email aren't duplicates.
					echo "Phase 3 Complete: Username and Email are unique.<br><hr>\n";
					if ($send_class != 'Red' && $send_class != 'Blue' && $send_class != 'White' && $send_class != 'Black') {
						echo "Phase 4 Incomplete: No proper class was specified.<br>";
					} else {
						echo "Phase 4 Complete: Class was specified.<br><hr>";
					    						
						// *** Signup is successful at this point  ***
						$preconfirm = 0;
						$preconfirm = preconfirm_some_emails($send_email);

						if (!$preconfirm) { /* not blacklisted by, so require a normal email confirmation */
							echo "Phase 5: When you receive an email from SysMsg,
							 it will describe how to activate your account.<br><br>\n";
						}
						// The preconfirmation message occurs later.
						$confirm = rand(1000,9999); //generate confirmation code
						
						// Use the function from lib_player
						$player_params = array(
						    'send_email'=>$send_email, 
						    'send_pass'=>$send_pass,
						    'send_class'=>$send_class,
						    'preconfirm'=>$preconfirm, 
						    'confirm'=>$confirm,
						    'referred_by'=>$referred_by);
						$successful = create_player($send_name, $player_params); // Create the player.
						if(!$successful){
						    echo "There was a problem with creating a player account.  Please contact us as below: ";
						} else {
    						if (!$preconfirm) {
    							//  *** Continues the page display ***
    							echo "Confirmation email has been sent to <b>".$send_email."</b>.  <br>
    				  					Be sure to also check for the email in any \"Junk Mail\" or \"Spam\" folders.
    				  					Delivery typically takes less than 15 minutes.";
    						} else {
    						    // Use the confirm function from lib_player.
    						    confirm_player($send_name, false, true); // name, no confirm #, just autoconfirm.
    							echo "<p>Account with the login name \"".$send_name
    							."\" is now confirmed!  Please login on the login bar of the ninjawars.net page.</p>";
    						}
    						echo "<p>Only one account per person is allowed.</p>";
						}
						echo "If you require help use the forums at <a href='".WEB_ROOT."forum/'>"
						    .WEB_ROOT."forum/</a> or email: ".SUPPORT_EMAIL;
					}	// *** End of class checking.
				} else {	// Default, displays when the username or email are not unique.
					$what = ($check_email != 0 ? "Email" : "Username");

					echo "Phase 3 Incomplete: That $what is already in use. Please choose a different one.\n";
				}
			}
		}
	} else {  //  ***  Response for when nothing was submitted.  ***
		echo "Phase 1 Incomplete: You did not correctly fill out all the necessary information.\n";
	}
	
	//This is the final signup return section, which only shows if a successful 
	//insert and confirmation number has -not- been acheived.
	
	echo "<br><br>";
	return $successful;
} // *** End of validate_signup() function.

?>
