<style>
.boxed-in{
	border:1px solid #000000;font-weight: bold;margin-left:1em;margin-right:1em;
}
</style>


<h1>Accept A New Clan Member</h1>

<hr>

{if !$clan}
<p>You have no clan.</p>
{elseif $error}
<div class='parent'>
	<p class='error child'>{$error}</p>
</div>
{else}
	<p>{$join_requester_name|escape} has requested to join your clan, {$clan_name|escape}.</p>


	{if $agree}
		{if $ninja_added}
	<section class='boxed-in'>
	  <p>Request Accepted.</p>
	  <p>{$join_requester_name|escape} is now a member of your clan.</p>
	</section>
		{/if}
	{else} <!-- Not yet agreed -->
		{if $clan}
	<form action="clan_confirm.php?clan_id={$clan_id|escape:'url'}&amp;clan_joiner={$join_requester_id|escape:'url'}" method="post">
		<input id="agree" type="hidden" name="agree" value="1">
		<input name='confirm' type='hidden' value='{$confirm|escape}'>
		<div><input type="submit" value="Accept request from {$join_requester_name}"></div>
	</form>
		{/if}
	{/if}
{/if}

<div class="return-to-main-link">
  <a href="{$smarty.const.WEB_ROOT}/clan.php">Return to Clan Page</a>
</div>
