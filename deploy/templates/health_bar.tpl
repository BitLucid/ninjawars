<span class='char-health-indicator' style='position:relative;display:inline-block;width:100%'>
    <span class='char-health-border' style='width:100%;border: 1px solid #ee2520;display:inline-block;text-align:left'>
        <span class='character-health-bar' style="width:{health_percent health=$health level=$level}%;background-color: #ee2520;display:inline-block;">&nbsp;</span>
    </span>
    <span class='char-health-number' style='position:absolute;top:0;left:1em;color:whitesmoke;display:inline-block;text-shadow: 2px 2px 2px #000;'>
        {if $health eq 0}<span style='color:crimson;font-weight:bolder'>Dead</span>{else}{$health} health{/if}
    </span>
</span>
