<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php");
require_once(LIB_ROOT."specific/tags_lib.php");
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Clan Panel";

include "interface/header.php";
?>
<script type="text/javascript" src="<?=WEB_ROOT?>js/clan.js"></script>

<span class="brownHeading">Clan Panel</span>

<br /><br />

<?php
$command       = in('command');
$process       = in('process');
$clan_name     = in('clan_name', ''); // View that clan name.
$clan_long_searched = in('clan_long_name'); // View that clan long name.
$new_clan_name = in('new_clan_name', '');
$sure          = in('sure', '');
$kicked        = in('kicked', '');
$person_invited= in('person_invited', '');
$clan          = getClan($username);
$viewer_level  = getLevel($username);
$clan_creation_level_requirement = 15;

if ($command == "new")                               //Clan Creation Action
  {
  	if ($viewer_level>$clan_creation_level_requirement)
  	{
	    setClan($username,$username);
		$default_clan_name = "Clan_".$username;
		renameClan($username,$default_clan_name);
	    echo "<div style=\"color:red;\">You have created a new clan!</div><br /> Name your clan: <br />\n";
		$command = "rename";
		$clan = getClan($username);
	}
	else // *** Level req wasn't met.
	{
		echo "<div style=\"color:red;\">You do not have enough renown to create a clan.<br />\n";
	}
  }

