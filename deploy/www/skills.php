<?php
$alive      = true;
$private    = true;
$quickstat  = "player";
$page_title = "Your Skills";

include SERVER_ROOT."interface/header.php";
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
echo "<p>You are a level $level, $class Ninja.</p>\n";
$status_output_list = render_status_output_list($status_array, $username);
echo "<p>Your status is: ".$status_output_list."</p>";
echo "<div id='skills-list'>";
$no_skills = true;

if ($skillsListObj->hasSkill('Stealth')) {
	echo "<div id='stealth-skills'>";
	echo "<p>By selecting Stealth you will go into a mode where enemies can not directly attack you for a short time.";
	echo "<a href=\"about.php#skills\">(help)</a></p><p>";
	echo "<form action=\"skills_mod.php\" method=\"post\">\n";
	echo "<div>\n";
	echo "<input type=\"submit\" name=\"command\" value=\"Stealth\" class=\"formButton\">\n";
	echo "</select> Turn Cost: ".$skillsListObj->getTurnCost('Stealth')." to Stealth.\n";
	echo "</div>\n";
	echo "</form></p><p>";

	echo "<form action=\"skills_mod.php\" method=\"post\">\n";
	echo "<div>\n";
	echo "<input type=\"submit\" name=\"command\" value=\"Unstealth\" class=\"formButton\">\n";
	echo "Turn Cost: ".$skillsListObj->getTurnCost('Unstealth')." to Unstealth.\n ";
	echo "</div>\n";
	echo "</form></p>";
	echo "</div>";
	$no_skills = false;
}

if ($skillsListObj->hasSkill('Chi')) {
    echo "<p id='chi-skill'>Your Chi skill increases the benefits of healing at the shrine.</p>";
}

if ($skillsListObj->hasSkill('speed')) {
    echo "<p id='speed-skill'>Due to your speed, you gain back turns at a faster rate.</p>";
} // +1 every hour, so 5 per hour instead of 4.


/*
if ($skillsListObj->hasSkill('midnight heal')) {
    echo "<p id='midnight-heal-skill'>When resurrected you will come back with more health than other ninja.</p>";
}  THIS IS NOT CURRENTLY THE CASE*/

if ($skillsListObj->hasSkill('hidden resurrect')) {
    echo "<p id='hidden-resurrect-skill'>When you are resurrected you will return already hidden and stealthed.</p>";
}

if($no_skills){
	echo "<p id='no-skills'>You do not have any skills you can use on yourself.</p>\n";
}
?>
</div>
<div id='search-for-ninja'>
<p><a href="list_all_players.php?hide=dead">Use a Skill on a ninja?</a></p>
<form action="list_all_players.php" method="get">
  <div>
    <input id="searched" type="text" maxlength="50" name="searched" class="textField">
    <input type="hidden" name="hide" value="dead">
    <input type="submit" value="Search for Ninja" class="formButton">
  </div>
</form>
</div>

<hr>

<a href="about.php#magic">Magic and Skills Information</a>

<?php
include SERVER_ROOT."interface/footer.php";
?>
