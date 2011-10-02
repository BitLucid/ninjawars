<ul>
{foreach from=$enemies item="loop_enemy"}
    <li><a href='enemies.php?add_enemy={$loop_enemy.player_id}'><img src='{$smarty.const.IMAGE_ROOT}icons/mono/plus32.png' height='16' width='16' title='Add Enemy' alt='Add enemy:'> Add {$loop_enemy.uname|escape}</a></li>
{/foreach}

{if count($enemies) > 10}
    <li>...with more matches...</li>
{/if}
</ul>
