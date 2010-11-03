<h1>Dojo Advancement Chart</h1>
<div class="description">
  <div style="margin-top: 10px;margin-bottom: 10px;">
    Hanging on the wall of the dojo is a scroll outlining the training requirements for all ninjas.
  </div>
</div>

<a href="dojo.php">Return to Dojo</a><hr>
Shows how many kills you need to progress to the next level and how your stats will change:
<table>
  <tr>
    <td>Level</td>
    <td>Kills</td>
    <td>Str</td>
    <td>Max HP</td>
  </tr>
{section name="chart" start=1 loop=$max_level step=1}
  <tr>
    <td>{$level_chart|escape}</td>
    <td>{$kills_chart|escape}</td>
    <td>{$str_chart|escape}</td>
    <td>{$hp_chart|escape}</td>
  </tr>
	{math assign="level_chart" equation="x + 1" x=$level_chart}
	{math assign="kills_chart" equation="x + 5" x=$kills_chart}
	{math assign="str_chart" equation="x + 5" x=$str_chart}
	{if $hp_chart lte $max_hp}
		{math assign="hp_chart" equation="x + 25" x=$hp_chart}
	{/if}
{/section}

</table>
