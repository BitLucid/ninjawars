<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Enemy List";
include SERVER_ROOT."interface/header.php";


function render_enemy_matches($match_string){
    global $sql;
    $enemy_rows = $sql->FetchAll("SELECT player_id, uname from players where uname ~* '".sql($match_string)."'");
    $res = null;
    foreach($enemy_rows as $loop_enemy){
        $res .= "<li><a href='enemies.php?enemy_id={$loop_enemy['player_id']}'>Potential match: {$loop_enemy['uname']}</a></li>";
    }
    return $res;
}

function add_enemy($enemy_id, $settings){
    if(!isset($settings['enemy_list']) || empty($settings['enemy_list'])){
        $settings['enemy_list'] = array($enemy_to_add);
    } else {
        $settings['enemy_list'] = $settings['enemy_list'] + array($enemy_id);
    }
    return save_settings($settings);
}

// function remove_enemy($enemy_id, $settings)

$settings = get_settings();

$new_enemy_id = in('new_enemy_id');
$new_enemy_id = 100772;
$match_string = in('enemy_to_match', null, 'no filter');
$enemy_to_add = in('enemy_to_add');


set_setting('blarg', false);

if($match_string){
    $found_enemies = render_enemy_matches($match_string);
}

if($enemy_to_add){
    $settings = add_enemy($enemy_id, $settings);
}




$enemy_list = array();
if(isset($settings['enemy_list']) && !empty($settings['enemy_list'])){
    $enemy_list = $settings['enemy_list'];
}


if($new_enemy_id){
    $enemy_list[$new_enemy_id] = $new_enemy_id;
    // Save the new enemy, indexed by id.
    set_setting('enemy_list', $enemy_list);
}

var_dump($enemy_list);

$enemy_section = '';
foreach($enemy_list as $loop_enemy_id){
    $enemies['player_id'] = $loop_enemy_id;
    $enemies['player_name'] = player_name_from_id($loop_enemy_id);
    $enemy_section .= "<li><a href='player.php?player_id=$loop_enemy_id'>{$enemies['player_name']}</a></li>";
    // TODO: Turn this into a template render.
}

$parts = get_certain_vars(get_defined_vars());

echo render_template('enemies.tpl', $parts);

include SERVER_ROOT."interface/footer.php";
?>
