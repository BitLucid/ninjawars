<h1>Working in the Village</h1>

{if $not_enough_energy}
    		<p class='ninja-notice'>You don't have the energy in turns to do that much work.</p>
{/if}

{if !$new_gold}
<div class="description">
    <p>On your way to the foreman's office, you pass by several peasants drenched in sweat from working in the sun all day.</p>
    <p>The foreman barely looks up at you as he busies himself with paperwork and a cigarette. "So, how much work can we expect from you?"</p>
</div>
{else}
<div class="description">
    <p>
        On your way back from the fields, you pass by a few young children 
        chasing grasshoppers in the tall grass.</p>
    
    <p>The foreman hands you a small pouch of gold as he says 
    "Care to put a little more work in? I'll pay the same rate."</p>
    
    <p class='ninja-notice'>You have worked for {$worked} {if $worked eq 1}turn{else}turns{/if} and earned {$new_gold} gold.</p>
    
</div>
{/if}

<p>You can earn money by working in the village fields. Field work will exchange turns for gold.</p>
<div>The current work exchange rate: 1 Turn = {$work_multiplier} Gold.</div>
{if $is_logged_in}
<form id="work" action="work.php" method="post" name="work">
  <div>
    <p>
      Work in the Fields?
    </p>
    <input id="worked" type="text" size="3" maxlength="3" name="worked" class="textField">
    <input id="workButton" type="submit" value="Turns" name="workButton" class="formButton">
  </div>
</form>
{else}
<p>
To earn pay for your work you must first <a href="signup.php?referrer=">become a citizen of this village.</a>
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

