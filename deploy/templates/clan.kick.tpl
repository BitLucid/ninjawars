{if $kicked == ''} {* Clan Leader Action Kick a chosen member *}
<form id='kick_form' action='/clan/kick' method='get' name='kick_form'>
	<div>
		Kick:
		<select id='kicked' name='kicked'>
			<option value=''>--Pick a Member--</option>
{* TODO - change this html_options control *}
		{foreach from=$members_and_ids key=row item=l_member}
			<option value='{$l_member.player_id|escape}'>{$l_member.uname|escape}</option>
		{/foreach}
		</select>
		<input type='submit' value='Kick' class='formButton'>
	</div>
</form>
{/if}
{* Otherwise the kick was successful and the action message will be displayed. *}
