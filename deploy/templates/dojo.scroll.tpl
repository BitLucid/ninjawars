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
{assign var="str_chart"     value=Player::baseStrengthByLevel(1)}
{assign var="speed_chart"   value=Player::baseSpeedByLevel(1)}
{assign var="stamina_chart" value=Player::baseStaminaByLevel(1)}
{assign var="hp_chart"      value=Player::maxHealthByLevel(1)}
        <tbody>
{section name="chart" start=1 loop=$max_level+1 step=1}
            <tr>
                <td>{$level_chart|escape}</td>
                <td>{$kills_chart|escape}</td>
                <td>{$str_chart|escape}</td>
                <td>{$stamina_chart|escape}</td>
                <td>{$speed_chart|escape}</td>
                <td>{$hp_chart|escape}</td>
            </tr>
    {assign var="level_chart" value=1+$level_chart}
    {assign var="kills_chart" value=5+$kills_chart}
    {assign var="str_chart" value=LEVEL_UP_STAT_RAISE+$str_chart}
    {assign var="stamina_chart" value=LEVEL_UP_STAT_RAISE+$stamina_chart}
    {assign var="speed_chart" value=LEVEL_UP_STAT_RAISE+$speed_chart}
    {if $hp_chart lt $max_hp}
        {assign var="hp_chart" value=LEVEL_UP_HP_RAISE+$hp_chart}
    {/if}
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
