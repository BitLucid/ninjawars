

    <div id='clan-members'>
			<h3 id='clan-members-title'>{$clan_name|escape}</h3>
			<ul id='clan-members-list'>
			
			
{foreach from=$members_array key=row item=member}

		<li class='member-info'>
                <a href='player.php?player={$member.uname|escape:'url'}'>
				<span class='member size{$member.size} {$current_leader_class}'>
				    {$member.uname|escape}
				</span>
				
                <span class="avatar"><img alt="" src="{$member.gravatar_url|escape}"></span>
		
        		</a>
		</li>
{/foreach}

        </ul>
	</div>
	<div id='clan-members-count' style='clear:both;margin-top:1em;'>Clan Members: {$count}</div>

