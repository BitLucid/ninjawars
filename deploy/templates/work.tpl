<script type='text/javascript'>
{literal}
$(document).ready(function (){
	$('#attack-peasant-link').click(function () {
		return confirm('A peasant?  Or a disguised ninja?  Kill one of the peasants?');
	});
	$('#attack-samurai-link').click(function () {
		return confirm('A samurai. Kill him?');
	});
});
{/literal}
</script>

<h1>Working in the Village</h1>

{if $not_enough_energy}
    		<p class='ninja-notice'>You don't have the energy in turns to do {if $worked} {$worked} turns of work.{else} that much work.{/if}</p>
{/if}

{if !$new_gold}
<div class="description">
<!-- For google ad targetting -->
<!-- google_ad_section_start -->
    <p>On your way to the foreman's office, you pass by several <a href='npc.php?attacked=1&victim=villager' target='main' id='attack-peasant-link' title='A peasant?  Or a disguised ninja?  Kill one of the peasants.' class='npc'>peasants</a> drenched in sweat from working in the sun all day.</p>
    <p><a href='npc.php?attacked=1&victim=samurai' target='main' title='A samurai?  Kill him.' id='attack-samurai-link' class='npc'>A foreman in samurai armor</a> barely looks up at you as he busies himself with paperwork and a cigarette. </p>
    <p class='speech'>So, how much work can we expect from you?</p>
<!-- google_ad_section_end -->
</div>
{else}
<div class="description">
    <p>
        On your way back from the fields, you pass by a few young children 
        chasing grasshoppers in the tall grass.</p>
    <p>You see a <a href='npc.php?victim=viper' class='npc'>Viper</a> in the tall grass.</p>
    
    <p>The samurai foreman hands you a small pouch of gold as he says
    <em class='speech'>Care to put a little more work in? I'll pay the same rate.</em></p>
    
    <p class='ninja-notice'>You have worked for {$worked} {if $worked eq 1}turn{else}turns{/if} and earned {$new_gold} gold.</p>
    
</div>
{/if}

<p>You can earn money by working in the village fields. Field work will exchange turns for gold.</p>
<div>The current work pay rate is: <span style='color:turquoise;'>1 Turn</span> = <span class='gold'>{$work_multiplier} Gold</span>.</div>
{if $is_logged_in}
<form id="work" action="work.php" method="post" name="work">
  <div>
    Work in the fields for: <input id="worked" type="text" size="3" maxlength="3" name="worked" class="textField" value='{$recommended_to_work}'>
    <input id="workButton" type="submit" value="Turns" name="workButton" class="formButton">
  </div>
</form>
<p class='gold-count'>
  Current gold: {$gold|escape}
<p>

{else}
<p>
To earn pay for your work you must first <a href="signup.php">become a citizen of this village.</a>
</p>
{/if}
<hr>



<!-- Google Ad -->
<script type="text/javascript"><!--
google_ad_client = "pub-9488510237149880";
/* 300x250, created 12/17/09 */
google_ad_slot = "9563671390";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>

