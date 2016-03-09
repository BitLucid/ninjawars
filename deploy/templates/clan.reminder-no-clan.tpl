<section class='glassbox'>
  <div>You are a lone ninja, not a member of any clan.</div>

  <div>
{if $player->level gte $clan_creator_min_level}
    <a href='/clan/new'>
{/if}
      <button type='button' class='btn btn-default' {if $player->level lt $clan_creator_min_level}disabled=disabled{/if}>Start a New Clan</button>
{if $player->level gte $clan_creator_min_level}
    </a>
{else}
    <small class='glassbox de-em'>
      You can start your own clan when you reach level {$clan_creator_min_level|escape}.
    </small>
{/if}
    </div>
</section>
