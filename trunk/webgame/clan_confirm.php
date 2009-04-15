<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Accept a New Clan Member";

include "interface/header.php";
?>
 
<span class="brownHeading">Accept A New Clan Member</span>

<hr  />

<?php
$confirm     = in('confirm');
$clan_name   = in('clan_name');
$clan_joiner = in('clan_joiner');
$passw       = in('passw');
$random = rand(1001, 9990);

echo "$clan_joiner has requested to join your clan, $clan_name.<br />\n";

if ($passw == "")
{
  echo "<form action=\"clan_confirm.php?clan_name=$clan_name&clan_joiner=$clan_joiner&confirm=$confirm\" method=\"post\">\n";
  echo "Your Password: <input id=\"passw\" type=\"password\" name=\"passw\" class=\"textField\" /><input type=\"submit\" value=\"Accept Request\" /><br />\n";
}
else 
{
$l_result= $sql->Query("SELECT pname FROM players WHERE uname='$clan_name' AND pname='$passw'");
$login = $sql->rows;
 $check = $sql->QueryItem("SELECT confirm FROM players WHERE uname = '$clan_joiner'");
 $current_clan = $sql->QueryItem("SELECT clan FROM players WHERE uname = '$clan_joiner'");

 echo "<div style=\"border:1 solid #000000;font-weight: bold;\">\n";

 if ($current_clan != "")
 {
  echo "This member is already part of a clan.\n";
  echo "<br /><br />\n";
  echo "<a href=\"/webgame/\">Return to Main</a>\n";
 }
 else if ($confirm == $check && $login > 0)
 {
  echo "Request Accepted.<br />\n";
  $clan_l_name = getClanLongName($clan_name);
  if (!$clan_l_name)
    {
      $clan_l_name = $clan_name."'s Clan";
    }
  $sql->Update("UPDATE players SET clan = '$clan_name',clan_long_name = '$clan_l_name',confirm = '$random' WHERE uname = '$clan_joiner'");

  echo "<br />$clan_joiner is now a member of your clan.<hr />\n";
  sendMessage($clan_name,$clan_joiner,"CLAN: You have been accepted into $clan_l_name");
 }
else
 {
   echo "This clan membership change can not be verified.\n";
 }
}

?>
<br /><br />
<a href="http://www.ninjawars.net">Return to Main ?</a>

</div>

<?php
include "interface/footer.php";
?>
