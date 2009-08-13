<?php
$alive      = true;
$private    = true;
$quickstat  = "player";
$page_title = "Your Skills";

include "interface/header.php";
require_once(LIB_ROOT."specific/lib_status.php"); // statuses for quickstats
?>
  
<span class="brownHeading">Skills</span>

<p>
<?php
// TODO: Consider more skills along the lines of: disguise, escape, concealment, archery, medicine, explosives, and poisons.
// TODO: Also consider "packageable" classes. 
include(OBJ_ROOT."Skill.php");
$skillsListObj = new Skill();

$level = getLevel($username);
$class = getClass($username);
$status_array = getStatus($username);

$row = $sql->data;
if ($class != "") {
	echo "You are a level $level, $class Ninja.<br />\n";
	$status_output_list = status_output_list($status_array, $username);
	echo "Your status is: ".$status_output_list."<br /><br />";

  if ($skillsListObj->hasSkill('Stealth')) {
  	  echo "By selecting Stealth you will go into a mode where enemies can not directly attack you for a short time. ";
  	  echo "<a href=\"about.php#skills\">(help)</a>\n";
      echo "<form action=\"skills_mod.php\" method=\"post\">\n";
      echo "<input type=\"submit\" name=\"command\" value=\"Stealth\" class=\"formButton\" />\n";
      echo "</select> Turn Cost: ".$skillsListObj->getTurnCost('Stealth')." to Stealth.\n";
      echo "</form><br />";

	  echo "<form action=\"skills_mod.php\" method=\"post\">\n";
      echo "<input type=\"submit\" name=\"command\" value=\"Unstealth\" class=\"formButton\" />\n";
      echo "Turn Cost: ".$skillsListObj->getTurnCost('Unstealth')." to Unstealth.\n ";
      echo "</form><br />\n";
    }
  else
    {
      echo "You do not have any skills you can use on yourself.\n";
    }
}
else
{
  echo "You do not possess a class, you must have signed up before classes where implemented.<br /><br /> Please inform <a href=\"mailto:Admin@NinjaWars.net?subject=Change My Ninja Class\">Admin</a> of which class(currently Red,White,Blue,Black) you want to be or it will be choosen later on.\n";
}
?>

<br /><br />

<a href="list_all_players.php?hide=dead">Use a Skill on a ninja?</a>
<form action="list_all_players.php" method="get">
<input id="searched" type="text" maxlength="50" name="searched" class="textField" />
<input type="hidden" name="hide" value="dead" />
<input type="submit" value="Search for Ninja" class="formButton" />
</form>

<hr />

<a href="about.php#magic">Magic and Skills Information</a>

</p>

</php
include "interface/footer.php";
?>
