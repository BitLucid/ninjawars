	<div id='clan-tags'>
			<h4 id='clan-tags-title'>
				All Clans
			</h4>
		<ul>

{foreach from=$clans key=clan_id item=clan}

		<li class='clan-tag size{$clan.score}'>
			<a href='clan.php?command=view&amp;clan_id={$clan_id}'>{$clan.name|escape}</a>
		</li>
		
{/foreach}

    	</ul>
    </div>
