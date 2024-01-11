<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<style>
{literal}
    @import url('https://fonts.googleapis.com/css2?family=Pacifico&display=swap');
    #scroll{
        margin:0 auto 1em;
        text-align:center;
    }
    #scroll .scroll-link{
        display:flex;
        justify-content:center;
        align-items:center;
        width:100%;
        min-height:270px;
    }
    .left-scroll-bookend{
        display:inline-block;
        fill:currentColor;
        background:url(/images/svg/shuriken-accent-white.svg) no-repeat left;
        height:100%;
        padding-left:57px;
        margin:0 auto;
    }
    .right-scroll-bookend{
        vertical-align:middle;
        fill:currentColor;
        background:url(/images/svg/shuriken-accent-white.svg) no-repeat right;
        height:100%;
        min-width:50%;
        padding-right:57px;
        display:inline-block;
        position:relative;        
    }
    #scroll .scroll-interior{
        background:url(/images/items/ancient-scroll.jpg) no-repeat center;
        min-height:270px;
        display:flex;
    }
    #scroll #scroll-title{
        font-family: 'Pacifico', cursive;
        text-transform:uppercase;
        text-decoration:underline;
        height:93px;
        display:flex;
        justify-content: center;
        align-items: center;
        padding: 35px .7em 35px;
        font-size: 3rem;
        color:#050505;
        text-align:center;
        font-weight:bold;
        margin:0 auto;
        cursor:pointer;
        -webkit-text-stroke-width: 1px;
        -webkit-text-stroke-color: rgb(194 67 67);
    }
    #scroll .scroll-title-area{
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .training-requirements tbody tr:nth-child(odd) {
        background-color: rgba(100, 100, 100, 0.5);
     }
     .training-requirements{
       width:80%;
       margin-left:auto;
       margin-right:auto;
     }
     .training-requirements .chart-title{
        font-size:1.5rem;
        font-weight:bold;
     }
     .training-requirements caption{
        text-align:center;padding:.2em;font-size:1.3em;color:rgb(150, 137, 128);
        padding-top: 3rem;
     }
     .training-requirements tfoot{
        background-color: rgba(100, 100, 100, 0.5);
        font-size:2rem;
        font-weight:bold;
        text-decoration:overline;
     }
{/literal}
</style>

<!-- Nesting of divs here to allow for bookending of the scroll images -->
<div id='scroll'>
    <a class='scroll-link' target='#scroll-unfurl'>
        <div class='left-scroll-bookend'>
            <div class='right-scroll-bookend'>
                <div class='scroll-interior'>
                    <div class='scroll-title-area'>
                        <strong id='scroll-title'>
                            Scroll of training requirements
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<section id='scroll-unfurled'>
    <h2>Dojo Advancement Chart</h2>

    <table class='training-requirements'>
        <caption colspan='100%'>
            <em>Kills needed to progress to each level and how a ninja's stats change:</em>
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
        <tfoot>
            <tr>
                <td colspan=6>
                    <div class='max-level text-centered' style='text-transform:smallcaps'>(Maximum level)</div>
                </td>
            </tr>
        </tfoot>
    </table>
</section>

<script>
{literal}
$().ready(function() {
    // Show the scroll section on a click of any part of the scroll area.
    var unfurled = $('#scroll-unfurled');
    unfurled.hide();
    $('#scroll').click(function(e) {
        unfurled.slideToggle('slow');
        return false;
    });
});
{/literal}
</script>
