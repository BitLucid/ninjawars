<h3>Attack a citizen:</h3>
<ul id='npc-list'>
{section name=person loop=$npcs}
    {if $npcs[person]}
  <li><a href='{$npcs[person].url}' target='main'><img alt='' src='images/characters/{$npcs[person].image}'> {$npcs[person].name}</a></li>
  {/if}
{/section}
</ul>
