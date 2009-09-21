<?php
require_once(LIB_ROOT."specific/lib_tags.php");
require_once(LIB_ROOT."specific/lib_clan.php");
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Clan Panel";

// What a horrible set of code this is.

include SERVER_ROOT."interface/header.php";

?>
<script type="text/javascript" src="<?=WEB_ROOT?>js/clan.js"></script>

<div id='clan-page-title' class="brownHeading">Clan Panel</div>

<?php
$command                         = in('command');
$process                         = in('process');
$clan_name                       = in('clan_name', ''); // View that clan name.
$clan_long_searched              = in('clan_long_name', null, 'none'); // View that clan long name.
$new_clan_name                   = in('new_clan_name', '');
$sure                            = in('sure', '');
$kicked                          = in('kicked', '');
$person_invited                  = in('person_invited', '');
$clan                            = getClan($username);
$player_clan_long_name           = getClanLongName($username);
$viewer_level                    = getLevel($username);
$clan_creation_level_requirement = 15;

$message = in('message');
if($message){
    message_to_clan($message);
    echo "<div id='message-sent' class='ninja-notice'>Message sent.</div>";
}

if ($command == "new") { // *** Clan Creation Action ***
	if ($viewer_level > $clan_creation_level_requirement) {
		setClan($username, $username);
		$default_clan_name = "Clan_".$username;
		renameClan($username,$default_clan_name);
		$command = "rename"; // *** Shortcut to rename after. ***
		$clan = getClan($username);
		echo "<div class='notice'>You have created a new clan!</div><p>Name your clan: </p>\n";
	} else { // *** Level req wasn't met. ***
		echo "<div class='notice'>You do not have enough renown to create a clan.</div>";
	}
}

if ($clan  != "") {
	if ($clan == $username) {
		if ($command == "rename" ) {     //Clan Leader Action Rename
			if ($new_clan_name != "" && strlen($new_clan_name) <= 20 and ( str_replace(array('/','\'','*','--', '<', '>'), '', $new_clan_name) ==$new_clan_name)) {
    	    // *** The clan doesn't contain any special characters, including apostrophes, asterixes, slashes, and html code.
				echo "<p>Your new clan name is <span style=\"font-weight: bold;\">".renameClan($username,$new_clan_name).".</span></p>\n";
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
		} else if ($command == "kick") {              //Clan Leader Action Kick a chosen member
			if ($kicked == "") {
				$sql->Query("SELECT uname FROM players WHERE clan = '$clan' and uname <> '$username' and confirmed = 1");
				$members =  $sql->data;

				echo "<form id=\"kick_form\" action=\"clan.php\" method=\"get\" name=\"kick_form\">\n";
				echo "<div>\n";
				echo "Kick: \n";
				echo "<select id=\"kicked\" name=\"kicked\">\n";
				echo "<option value=\"\">--Pick a Member--</option>\n";

				for ($i = 0; $i < $sql->rows; $i++) {
					$sql->Fetch($i);
					$name = $sql->data[0];
					echo "<option value=\"$name\">$name</option>\n";
				}

				echo "</select>\n";
				echo "<input id=\"command\" type=\"hidden\" value=\"kick\" name=\"command\">\n";
				echo "<input type=\"submit\" value=\"Kick\" class=\"formButton\">\n";
				echo "</div>\n";
				echo "</form>\n";
			} else {	// *** An actual successful kick of a member. ***
				kick($kicked);
				echo "<p>You have removed $kicked from your clan.</p>";
			}
		} else if ($command == "disband") {	// *** Clan Leader Confirmation of Disbanding of the Clan ***
			if (!$sure) {
				echo "Are you sure you want to continue? This will remove all members from your clan.<br />\n";
				echo "<form id=\"disband\" method=\"get\" action=\"clan.php\" name=\"disband\">\n";
				echo "<div>\n";
				echo "<input type=\"submit\" value=\"Disband\" class=\"formButton\">\n";
				echo "<input id=\"command\" type=\"hidden\" value=\"disband\" name=\"command\">\n";
				echo "<input id=\"sure\" type=\"hidden\" value=\"yes\" name=\"sure\">\n";
				echo "</div>\n";
				echo "</form>\n";
			} else if ($sure == "yes") {	// **** Clan Leader Action Disbanding of the Clan ***
				disbandClan($username);
				die("<div class='notice'>Your clan has been disbanded.</div>\n");
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
				$failure_message = invitePlayer($person_invited, $username);	// *** Clan leader Invite Action ***

				if ($failure_message == "None.") {
					echo "<p>You have invited $person_invited to join your clan.</p>";
				} else {
					echo "<p>You cannot invite $person_invited.  ".$failure_message."</p>";
				}
			}
		}

		echo "<div id='leader-panel'>
      <div id='leader-panel-title'>$player_clan_long_name Clan Leader Panel</div>
        <ul id='leader-options'>
            <li><a href=\"clan.php?command=invite\">Recruit for your Clan</a></li>
            <li><a href=\"clan.php?command=rename\">Rename Clan</a></li>
            <li><a href=\"clan.php?command=disband\">Disband Your Clan</a></li>
            <li><a href=\"clan.php?command=kick\">Kick a Clan Member</a></li>
        </ul>
      </div>";

	} else {
		if ($command == "leave") {	// *** Clan Member Action to Leave their Clan ***
			setClan($username,"");
			setClanLongName($username,"");

			echo "<p>You have left your clan.</p>";
			die();
		}

		echo "<p>You are currently a member of the $clan Clan.</p>";
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

	echo "<ul id='clan-options'>
            <li><a href=\"clan.php?command=msgclan\">Message Clan Members</a></li>
            <li><a href=\"clan.php?command=view&amp;clan_name=$clan\">View Your Clan</a></li>
        </ul>";
} else {
	if ($command == "join") {	// *** Clan Joining Action ***
		echo render_clan_join($process, $username, $clan_name);
	}

	echo "<div>You are not a member of any clan.</div>\n";
	echo "<div><a href=\"clan.php?command=join\">Join a Clan</a></div>\n";
	if ($viewer_level >= $clan_creation_level_requirement) {
		//Prevents characters under the level req from seeing clan creation option.
		echo "<div><a href=\"clan.php?command=new\">Start a New Clan</a></div>";
	} else {
		echo "<div>You can start your own clan when you reach level $clan_creation_level_requirement.</div>";
	}
}

if ($command == "view") {	// *** A view of the member list of any clan ***
	echo render_clan_view($clan, $clan_name, $clan_long_searched, $sql);
}

echo render_clan_tags(); // *** Display the clan tags section. ***

include SERVER_ROOT."interface/footer.php";
?>
