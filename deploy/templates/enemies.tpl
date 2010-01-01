<h1>Enemies</h1>

{if $max_enemies neq true}
<div id='ninja-enemy'>
    Search for ninja to add as enemies:
    <form id='enemy-add' action='enemies.php' method='get' name='enemy_add'>
        <input type='text' maxlength='50' name='enemy_match' class='textField'>
        <input type='submit' value='Find Enemies' class='formButton'>
    </form>    
</div>
{/if}

{if $found_enemies}
<ul>
    {$found_enemies}
</ul>
{/if}

{if $enemy_section}
<ul>
    {$enemy_section}
</ul>

{else}
<p>You haven't decided who your enemies are yet, <a href='list_all_players.php' target='main'>find some</a>.</p>

{/if}

{$recent_attackers_section}

{$active_ninja}

