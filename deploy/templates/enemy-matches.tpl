<ul id='enemy-match-list'>
{foreach from=$enemies item="loop_enemy"}
    <li id='enemy-match-{$loop_enemy.player_id}'>
    	<form name='add-an-enemy' action='' method='POST'>
	    	<input type='hidden' name='command' value='add'>
	    	<input type='hidden' name='add_enemy' value='{$loop_enemy.player_id}'>
	    	<button type='submit'><img src='{$smarty.const.IMAGE_ROOT}icons/mono/plus32.png' height='16' width='16' title='Add Enemy' alt=''> Add {$loop_enemy.uname|escape}</button>
    	</form>
    </li>
{/foreach}

{if count($enemies) > 10}
    <li>...with more matches...</li>
{/if}
</ul>
