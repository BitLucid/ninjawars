<?php
$alive      = true;
$private    = true;
$quickstat  = "player";
$page_title = "Your Skills";

include SERVER_ROOT."interface/header.php";
require_once(LIB_ROOT."specific/lib_status.php"); // statuses for quickstats
require_once(LIB_ROOT."specific/lib_player.php"); // Player info display pieces.
?>

<h1>Skills</h1>

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
$status_output_list = render_status_section();
$no_skills = true;
$stealth = $skillsListObj->hasSkill('Stealth');
$stealth_turn_cost = $skillsListObj->getTurnCost('Stealth');
$unstealth_turn_cost = $skillsListObj->getTurnCost('Unstealth');
$chi = $skillsListObj->hasSkill('Chi');
$speed = $skillsListObj->hasSkill('Chi');
$hidden_resurrect = $skillsListObj->hasSkill('hidden resurrect');

echo "<p>You are a level $level, $class Ninja.</p>\n";
echo "<p>Your status is: ".$status_output_list."</p>";
echo "<div id='skills-list'>";

if ($stealth) {
	echo "<div id='stealth-skills'>";
	echo "<p>By selecting Stealth you will go into a mode where enemies can not directly attack you for a short time.";
	echo "<a href=\"about.php#skills\">(help)</a></p><p>";
	echo "<form action=\"skills_mod.php\" method=\"post\">\n";
	echo "<div>\n";
	echo "<input type=\"submit\" name=\"command\" value=\"Stealth\" class=\"formButton\">\n";
	echo "</select> Turn Cost: ".$stealth_turn_cost." to Stealth.\n";
	echo "</div>\n";
	echo "</form></p><p>";

	echo "<form action=\"skills_mod.php\" method=\"post\">\n";
	echo "<div>\n";
	echo "<input type=\"submit\" name=\"command\" value=\"Unstealth\" class=\"formButton\">\n";
	echo "Turn Cost: ".$unstealth_turn_cost." to Unstealth.\n ";
	echo "</div>\n";
	echo "</form></p>";
	echo "</div>";
	$no_skills = false;
}

if ($chi) {
    echo "<p id='chi-skill'>Chi: Your Chi skill increases the benefits of healing at the shrine.</p>";
}

if ($speed) {
    echo "<p id='speed-skill'>Speed: Due to your speed, you gain back turns at a faster rate.</p>";
} // +1 every hour, so 5 per hour instead of 4.


/*
if ($skillsListObj->hasSkill('midnight heal')) {
    echo "<p id='midnight-heal-skill'>Midnight Heal: When resurrected you will come back with more health than other ninja.</p>";
}  THIS IS NOT CURRENTLY THE CASE TODO NEEDS A FIX*/

if ($hidden_resurrect) {
    echo "<p id='hidden-resurrect-skill'>Hidden Resurrect: When you are resurrected you will return already hidden and stealthed.</p>";
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
    <button type="submit" value="1" class="formButton">Search for Ninja</button>
  </div>
</form>
</div>

<hr>

<a href="about.php#magic">Magic and Skills Information</a>

<?php
include SERVER_ROOT."interface/footer.php";
?>
