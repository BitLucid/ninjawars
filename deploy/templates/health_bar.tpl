<!-- This is for generating a health status bar on various pages -->
<style>
.char-health-indicator{
	position:relative;display:inline-block;width:100%;
}
.char-health-border{
	width:100%;border: 1px solid #ee2520;display:inline-block;text-align:left;
}
.character-health-bar{
	background-color: #ee2520;display:inline-block;
}
.char-health-number{
	position:absolute;top:0;left:1em;color:whitesmoke;display:inline-block;text-shadow: 2px 2px 2px #000;
}
.char-health-number .dead-notice{
	color:crimson;font-weight:bolder;
}
</style>
<span class='char-health-indicator'>
    <span class='char-health-border'>
        <span class='character-health-bar' style="width:{health_percent health=$health level=$level}%;">&nbsp;</span>
    </span>
    <span class='char-health-number' title="{$health} health">
        {if $health eq 0}<i class="fa fa-heart-o" aria-hidden="true"></i> <span class='dead-notice'>Dead</span>{else}<i class="fas fa-heart" aria-hidden="true"></i> {$health}{/if}
    </span>
</span>
