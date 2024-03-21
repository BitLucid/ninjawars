{literal}
<style>
/** Scroll css handled in dojo.scroll.tpl **/
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
/** Training requirements in dojo.scroll.tpl as well **/
</style>
{/literal}

<h1>Dojo</h1>
<nav>
	<a href="/map" class="return-to-location block">Return to the Village</a>
</nav>

<div class="description glassbox">
  <figure class='float-left glassbox'>
    <img 
        src='/images/scenes/dojo-courtyard.jpg' 
        width='500' 
        class='img-fluid mx-auto d-block'
        alt='Training in the Dojo Courtyard' />
  </figure>
  <p>
    Approaching the most majestic structure in the village, you ascend the steps leading to a magnificent pagoda adorned with jade tiles and freshly painted red wooden beams. Within its walls, a prestigious dojo hones the skills of numerous esteemed ninja.
  </p>
  <p>
    As you approach, you can hear the sounds of fighting coming from the wooden doors in front of you.
  </p>
</div>

{if $error}
<div class='parent'>
    <p class='ninja-error'>{$error}</p>
</div>
{/if}

<section class='glassbox'>
{foreach from=$dojoSections item="section"}
    {include file="dojo.$section.tpl"}
{/foreach}
</section>
