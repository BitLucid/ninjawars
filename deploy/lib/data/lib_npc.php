<?php

// Npc matrix planning document: https://docs.google.com/spreadsheet/ccc?key=0AkoUgtBBP00HdGZ1eUhaekhTb1dnZVh3ZlpoRExWdGc#gid=0

// TODO: Abstract all the unique npc behaviors into the generic system.
// Note that gold=>0 prevents all gold collection.
function get_npcs(){
	return array(
		/*'peasant2'=>array('name'=>'Peasant', 'race'=>'human', 'img'=>'fighter.png', 'strength'=>'10', 'stamina'=>10, 'speed'=>10, 'ki'=>1, 'damage'=>1, 'gold'=>20, 'bounty'=>1),
		'merchant2'=>array('name'=>'Merchant', 'race'=>'human', 'strength'=>'20', 'stamina'=>20, 'speed'=>10, 'ki'=>1, 
			'damage'=>15, 'gold'=>50, 'inventory'=>'phosphor', 'bounty'=>3, 'img'=>'merchant.png', 'item'=>'phosphor'),
		'guard2'=>array('name'=>'Guard', 'race'=>'human', 'strength'=>'30', 'stamina'=>30, 'speed'=>12, 'ki'=>1, 
        'damage'=>0, 'gold'=>50, 'inventory'=>'phosphor', 'bounty'=>3, 'img'=>'guard.png', 'item'=>'ginsengroot'),*/
		'monk'=>array('name'=>'Monk', 'strength'=>10, 'stamina'=>10, 'speed'=>10, 'ki'=>30, 'race'=>'human', 'item'=>'prayerwheel'),
		'geisha'=>array('name'=>'Geisha', 'strength'=>5, 'stamina'=>10, 'speed'=>15, 'ki'=>10, 'gold'=>20, 'race'=>'human', 'item'=>'tessen'),
		'fireflies'=>array('name'=>'Fireflies', 'strength'=>0, 'damage'=>0), // Baseline weakest mob
		'pig'=>array('name'=>'Wild pig', 'short'=>'rolls about in the muck contentedly', 'strength'=>1, 'speed'=>10, 'damage'=>2, 'race'=>'animal'),
		'chicken'=>array('name'=>'chicken', 'short'=>'saunters around like it owns the place', 'strength'=>1, 'speed'=>5, 'damage'=>0, 'race'=>'bird'),
		'bees'=>array('name'=>'Swarm of Bees', 'short'=>'swarms and buzzes through the air', 'strength'=>17, 'speed'=>70, 'damage'=>50, 'gold'=>0, 'race'=>'insect'),
		'goat'=>array('name'=>'Goat', 'short'=>'chews on anything it can get to', 'strength'=>10, 'speed'=>25, 'damage'=>3, 'race'=>'animal'),
		'horse'=>array('name'=>'Horse', 'short'=>'', 'strength'=>10, 'speed'=>50, 'stamina'=>30, 'damage'=>10, 'race'=>'animal'),
		'ox'=>array('name'=>'Ox', 'short'=>'', 'strength'=>50, 'speed'=>20, 'stamina'=>40, 'damage'=>20, 'race'=>'animal'),
		'dog'=>array('name'=>'Dog', 'short'=>'barks wildly', 'strength'=>20, 'speed'=>20, 'stamina'=>10, 'damage'=>10, 'race'=>'animal'),
		'tiger'=>array('name'=>'Tiger', 'short'=>'circles in for the kill', 'strength'=>60, 'speed'=>60, 'stamina'=>60, 'damage'=>60, 'race'=>'animal'),
		'koi'=>array('name'=>'Koi', 'short'=>'swims through the water', 'img'=>'koi.jpg', 'strength'=>0, 'speed'=>5, 'stamina'=>2, 'damage'=>1, 'race'=>'fish', 'item'=>'sushi'),
		'spider'=>array('name'=>'Spider', 'img'=>'spider_icon.png', 'strength'=>1, 'damage'=>10, 'gold'=>10, 'race'=>'insect', 'strength'=>1, 'stamina'=>1, 'speed'=>1, 'ki'=>1), 
		'viper'=>array('name'=>'Black Viper', 'race'=>'animal', 'strength'=>'1', 'stamina'=>1, 'speed'=>1, 'ki'=>1, 'damage'=>99, 'status'=>POISON, 'gold'=>30),
		'kappa'=>array('name'=>'Kappa', 'short'=>'is a reptilian creature with a scooped-out head', 'race'=>'kappa', 'img'=>'kappa.jpg', 'item'=>'shell'),
		'nureonna'=>array('name'=>'Nureonna', 'img'=>'nureonna', 'race'=>'yokai', 'img'=>'nureonna.jpg'),
		'basan'=>array('name'=>'Basan', 'img'=>'basan.jpg'), // Uses default race of: creature.
		'kamaitachi'=>array('name'=>'Kama-itachi', 'img'=>'kamaitachi.jpg', 'race'=>'yokai'),
		'nuribotoke'=>array('name'=>'Nuri-Botoke', 'img'=>'nuribotoke.jpg', 'race'=>'yokai'),
		'hitodama'=>array('name'=>'Hitodama', 'short'=>'are spirit orbs of fire', 'img'=>'hitodama.gif', 'race'=>'kami'),
		'karakasaobake'=>array('name'=>'﻿Karakasa-obake', 'short'=>'a one-legged umbrella spirit', 'race'=>'kami'),
		'kodama'=>array('name'=>'Ko-dama', 'short'=>'is a tree spirit', 'race'=>'kami'),
		'umibozu'=>array('name'=>'Umi-Bozu', 'short'=>'are bulbous floating jellyfish', 'img'=>'umibozu.jpg'),
		'shojo'=>array('name'=>'Shojo', 'short'=>'is a monkey man', 'strength'=>8, 'speed'=>50, 'stamina'=>20, 'race'=>'yokai'),
		'kamanari'=>array('name'=>'Kamanari', 'short'=>'is a gateway spirit that inhabits an iron pot', 'race'=>'kami'),
		'furaribi'=>array('name'=>'Furaribi', 'short'=>'is a flame spirit', 'race'=>'kami', 'img'=>'furaribi.jpg'),
		'jorogumo'=>array('name'=>'Jorogumo', 'short'=>'spider woman', 'race'=>'yokai', 'img'=>'jorogumo.jpg'),
		'tesso'=>array('name'=>'Tesso', 'short'=>'rat man', 'race'=>'yokai', 'img'=>'tesso.jpg'),
		'shoukera'=>array('name'=>'Shoukera', 'short'=>'is a muscled demon that lies wait in the shadows', 'race'=>'yokai', 'img'=>'shoukera.jpg'),
		'waira'=>array('name'=>'Waira', 'short'=>'is a clawed beast', 'race'=>'yokai', 'img'=>'waira.jpg'),
		'tsurebeotoshi'=>array('name'=>'Tsurube-otoshi', 'short'=>'is a fire elemental', 'race'=>'kami', 'img'=>'tsurubeotoshi.jpg'),
		'kasha'=>array('name'=>'Kasha', 'img'=>'kasha.jpg', 'race'=>'yokai'),
		'yanari'=>array('name'=>'Yanari', 'short'=>'a group of small demons', 'img'=>'yanari.jpg', 'race'=>'yokai'),
		'aobouzu'=>array('name'=>'Ao-bouzu', 'short'=>'is a one eyed monk', 'img'=>'aobouzu.jpg', 'race'=>'yokai'),
		'akashita'=>array('name'=>'Akashita', 'short'=>'is a storm demon', 'img'=>'akashita.jpg', 'race'=>'kami'),
		'kamakiri'=>array('name'=>'Kama-Kiri', 'short'=>'is a hair eating beast', 'img'=>'kamakiri.jpg', 'race'=>'yokai'),
		'hakutaku'=>array('name'=>'Hakutaku', 'short'=>'is a winged lion demon', 'img'=>'hakutaku.jpg', 'race'=>'yokai'),
		'hainu'=>array('name'=>'Hainu', 'short'=>'is a winged wolf', 'race'=>'yokai'),
		'tengu'=>array('name'=>'Tengu', 'short'=>'is a large winged demon', 'strength'=>70, 'speed'=>20, 'stamina'=>80, 'race'=>'tengu', 'item'=>'tetsubo'),
		'oni'=>array('name'=>'Oni', 'strength'=>5, 'stamina'=>5, 'speed'=>15, 'ki'=>10, 'short'=>'a horned demon', 'img'=>'hokusai-oni.jpg', 'race'=>'oni'),
		'ushioni'=>array('name'=>'Ushi-Oni', 'strength'=>90, 'stamina'=>130, 'speed'=>50, 'race'=>'ushioni', 'img'=>'ushioni2.jpg'),
		'ryu'=>array('name'=>'Ryu', 'strength'=>150, 'stamina'=>200, 'speed'=>190, 'short'=>'is a serpent-dragon, with the gleam of intelligence in it\'s eyes and the glint of death on it\'s claws',  'img'=>'hokusai-dragon.jpg', 'race'=>'ryu'),
	);
}

?>
