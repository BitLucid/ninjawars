<style>
.ki-amount{
  font-weight:bolder; color:ghostwhite;
}
</style>

<h1>Skills</h1>

{include file='flash-message.tpl'}

<div class='major-stats'>
  <ul class='thin'>
    <li>You are a <span class="class-name {$player->theme|escape}">{$player->getClassName()|escape}</span> ninja.</li>
    <li>Your status is {include file="status_section.tpl" statuses=$status_list}</li>
    {if !$starting_turns}
    <li>You currently <span class='ninja-notice'>do not have any</span> turns.</li>
    {else}
    <li>You currently have have <span class='turns-count'>{$starting_turns|number_format:0|escape}{/if} turn{if $starting_turns > 1}s</span>.</li>
    {/if}
    {if !$starting_ki}
    <li>You currently <span class='ninja-notice'>do not have any</span> ki.</li>
    {else}
    <li>You currently have <span class='ki-amount'>{$starting_ki|number_format:0|escape} ki</span>.</li>
    {/if}
  </ul>
</div>
<div id='skills-list'>


{if $heal}
  <div id='heal-skill'>
    <form action="/skill/post_self_use/" method="post">
      <fieldset>
        <legend>Heal</legend>
        <p>
          You can heal your wounds or another's wounds.
        </p>
      <div>
        <input type="submit" name="act" value="Heal" class="formButton">
        Turn Cost: {$heal_turn_cost} to heal yourself (or another).
      </div>
      </fieldset>
    </form>
  </div>
{/if}

{if $stealth}
  <div id='stealth-skills'>
    <form action="/skill/post_self_use/" method="post">
      <fieldset>
        <legend>Stealth</legend>
    <p>By stealthing you will keep to the shadows where enemies cannot directly duel, for about the next half hour.</p>
      <div>
        <input type="submit" name="act" value="Stealth" class="formButton">
        Turn Cost: {$stealth_turn_cost} to Stealth.
      </div>

      <div>
        <input type="submit" name="act" value="Unstealth" class="formButton">
        Turn Cost: {$unstealth_turn_cost} to Unstealth.
      </div>
      </fieldset>
    </form>
  </div>
{/if}

{if $stalk}
  <div id='stalk-skills'>
    <form action="/skill/post_self_use/" method="post">
      <fieldset>
        <legend>Stalk</legend>
        <p>Focus with single-minded fury on your prey, ignoring other dangers.</p>
        <div>
          <input type="submit" name="act" value="Stalk" class="formButton">
          Turn Cost: {$stalk_turn_cost} to Stalk.
        </div>
      </fieldset>
    </form>
  </div>
{/if}

{if $kampo}
  <div id='kampo-skill'>
    <form action="/skill/post_self_use/" method="post">
      <fieldset>
        <legend>Kampo</legend>
        <p>
          The ancient and mystical art of herbal medicine. Your knowledge of Kampo allows you to convert various herbs into potent medicines.
        </p>
      <div>
        <input type="submit" name="act" value="Kampo" class="formButton">
        Turn Cost: {$kampo_turn_cost} to make all your ginseng into tiger salves.
      </div>
      </fieldset>
    </form>
  </div>
{/if}


{if $can_harmonize}
  <div id='harmonize-skill'>
    <form action="/skill/post_self_use/" method="post">
      <fieldset>
        <legend>Harmonize Chakra</legend>
        <p>
          Heal yourself using your ki.
        </p>
        <div>
        <input type="submit" name="act" value="Harmonize" class="formButton">
        </div>
      </fieldset>
    </form>
  </div>
{/if}

{if $clone_kill}
  <div id='clone-kill'>
    <form action="/skill/post_use/" method="post">
      <fieldset>
        <legend>Clone Kill</legend>
    <p>Obliterate two ninja if they are clones.</p>
      <div>
      	Possible Clones: <input type='text' name='target1' class='char-name'> and
      	<input type='text' name='target2' class='char-name'>
      	<input type='submit' name='act' value='Clone Kill' class='formButton'>
        Turn Cost: {$clone_kill_turn_cost}
      </div>
      </fieldset>
    </form>
  </div>
{/if}

<!-- Skills not described elsewhere -->

{if $chi}
    <p id='chi-skill' class='skill-description'><strong>Chi:</strong> Your Chi skill increases the benefits of healing and resurrecting at the shrine.</p>
{/if}

{if $speed}
    <p id='speed-skill' class='skill-description'><strong>Speed:</strong> Due to your speed, you gain back turns at a faster rate.</p>
    <!-- +1 every hour, so 5 per hour instead of 4. -->
{/if}



{if $midnight_heal}
    <!-- Not currently working, so not currently being shown.  -->
    <p id='midnight-heal-skill' class='skill-description'><strong>Midnight Heal:</strong> When resurrected you will come back with more health than other ninja.</p>
{/if}

{if $hidden_resurrect}
    <p id='hidden-resurrect-skill' class='skill-description'><strong>Hidden Resurrect:</strong> When you are resurrected you will return already hidden and stealthed.</p>
{/if}

{if $blaze}
    <p id='blaze-skill' class='skill-description'><strong>Blaze:</strong> Actively use when dueling to do more damage at a cost of additional turns.</p>
{/if}

{if $deflect}
    <p id='deflect-skill' class='skill-description'><strong>Deflect:</strong> Actively use when dueling to take less damage at a cost of additional turns.</p>
{/if}

{if $evasion}
    <p id='evasion-skill' class='skill-description'><strong>Evasion:</strong> Actively use when dueling to get a chance to escape combat before dying at a cost of additional turns.</p>
{/if}

{if $wrath}
    <p id='wrath-skill' class='skill-description'><strong>Wrath:</strong> Gain a small amount of health back per kill when dueling.</p>
{/if}

{if $no_skills}
	<p id='no-skills'>You do not have any skills you can use on yourself.</p>
{/if}

</div>
