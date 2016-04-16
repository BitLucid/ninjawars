
<section id='npcs'>
  <h3>Attack a:</h3>
  <div id='npc-list' class='centered'>
{foreach name="person" from=$npcs key="idx" item="npc"}
      <div class='creature person'>
        <a href='/npc/attack/{$npc.identity|escape}' target='main' class='m-box'><img alt='' src='images/characters/{$npc.image|escape:'url'|escape}'> {$npc.name|escape}</a>
      </div>
{/foreach}
{foreach name="creatures" from=$other_npcs key="idx" item="npc"}
      <div class='creature'>
        <a href='/npc/attack/{$idx|escape}' target='main' class='m-box'>
        {if isset($npc.img) && $npc.img}
          <img alt='' class='creature-image' src='images/characters/{$npc.img|escape:'url'|escape}'>
        {else}
          <i class="fa fa-asterisk" aria-hidden="true"></i>
        {/if}
        {$npc.name|escape}</a>
      </div>
{/foreach}
  </div>
</section>