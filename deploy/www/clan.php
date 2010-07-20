<?php
require_once(LIB_ROOT.'specific/lib_clan.php');
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = 'Clan Panel';

include SERVER_ROOT.'interface/header.php';
$dbconn = DatabaseConnection::getInstance();

?>
<script type="text/javascript" src="<?=WEB_ROOT?>js/clan.js"></script>

<h1 id='clan-page-title'>Clan Panel</h1>

<?php
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

// *** Used Variables ***

$player_id    = get_char_id();
$player       = new Player($player_id);
$char_info    = get_player_info();
$username     = $char_info['uname'];

$leader_id = whichever(get_clan_leader_id($clan_id_viewed), null);
if($clan_id_viewed){
    $viewed_clan_data = get_clan($clan_id_viewed);
}
$self_is_leader = ($leader_id && $player_id && $leader_id == $player_id);

if ($player_id) {
    // These are only used for logged in viewers.
	$clan         = get_clan_by_player_id($player_id); // Own clan.
	$viewer_level = $player->vo->level;
    $can_create_a_clan = ($viewer_level >= 	CLAN_CREATOR_MIN_LEVEL);
}

if (!$player_id) {
    $action_message = "You are not part of any clan.";
	echo '<p class="ninja-notice">You are not part of any clan.</p>';
} else {
	$self_is_leader = ($clan && (get_clan_leader_id($clan->getID()) == $player_id));
	$self_clan_id = clan_id($player_id);
	
    if($self_is_leader){
        if($new_clan_avatar_url){
            save_clan_avatar_url($new_clan_avatar_url, $self_clan_id);
        }
        if($new_clan_description){
            save_clan_description($new_clan_description, $self_clan_id);
        }
    }

	if ($command == 'disband' && $sure == 'yes' && $self_is_leader) {	// **** Clan Leader Action Disbanding of the Clan ***
		$clan->disband();
        $action_message = "Your clan has been disbanded.";
		echo "<div class='notice'>Your clan has been disbanded.</div>";
		$clan = $self_is_leader = false;
	}

	if ($command == 'new') {
		// *** Clan Creation Action ***
		if ($can_create_a_clan) {
			$default_clan_name = 'Clan '.$username;
			$clan              = createClan($player_id, $default_clan_name);
			$command           = 'rename'; // *** Shortcut to rename after. ***
            $action_message = "You have created a new clan!";
			echo "<div class='notice'>You have created a new clan!</div>";
		} else {	// *** Level req wasn't met. ***
		    $action_message = "You do not have enough renown to create a clan. You must be at least level ".CLAN_CREATOR_MIN_LEVEL.".";
			echo "<div class='notice'>{$action_message}</div>";
		}
	}

	$self_is_leader = ($clan && (get_clan_leader_id($clan->getID()) == $player_id));

	if ($self_is_leader) {
		echo '<div>You are the leader of this clan.</div>';
	}

	if ($message) {
		message_to_clan($message);
		$action_message = "Message sent.";
		echo "<div id='message-sent' class='ninja-notice'>Message sent.</div>";
	}
	
	


	if ($clan) {
		if ($self_is_leader) {
		    
		    
			if ($command == 'rename') {
			    $clan_rename_requested = true;
				//Clan Leader Action Rename
				if (is_valid_clan_name($new_clan_name)) {
					// *** Rename the clan if it is valid.
					$clan_renamed = true;
					$new_clan_name = renameClan($clan->getID(), $new_clan_name);
					
					echo "<p>Your new clan name is <strong>{$new_clan_name}.</strong></p>";
					$clan->setName($new_clan_name); // Store the renamed value for the rest of this document.
				} else {
				    
				    echo "
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
				}
			} else if ($command == 'kick') {	
			    //Clan Leader Action Kick a chosen member
				if ($kicked == '') {

					$query = 'SELECT player_id, uname FROM players JOIN clan_player ON _player_id = player_id AND _clan_id = :clanID WHERE uname <> :username AND confirmed = 1';
					$statement = query_resultset($query, array(':clanID'=>array($clan->getID(), PDO::PARAM_INT), ':username'=>$username));

				    $display_clan_kick_form = true;

					echo "<form id='kick_form' action='clan.php' method='get' name='kick_form'>
					<div>
					Kick: 
					<select id='kicked' name='kicked'>
					<option value=''>--Pick a Member--</option>";

					while ($data = $statement->fetch()) {
						$pid  = $data[0];
						$name = $data[1];
						echo "<option value='$pid'>$name</option>";
					}

					echo "</select>
					<input id='command' type='hidden' value='kick' name='command'>
					<input type='submit' value='Kick' class='formButton'>
					</div>
					</form>";
					
					
				} else {	// *** An actual successful kick of a member. ***
					$kicked_name = get_char_name($kicked);
					$clan->kickMember($kicked);
					
					$action_message = "You have removed {$kicked_name} from your clan.";
					echo '<p>You have removed {$kicked_name|escape} from your clan.</p>';
				}
			} else if ($command == 'disband') {	// *** Clan Leader Confirmation of Disbanding of the Clan ***
				if (!$sure) {
				    $display_disband_form = true;
					echo "
					Are you sure you want to continue? This will remove all members from your clan.<br>
					<form id='disband' method='get' action='clan.php' name='disband'>
    					<div>
        					<input type='submit' value='Disband' class='formButton'>
        					<input id='command' type='hidden' value='disband' name='command'>
        					<input id='sure' type='hidden' value='yes' name='sure'>
    					</div>
					</form>";
				} elseif ($sure == 'yes' && $self_is_leader) {	// **** Clan Leader Action Disbanding of the Clan ***
            		$clan->disband();
            		$clan_disbanded = true;
                    $action_message = "Your clan has been disbanded.";
            		echo "<div class='notice'>Your clan has been disbanded.</div>";
            		$clan = $self_is_leader = $clan_id = false;
            	}
			} else if ($command == 'invite') {	// *** Clan Leader Invite Input ***
			    if($person_invited){
				    $char_id_invited = get_char_id($person_invited);
				    if(!$char_id_invited){
				        $action_message = "No such ninja as <i>{$person_invited}</i> exists.";
				    } else {
    					$invite_failure_message = inviteChar($char_id_invited, $clan->getID());	// *** Clan leader Invite Action ***
    					if(!$invite_failure_message){
    					    $action_message  = "You have invited {$person_invited} to join your clan.";
    					    $invite_failed = true;
    					} else {
    					    $action_message = "You cannot invite $person_invited.  {$invite_failure_message}";
    					}
					}			
				}
				
				// Remove me in template:
				echo "<div class='notice'>{$action_message}</div>";
				
			    $display_invite_form = true;
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
			}



			if ($clan && $self_is_leader){
            // ******* CLAN LEADER OPTIONS ******
            
                $display_clan_leader_options = true;
            
            			
        $clan_avatar_current = whichever($new_clan_avatar_url, @$viewed_clan_data['clan_avatar_url']);
//        var_dump($new_clan_description);
        $clan_description_current = whichever($new_clan_description, @$viewed_clan_data['description']);
        
	echo "
	<!-- Checks whether the viewer is the leader to display these sections.  -->
	<div id='leader-panel'>
	      <div id='leader-panel-title'>", $clan->getName(), " Leader Actions</div>
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
	      
			}
		} else {
		
		// ***  NON LEADER CLAN MEMBER OPTIONS ***
		
		
			if ($command != 'leave') {	
			    // *** Clan Member Action to Leave their Clan ***
			    
			    
				$query = "DELETE FROM clan_player WHERE _player_id = :playerID";
				$statement = DatabaseConnection::$pdo->prepare($query);
				$statement->bindValue(':playerID', $player_id);
				$statement->execute();
				
				$clan_id = $clan = 
				
				
				
                $action_message = "You have left your clan.";
				echo '<p>You have left your clan.</p>';
				die();
			} else {
    			echo "<p>You are currently a member of the ", $clan->getName(), " Clan.</p>
    			<p><a href='clan.php?command=leave' onclick='leave_clan(); return false;'>Leave Current Clan</a></p>";
    		}
		}

		if ($command == 'msgclan') {	// *** Clan Member Input for Messaging their Entire Clan ***
			echo "<form id='msg_clan' action='clan.php' method='get' name='msg_clan'>
	          <div>
	          Message: <input id='message' type='text' size='50' maxlength='1000' name='message' class='textField'>
	          <input type='submit' value='Send This Message' class='formButton'>
	          </div>
	          </form>";
		}

		if ($clan){
			echo "<ul id='clan-options'>
	            <li><a href='clan.php?command=msgclan'>Message Clan Members</a></li>
	            <li><a href='clan.php?command=view&amp;clan_id=", $clan->getID(), "'>View Your Clan</a></li>
	        </ul>";
		}
	} else {
	
	    // ****** NON-CLAN-MEMBER *******
	
		if ($command == "join") {	// *** Clan Joining Action ***
			echo render_clan_join($process, $username, $clan_id_viewed);
		}

		echo "<div>You are not a member of any clan.</div>
		<div><a href='clan.php?command=join'>View clans available to join</a></div>";
		if($clan_id_viewed){
		    $viewed_clan = get_clan($clan_id_viewed);
		    $viewed_clan_name = $viewed_clan['clan_name'];
    		echo "<div><a href='clan.php?command=join&amp;clan_id=". $clan_id_viewed ."&process=1'>
    		        Send a request to join Clan ". $viewed_clan_name ."
    		        </a></div>"; 
    	}
    	
    	
    	
    	
		if ($can_create_a_clan) {
			//Prevents characters under the level req from seeing clan creation option.
			echo "<div><a href='clan.php?command=new'>Start a New Clan</a></div>";
		} else {
			echo "<div>You can start your own clan when you reach level ".CLAN_CREATOR_MIN_LEVEL.".</div>";
		}
	}
}	// End of logged-in display.



if ($command == "view"){    
	// *** A view of the member list of any clan ***
	$clan_view = render_clan_view($clan_id_viewed);
	echo $clan_view;
}

$clan_tags = render_clan_tags(); // *** Display the clan tags section. ***

echo $clan_tags; 

include SERVER_ROOT.'interface/footer.php';
?>
