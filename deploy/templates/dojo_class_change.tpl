{if $classChangeSequence neq 2}
A white-robed monk stands near the entrance to the dojo.

	{if $classChangeSequence neq 1} {* Link to start the Class Change sequence *}
The white monk approaches you and offers to give you <a href="dojo.php?classChangeSequence=1">the knowledge of your enemies</a> at the cost of your own memories.</a>
	{else} {* Strips the link after it's been clicked. *}
The white monk approaches you and offers to give you the knowledge of your enemies at the cost of your own memories.
	{/if}
<br>
{/if}

{if $classChangeSequence eq 1}
<form id="Buy_classChange" action="dojo.php?classChangeSequence=2" method="post" name="changeofclass">
  <div style='margin-top: 10px;margin-bottom: 10px;'>
    Trade your memories of {$classChangeCost|escape} kills to change your skills to those of the {$destination_class|escape} ninja?
    <input id="classchangeSequence" type="hidden" value="2" name="wantanewclass">
    <input type="submit" value="Become A {$destination_class|escape} Ninja" class="formButton">
  </div>
</form>
{elseif $classChangeSequence eq 2}
The monk tosses white powder in your face. You blink at the pain, and when you open your eyes, everything looks different somehow.<br>
The white monk grins at you and walks slowly back to the dojo.<br>
{/if}
<hr><br>
