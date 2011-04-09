<h1>Skills</h1>


<p>You are a level {$level}, {$class} Ninja.</p>
<p>Your status is: {include file="status_section.tpl" statuses=$status_list}</p>
<p>You currently {if !$starting_turns}<span class='ninja-notice'>do not have any</span>{else}have {$starting_turns}{/if} turns.</p>
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


{if $heal}
  <div id='heal-skill'>
    <form action="skills_mod.php" method="post">
      <fieldset>
        <legend>Heal</legend>
        <p>
          You can heal your wounds or another's wounds.
        </p>
      <div>
        <input type="submit" name="command" value="Heal" class="formButton">
        Turn Cost: {$heal_turn_cost} to heal yourself (or another).
      </div>
      </fieldset>
    </form>
  </div>
{/if}

{if $harmonize}
  <div id='harmonize-skill'>
    <form action="skills_mod.php" method="post">
      <fieldset>
      	<legend>Harmonize Chakra</legend>
      	<div>
        <input type="submit" name="command" value="Harmonize" class="formButton">
        </div>
      </fieldset>
    </form>
  </div>
{/if}



{if $clone_kill}
  <div id='clone-kill'>
    <form action="skills_mod.php" method="post">
      <fieldset>
        <legend>Clone Kill</legend>
    <p>Obliterate two ninja if they are clones.</p>
      <div>
      	Possible Clones: <input type='text' name='clone1' class='char-name'> and 
      	<input type='text' name='clone2' class='char-name'>
      	<input type='submit' name='command' value='Clone Kill' class='formButton'>
        Turn Cost: {$clone_kill_turn_cost}
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



{if $midnight_heal}
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
<p><a href="list.php?hide=dead">Use a Skill on a ninja?</a></p>
<form action="list.php" method="get">
  <div>
    <input id="searched" type="text" maxlength="50" name="searched" class="textField">
    <input type="hidden" name="hide" value="dead">
    <button type="submit" value="1" class="formButton">Search for Ninja</button>
  </div>
</form>
</div>

<hr>
