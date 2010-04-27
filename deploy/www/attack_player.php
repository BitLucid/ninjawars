<?php
$private    = false;
$alive      = false;
$page_title = "Village";
$quickstat  = "player";

include SERVER_ROOT."interface/header.php";

function village_locations_list() {
    $locations = array(
        array('name'=>'Shrine', 'url'=>'shrine.php', 'image'=>'shrine.png'), 
        array('name'=>'Doshin', 'url'=>'doshin_office.php', 'image'=>'doshin.png'), 
        array('name'=>'Fields', 'url'=>'work.php'), 
        array('name'=>'Shop', 'url'=>'shop.php'), 
        array('name'=>'Dojo', 'url'=>'dojo.php'), 
        array('name'=>'Casino', 'url'=>'casino.php')
    );
    return $locations;
}

function npcs_list() {
    // Array that simulates database information for switching out for an npc database solution.
    $npcs = array(
        array('name'=>'Villager', 'url'=>'attack_npc.php?attacked=1&amp;victim=villager', 'image'=>'fighter.png'), 
        array('name'=>'Thief', 'url'=>'attack_npc.php?attacked=1&amp;victim=thief', 'image'=>'thief.png'), 
        array('name'=>'Merchant', 'url'=>'attack_npc.php?attacked=1&amp;victim=merchant', 'image'=>'merchant.png'), 
        array('name'=>"Emperor's Guard", 'url'=>'attack_npc.php?attacked=1&amp;victim=guard', 'image'=>'guard.png'), 
        array('name'=>'Samurai', 'url'=>'attack_npc.php?attacked=1&amp;victim=samurai', 'image'=>'samurai.png')
    );
    return $npcs;
}

$locations = village_locations_list();

$npcs = npcs_list();

echo render_template('attack_player.tpl', array('locations'=>$locations, 'npcs'=>$npcs));

include SERVER_ROOT."interface/footer.php";
?>
