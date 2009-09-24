<h2>Enemies</h2>

<div id='ninja-enemy'>
    Mark a ninja as an enemy:
    <form id='enemy-add' action='enemies.php' method='get' name='enemy_add'>
        <input type='text' maxlength='50' name='enemy_match' class='textField'>
        <input type='submit' value='Find Enemies' class='formButton'>
    </form>    
</div>

{if $found_enemies}
<ul>

{$found_enemies}

</ul>
{/if}


<ul>

{$enemy_section}

</ul>


<!--
<div id='ninja-search'>
    Find a ninja enemy:
    <form id='player_search' action='list_all_players.php' method='get' name='player_search'>
        <input id='searched' type='text' maxlength='50' name='searched' class='textField'>
        <input type='submit' value='Find' class='formButton'>
    </form>
</div>
-->

