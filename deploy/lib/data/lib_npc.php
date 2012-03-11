<?php



// TODO: Abstract all the unique npc behaviors into the generic system.
function get_npcs(){
	return array(
		'spider'=>array('name'=>'Spider', 'img'=>'spider_icon.png', 'strength'=>1, 'damage'=>10, 'gold'=>10, 'race'=>'insect', 'strength'=>1, 'stamina'=>1, 'speed'=>1, 'ki'=>1), 
		'viper'=>array('name'=>'Black Viper', 'race'=>'animal', 'strength'=>'1', 'stamina'=>1, 'speed'=>1, 'ki'=>1, 'damage'=>99, 'status'=>POISON, 'gold'=>30),
		'fireflies'=>array('name'=>'Fireflies', 'strength'=>0, 'damage'=>0), // Baseline weakest mob
		'monk'=>array('name'=>'Monk', 'strength'=>10, 'stamina'=>10, 'speed'=>10, 'ki'=>30, 'race'=>'human'),
		'geisha'=>array('name'=>'Geisha', 'strength'=>5, 'stamina'=>5, 'speed'=>15, 'ki'=>10, 'race'=>'human'),
		'kappa'=>array('name'=>'Kappa', 'short'=>'a reptilian creature with a scooped-out head', 'race'=>'kappa', 'img'=>'kappa.jpg'),
		'tengu'=>array('name'=>'Tengu', 'short'=>'a large winged demon', 'race'=>'tengu'),
		'nureonna'=>array('name'=>'Nureonna', 'img'=>'nureonna', 'race'=>'yokai', 'img'=>'nureonna.jpg'),
		'basan'=>array('name'=>'Basan', 'img'=>'basan.jpg'), // Uses default race of: creature.
		'kamaitachi'=>array('name'=>'Kama-itachi', 'img'=>'kamaitachi.jpg', 'race'=>'yokai'),
		'nuribotoke'=>array('name'=>'Nuri-Botoke', 'img'=>'nuribotoke.jpg', 'race'=>'yokai'),
		'hitodama'=>array('name'=>'Hitodama', 'short'=>'a spirit orbs of fire', 'img'=>'hitodama.gif', 'race'=>'kami'),
		'karakasaobake'=>array('name'=>'﻿Karakasa-obake', 'short'=>'a one-legged umbrella spirit', 'race'=>'kami'),
		'kodama'=>array('name'=>'Ko-dama', 'short'=>'a tree spirit', 'race'=>'kami'),
		'umibozu'=>array('name'=>'Umi-Bozu', 'short'=>'a bulbous floating jellyfish', 'img'=>'umibozu.jpg'),
		'shojo'=>array('name'=>'Shojo', 'short'=>'Monkey man', 'race'=>'yokai'),
		'kamanari'=>array('name'=>'Kamanari', 'short'=>'a gateway spirit that inhabits an iron pot', 'race'=>'kami'),
		'furaribi'=>array('name'=>'Furaribi', 'short'=>'a flame spirit', 'race'=>'kami', 'img'=>'furaribi.jpg'),
		'jorogumo'=>array('name'=>'Jorogumo', 'short'=>'spider woman', 'race'=>'yokai', 'img'=>'jorogumo.jpg'),
		'tesso'=>array('name'=>'Tesso', 'short'=>'rat man', 'race'=>'yokai', 'img'=>'tesso.jpg'),
		'shoukera'=>array('name'=>'Shoukera', 'race'=>'yokai', 'img'=>'shoukera.jpg'),
		'waira'=>array('name'=>'Waira', 'short'=>'clawed beast', 'race'=>'yokai', 'img'=>'waira.jpg'),
		'tsurebeotoshi'=>array('name'=>'Tsurube-otoshi', 'short'=>'a fire elemental', 'race'=>'kami', 'img'=>'tsurubeotoshi.jpg'),
		'kasha'=>array('name'=>'Kasha', 'img'=>'kasha.jpg', 'race'=>'yokai'),
		'yanari'=>array('name'=>'Yanari', 'short'=>'a group of small demons', 'img'=>'yanari.jpg', 'race'=>'yokai'),
		'aobouzu'=>array('name'=>'Ao-bouzu', 'short'=>'a one eyed monk', 'img'=>'aobouzu.jpg', 'race'=>'yokai'),
		'akashita'=>array('name'=>'Akashita', 'short'=>'a storm demon', 'img'=>'akashita.jpg', 'race'=>'kami'),
		'kamakiri'=>array('name'=>'Kama-Kiri', 'short'=>'a hair eating beast', 'img'=>'kamakiri.jpg', 'race'=>'yokai'),
		'hakutaku'=>array('name'=>'Hakutaku', 'short'=>'a winged lion demon', 'img'=>'hakutaku.jpg', 'race'=>'yokai'),
		'hainu'=>array('name'=>'Hainu', 'short'=>'a winged wolf', 'race'=>'yokai'),
		'oni'=>array('name'=>'Oni', 'strength'=>5, 'stamina'=>5, 'speed'=>15, 'ki'=>10, 'short'=>'a horned demon', 'race'=>'oni'),
		'ushioni'=>array('name'=>'Ushi-Oni', 'strength'=>50, 'stamina'=>50, 'speed'=>50, 'race'=>'ushioni', 'img'=>'ushioni2.jpg'),
		'ryu'=>array('name'=>'Ryu', 'strength'=>100, 'stamina'=>200, 'speed'=>80, 'short'=>'a serpent-dragon', 'race'=>'ryu'),
	);
}

?>
