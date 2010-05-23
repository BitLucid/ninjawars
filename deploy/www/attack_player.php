<?php
init();

$locations = array(
	array('name'=>'Shrine', 'url'=>'shrine.php', 'image'=>'shrine.png')
	, array('name'=>'Doshin', 'url'=>'doshin_office.php', 'image'=>'doshin.png')
	, array('name'=>'Fields', 'url'=>'work.php')
	, array('name'=>'Shop',   'url'=>'shop.php')
	, array('name'=>'Dojo',   'url'=>'dojo.php')
	, array('name'=>'Casino', 'url'=>'casino.php')
);

// Array that simulates database information for switching out for an npc database solution.
$npcs = array(
	  array('name'=>'Villager',        'url'=>'attack_npc.php?attacked=1&amp;victim=villager', 'image'=>'fighter.png')
	, array('name'=>'Thief',           'url'=>'attack_npc.php?attacked=1&amp;victim=thief',    'image'=>'thief.png')
	, array('name'=>'Merchant',        'url'=>'attack_npc.php?attacked=1&amp;victim=merchant', 'image'=>'merchant.png')
	, array('name'=>"Emperor's Guard", 'url'=>'attack_npc.php?attacked=1&amp;victim=guard',    'image'=>'guard.png')
	, array('name'=>'Samurai',         'url'=>'attack_npc.php?attacked=1&amp;victim=samurai',  'image'=>'samurai.png')
);

echo render_page('attack_player.tpl', "Village", array('locations'=>$locations, 'npcs'=>$npcs, 'quickstat'=>'player'), $options=array('alive'=>false, 'private'=>false));
?>
