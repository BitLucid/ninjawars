    <div id="clan-members">
      <h3 id="clan-members-title">{$clan->getName()|escape}</h3>
<div id="clan-info">
{if $clan->getAvatarUrl()}
<div id="clan-avatar-section">
          <img id="clan-avatar" alt="Clan Avatar" title="{$clan->getName()|escape}" src="{$clan->getAvatarUrl()|escape}">
        </div>
{/if}
{if $clan->getDescription()}
        <div id="clan-description">
          {$clan->getDescription()|escape}
        </div>
{/if}
      </div>
