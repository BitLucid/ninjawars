<?php
require_once(LIB_ROOT.'specific/lib_clan.php');
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = 'Clans';





if ($error = init($private, $alive)) {
	display_error($error);
	die();
}

//include SERVER_ROOT.'interface/header.php';
$dbconn = DatabaseConnection::getInstance();
/*
?>
<script type="text/javascript" src="<?=WEB_ROOT?>js/clan.js"></script>

<h1 id='clan-page-title'>Clan Panel</h1>

<?php
*/
// *** Possible Input Values ***

$command                         = in('command');
$process                         = in('process');
$clan_name_viewed                = in('clan_name', ''); // View that clan name.
$clan_id_viewed                  = in('clan_id', null); // View that clan
$new_clan_name                   = in('new_clan_name', '');
$sure                            = in('sure', '');
$kicked                          = in('kicked', '');
$person_invited                  = in('person_invited', '');
$message                         = in('message', null, null); // Don't filter messages sent in.
$new_clan_avatar_url                 = in('clan-avatar-url');
$new_clan_description                 = in('clan-description');

$action_message = null; // Action or error message for template.


// *** Useful Constants ***
define('CLAN_CREATOR_MIN_LEVEL', 15);
$clan_creator_min_level = CLAN_CREATOR_MIN_LEVEL; // For the template.


// *** Used Variables ***

$player_id    = get_char_id();
$player       = $player_id? new Player($player_id) : null;
$char_info    = $player_id? get_player_info() : null;
$username     = @$char_info['uname'];


if($clan_id_viewed){
    $viewed_clan_data = get_clan($clan_id_viewed);
}

$own_clan_id = null;


// Logical cascade: No player id? Display error message.
// No clan? Display no clan message, clan list, join link, creation limit
// Clan member not leader? Display clan list, view clan link, msg link, leave clan link.
// Clan leader -> Display leader options (make expand/contract), view clan, msg, disband.

// TODO: Made the clan tags list hidden&toggleable when leader or view options are being displayed.
// TODO: Make leader options hidden&toggle-displayable.

$own_clan_id = null;
$own_clan_info = null;
$own_clan_name = null;
$own_clan_obj = null;

$led_clan_info = null;
$leader_of_own_clan = null;
$led_clan_id = null;
$self_is_leader = null;
$leader_of_viewed_clan = null;


if ($player_id) {
    // ***** A LOGGED IN CHARACTER *****


	$viewer_level = $player->vo->level;
    $can_create_a_clan = ($viewer_level >= 	CLAN_CREATOR_MIN_LEVEL);



    $own_clan_id = clan_id($player_id); 
    if($own_clan_id){
        // Is a member of a clan.
        $own_clan_info = clan_info($own_clan_id);
        $own_clan_name = $own_clan_info['clan_name'];
    	$own_clan_obj  = get_clan_by_player_id($player_id); // Own clan.
    	
    	
    	
    	$led_clan_info = clan_char_is_leader_of($player_id);
        $leader_of_own_clan = !empty($led_clan_info)? true : false;    	
    	if($leader_of_own_clan){
            $led_clan_id = whichever($led_clan_info['clan_id'], null);
            $leader_of_viewed_clan = 
                ($clan_id_viewed && !empty($led_clan_info) && $clan_id_viewed == $led_clan_info['clan_id'])?
                true : false;
                
        }
    	    	
    }

	
}


