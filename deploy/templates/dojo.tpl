{literal}
<style>
#scroll{
	margin:0 auto 1em;
	text-align:center;
}
.left-scroll-bookend{
	display:inline-block;
	background:url(/images/scroll_accent_left.png) no-repeat left;
	height:100px;
	padding-left:57px;
	margin:0 auto;
}
.right-scroll-bookend{
	vertical-align:middle;
	background:url(/images/scroll_accent_right.png) no-repeat right;
	height:100px;
	min-width:50%;
	padding-right:57px;
	display:inline-block;
	position:relative;
}
#scroll #scroll-title{
	height:30px;
	display:inline-block;
	padding: 35px .7em 35px;
	font-size: 1.3em;
	background:#333;
}
table{
	width:90%;
	margin-left:5%;
	margin-right:5%;
	margin-bottom:2em;
}
table .char-title td{
	font-style:1.5em;
}
.black-robed-monk{
	font-weight:bold;color:gray;
}
.white-robed-monk{
	font-weight:bold;color:#F8F9CF;
}
.training-requirements tbody tr:nth-child(odd) {
   background-color: rgba(100, 100, 100, 0.5);
}
</style>
{/literal}

<h1>Dojo</h1>

<div class="description">
  <p>
    You walk up the steps to the grandest building in the village. The dojo trains many respected ninja.
  </p>
  <p>
    As you approach, you can hear the sounds of fighting coming from the wooden doors in front of you.
  </p>
</div>

