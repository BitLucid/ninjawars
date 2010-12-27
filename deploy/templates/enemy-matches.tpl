<ul>
{foreach from=$enemies item="loop_enemy"}
    <li><a href='enemies.php?add_enemy={$loop_enemy.player_id}'><img src='{$smarty.const.IMAGE_ROOT}icons/add.png' alt='Add enemy:'> Add {$loop_enemy.uname|escape}</a></li>
{/foreach}

{if count($enemies) > 10}
    <li>...with more matches...</li>
{/if}
</ul>
