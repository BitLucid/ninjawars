{if $process == 1}
<div id='clan-join-request-sent' class='ninja-notice'>
	Your request to join {$viewed_clan.clan_name|escape} has been sent to {$leader.uname|escape}
</div>
{else}
<h2>Clans Available to Join</h2>
<ul>
	{foreach from=$leaders item="leader_vo"}
	<li>
		<a target='main' class='clan-join' href="clan.php?command=join&amp;clan_id={$leader_vo.clan_id|escape:'url'|escape}&amp;process=1"><img alt='' src='/images/icons/mono/usersplus32.png' height=16 width=16 style='vertical-align:middle'> Join {$leader_vo.clan_name|escape}</a>
		Its leader is <a href="player.php?player_id={$leader_vo.player_id|escape:'url'|escape}">{$leader_vo.uname|escape}</a>, level {$leader_vo.level|escape}.
		<a target='main' href="clan.php?command=view&amp;clan_id={$leader_vo.clan_id|escape:'url'|escape}">View This Clan <img alt='' src='/images/icons/mono/circleright32.png' height="16" width="16" style='vertical-align:middle'></a>
	</li>
	{/foreach}
</ul>
{/if}
