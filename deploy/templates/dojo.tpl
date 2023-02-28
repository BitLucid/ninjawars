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

<div class="description">
  <p>
    You walk up the steps to the grandest building in the village. The dojo trains many respected ninja.
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
