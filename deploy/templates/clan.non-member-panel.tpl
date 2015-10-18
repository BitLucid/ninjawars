<section class="glassbox">
	<div>You are a lone ninja, not a member of any clan.</div>

	<div><a href="clan.php?command=join">View clans available to join</a></div>

{if $clan_id_viewed}
	<div>
		<a href="clan.php?command=join&amp;clan_id={$clan_id_viewed|escape}&amp;process=1">
			Send a request to join the Clan {$viewed_clan_name|escape}
		</a>
	</div>
{/if}

</section>
