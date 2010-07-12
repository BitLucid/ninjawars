{if $dimmak_sequence neq 2}
A black-robed monk stands near the entrance to the dojo.

	{if $dimmak_sequence neq 1} {* Link to start the Dim Mak sequence *}
The black monk approaches you and offers to give you <a href="dojo.php?dimmak_sequence=1">power over life and death,</a> at the cost of some of your memories.
	{else} {* Strips the link after it's been clicked. *}
The black monk offers to give you power over life and death, at the cost of some of your memories.
	{/if}
<br>
{/if}

{if $dimmak_sequence eq 1}
<form id="Buy_DimMak" action="dojo.php?dimmak_sequence=2" method="post" name="buy_dimmak">
  <div style='margin-top: 10px;margin-bottom: 10px;'>
    Trade your memories of {$dimMakCost|escape} kills for the DimMak Scroll?
    <input id="dimmak_sequence" type="hidden" value="2" name="obtainscroll">
    <input type="submit" value="Obtain Dim Mak" class="formButton">
  </div>
</form>
{elseif $dimmak_sequence eq 2}
The monk meditates for a moment, then passes his hand over your forehead. You feel a moment of dizziness.
He hands you a pure black scroll.<br>
{/if}

<hr>