{if !is_logged_in()}
<p style='text-align:center'>The guards at the door block your way, saying "Stranger, go on your way, you haven't the skill to enter here."</p>
{else}

	{if !$dim_mak_requirement_error}
	<!-- DIMMAK OBTAINING EVENT REQUESTED -->
    {if $dimmak_sequence neq 2}
    A <span class='black-robed-monk'>black-robed monk</span> stands near the entrance to the dojo.
    <br>

    	{if $dimmak_sequence neq 1} {* Link to start the Dim Mak sequence *}
    The <span class='black-robed-monk'>black monk</span> approaches you and offers to give you <a href="dojo.php?dimmak_sequence=1">power over life and death,</a> at the cost of some of your memories.
    	{else} {* Strips the link after it's been clicked. *}
    The <span class='black-robed-monk'>black monk</span> offers to give you power over life and death, at the cost of some of your memories.
    	{/if}
    <br>
    {/if}

    {if $dimmak_sequence eq 1}
    <form id="Buy_DimMak" action="dojo.php?dimmak_sequence=2" method="post" name="buy_dimmak">
      <div style='margin-top: 10px;margin-bottom: 10px;'>
        Give up your memories of {$dimMakCost|escape} kills for the DimMak Scroll?
        <input id="dimmak_sequence" type="hidden" value="2" name="obtainscroll">
        <input type="submit" value="Obtain Dim Mak" class="formButton">
      </div>
    </form>
    {elseif $dimmak_sequence eq 2}
    <p>The monk meditates for a moment, then passes his hand over your forehead. A black fog passes over your vision and you feel a moment of dizziness.</p>
    <p>For a moment you become aware of the dirt on the walls, the darkness in the room, a <a href='npc.php?victim=spider' class='npc'>Spider</a> crawling across the wall.</p>
    <p>He hands you a scroll that seems to writhe with shadows.</p>
    {/if}

    <hr>
    <!-- END OF DIMMAK OBTAINING EVENT DISPLAY -->
    
	{/if}
	{if !$class_change_requirement_error}
        {if $classChangeSequence neq 2}
        <!-- CLASS CHANGING SPECIAL EVENT DISPLAY -->
        A <span class='white-robed-monk'>white-robed monk</span> stands near the entrance to the dojo.

        	{if $classChangeSequence neq 1} {* Link to start the Class Change sequence *}
        <p>The <span class='white-robed-monk'>white monk</span> approaches you and offers to give you <a href="dojo.php?classChangeSequence=1">the knowledge of your enemies</a> at the cost of your own memories.</a></p>
        	{else} {* Strips the link after it's been clicked. *}
        <p>The white monk approaches you and offers to give you the knowledge of your enemies at the cost of your own memories.</p>
        	{/if}
        {/if}
        
        {if $class_change_error}
            <p class='ninja-error'>{$class_change_error}</p>
        {/if}

        {if $classChangeSequence eq 1}
          {foreach from=$classes item='class' key='identity'}
        <form id="Buy_classChange" action="dojo.php" method="post" name="changeofclass">
          <div style='margin-top: .3em;margin-bottom: .3em;'>
            Give up your memories of {$class_change_cost|escape} kills to learn the skills of the <span class='class-name {$class.theme}'>{$class.class_name|escape}</span> ninja?
            <input id='classchangeSequence' name='classChangeSequence' type='hidden' value='2'>
            <input id='current_class' name='current_class' type='hidden' value='{$userClass|escape}'>
            <input id='requested_identity' name='requested_identity' type='hidden' value='{$identity|escape}'>
            <input type="submit" value="Become A {$class.class_name|escape} Ninja" class="formButton">
          </div>
        </form>
          {/foreach}
        {elseif $classChangeSequence eq 2}
        <p>
        The monk tosses white powder in your face. You blink at the pain, and when you open your eyes, everything looks different somehow.</p>
        <p>The white monk smiles at you and walks slowly back to the dojo.</p>
        {/if}
        <hr>
        <!-- End of class changing special event display -->
	{/if}

<p>Your current class is <span class='class-name {$possibly_changed_class_theme}'>{$possibly_changed_class_name|escape}</span>.</p>
<p>Your current level is {$userLevel|escape}. Your current kills are {$userKills|escape}.</p>
<p>Level {$nextLevel|escape} requires {$required_kills|escape} kills.</p>

	{if $upgrade_requested}
		{if $userLevel+1 > $max_level}
<div>There are no trainers that can teach you beyond your current skill. You are legendary among the ninja.</div>
		{elseif $userKills < $required_kills}
<div>You do not have enough kills to proceed at this time.</div>
		{else}
		<!-- ************************** GLORIOUS LEVEL UP MESSAGE!  ************************ -->
	<div class='ninja-notice'>
		Your trainer puts you through your paces and you learn a great deal from your bruises. Welcome to Ninja Rank {$userLevel|escape}!
	</div>
		<p>Your strength is now {$char_data.strength}.</p>
		<p>Your speed is now {$char_data.speed}.</p>
		<p>Your stamina is now {$char_data.stamina}.</p>
		<p>Your Karma changed only a little, and is now {$char_data.karma}.</p>
		<p>Your Ki changed only a little, and is now {$char_data.ki}.</p>
		{/if}
	{/if}
	{if $userLevel + 1 gt $max_level}
<div>You enter the dojo as one of the elite ninja. No trainer has anything left to teach you.</div>
	{elseif $userKills lt $required_kills}
<div>Your trainer finds you lacking in experience. You are instructed to prove your might against more ninja before you return.</div>
	{else}
<form id="level_up" action="dojo.php" method="post" name="level_up">
  <div style='margin-top: 10px;margin-bottom: 10px;'>
    <input id="upgrade" type="hidden" value="1" name="upgrade">
    <div>Do you wish to upgrade to level {$nextLevel|escape}? <input type="submit" value="Level Up" class="formButton"></div>
  </div>
</form>
	{/if}
	
<!-- End of logged in display section -->
{/if}







<!-- Nesting of divs here to allow for bookending of the scroll images -->
<div id='scroll'>
	<div class='left-scroll-bookend'>
		<div class='right-scroll-bookend'>
			<strong id='scroll-title'>
				<a target='#scroll-reveal'>Scroll of training requirements</a>
			</strong>
		</div>
	</div>
</div>

<section id='scroll-reveal'>

	<h2>Dojo Advancement Chart</h2>

<table class='training-requirements' style='width:80%'>

	<caption colspan='100%' style='text-align:center;padding:.2em;font-size:1.3em;color:chocolate;'>
		Kills needed to progress to each level and how a ninja's stats change:
	</caption>

  <thead>
  <tr class='chart-title'>
    <td>Level</td>
    <td>Kills</td>
    <td>Strength</td>
    <td>Stamina</td>
    <td>Speed</td>
    <td>Max Health</td>
  </tr>
  </thead>
{section name="chart" start=1 loop=$max_level step=1}
  <tr>
    <td>{$level_chart|escape}</td>
    <td>{$kills_chart|escape}</td>
    <td>{$str_chart|escape}</td>
    <td>{$stamina_chart|escape}</td>
    <td>{$speed_chart|escape}</td>
    <td>{$hp_chart|escape}</td>
  </tr>
	{math assign="level_chart" equation="x + 1" x=$level_chart}
	{math assign="kills_chart" equation="x + 5" x=$kills_chart}
	{math assign="str_chart" equation="x + 5" x=$str_chart}
	{math assign="stamina_chart" equation="x + 5" x=$stamina_chart}
	{math assign="speed_chart" equation="x + 5" x=$speed_chart}
	{if $hp_chart lte $max_hp}
		{math assign="hp_chart" equation="x + 25" x=$hp_chart}
	{/if}
{/section}

</table>
	<p class='text-centered'>(Maximum level)</p>

</section>




<nav>
	<a href="map.php" class="return-to-location block">Return to the Village</a>
</nav>




<script>
{literal}
$().ready(function(){
	// Show the scroll section on a click of any part of the scroll area.
	var hidden = $('#scroll-reveal').hide();
	$('#scroll').click(function(){hidden.toggle();return false;});
});
{/literal}
</script>