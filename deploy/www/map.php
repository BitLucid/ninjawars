<?php
$private = false;
$alive   = false;


if ($error = init($private, $alive)) {
	display_error($error);
} else {

$locations = array(
	array('name'=>'Shrine', 'url'=>'shrine.php', 'image'=>'shrine.png', 'tile_image'=>'concentric_shrine.png')
	, array('name'=>'Doshin', 'url'=>'doshin_office.php', 'image'=>'doshin.png', 'tile_image'=>'doshin_building.png')
	, array('name'=>'Fields', 'url'=>'work.php', 'tile_image'=>'concentric_field.png')
	, array('name'=>'Shop',   'url'=>'shop.php', 'tile_image'=>'concentric_star.png')
	, array('name'=>'Dojo',   'url'=>'dojo.php', 'tile_image'=>'concentric_leaf.png')
	, array('name'=>'Casino', 'url'=>'casino.php', 'tile_image'=>'elemental_coin.png')
);

// Array that simulates database information for switching out for an npc database solution.
$npcs = array(
	  array('name'=>'Peasant',        'identity'=>'peasant', 'image'=>'fighter.png')
	, array('name'=>'Thief',          'identity'=>'thief', 'image'=>'thief.png')
	, array('name'=>'Merchant',       'identity'=>'merchant', 'image'=>'merchant.png')
	, array('name'=>"Emperor's Guard", 'identity'=>'guard', 'image'=>'guard.png')
	, array('name'=>'Samurai',         'identity'=>'samurai', 'image'=>'samurai.png')
);

display_page(
	'map.tpl'
	, 'Map'
	, array(
		'locations'   => $locations
		, 'npcs'      => $npcs
	)
	, array(
		'quickstat' => 'player'
	)
);
}
?>
