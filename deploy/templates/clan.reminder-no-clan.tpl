<section class='glassbox'>
  <div>You are a lone ninja, not a member of any clan.</div>

  <div>
{if $player->level() gte $clan_creator_min_level}
    <a href='clan.php?command=new'>
{/if}
      <button type='button'{if $player->level() lt $clan_creator_min_level} class="disabled"{/if}>Start a New Clan</button>
{if $player->level() gte $clan_creator_min_level}
    </a>
{/if}
    </div>

    <small class='glassbox de-em'>
      You can start your own clan when you reach level {$clan_creator_min_level|escape}.
    </small>
</section>
