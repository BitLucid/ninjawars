<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Accept a New Clan Member";

include "interface/header.php";
?>
 
<span class="brownHeading">Accept A New Clan Member</span>

<hr>

<?php
$confirm     = in('confirm');
$username = get_username();
$clan_name   = getClan($username);
$clan_l_name = getClanLongName($username);
$clan_joiner = in('clan_joiner');
$agree       = in('agree');
$random = rand(1001, 9990);

if (!$clan_name){
    echo "You have no clan.";
} elseif(!$clan_joiner){
    echo "There is no potential ninja specified, so the induction cannot occur."; 
} else {
    if (!$clan_l_name) {
        $clan_l_name = $clan_name."'s Clan";
    }
    echo "$clan_joiner has requested to join your clan, $clan_l_name.<br>\n";

    if (!$agree) {
      echo "<form action=\"clan_confirm.php?clan_name=$clan_name&clan_joiner=$clan_joiner&confirm=$confirm\" method=\"post\">\n";
      echo "<input id=\"agree\" type=\"hidden\" name=\"agree\" value='1'/><input type=\"submit\" value=\"Accept Request\"><br>\n";
      echo "</form>";
    } else {
        $check = $sql->QueryItem("SELECT confirm FROM players WHERE uname = '$clan_joiner'");
        $current_clan = $sql->QueryItem("SELECT clan FROM players WHERE uname = '$clan_joiner'");

        echo "<div style=\"border:1 solid #000000;font-weight: bold;\">\n";

        if ($current_clan != "") {
            echo "This member is already part of a clan.\n";
            echo "<br><br>\n";
            echo "<a href=\"/webgame/\">Return to Main</a>\n";
        } else if (!$check){
            echo "<p>No such ninja.</p>";
            echo "<p><a href=\"/webgame/\">Return to Main</a></p>\n";
        } elseif ($confirm == $check && $agree > 0) {
            echo "Request Accepted.<br>\n";
            $sql->Update("UPDATE players SET clan = '$clan_name', clan_long_name = '$clan_l_name', 
                confirm = '$random' WHERE uname = '$clan_joiner'");
            echo "<br>$clan_joiner is now a member of your clan.<hr>\n";
            sendMessage($clan_name,$clan_joiner,"CLAN: You have been accepted into $clan_l_name");
        } else {
            echo "This clan membership change can not be verified, please ask the ninja to request joining again.\n";
        }
    }
} // End of else (when clan_name is available).

?>
<br><br>
<a href="http://www.ninjawars.net">Return to Main ?</a>

</div>

<?php
include "interface/footer.php";
?>
