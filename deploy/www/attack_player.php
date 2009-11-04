<?php
$private    = true;
$alive      = false;
$page_title = "Village";
$quickstat  = "player";

include SERVER_ROOT."interface/header.php";


function render_village_locations(){
    $locations = array(
        array('name'=>'Shrine', 'url'=>'shrine.php', 'image'=>'shrine.png'), 
        array('name'=>'Doshin', 'url'=>'doshin_office.php', 'image'=>'doshin.png'), 
        array('name'=>'Fields', 'url'=>'work.php'), 
        array('name'=>'Shop', 'url'=>'shop.php'), 
        array('name'=>'Dojo', 'url'=>'dojo.php'), 
        array('name'=>'Casino', 'url'=>'casino.php')
    );
    return render_template('village_locations.tpl', array('locations'=>$locations, 'IMAGE_ROOT'=>IMAGE_ROOT));
}

function render_npc_list(){
    // Array that simulates database information for switching out for an npc database solution.
    $npcs = array(
        array('name'=>'Villager', 'url'=>'attack_npc.php?attacked=1&victim=villager', 'image'=>'fighter.png'), 
        array('name'=>'Thief', 'url'=>'attack_npc.php?attacked=1&victim=thief', 'image'=>'thief.png'), 
        array('name'=>'Merchant', 'url'=>'attack_npc.php?attacked=1&victim=merchant', 'image'=>'merchant.png'), 
        array('name'=>"Emperor's Guard", 'url'=>'attack_npc.php?attacked=1&victim=guard', 'image'=>'guard.png'), 
        array('name'=>'Samurai', 'url'=>'attack_npc.php?attacked=1&victim=samurai', 'image'=>'samurai.png')
    );
    return render_template('npc_list.tpl', array('npcs'=>$npcs)); 
}

$locations = render_village_locations();

$npcs = render_npc_list();


echo render_template('attack_player.tpl', array('locations'=>$locations, 'npcs'=>$npcs));

include SERVER_ROOT."interface/footer.php";
?>
