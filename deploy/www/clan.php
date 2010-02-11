<?php
require_once(LIB_ROOT."specific/lib_clan.php");
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Clan Panel";

include SERVER_ROOT."interface/header.php";
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
$message                         = in('message');

// *** Used Variables ***

$player_id    = get_user_id();
$username     = get_username();

if ($player_id) {
	$clan         = get_clan_by_player_id($player_id);
}

if ($username) {
	$viewer_level = getLevel($username);
}

// *** Useful Constants ***
define('CLAN_CREATOR_MIN_LEVEL', 15);

if (!$player_id || !$clan) {
	echo "<p class='ninja-notice'>You are not part of any clan.</p>";
} else {
	$self_is_leader = ($clan && (get_clan_leader_id($clan->getID()) == $player_id));

	if ($command == "disband" && $sure == "yes" && $self_is_leader) {	// **** Clan Leader Action Disbanding of the Clan ***
		disbandClan($clan->getID());
		echo "<div class='notice'>Your clan has been disbanded.</div>\n";
		$clan = $self_is_leader = false;
	}

	if ($command == "new") {
		// *** Clan Creation Action ***
		if ($viewer_level >= CLAN_CREATOR_MIN_LEVEL) {
			$default_clan_name = "Clan ".$username;
			$clan              = createClan($player_id, $default_clan_name);
			$command           = "rename"; // *** Shortcut to rename after. ***

			echo "<div class='notice'>You have created a new clan!</div>\n";
		} else {	// *** Level req wasn't met. ***
			echo "<div class='notice'>You do not have enough renown to create a clan. You must be at least level ".CLAN_CREATOR_MIN_LEVEL.".</div>";
		}
	}

	$self_is_leader = ($clan && (get_clan_leader_id($clan->getID()) == $player_id));

	if ($self_is_leader) {
		echo "<div>You are the leader of this clan.</div>";
	}

	if ($message) {
		message_to_clan($message);
		echo "<div id='message-sent' class='ninja-notice'>Message sent.</div>";
	}

	if ($clan) {
		if ($self_is_leader) {
			if ($command == "rename") {
				//Clan Leader Action Rename
				if ($new_clan_name != "" && strlen($new_clan_name) <= 20 && (str_replace(array('/','\'','*','--', '<', '>'), '', $new_clan_name) == $new_clan_name)) {
					// *** The clan doesn't contain any special characters, including apostrophes, asterixes, slashes, and html code.
					echo "<p>Your new clan name is <span style=\"font-weight: bold;\">", renameClan($clan->getID(), $new_clan_name), ".</span></p>\n";
					$clan->setName($new_clan_name);
				} else {
					if (strlen($new_clan_name) >= 21) {
						echo "<div style=\"color:red;\">Your clan name cannot be blank or greater than 20 characters.</div>";
					} elseif ("" == $new_clan_name) {
						echo "<p>(Clan names must be between 1 and 20 characters long.)</p>\n";
					} else {
						echo "<p>(Clan names cannot contain special characters other than a single dash.)</p>";
					}

					echo "<form id=\"clan_rename\" action=\"clan.php\" name=\"clan_rename\">
	                     <div>
	    	            <input id=\"command\" type=\"hidden\" value=\"rename\" name=\"command\">
	    	            <input id=\"new_clan_name\" type=\"text\" name=\"new_clan_name\" class=\"textField\">
	    	            <input type=\"submit\" class=\"formButton\" value=\"Rename Clan\">
	                    </div>
	    	            </form>";
				}
			} else if ($command == "kick") {	//Clan Leader Action Kick a chosen member
				if ($kicked == "") {
					$query = "SELECT player_id, uname FROM players JOIN clan_player ON _player_id = player_id AND _clan_id = :clanID WHERE uname <> :username AND confirmed = 1";
					$statement = DatabaseConnection::$pdo->prepare($query);
					$statement->bindValue(':clanID', $clan->getID());
					$statement->bindValue(':username', $username);
					$statement->execute();

					echo "<form id=\"kick_form\" action=\"clan.php\" method=\"get\" name=\"kick_form\">\n";
					echo "<div>\n";
					echo "Kick: \n";
					echo "<select id=\"kicked\" name=\"kicked\">\n";
					echo "<option value=\"\">--Pick a Member--</option>\n";

					while ($data = $statement->fetch()) {
						$pid  = $data[0];
						$name = $data[1];
						echo "<option value=\"$pid\">$name</option>\n";
					}

					echo "</select>\n";
					echo "<input id=\"command\" type=\"hidden\" value=\"kick\" name=\"command\">\n";
					echo "<input type=\"submit\" value=\"Kick\" class=\"formButton\">\n";
					echo "</div>\n";
					echo "</form>\n";
				} else {	// *** An actual successful kick of a member. ***
					kick($kicked);
					echo "<p>You have removed ".getPlayerName($kicked)." from your clan.</p>";
				}
			} else if ($command == "disband") {	// *** Clan Leader Confirmation of Disbanding of the Clan ***
				if (!$sure) {
					echo "Are you sure you want to continue? This will remove all members from your clan.<br>\n";
					echo "<form id=\"disband\" method=\"get\" action=\"clan.php\" name=\"disband\">\n";
					echo "<div>\n";
					echo "<input type=\"submit\" value=\"Disband\" class=\"formButton\">\n";
					echo "<input id=\"command\" type=\"hidden\" value=\"disband\" name=\"command\">\n";
					echo "<input id=\"sure\" type=\"hidden\" value=\"yes\" name=\"sure\">\n";
					echo "</div>\n";
					echo "</form>\n";
				}
			} else if ($command == "invite") {	// *** Clan Leader Invite Input ***
				if (!$person_invited) {
					echo "Name of potential clan member:<br>\n";
					echo "<form id=\"clan_invite\" action=\"clan.php\" name=\"clan_rename\">\n";
					echo "<div>\n";
					echo "<input id=\"command\" type=\"hidden\" value=\"invite\" name=\"command\">\n";
					echo "<input id=\"person_invited\" type=\"text\" name=\"person_invited\" class=\"textField\">";
					echo "<input type=\"submit\" class=\"formButton\" value=\"Invite\">\n";
					echo "</div>\n";
					echo "</form>\n";
					echo "<hr>\n";
				} else {
					$failure_message = invitePlayer($person_invited, $clan->getID());	// *** Clan leader Invite Action ***

					if ($failure_message == "None.") {
						echo "<p>You have invited $person_invited to join your clan.</p>";
					} else {
						echo "<p>You cannot invite $person_invited.  ", $failure_message, "</p>";
					}
				}
			}

			if ($clan && $self_is_leader)
			{
				echo "<div id='leader-panel'>
	      <div id='leader-panel-title'>", $clan->getName(), " Clan Leader Panel</div>
	        <ul id='leader-options'>
	            <li><a href=\"clan.php?command=invite\">Recruit for your Clan</a></li>
	            <li><a href=\"clan.php?command=rename\">Rename Clan</a></li>
	            <li><a href=\"clan.php?command=disband\">Disband Your Clan</a></li>
	            <li><a href=\"clan.php?command=kick\">Kick a Clan Member</a></li>
	        </ul>
	      </div>";
			}
		} else {
			if ($command == "leave") {	// *** Clan Member Action to Leave their Clan ***
				$query = "DELETE FROM clan_player WHERE _player_id = :playerID";
				$statement = DatabaseConnection::$pdo->prepare($query);
				$statement->bindValue(':playerID', $player_id);
				$statement->execute();

				echo "<p>You have left your clan.</p>";
				die();
			}

			echo "<p>You are currently a member of the ", $clan->getName(), " Clan.</p>";
			echo "<p><a href=\"clan.php?command=leave\" onclick='leave_clan(); return false;'>Leave Current Clan</a></p>";
		}

		if ($command == "msgclan") {	// *** Clan Member Input for Messaging their Entire Clan ***
			echo "<form id='msg_clan' action='clan.php' method='get' name='msg_clan'>
	          <div>
	          Message: <input id=\"message\" type=\"text\" size=\"50\" maxlength=\"1000\" name=\"message\" class=\"textField\">
	          <input type=\"submit\" value=\"Send This Message\" class=\"formButton\">
	          </div>
	          </form>\n";
		}

		if ($clan)
		{
			echo "<ul id='clan-options'>
	            <li><a href=\"clan.php?command=msgclan\">Message Clan Members</a></li>
	            <li><a href=\"clan.php?command=view&amp;clan_id=", $clan->getID(), "\">View Your Clan</a></li>
	        </ul>";
		}
	} else {
		if ($command == "join") {	// *** Clan Joining Action ***
			echo render_clan_join($process, $username, $clan_id_viewed);
		}

		echo "<div>You are not a member of any clan.</div>\n";
		echo "<div><a href=\"clan.php?command=join\">Join a Clan</a></div>\n";
		if ($viewer_level >= CLAN_CREATOR_MIN_LEVEL) {
			//Prevents characters under the level req from seeing clan creation option.
			echo "<div><a href=\"clan.php?command=new\">Start a New Clan</a></div>";
		} else {
			echo "<div>You can start your own clan when you reach level ".CLAN_CREATOR_MIN_LEVEL.".</div>";
		}
	}
}	// End of logged-in display.

if ($command == "view")
{	// *** A view of the member list of any clan ***
	echo render_clan_view($clan_id_viewed);
}

echo render_clan_tags(); // *** Display the clan tags section. ***

include SERVER_ROOT."interface/footer.php";
?>
