<!-- Nesting of divs here to allow for bookending of the scroll images -->
<div id='scroll'>
    <div class='left-scroll-bookend'>
        <div class='right-scroll-bookend'>
            <strong id='scroll-title'>
                <a target='#scroll-reveal'>Scroll of training requirements</a>
            </strong>
        </div>
    </div>
</div>

<section id='scroll-reveal'>
    <h2>Dojo Advancement Chart</h2>

    <table class='training-requirements' style='width:80%'>
        <caption colspan='100%'>
            Kills needed to progress to each level and how a ninja's stats change:
        </caption>
        <thead>
            <tr class='chart-title'>
                <td>Level</td>
                <td>Kills Needed</td>
                <td>Strength</td>
                <td>Stamina</td>
                <td>Speed</td>
                <td>Max Health</td>
            </tr>
        </thead>
{assign var="level_chart"   value=1}
{assign var="kills_chart"   value=0}
{assign var="str_chart"     value=1}
{assign var="speed_chart"   value=1}
{assign var="stamina_chart" value=1}
{assign var="hp_chart"      value=1}
        <tbody>
{section name="chart" start=1 loop=$max_level+1 step=1}
            <tr>
                <td>{$level_chart|escape}</td>
                <td>{$kills_chart|escape}</td>
                <td>{strength_by_level level=$level_chart}</td>
                <td>{stamina_by_level level=$level_chart}</td>
                <td>{speed_by_level level=$level_chart}</td>
                <td>{max_health_by_level level=$level_chart}</td>
            </tr>
    {assign var="level_chart" value=1+$level_chart}
    {assign var="kills_chart" value=5+$kills_chart}
{/section}
        </tbody>
    </table>

    <p class='text-centered'>(Maximum level)</p>
</section>

<script>
{literal}
$().ready(function() {
          // Show the scroll section on a click of any part of the scroll area.
          var hidden = $('#scroll-reveal').hide();
          $('#scroll').click(function(){hidden.toggle();return false;});
          });
{/literal}
</script>
