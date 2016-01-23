{if $dimmak_sequence neq 1} {* Link to start the Dim Mak sequence *}
The <span class='black-robed-monk'>black monk</span> approaches you and offers to give you <a href="dojo.php?dimmak_sequence=1">power over life and death,</a> at the cost of some of your memories.
{else} {* Strips the link after it's been clicked. *}
The <span class='black-robed-monk'>black monk</span> offers to give you power over life and death, at the cost of some of your memories.
{/if}
<br>
{/if}

{if $dimmak_sequence eq 1}
<form id="Buy_DimMak" action="dojo.php?dimmak_sequence=2" method="post" name="buy_dimmak">
<div style='margin-top: 10px;margin-bottom: 10px;'>
Give up your memories of {$dim_mak_cost|escape} kills for the DimMak Scroll?
<input id="dimmak_sequence" type="hidden" value="2" name="obtainscroll">
<input type="submit" value="Obtain Dim Mak" class="formButton">
</div>
</form>
{elseif $dimmak_sequence eq 2}
<p>The monk meditates for a moment, then passes his hand over your forehead. A black fog passes over your vision and you feel a moment of dizziness.</p>
<p>For a moment you become aware of the dirt on the walls, the darkness in the room, a <a href='npc.php?victim=spider' class='npc'>Spider</a> crawling across the wall.</p>
<p>He hands you a scroll that seems to writhe with shadows.</p>
{/if}
<hr>
