<?php


// TODO: Abstract all the unique npc behaviors into the generic system.
function get_npcs(){
	return array(
		'spider'=>array('name'=>'Spider', 'img'=>'spider_icon.png', 'damage'=>10, 'gold'=>10, 'race'=>'insect', 'strength'=>1, 'stamina'=>1, 'speed'=>1, 'ki'=>1), 
		'viper'=>array('name'=>'Black Viper', 'race'=>'animal', 'strength'=>'1', 'stamina'=>1, 'speed'=>1, 'ki'=>1, 'damage'=>99, 'status'=>POISON, 'gold'=>30),
		'fireflies'=>array('name'=>'Fireflies', 'strength'=>0), // Baseline
		'monk'=>array('name'=>'Monk', 'race'=>'human'),
		'geisha'=>array('name'=>'Geisha', 'race'=>'human'),
		'kappa'=>array('name'=>'Kappa', 'short'=>'the reptilian creature with a scooped-out head', 'race'=>'kappa'),
		'tengu'=>array('name'=>'Tengu', 'short'=>'the large winged demon', 'race'=>'tengu'),
		'oni'=>array('name'=>'Oni', 'short'=>'the horned demon', 'race'=>'oni'),
		'ushioni'=>array('name'=>'Ushi-Oni', 'race'=>'ushioni', 'img'=>'ushioni.jpg'),
		'nureonna'=>array('name'=>'Nureonna', 'img'=>'nureonna', 'race'=>'yokai', 'img'=>'nureonna.jpg'),
		'basan'=>array('name'=>'Basan', 'img'=>'basan.jpg'), // Uses default race of: creature.
		'kamaitachi'=>array('name'=>'Kama-itachi', 'img'=>'kamaitachi.jpg', 'race'=>'yokai'),
		'nuribotoke'=>array('name'=>'Nuri-Botoke', 'img'=>'nuribotoke.jpg', 'race'=>'yokai'),
		'hitodama'=>array('name'=>'Hitodama', 'short'=>'The spirit orbs of fire', 'img'=>'hitodama.gif', 'race'=>'kami'),
		'karakasaobake'=>array('name'=>'﻿Karakasa-obake', 'race'=>'kami', 'img'=>'karakasaobake.jpg'),
		'kodama'=>array('name'=>'Ko-dama', 'short'=>'Tree spirit', 'race'=>'kami', 'img'=>'kodama.jpg'),
		'umibozu'=>array('name'=>'Umi-Bozu', 'short'=>'Bulbous floating jellyfish', 'img'=>'umibozu.jpg'),
		'shojo'=>array('name'=>'Shojo', 'short'=>'Monkey man', 'race'=>'yokai', 'img'=>'shojo.jpg'),
		'kamanari'=>array('name'=>'Kamanari', 'short'=>'Iron pot gateway spirit', 'race'=>'kami', 'img'=>'kamanari.jpg'),
		'ryu'=>array('name'=>'Ryu', 'short'=>'a serpent-dragon', 'race'=>'ryu'),
		'furaribi'=>array('name'=>'Furaribi', 'short'=>'a flame spirit', 'race'=>'kami', 'img'=>'furaribi.jpg'),
		'jorogumo'=>array('name'=>'Jorogumo', 'short'=>'spider woman', 'race'=>'yokai', 'img'=>'jorogumo.jpg'),
		'tesso'=>array('name'=>'Tesso', 'short'=>'rat man', 'race'=>'yokai', 'img'=>'tesso.jpg'),
		'shoukera'=>array('name'=>'Shoukera', 'race'=>'yokai', 'img'=>'shoukera.jpg'),
		'waira'=>array('name'=>'Waira', 'short'=>'clawed beast', 'race'=>'yokai', 'img'=>'waira.jpg'),
		'tsurebeotoshi'=>array('name'=>'Tsurube-otoshi', 'short'=>'fire elemental', 'race'=>'kami', 'img'=>'tsurubeotoshi.jpg'),
		'kasha'=>array('name'=>'Kasha', 'img'=>'kasha.jpg', 'race'=>'yokai'),
		'yanari'=>array('name'=>'Yanari', 'short'=>'small demons', 'img'=>'yanari.jpg', 'race'=>'yokai'),
		'aobouzu'=>array('name'=>'Ao-bouzu', 'short'=>'one eyed monk', 'img'=>'aobouzu.jpg', 'race'=>'yokai'),
		'akashita'=>array('name'=>'Akashita', 'short'=>'storm demon', 'img'=>'akashita.jpg', 'race'=>'kami'),
		'kamakiri'=>array('name'=>'Kama-Kiri', 'short'=>'the hair eater', 'img'=>'kamakiri.jpg', 'race'=>'yokai'),
	);
}

?>
