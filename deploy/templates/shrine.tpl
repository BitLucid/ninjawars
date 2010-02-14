<h1>Shrine</h1>

<div class="description">
  <div style="margin-bottom: 10px;">The shrine to the gods is peacefully quiet as you enter. The sound of flowing water soothes your mind.</div>
  <div>A monk approaches you quietly and asks, "Are you in need of healing?"</div>
</div>
{if !$username}
    <div id='ninja-notice'>You have no need of healing.</div>
{else}
	{if !$players_health}
		<form action="shrine_mod.php" method="post">
		<span class="brownHeading">Resurrect</span>


		<p>Resurrect to return to life.</p>
		{if $freeResurrection}
			<p style="color: red;">
			Since you have not killed more than twenty ninja or gained beyond five levels, you will not lose power by resurrecting!
			</p>
		{else}
			<p> You will lose a kill point for every resurrection. &nbsp;</p>
		{/if}

		<input type="hidden" name="restore" value="1">
		<input type="submit" value="Return to life" class="formButton">
		</p>
		</form>
		<hr>
	{elseif $at_max_health}
		<p>You are at full health.</p>
	{else}
		<form id="heal_form" action="shrine_mod.php" method="post" name="heal_form">
		<div style="margin-top: 10px;">
		<div>The cost is one gold per point of health</div>
		<input type="submit" value="Heal" class="formButton">
		<input id="heal_points" type="text" size="3" maxlength="4" name="heal_points" class="textField">HP
		<input id="healed" type="hidden" value="1" name="healed">
		</div>
		</form>
		<form id="max_heal_form" action="shrine_mod.php" method="post" name="max_heal_form">
		<div>
		<input id="max_heal" type="hidden" value="1" name="max_heal">
		<input type="submit" value="Full Heal" class="formButton">
		</div>
		</form>
	{/if}
	{if $poisoned}
		<hr>
		<form action="shrine_mod.php" method="post">
		<span class="brownHeading">Antidote(remove poison)</span>
		<p>
		Cure Poison effect, Cost: 50 gold.
		<input type="hidden" name="poisoned" value="1">
		<input type="submit" value="Antidote" class="formButton">
		</p>
		</form>
    {/if}
{/if}<!-- End of if username block -->
