<p>The <span class='white-robed-monk'>white monk</span> approaches you and offers to give you the knowledge of your enemies at the cost of your own memories.</p>

{foreach from=$classOptions item='class' key='identity'}
<form id="Buy_classChange" action="/dojo/changeClass" method="post" name="changeofclass">
    <div style='margin-top: .3em;margin-bottom: .3em;'>
        Train for {$class_change_cost|escape} turns to learn the skills of the <span class='class-name {$class.theme}'>{$class.class_name|escape}</span> ninja?
        <input id='requested_identity' name='requested_identity' type='hidden' value='{$identity|escape}'>
        <input type="submit" value="Become A {$class.class_name|escape} Ninja" class="formButton">
    </div>
</form>
{/foreach}
<a href="/dojo/">Walk away...</a>
<hr>
