    <div id='clan-members'>
      <h3 id='clan-members-title'>{$clan_name|escape}</h3>
{literal}
      <style type='text/css'>
		#clan-avatar {
			max-height: 240px;
			max-width: 400px;
		}

		#clan-info {
			margin-left: auto;
			margin-right: auto;
		}

		#clan-info #clan-avatar-section {
			margin: 0 auto;
			text-align: center;
		}

		#clan-description {
			max-height: 240px;
			max-width: 400px;
		}

		#clan-info #clan-description {
			margin-left: auto;
			margin-right: auto;
		}

		#clan-members-list .clan-leader .member {
			background-color: rgb(60,60,120);
		}
      </style>
{/literal}
      <div id='clan-info'>
{if $avatar_url}
        <div id='clan-avatar-section'>
          <img id='clan-avatar' alt='Clan Avatar' title='{$clan_name|escape}' src='{$avatar_url|escape}'>
        </div>
{/if}
{if $clan_description}
        <div id='clan-description'>
          {$clan_description|escape}
        </div>
{/if}
      </div>
      <ul id='clan-members-list'>
{foreach from=$members_array key=row item=member}
        <li class='member-info {if $member.leader} clan-leader{/if}'>
          <a href='player.php?player={$member.uname|escape:'url'}'>
            <span class='member size{$member.size}'>{$member.uname|escape}</span>
            <span class="avatar"><img alt="{$member.uname|escape}" src="{$member.gravatar_url|escape}"></span>
          </a>
        </li>
{/foreach}
      </ul>
    </div>
    <div id='clan-members-count' style='clear:both;margin-top:1em;'>
	    Clan Members: {$members_array|@count}
    </div>