if (!$player_id) {
    $action_message = "You are not part of any clan.";
	////   echo '<p class="ninja-notice">You are not part of any clan.</p>';
} else {
	
    if($leader_of_own_clan){
        // Saving incoming changes to clan leader edits.
        if($new_clan_avatar_url){
            save_clan_avatar_url($new_clan_avatar_url, $own_clan_id);
        }
        if($new_clan_description){
            save_clan_description($new_clan_description, $own_clan_id);
        }
    }
    
    
    // Commands Section

	if ($command == 'new') {
		// *** Clan Creation Action ***
		if ($can_create_a_clan) {
			$default_clan_name = 'Clan '.$username;
			$clan              = createClan($player_id, $default_clan_name);
			$command           = 'rename'; // *** Shortcut to rename after. ***
            $action_message = "You have created a new clan!";
			////   echo "<div class='notice'>You have created a new clan!</div>";
		} else {	// *** Level req wasn't met. ***
		    $action_message = "You do not have enough renown to create a clan. You must be at least level ".CLAN_CREATOR_MIN_LEVEL.".";
			////   echo "<div class='notice'>{$action_message}</div>";
		}
	}

	//if ($leader_of_viewed_clan) {
		////   echo '<div>You are the leader of this clan.</div>';
	//}

	if ($message) {
		message_to_clan($message);
		$action_message = "Message sent.";
		////   echo "<div id='message-sent' class='ninja-notice'>Message sent.</div>";
	}
	
	


	if ($own_clan_id) {
		if ($leader_of_own_clan) {
		    
		    
			if ($command == 'rename') {
				//Clan Leader Action Rename
				if (is_valid_clan_name($new_clan_name)) {
					// *** Rename the clan if it is valid.
					$clan_renamed = true;
					$new_clan_name = rename_clan($own_clan_obj->getID(), $new_clan_name);
					
					////   echo "<p>Your new clan name is <strong>{$new_clan_name}.</strong></p>";
					$own_clan_obj->setName($new_clan_name); // Store the renamed value for the rest of this document.
				} else {
				    
				    /*   echo "
				    <div class='notice'>
				        Clan names must be from 3 to 24 characters, and can only contain letters, numbers, spaces, underscores, or dashes, although you can request exceptions if they're fun.
				    </div>                    
					<form id='clan_rename' action='clan.php' name='clan_rename'>
	                     <div>
    	    	            <input id='command' type='hidden' value='rename' name='command'>
    	    	            <input id='new_clan_name' type='text' name='new_clan_name' class='textField'>
    	    	            <input type='submit' class='formButton' value='Rename Clan'>
	                    </div>
    	            </form>";
    	            
    	            */
				}
			} else if ($command == 'kick') {	
			
			
			
			    //Clan Leader Action Kick a chosen member
				if ($kicked == '') {
				
				    // Get the member info for the select dropdown list.
                    $members_and_ids = clan_member_names_and_ids((int) $own_clan_id, (int) get_char_id());
                    
                    // There's a bug with this request.

                    /*
					echo "<form id='kick_form' action='clan.php' method='get' name='kick_form'>
					<div>
					Kick: 
					<select id='kicked' name='kicked'>
					<option value=''>--Pick a Member--</option>";
					

					echo "</select>
					<input id='command' type='hidden' value='kick' name='command'>
					<input type='submit' value='Kick' class='formButton'>
					</div>
					</form>";
					*/
					
				} else {	// *** An actual successful kick of a member. ***
					$kicked_name = get_char_name($kicked);
					$own_clan_obj->kickMember($kicked);
					
					$action_message = "You have removed ".htmlentities($kicked_name)." from your clan.";
					/// echo '<p>You have removed {$kicked_name|escape} from your clan.</p>';
				}
			} else if ($command == 'disband') {	// *** Clan Leader Confirmation of Disbanding of the Clan ***
				if (!$sure) {
				    $display_disband_form = true;
					/* echo "
					Are you sure you want to continue? This will remove all members from your clan.<br>
					<form id='disband' method='get' action='clan.php' name='disband'>
    					<div>
        					<input type='submit' value='Disband' class='formButton'>
        					<input id='command' type='hidden' value='disband' name='command'>
        					<input id='sure' type='hidden' value='yes' name='sure'>
    					</div>
					</form>";
					
					*/
				} elseif ($sure == 'yes' && $leader_of_own_clan) {	// **** Clan Leader Action Disbanding of the Clan ***
            		$own_clan_obj->disband();
            		$clan_disbanded = true;
                    $action_message = "Your clan has been disbanded.";
            		/// echo "<div class='notice'>Your clan has been disbanded.</div>";

                    $own_clan_id = null;
                    $own_clan_info = null;
                    $own_clan_name = null;
                    $own_clan_obj = null;

                    $led_clan_info = null;
                    $leader_of_own_clan = null;
                    $led_clan_id = null;
                    $self_is_leader = null;
                    $leader_of_viewed_clan = null;

            	}
			} else if ($command == 'invite') {	// *** Clan Leader Invite Input ***
			    if($person_invited){
				    $char_id_invited = get_char_id($person_invited);
				    if(!$char_id_invited){
				        $action_message = "No such ninja as <i>".htmlentities($person_invited)."</i> exists.";
				    } else {
    					$invite_failure_message = inviteChar($char_id_invited, $own_clan_obj->getID());	// *** Clan leader Invite Action ***
    					if(!$invite_failure_message){
    					    $action_message  = "You have invited {$person_invited} to join your clan.";
    					} else {
    					    $action_message = "You cannot invite $person_invited.  {$invite_failure_message}";
    					}
					}			
				}
				
				/*
				// Remove me in template:
				echo "<div class='notice'>{$action_message}</div>";
				
				echo "
				    Name of potential clan member:<br>
    				<form id='clan_invite' action='clan.php' name='clan_rename'>
        				<div>
        				<input id='command' type='hidden' value='invite' name='command'>
        				<input id='person_invited' type='text' name='person_invited' class='textField'>
        				<input type='submit' class='formButton' value='Invite'>
        				</div>
    				</form>
				<hr>";
				
				*/
			} // End of invite command.



			if ($leader_of_own_clan){
            // ******* CLAN LEADER OPTIONS ******
            
            
            $own_clan_data = get_clan($own_clan_id);
            
            			
        $clan_avatar_current = whichever($new_clan_avatar_url, @$own_clan_data['clan_avatar_url']);
//        var_dump($new_clan_description);
        $clan_description_current = whichever($new_clan_description, @$own_clan_data['description']);
        
        /*
	echo "
	<!-- Checks whether the viewer is the leader to display these sections.  -->
	<div id='leader-panel'>
	      <div id='leader-panel-title'>", $own_clan_name, " Leader Actions</div>
	        <ul id='leader-options'>
	            <li><a href='clan.php?command=invite'>Recruit for your Clan</a></li>
	            <li><a href='clan.php?command=rename'>Rename Clan</a></li>
	            <li><a href='clan.php?command=disband'>Disband Your Clan</a></li>
	            <li><a href='clan.php?command=kick'>Kick a Clan Member</a></li>
	        </ul>
	      
	    <div>
	    <div>Clan Image</div>
	    To create a clan avatar, upload an image to <a href='http://www.imageshack.com'>imageshack.com</a>
    	    <form>
    	        <input type='hidden' name='command' value='view'>
    	        <input type='hidden' name='clan_id' value='".htmlentities($self_clan_id)."'>
    	        Then put the image's full url here:
    	        <input name='clan-avatar-url' type='text' value='".htmlentities($clan_avatar_current)."'>
    	        (Image can be .jpg or .png)
	        <div>Clan Message</div>
    	        Change your clan description below:
    	        <textarea name='clan-description'>".htmlentities($clan_description_current)."</textarea>
    	        <input type='submit'>
	        </form>
	        
	    </div>
	        
	        
    </div><!-- End of leader-panel -->
	        ";
	        
	        */
	      
			}
		} else {
		
		// ***  NON LEADER CLAN MEMBER OPTIONS ***
		
		
			if ($command == 'leave') {	
			    // *** Clan Member Action to Leave their Clan ***
			    
			    
				$query = "DELETE FROM clan_player WHERE _player_id = :playerID";
				$statement = DatabaseConnection::$pdo->prepare($query);
				$statement->bindValue(':playerID', $player_id);
				$statement->execute();
				
				$clan_id = $clan = null;
				
				
				
                $action_message = "You have left your clan.";
				 echo '<p>You have left your clan.</p>';
				 
				 

                $own_clan_id = null;
                $own_clan_info = null;
                $own_clan_name = null;
                $own_clan_obj = null;
				 
				die();
			} 
			//else {
    			/* echo "<p>You are currently a member of the ", $own_clan_name, " Clan.</p>
    			<p><a href='clan.php?command=leave' onclick='leave_clan(); return false;'>Leave Current Clan</a></p>";
    			*/
    		//}

    		if ($command == 'msgclan') {	// *** Clan Member Input for Messaging their Entire Clan ***
    			/*echo "<form id='msg_clan' action='clan.php' method='get' name='msg_clan'>
    	          <div>
    	          Message: <input id='message' type='text' size='50' maxlength='1000' name='message' class='textField'>
    	          <input type='submit' value='Send This Message' class='formButton'>
    	          </div>
    	          </form>";
    	          */
    		}


            /*
			echo "<ul id='clan-options'>
	            <li><a href='clan.php?command=msgclan'>Message Clan Members</a></li>
	            <li><a href='clan.php?command=view&amp;clan_id=", $clan->getID(), "'>View Your Clan</a></li>
	        </ul>";
	        */
		} // End of non-leader clan options.
	} else { 
	
	    // ****** NOT-MEMBER OF ANY CLAN *******
	
		if ($command == "join") {	// *** Clan Joining Action ***
			$clan_join_section = render_clan_join($process, $username, $clan_id_viewed);
			//echo $clan_join_section;
		} // End of join command code.

        /*
		echo "<div>You are not a member of any clan.</div>
		<div><a href='clan.php?command=join'>View clans available to join</a></div>";
		
		*/
		if($clan_id_viewed){
		    // Provide a link to join any clan that you're currently viewing.
		    $viewed_clan = get_clan($clan_id_viewed);
		    $viewed_clan_name = $viewed_clan['clan_name'];
		    
		    /*
    		echo "<div><a href='clan.php?command=join&amp;clan_id=". $clan_id_viewed ."&process=1'>
    		        Send a request to join Clan ". $viewed_clan_name ."
    		        </a></div>"; 
    		        
    		*/
    	}// End of clan_id_viewed as a non-member code.
    	
    	
    	
    	
		//if ($can_create_a_clan) {
			//Prevents characters under the level req from seeing clan creation option.
			// echo "<div><a href='clan.php?command=new'>Start a New Clan</a></div>";
		//} 
		//else {
			//   echo "<div>You can start your own clan when you reach level ".CLAN_CREATOR_MIN_LEVEL.".</div>";
		//}
		
		
		
		
	} // End of not-a member code
}	// End of logged-in code



if ($command == "view"){    
	// *** A view of the member list of any clan ***
	$clan_view = render_clan_view($clan_id_viewed);
	//  echo $clan_view;
}

$clan_tags = render_clan_tags(); // *** Display all the clans in their tag list. ***

//  echo $clan_tags; 

display_page('clan.tpl', $page_title, get_defined_vars(), array('quickstat'=>false));
//include SERVER_ROOT.'interface/footer.php';
?>
