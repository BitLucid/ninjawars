<!-- This is for generating a health status bar on various pages -->
<span class='char-health-indicator'>
    <span class='char-health-border'>
        <span class='character-health-bar' style="width:{health_percent health=$health level=$level}%;">&nbsp;</span>
    </span>
    <span class='char-health-number' title="{$health} health">
        {if $health eq 0}<i class="far fa-heart" aria-hidden="true"></i> <span class='dead-notice'>Dead</span>{else}<i class="fas fa-heart" aria-hidden="true"></i> {$health}{/if}
    </span>
</span>
