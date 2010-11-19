<h1>Dojo Advancement Chart</h1>


<div class="description" style="margin-top: .7em;margin-bottom: .7em;">
    Hanging on the wall of the dojo is a scroll outlining the training requirements for all ninjas.
</div>

<p><a href="dojo.php">Return to Dojo</a></p>
<style>
{literal}
	table{
		width:90%;
		margin-left:5%;
		margin-right:5%;
		margin-bottom:2em;
	}
	table .char-title td{
		font-style:1.5em;
	}
{/literal}
</style>
<table>

	<caption colspan='6' style='text-align:center;padding:.2em;font-size:1.3em;color:chocolate;'>
		Kills needed to progress to each level and how a ninja's stats change:
	</caption>

  <thead>
  <tr class='chart-title'>
    <td>Level</td>
    <td>Kills</td>
    <td>Strength</td>
    <td>Stamina</td>
    <td>Speed</td>
    <td>Max Health</td>
  </tr>
  </thead>
{section name="chart" start=1 loop=$max_level step=1}
  <tr>
    <td>{$level_chart|escape}</td>
    <td>{$kills_chart|escape}</td>
    <td>{$str_chart|escape}</td>
    <td>{$stamina_chart|escape}</td>
    <td>{$speed_chart|escape}</td>
    <td>{$hp_chart|escape}</td>
  </tr>
	{math assign="level_chart" equation="x + 1" x=$level_chart}
	{math assign="kills_chart" equation="x + 5" x=$kills_chart}
	{math assign="str_chart" equation="x + 5" x=$str_chart}
	{math assign="stamina_chart" equation="x + 5" x=$stamina_chart}
	{math assign="speed_chart" equation="x + 5" x=$speed_chart}
	{if $hp_chart lte $max_hp}
		{math assign="hp_chart" equation="x + 25" x=$hp_chart}
	{/if}
{/section}

</table>
<p>(Max level)</p>