if ($clan  != "")
{
  if ($clan == $username)
    {
      if ($command == "rename" )     //Clan Leader Action Rename
	{
	  if ($new_clan_name != "" && strlen($new_clan_name) <= 20 and ( str_replace(array('/','\'','*','--', '<', '>'), '', $new_clan_name) ==$new_clan_name)) // *** The clan doesn't contain any special characters, including apostrophes, asterixes, slashes, and html code.
	    {
	      echo "Your new clan name is <span style=\"font-weight: bold;\">".renameClan($username,$new_clan_name).".</span><br />\n";
	    }
	  else
	    {
	      if (strlen($new_clan_name) >= 21)
			{
			  echo "<div style=\"color:red;\">Your clan name cannot be blank or greater than 20 characters.</div><br />\n";
			}
		elseif ("" == $new_clan_name) 
			{
			  echo "(Clan names must be between 1 and 20 characters long.)<br />\n";
			}
		else {
		    echo "(Clan names cannot contain special characters other than a single dash.)<br />\n";
		}
	      echo "<form id=\"clan_rename\" action=\"clan.php\" name=\"clan_rename\">\n";
	      echo "<input id=\"command\" type=\"hidden\" value=\"rename\" name=\"command\" />\n";
	      echo "<input id=\"new_clan_name\" type=\"text\" name=\"new_clan_name\" class=\"textField\" />".
		"<input type=\"submit\" class=\"formButton\" value=\"Rename Clan\" />\n";
	      echo "</form>\n";
	      echo "<hr />\n";
	    }
	} else if ($command == "kick") {              //Clan Leader Action Kick a chosen member
	  if ($kicked == "") {
	      $sql->Query("SELECT uname FROM players WHERE clan = '$clan' && uname <> '$username' && confirmed = 1");
	      $members =  $sql->data;

	      echo "<form id=\"kick_form\" action=\"clan.php\" method=\"get\" name=\"kick_form\">\n";
	      echo "Kick: \n";
	      echo "<select id=\"kicked\" name=\"kicked\">\n";
	      echo "<option value=\"\">--Pick a Member--</option>\n";

	   for ($i = 0; $i < $sql->rows; $i++) {
		  $sql->Fetch($i);
		  $name = $sql->data[0];
		  echo "<option value=\"$name\">$name</option>\n";
		}

	      echo "</select>\n";
	      echo "<input id=\"command\" type=\"hidden\" value=\"kick\" name=\"command\" />\n";
	      echo "<input type=\"submit\" value=\"Kick\" class=\"formButton\" />\n";
	      echo "</form>\n";
	      echo "<br />\n";
	    } else {
	      kick($kicked);

	      echo "You have removed $kicked from your clan.<br />\n";
	    }
	} else if ($command == "disband") {                      //Clan Leader Confirmation of Disbanding of the Clan
	  if (!$sure) {
	      echo "Are you sure you want to continue? This will remove all members from your clan.<br />\n";
	      echo "<form id=\"disband\" method=\"get\" action=\"clan.php\" name=\"disband\">\n";
	      echo "<input type=\"submit\" value=\"Disband\" class=\"formButton\" />\n";
	      echo "<input id=\"command\" type=\"hidden\" value=\"disband\" name=\"command\" />\n";
	      echo "<input id=\"sure\" type=\"hidden\" value=\"yes\" name=\"sure\" />\n";
	      echo "</form>\n";
	    }
	  else if ($sure == "yes") {                            //Clan Leader Action Disbanding of the Clan
	      disbandClan($username);
	      die("Your clan has been disbanded.<br />\n");
	    }
	}
	else if ($command == "invite") {                        //Clan Leader Invite Input
		if (!$person_invited) {
			  echo "Name of potential clan member:<br />\n";
	          echo "<form id=\"clan_invite\" action=\"clan.php\" name=\"clan_rename\">\n";
	          echo "<input id=\"command\" type=\"hidden\" value=\"invite\" name=\"command\" />\n";
    	      echo "<input id=\"person_invited\" type=\"text\" name=\"person_invited\" class=\"textField\" />".
		      "<input type=\"submit\" class=\"formButton\" value=\"Invite\" />\n";
	          echo "</form>\n";
	          echo "<hr />\n";
			} else {
			$failure_message = invitePlayer($person_invited,$username);          //Clan leader Invite Action
			if ($failure_message == "None.") {
				echo "You have invited $person_invited to join your clan.<br />\n";
				}
			else
			echo "You cannot invite $person_invited.<br />".$failure_message."<br /><br />\n";
			}
		}
      echo "<span style=\"font-weight: bold;\">Leader Panel</span><br />\n";                  //Display of the Leader Panel
      echo "<div style=\"border: thin solid white;padding-left: 10px;padding-top: 10px;padding-bottom: 10px;width: 190px;margin-left: 5px;\">\n";
      echo "You are leader of the ".$clan." Clan.<br /><br />\n";
      echo "<a href=\"clan.php?command=invite\">Recruit for your Clan</a><br  />\n";         //Future Clan Leader Invite Command
      echo "<a href=\"clan.php?command=rename\">Rename Clan</a><br />\n";                    //Clan Leader Rename Clan
      echo "<a href=\"clan.php?command=disband\">Disband Your Clan</a><br />\n";             //Clan Leader Disband Clan
      echo "<a href=\"clan.php?command=kick\">Kick a Clan Member</a><br />\n";               //Clan Leader Kick Clan Member
      echo "</div>\n";
      echo "<br /><br />\n";
    } else {
      if ($command == "leave"){                                   //Clan Member Action to Leave their Clan
	  setClan($username,"");
	  setClanLongName($username,"");
	  die("You have left your clan.<br />\n");
	}
      
      echo "You are currently a member of the $clan Clan.<br />\n";
      echo "<a href=\"clan.php?command=leave\" onclick='leave_clan(); return false;'>Leave Current Clan</a><br />";
    }
  
  if ($command == "msgclan") {                          //Clan Member Input for Messaging their Entire Clan
      echo "<form id=\"msg_clan\" action=\"mail_send.php\" method=\"get\" name=\"msg_clan\">\n";
      echo "Msg: <input id=\"message\" type=\"text\" size=\"50\" maxlength=\"1000\" name=\"message\" class=\"textField\" /><br />\n";
      echo "<input id=\"to\" type=\"hidden\" value=\"clansend\" name=\"to\" />\n";
      echo "<input id=\"messenger\" type=\"hidden\" value=\"1\" name=\"messenger\" />\n";
      echo "<input type=\"submit\" value=\"Send This Message\" class=\"formButton\" />\n";
      echo "</form>\n";
    }

  echo "<a href=\"clan.php?command=msgclan\">Msg Clan Members</a><br />";    //Clan Member Choice for Messaging their Entire Clan
  echo "<a href=\"clan.php?command=view&clan_name=$clan\">View Your Clan</a><br />";    //Clan Member Choice to View Clan
} else {
  if ($command == "join") {                       //Clan Joining Action
      if ($process == 1) {
		  $confirm = $sql->QueryItem("SELECT confirm FROM players WHERE uname = '$username'");
		  $join_request_message = "CLAN JOIN REQUEST: $username has sent you a clan request.  If you wish to allow this ninja into your clan click the following link: <a href='clan_confirm.php?clan_joiner=".rawurlencode($username)."&confirm=$confirm&clan_name=".rawurlencode($clan_name)."'>Confirm Request</a>.";
		  sendMessage($username,$clan_name,$join_request_message);
     	  echo "***Your request to join this clan has been sent to $clan_name***<br /><br />\n";
	} else {                                            //Clan Join list of available Clans
	  $sql->Query("SELECT uname,level,clan,clan_long_name FROM players WHERE lower(uname) = lower(clan) AND clan_long_name != '' AND confirmed = 1");
	  echo "Clans Available to Join<br />\n";             
	  echo "To send a clan request click on that clan leader's name.<br /><br />\n";
		while ($data = $sql->Fetch()) {
	      $name = $data[0];
	      $level = $data[1];
	      $clan = $data[2];
		  $clan_long_name = $data[3];
	      echo "<a href=\"clan.php?command=join&clan_name=$clan&process=1\">Join $clan_long_name</a>.  Its leader is <a href=\"player.php?player=$name\">$name</a>, level $level.  <a href=\"clan.php?command=view&clan_name=$clan\">View This Clan</a><br />\n";
	    }
	}
    }
  
  echo "You are not a member of any clan.<br /><br />\n";
  echo "<a href=\"clan.php?command=join\">Join a Clan</a><br />\n";
  if ($viewer_level >= $clan_creation_level_requirement) { //Prevents characters under the level req from seeing clan creation option.
	  echo "<a href=\"clan.php?command=new\">Start a New Clan</a><br />\n";
	} else {
	  echo "You can start your own clan when you reach level $clan_creation_level_requirement.<br />\n";
	}
}

