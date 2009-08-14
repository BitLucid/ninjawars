<?php
if(!DEBUG) { die(); } // Development side only.
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Tchalvak";

include "interface/header.php";
?>
 
<span class="brownHeading">Tchalvak Test Page</span>

<hr >

<?php
$command = in('command');
$message = in('message');
$player_to_confirm = in('player_name');

echo "BOTH WEBGAME AND NWTEST:<br>";



echo "<br>END OF GLOBAL OUTPUT, BEGIN TEMPORARY CHANGE SECTION<br>";                 //For temporary database update hacks.

$sql->Insert("INSERT INTO dueling_log VALUES ('', 'TestofSystem', 'SystemWorking', 1, 20, CURRENT_TIMESTAMP)");


echo "Temporary Update Variable: Insert Succeeded.";
echo "<pre>\n";
echo "</pre>\n";
echo "<br>END TEMP SECTION<br>\n";

$nwtest_kills = $sql->QueryItem("SELECT kills FROM players WHERE uname = 'tchalvak'"); 
if ($nwtest_kills) 
{
echo "CURRENTLY ON NWTEST:<br>\n";                          //Location Check.

/*
$sql->Update("UPDATE players SET health = 100 WHERE health = 0");
$sql->Update("UPDATE players SET kills=900 WHERE uname='tchalvak'");            //Temporary
$tchalvak_kills = $sql->QueryRow("SELECT kills FROM players WHERE uname = 'tchalvak' LIMIT 1");
echo "Tchalvak's kills: $tchalvak_kills<br>";
//*/

}


$webgame_kills = $sql->QueryItem("SELECT kills FROM players WHERE uname='testtest'");
if ($webgame_kills)
{
      echo "CURRENTLY ON WEBGAME:<br>\n";   //Location Check.

	  
      $dead_ninja=$sql->QueryItem("SELECT count(*) FROM players WHERE health<1 AND confirmed=1");
	  echo "Dead Ninja: $dead_ninja<br>";
	  $players_resurrected=$sql->Query("SELECT uname FROM players WHERE health < 1 AND confirmed=1 AND ( days<21 OR (days % FLOOR(days / 10)=0) ) LIMIT 100");
	  $players_resurrected_data = $sql->rows;
	  print_r($players_resurrected);
	  if (!is_array($players_resurrected_data))
	{
		  echo "players resurrected data Not an array.<br>";
	}
	else
	{
		foreach ($players_resurrected_data as $value)
			{
			echo "$value will be resurrected.<br>";
			}
	}	  
	if (!is_array($players_resurrected))
	{
		  echo "players resurrected Not an array.<br>";
	}
	else
	{
		foreach ($players_resurrected as $value)
			{
			echo "$value will be resurrected.<br>";
			}
	}
	  $affected_rows[3] = $sql->a_rows;
	  echo "There will be $affected_rows[3] players resurrected tonight.<br>\n";

	  $sql->Update("UPDATE players SET kills = 200 , level = 20 WHERE uname = 'testtest' LIMIT 1");
	  $sql->Query("SELECT kills,level FROM players WHERE uname = 'testtest'");
		while ($data = $sql->Fetch())
		{
		  $kills = $data[0];
		  $level = $data[1];
		  echo "player kills: $kills player level: $level<br>";
		}

//Message Posting Section of page.
	  echo "<form id=\"post_msg\" action=\"tchalvak.php\" method=\"post\" name=\"post_msg\">\n";
      echo "Message: <input id=\"message\" type=\"text\" size=\"50\" maxlength=\"1000\" name=\"message\" class=\"textField\">\n";
      echo "<input id=\"command\" type=\"hidden\" value=\"postnow\" name=\"command\">";
      echo "<input type=\"submit\" value=\"Send\" class=\"formButton\">\n";
      echo "</form>\n";
      if ($command == "postnow" && $message != "")
      {
        sendMessage(Tchalvak,ChatMsg,$message);    
        echo "Your post has been added.<br> <a href=\"village.php\">Go to the Chat Board?</a>";
      }

} 




/*/                   Turn player confirming script On/OFF
if (!$player_to_confirm) {$player_to_confirm = 'SETNAMETOCONFIRMHERE';}
if ($player_to_confirm)
{
  $sql->QueryRow("SELECT * FROM players WHERE uname = '$player_to_confirm'");
  $row_count = $sql->rows;
  echo "Row Count Resulting: $row_count<br>\n";
  var_dump($sql);
  $player_email_to_confirm = $sql->data[10];
  $sql->QueryRow("SELECT count(*) FROM players WHERE email LIKE '$player_email_to_confirm'");
  $duplicate_emails = $sql->rows;
  echo "Equivalent Email Count: $duplicate_emails";
  //$sql->Update("UPDATE players SET confirmed = 1 WHERE uname = '$player_to_confirm'");
}
//*/


?>
<br><br>
<a href="http://www.ninjawars.net">Return to Main ?</a>
</div>

<?php
include "interface/footer.php";
?>
