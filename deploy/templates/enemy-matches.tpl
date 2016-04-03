<ul id='enemy-match-list'>
{foreach from=$enemies item="loop_enemy"}
    <li id='enemy-match-{$loop_enemy.player_id}'>
    	<form name='add-an-enemy' action='/enemies/add' method='POST'>
	    	<input type='hidden' name='add_enemy' value='{$loop_enemy.player_id}'>
	    	<button type='submit' title='Add enemy'><i class="fa fa-plus-circle"></i> Add {$loop_enemy.uname|escape}</button>
    	</form>
    </li>
{/foreach}

{if count($enemies) > 10}
    <li>...with more matches...</li>
{/if}
</ul>
