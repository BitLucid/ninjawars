
<section id='npcs'>
  <h3>Attack a:</h3>
  <div class='centered'>
  <ul id='npc-list'>
{foreach name="person" from=$npcs key="idx" item="npc"}
      <li class='person'><a href='/npc/attack/{$npc.identity|escape}' target='main'><img alt='' src='images/characters/{$npc.image|escape:'url'|escape}'> {$npc.name|escape}</a></li>
{/foreach}
{foreach name="creatures" from=$other_npcs key="idx" item="npc"}
      <li><a href='/npc/attack/{$idx|escape}' target='main'>
      	{if isset($npc.img) && $npc.img}
      	<img alt='' class='creature-image' src='images/characters/{$npc.img|escape:'url'|escape}'>
      	{else}<span style='width:25px;height:46px'>&#9733;</span>
      	{/if}
      	{$npc.name|escape}</a></li>
{/foreach}
  </ul>
  </div>
</section>