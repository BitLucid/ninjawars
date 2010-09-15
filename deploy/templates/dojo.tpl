<h1>Dojo</h1>

<div class="description">
  <div style="margin-bottom: 10px;">
    You walk up the steps to the grandest building in the village. The dojo trains many respected ninja.
  </div>
  <div>
    As you approach, you can hear the sounds of fighting coming from the wooden doors in front of you.
  </div>
</div>

{if !is_logged_in()}
<p>The guards at the door block your way, saying "Stranger, go on your way, you haven't the skill to enter here."</p>
{else}

	{if $dimMakAllowed}
	<!-- DIMMAK OBTAINING EVENT REQUESTED -->
    {if $dimmak_sequence neq 2}
    A black-robed monk stands near the entrance to the dojo.

    	{if $dimmak_sequence neq 1} {* Link to start the Dim Mak sequence *}
    The black monk approaches you and offers to give you <a href="dojo.php?dimmak_sequence=1">power over life and death,</a> at the cost of some of your memories.
    	{else} {* Strips the link after it's been clicked. *}
    The black monk offers to give you power over life and death, at the cost of some of your memories.
    	{/if}
    <br>
    {/if}

    {if $dimmak_sequence eq 1}
    <form id="Buy_DimMak" action="dojo.php?dimmak_sequence=2" method="post" name="buy_dimmak">
      <div style='margin-top: 10px;margin-bottom: 10px;'>
        Trade your memories of {$dimMakCost|escape} kills for the DimMak Scroll?
        <input id="dimmak_sequence" type="hidden" value="2" name="obtainscroll">
        <input type="submit" value="Obtain Dim Mak" class="formButton">
      </div>
    </form>
    {elseif $dimmak_sequence eq 2}
    The monk meditates for a moment, then passes his hand over your forehead. You feel a moment of dizziness.
    He hands you a pure black scroll.<br>
    {/if}

    <hr>
    <!-- END OF DIMMAK OBTAINING EVENT DISPLAY -->
    
	{/if}
	{if $classChangeAllowed}
        {if $classChangeSequence neq 2}
        <!-- CLASS CHANGING SPECIAL EVENT DISPLAY -->
        A white-robed monk stands near the entrance to the dojo.

        	{if $classChangeSequence neq 1} {* Link to start the Class Change sequence *}
        The white monk approaches you and offers to give you <a href="dojo.php?classChangeSequence=1">the knowledge of your enemies</a> at the cost of your own memories.</a>
        	{else} {* Strips the link after it's been clicked. *}
        The white monk approaches you and offers to give you the knowledge of your enemies at the cost of your own memories.
        	{/if}
        <br>
        {/if}
        
        {if $class_change_error}
            <p class='ninja-error'>{$class_change_error}</p>
        {/if}

        {if $classChangeSequence eq 1}
        <form id="Buy_classChange" action="dojo.php" method="post" name="changeofclass">
          <div style='margin-top: 10px;margin-bottom: 10px;'>
            Trade your memories of {$classChangeCost|escape} kills to change your skills to those of the <span class='class-name {$destination_class_theme}'>{$destination_class_display|escape}</span> ninja?
            <input id='classchangeSequence' name='classChangeSequence' type='hidden' value='2'>
            <input id='current_class' name='current_class' type='hidden' value='{$userClass|escape}'>
            <input type="submit" value="Become A {$destination_class_display|escape} Ninja" class="formButton">
          </div>
        </form>
        {elseif $classChangeSequence eq 2}
        The monk tosses white powder in your face. You blink at the pain, and when you open your eyes, everything looks different somehow.<br>
        The white monk grins at you and walks slowly back to the dojo.<br>
        {/if}
        <hr><br>
        <!-- End of class changing special event display -->
	{/if}

<a href="chart.php">View the scroll of kills needed for each Rank</a><hr>

<div>Your current level is {$userLevel|escape}.</div>
<div style='margin-bottom: 10px;'>Your current kills are {$userKills|escape}.</div>
<div style='margin-bottom: 10px;'>Level {$nextLevel|escape} requires {$required_kills|escape} kills.</div>
<p>Your current class is <span class='class-name {$possibly_changed_class_theme}'>{$possibly_changed_class_name|escape}</span>.</p>

	{if $upgrade_requested}
		{if $userLevel+1 > $max_level}
<div>There are no trainers that can teach you beyond your current skill. You are legendary among the ninja.</div>
		{elseif $userKills < $required_kills}
<div>You do not have enough kills to proceed at this time.</div>
		{else}
		<!-- ************************** GLORIOUS LEVEL UP MESSAGE!  ************************ -->
<div id='ninja-error'>Your trainer puts you through your paces and you learn a great deal from your bruises. Welcome to Ninja Rank {$userLevel|escape}!</div>
		{/if}
	{/if}
	{if $userLevel + 1 gt $max_level}
<div>You enter the dojo as one of the elite ninja. No trainer has anything left to teach you.</div>
	{elseif $userKills lt $required_kills}
<div>Your trainer finds you lacking. You are instructed to prove your might against more ninja before you return.</div>
	{else}
<form id="level_up" action="dojo.php" method="post" name="level_up">
  <div style='margin-top: 10px;margin-bottom: 10px;'>
    <div>Do you wish to upgrade to level {$nextLevel|escape}?</div>
    <input id="upgrade" type="hidden" value="1" name="upgrade">
    <input type="submit" value="Upgrade" class="formButton">
  </div>
</form>
	{/if}
	
<!-- End of logged in display section -->
{/if}
