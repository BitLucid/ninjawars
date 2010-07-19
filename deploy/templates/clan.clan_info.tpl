<style type='text/css'>
{literal}
#clan-avatar{
    max-height:240px;
    max-width:240px;
}
#clan-info #clan-avatar{
    text-align:center;
}

{/literal}
</style>
<div id='clan-info'>
    {if $avatar_url}
    <div id='clan-avatar-section'>
      <img id='clan-avatar' alt='Upload a photo to flickr' title='{$clan_name}' src='{$avatar_url}'>
    </div>
    {/if}
    {if $clan_description}
    <div id='clan-description'>
        {$clan_description}
    </div>
    {/if}
</div>
