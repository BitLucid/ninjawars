<?php
$private = false;
$alive   = false;


if ($error = init($private, $alive)) {
	display_error($error);
} else {

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
	  array('name'=>'Villager',        'url'=>'attack_npc.php?attacked=1&victim=villager', 'image'=>'fighter.png')
	, array('name'=>'Thief',           'url'=>'attack_npc.php?attacked=1&victim=thief',    'image'=>'thief.png')
	, array('name'=>'Merchant',        'url'=>'attack_npc.php?attacked=1&victim=merchant', 'image'=>'merchant.png')
	, array('name'=>"Emperor's Guard", 'url'=>'attack_npc.php?attacked=1&victim=guard',    'image'=>'guard.png')
	, array('name'=>'Samurai',         'url'=>'attack_npc.php?attacked=1&victim=samurai',  'image'=>'samurai.png')
);

display_page(
	'attack_player.tpl'
	, 'Village'
	, array(
		'locations'   => $locations
		, 'npcs'      => $npcs
		, 'show_ad'   => rand(1, 3) // Only show the ad in the village 1/3rd of the time.
	)
	, array(
		'quickstat' => 'player'
	)
);
}
?>
