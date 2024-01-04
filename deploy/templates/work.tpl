<h1>Working in Fields of Grain</h1>

<nav>
  <a href="/map" class="return-to-location block">Return to the Village</a>
</nav>

{if $not_enough_energy}
    <p class='ninja-notice'>You don't have the energy in turns to do {if $worked} {$worked} turns of work.{else} that much work.{/if}</p>
{/if}

{if !$earned_gold}
<div class="description">
<!-- For google ad targetting -->
<!-- google_ad_section_start -->
    <p>On your way to the foreman's office, you pass by several <a href='/npc/attack/peasant' class='npc click-message'>peasants</a> drenched in sweat from working in the sun all day. 
        <a href='/npc/attack/samurai' target='main' title='A samurai?  Kill him.' id='attack-samurai-link' class='npc click-message'>A foreman in samurai armor</a>
         barely looks up at you as he busies himself with counting coins and smoking a long pipe.</p>
    <p class='speech'>So, how much work can we expect from you?</p>
<!-- google_ad_section_end -->
</div>
{else}
<div class="description">
    <p>
    As you step onto the dusty path, the scorching sun beats down relentlessly, casting long shadows across the fields of golden grain. 
The air is thick with the earthy scent of freshly tilled soil, and the sounds of laborers toiling in the distance create a rhythmic melody that echoes through air.
        You pass by a few young children
        chasing grasshoppers.</p>
    <p>You see a <a href='/npc/attack/viper' class='npc'>Viper</a> in the tall grass.</p>

    <p>The samurai foreman hands you a small pouch of gold as he says
    <em class='speech'>Care to put a little more work in? I'll pay the same rate.</em></p>

    <p class='ninja-notice slide-in-from-left' style='font-size:larger'>You have worked for {$worked} {if $worked eq 1}turn{else}turns{/if} and
earned 石{$earned_gold}.</p>
</div>
{/if}

<section class='glassbox'>

{if $authenticated}
    <div style="width:40%;margin:3rem auto;">
        <form id="work" action="/work/request_work" method="post" name="work">
            <span class="input-group">
                <span class='input-group-addon'>Work for</span>
                <input id="worked" type="number" size="3" maxlength="3" min=1 max=999 name="worked" class="textField form-control">
                <span class='input-group-btn'>
                    <input id="workButton" class="formButton btn btn-primary" type="submit" value="Turns" name="workButton">
                </span>
            </span>
        </form>

    <small>Work the fields for the daimyo to earn your keep. <span
            style='color:turquoise;'>1 Turn</span> = <span class='gold'>石{$work_multiplier}</span>.</small>
    </div>
    <p class='gold-count'>
        Current gold: 石{$gold_display|escape}
    <p>
    <p>
        Current turns: <span class='turns-count'>{($char)? $char->turns : ''}</span>
    </p>
{else}
<p class='slide-in-from-left'>
To earn pay for your work you must first <a href="/signup">become a citizen of this village.</a>
</p>
{/if}

</section>
<hr style='margin-top:3rem' />

<div class='inline-block glassbox'>
    <SCRIPT charset="utf-8" type="text/javascript" src="https://ws-na.amazon-adsystem.com/widgets/q?rt=ss_ssw&ServiceVersion=20070822&MarketPlace=US&ID=V20070822%2FUS%2Fbit0d3-20%2F8003%2F0e21130c-3468-4f24-bbd7-acaeb7142afc&Operation=GetScriptTemplate"> </SCRIPT> <NOSCRIPT><A HREF="https://ws-na.amazon-adsystem.com/widgets/q?rt=ss_ssw&ServiceVersion=20070822&MarketPlace=US&ID=V20070822%2FUS%2Fbit0d3-20%2F8003%2F0e21130c-3468-4f24-bbd7-acaeb7142afc&Operation=NoScript">Amazon.com Widgets</A></NOSCRIPT>
</div>


<!-- Google Ad -->
<!-- Google ad not working currently, so commented out -->

<script type='text/javascript'>
var userStore = {if $authenticated}true{else}false{/if};
var recommendedTurns = parseInt("{$recommended_to_work|intval}") ?? 1;
{literal}
$(function () {
    $('#attack-peasant-link').click(function () {
        return confirm('A peasant?  Or a disguised ninja?  Attack one of the peasants?');
    });

    $('#attack-samurai-link').click(function () {
        return confirm('A samurai. Attack him?');
    });

    if(userStore){
        $("#worked").val(NW.storage.appState.get("worked", recommendedTurns));
        $("#work").submit(function() {
            NW.storage.appState.set("worked", $("#worked").val());
            return true;
        });
    }
});
{/literal}
</script>
