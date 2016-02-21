<ul id="clan-members-list">
{* clan->getMembers() incurs a DB query *}
{* TODO refactor this when clan is refactored with lazy-loading *}
{assign var="members" value=$clan->getMembers()}
{foreach from=$members key="row" item="member"}
	<li class="member-info {if $member.leader} clan-leader{/if}">
		<a href="/player?player={$member.uname|escape:"url"}">
			<span class="member size{$member.size|escape}">{$member.uname|escape}</span>
			<span class="avatar"><img alt="{$member.uname|escape}" src="{$member.gravatar_url|escape}"></span>
		</a>
	</li>
{/foreach}
</ul>
<div id="clan-members-count" style="clear:both;margin-top:1em;">
	Clan Members: {$members|@count}
</div>
