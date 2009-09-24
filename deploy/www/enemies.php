<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Enemy List";
include SERVER_ROOT."interface/header.php";


function render_enemy_matches($match_string){
    global $sql;
    $user_id = get_user_id();
    $enemy_rows = $sql->FetchAll("SELECT player_id, uname from players where uname ~* '".sql($match_string)."' and confirmed = 1 and player_id != '".sql($user_id)."' limit 11");
    $res = null;
    foreach($enemy_rows as $loop_enemy){
        $res .= "<li><a href='enemies.php?add_enemy={$loop_enemy['player_id']}'><img src='".IMAGE_ROOT."icons/add.png' alt='Add enemy:'> {$loop_enemy['uname']}</a></li>";
    }
    if(!empty($enemy_rows) && count($enemy_rows)>10){
        $res .= "<li>...with more matches...</li>";
    }
    return $res;
}

function add_enemy($enemy_id){
    if(!is_numeric($enemy_id)){
        throw new Exception('Enemy id to add must be present to succeed.');
    }
    $enemy_list = get_setting('enemy_list');
    $enemy_list[$enemy_id] = $enemy_id;
    set_setting('enemy_list', $enemy_list);
}

function remove_enemy($enemy_id){
    if(!is_numeric($enemy_id)){
        throw new Exception('Enemy id to remove must be present to succeed.');
    }
    $enemy_list = get_setting('enemy_list');
    if(isset($enemy_list[$enemy_id]))
        unset($enemy_list[$enemy_id]);
    set_setting('enemy_list', $enemy_list);
}


function render_current_enemies($enemy_list){
    $enemy_section = '';
    if(!is_array($enemy_list)){
        return $enemy_section;
    }
    foreach($enemy_list as $loop_enemy_id){
        $enemies['player_id'] = $loop_enemy_id;
        $enemies['player_name'] = player_name_from_id($loop_enemy_id);
        $enemy_section .= "<li><a href='enemies.php?remove_enemy=$loop_enemy_id'><img src='".IMAGE_ROOT."icons/delete.png' alt='remove'></a> <a href='player.php?player_id=$loop_enemy_id'>".out($enemies['player_name'])."</a></li>";
        // TODO: Turn this into a template render.
    }
    return $enemy_section;
}


// function remove_enemy($enemy_id, $settings)
//$settings = save_settings(array());
//set_setting('bob', 5);
//set_setting('bam', 'piehole');

$match_string = in('enemy_match', null, 'no filter');
$add_enemy = in('add_enemy', null, 'toInt');
$remove_enemy = in('remove_enemy', null, 'toInt');

$enemy_list = get_setting('enemy_list');


if($match_string){
    $found_enemies = render_enemy_matches($match_string);
}

if(is_numeric($remove_enemy)){
    remove_enemy($remove_enemy);
    $enemy_list = get_setting('enemy_list'); // Update to new enemy list.
}

if(is_numeric($add_enemy)){
    add_enemy($add_enemy);
    $enemy_list = get_setting('enemy_list'); // Update to new enemy list.
}

$enemy_section = render_current_enemies($enemy_list);


$parts = get_certain_vars(get_defined_vars());

echo render_template('enemies.tpl', $parts);

include SERVER_ROOT."interface/footer.php";
?>
