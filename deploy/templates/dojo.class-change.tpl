{if $classChangeSequence < 2}
A <span class='white-robed-monk'>white-robed monk</span> stands near the entrance to the dojo.
{/if}

{if $classChangeSequence < 1}
<p>The <span class='white-robed-monk'>white monk</span> approaches you and offers to give you <a href="dojo.php?classChangeSequence=1">the knowledge of your enemies</a> at the cost of your own memories.</a></p>
{/if}

<p>The white monk approaches you and offers to give you the knowledge of your enemies at the cost of your own memories.</p>

{if $classChangeSequence eq 1}
    {foreach from=$classes item='class' key='identity'}
<form id="Buy_classChange" action="dojo.php" method="post" name="changeofclass">
<div style='margin-top: .3em;margin-bottom: .3em;'>
Give up your memories of {$class_change_cost|escape} kills to learn the skills of the <span class='class-name {$class.theme}'>{$class.class_name|escape}</span> ninja?
<input id='classchangeSequence' name='classChangeSequence' type='hidden' value='2'>
<input id='current_class' name='current_class' type='hidden' value='{$userClass|escape}'>
<input id='requested_identity' name='requested_identity' type='hidden' value='{$identity|escape}'>
<input type="submit" value="Become A {$class.class_name|escape} Ninja" class="formButton">
</div>
</form>
    {/foreach}
{elseif $classChangeSequence eq 2}
<p>
The monk tosses white powder in your face. You blink at the pain, and when you open your eyes, everything looks different somehow.</p>
<p>The white monk smiles at you and walks slowly back to the dojo.</p>
{/if}
<hr>
