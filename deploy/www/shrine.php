<?php
$page_title = "Shrine";
$alive      = false;
$private    = true;
$quickstat  = "player";

include SERVER_ROOT."interface/header.php";
?>

<div class="brownTitle">Shrine</div>

<div class="description">
The shrine to the gods is peacefully quiet as you enter. The sound of flowing water soothes your mind.
<br><br>
A monk approaches you quietly and asks, "Are you in need of healing?"
</div>

<?php
$freeResLevelLimit = 6;
$freeResKillLimit = 25;
$lostTurns=10; // *** Default turns lost when the player has no kills.
$startingKills = getKills($username);
$userLevel = getLevel($username);

// *** A True or False as to whether resurrection will be free.
$freeResurrection = ($userLevel<$freeResLevelLimit && $startingKills<$freeResKillLimit);
if (!$players_health)
{
	echo '<form action="shrine_mod.php" method="post">';
    echo '<span class="brownHeading">Resurrect</span>';
    if ($freeResurrection)
		{
		echo '<p style="color: red;">';
		echo 'Since you have not killed more than twenty ninja or gained beyond five levels, you will not lose power by resurrecting!';
		echo '</p>';
		}
	echo '<p>';
	echo 'Resurrect to return to life.';
	if (!$freeResurrection)
		{
			echo ' You will lose a kill point for every resurrection. &nbsp';
		}
	echo '<input type="hidden" name="restore" value="1">';
	echo '<input type="submit" value="Return to life" class="formButton">';
	echo '</p>';
	echo '</form>';
	echo '<hr>';
}	
elseif ($players_health >= (150+(($players_level-1)*25))) // *** If at or above the maximum, no healin'.
{
	echo 'You are at full health.';	
}
else
{
	echo '<form id="heal_form" action="shrine_mod.php" method="post" name="heal_form">';
	echo '<br>The cost is one gold per point of health<br>';
	echo '<input type="submit" value="Heal" class="formButton">';
	echo '<input id="heal_points" type="text" size="3" maxlength="4" name="heal_points" class="textField">HP';
	echo '<input id="healed" type="hidden" value="1" name="healed">';
	echo '</form>';
	echo '<form id="max_heal_form" action="shrine_mod.php" method="post" name="max_heal_form">';
	echo '<input id="max_heal" type="hidden" value="1" name="max_heal">';
	echo '<input type="submit" value="Full Heal" class="formButton">';
	echo '</form>';
}
if (getStatus($username) && isset($status_array['Poisoned']) && $status_array['Poisoned'])
{
	echo '<hr>';
	echo '<form action="shrine_mod.php" method="post">';
	echo '<span class="brownHeading">Antidote(remove poison)</span>';
	echo '<p>';
	echo 'Cure Poison effect, Cost: 50 gold.';
	echo '<input type="hidden" name="poisoned" value="1">';
	echo '<input type="submit" value="Antidote" class="formButton">';
	echo '</p>';
	echo '</form>';
}

?>

<?php
include SERVER_ROOT."interface/footer.php";
?>