echo "<a href=\"clan.php?command=list\">List Clans</a><br />\n";

if ($command == "list") {                                //Lists the clans that exist and their leaders.
  $sql->Query("SELECT count(uname) as c, clan, clan_long_name ".
		"FROM players WHERE clan <> '' ".
                "AND confirmed = 1 ".
		"GROUP BY clan, clan_long_name ORDER BY c DESC");
  echo "<hr />Clans List: <br /><br />\n";
  echo "<table>\n";
  echo "<tr>\n";
  echo "  <td style=\"font-weight: bold;\">\n";
  echo "  Clan\n";
  echo "  </td>\n";
  
  echo "  <td style=\"font-weight: bold;\">\n";
  echo "  Leader\n";
  echo "  </td>\n";
  
  echo "  <td style=\"font-weight: bold;\">\n";
  echo "  View\n";
  echo "  </td>\n";
  echo "</tr>\n";
  
	while ($data = $sql->Fetch()) {
      $clan = $data[1];
      $clan_l_name = $data[2];
      echo "<tr>\n";
      echo "  <td>\n";
      echo "  $clan_l_name\n";
      echo "  </td>\n";
      
      echo "  <td>\n";
      echo "  $clan\n";
      echo "  </td>\n";
      
      echo "  <td>\n";
      echo "  <a href=\"clan.php?command=view&clan_name=$clan\">View Clan</a>\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
  echo "</table>\n";
}
else if ($command == "view"){                                     //A view of the member list of any clan
    $search = "clan = '$clan_name'";
    if($clan_long_searched){
        $search = "clan_long_name = '$clan_long_searched'";
    }
    $sql->Query("SELECT uname, clan_long_name FROM players WHERE $search AND confirmed = 1");
    echo "<div>".($clan_long_searched? "Clan $clan_long_searched" : $clan."'s Clan")."</div>";
    echo "Clans Members: $sql->rows <br /><br />\n";
    while ($data = $sql->Fetch()) {
        $name = $data[0];
        echo "<a href=\"player.php?player=$name\">$name</a><br />\n";
    }
}

echo render_clan_tags(); // Display the clan tags section.

include "interface/footer.php";
?> 
