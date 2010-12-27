<h1>Accept A New Clan Member</h1>

<hr>

{if !$clan}
<p>You have no clan.</p>
{elseif !$clan_joiner}
<p>There is no potential ninja specified, so the induction cannot occur.</p>
{else}
<p>{$clan_joiner_name|escape} has requested to join your clan, {$clan_name|escape}.</p>
	{if $agree}
<div style="border:1px solid #000000;font-weight: bold;">
		{if $joiner_current_clan}
  <p>This member is already part of a clan.</p>
		{elseif !$joiner_confirmation_no}
  <p>No such ninja.</p>
		{elseif $confirm eq $joiner_confirmation_no and $agree gt 0}
  <p>Request Accepted.</p>
  <p>{$clan_joiner_name|escape} is now a member of your clan.</p>
  <hr>
		{else}
  <p>This clan membership change can not be verified, please ask the ninja to request joining again.</p>
		{/if}
</div>
	{else}
<form action="clan_confirm.php?clan_id={$clan_id|escape:'url'}&amp;clan_joiner={$clan_joiner|escape:'url'}&amp;confirm={$confirm|escape:'url'}" method="post">
  <div><input id="agree" type="hidden" name="agree" value="1"><input type="submit" value="Accept Request"></div>
</form>
	{/if}
{/if}

<div class="return-to-main-link">
  <a href="{$smarty.const.WEB_ROOT}">Return to Main ?</a>
</div>
