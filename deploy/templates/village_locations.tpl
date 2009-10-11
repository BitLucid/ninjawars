<h3>Locations</h3>
<ul>
{section name=looploc loop=$locations}
  <li><a href='{$locations[looploc].url}'>{$locations[looploc].name}</a></li>
{/section}
</ul>
