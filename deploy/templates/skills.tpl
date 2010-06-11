<h1>Skills</h1>


<p>You are a level {$level}, {$class} Ninja.</p>
<p>Your status is: {$status_output_list}</p>
<div id='skills-list'>

{if $stealth}
  <div id='stealth-skills'>
    <form action="skills_mod.php" method="post">
      <fieldset>
        <legend>Stealth</legend>
    <p>By stealthing you will keep to the shadows where enemies cannot directly duel, for about the next half hour.</p>
      <div>
        <input type="submit" name="command" value="Stealth" class="formButton">
        Turn Cost: {$stealth_turn_cost} to Stealth.
      </div>

      <div>
        <input type="submit" name="command" value="Unstealth" class="formButton">
        Turn Cost: {$unstealth_turn_cost} to Unstealth.
      </div>
      </fieldset>
    </form>
  </div>
{/if}

{if $kampo}
  <div id='kampo-skill'>
    <form action="skills_mod.php" method="post">
      <fieldset>
        <legend>Kampo</legend>
        <p>
          The ancient and mystical art of herbal medicine. Your knowledge of Kampo allows you to convert various herbs into potent medicines.
        </p>
      <div>
        <input type="submit" name="command" value="Kampo" class="formButton">
        Turn Cost: {$kampo_turn_cost} to make a Kampo formula.
      </div>
      </fieldset>
    </form>
  </div>
{/if}

{if $chi}
    <p id='chi-skill'>Chi: Your Chi skill increases the benefits of healing and resurrecting at the shrine.</p>
{/if}

{if $speed}
    <p id='speed-skill'>Speed: Due to your speed, you gain back turns at a faster rate.</p>
    <!-- +1 every hour, so 5 per hour instead of 4. -->
{/if}



{if false or $midnight_heal}
    <!-- Not currently working, so not currently being shown.  -->
    <p id='midnight-heal-skill'>Midnight Heal: When resurrected you will come back with more health than other ninja.</p>
{/if}

{if $hidden_resurrect}
    <p id='hidden-resurrect-skill'>Hidden Resurrect: When you are resurrected you will return already hidden and stealthed.</p>
{/if}

{if $no_skills}
	<p id='no-skills'>You do not have any skills you can use on yourself.</p>
{/if}

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
