<h3>Locations</h3>
<ul>
{section name=looploc loop=$locations}
  <li><a href='{$locations[looploc].url}'>{if $locations[looploc].image}<img src='{$IMAGE_ROOT}{$locations[looploc].image}' alt=''>{/if}{$locations[looploc].name}</a></li>
{/section}
</ul>
