<?php

// Npc matrix planning document: https://docs.google.com/spreadsheet/ccc?key=0AkoUgtBBP00HdGZ1eUhaekhTb1dnZVh3ZlpoRExWdGc#gid=0
class Npc{
    private $data;
    function __construct($data){
        $this->data = $data;
        $this->inventory_chances = @$data['inventory'];
        $this->traits = @$data['traits'];
        $this->strength = (int) @$data['strength'];
        $this->speed = (int) @$data['speed'];
        $this->stamina = (int) @$data['stamina'];
        $this->damage = (int) @$data['damage'];
        $this->ki = (int) @$data['ki'];
        $this->race = @$data['race'];
        $this->traits_array = null;
        $this->inventory = null; // Initially just null;
    }

    // Calculcate the max damage of an npc.  Needed for effectiveness calc.
    function max_damage(){
        return ((1+ ($this->strength * 2)) + $this->damage);
    }

    // Calculate the initial naive damage from npcs.
    function damage(){
        return rand(0, $this->max_damage());
    }

    // Calculate difficulty, naively at the moment.
    function difficulty(){
        // Just add together all the points of the mob, so to speak.
        $has_bounty = (int) isset($this->data['bounty']);
        $armored = $this->has_trait('armored')? 1 : 0;
        return 10 + $this->strength * 2 + $this->damage + $has_bounty + $armored * 5;
    }

    // Check for specific traits.
    function has_trait($trait){
        if(!isset($this->traits_array) && isset($this->traits)){
            // Initialize traits as an array at this point.
            $this->traits_array = $this->traits? explode(',', $this->traits) : array();
        }
        return count($this->traits_array) && in_array($trait, $this->traits_array);
    }

    function speed(){
        return $this->speed;
    }
    function strength(){
        return $this->strength;
    }
    function stamina(){
        return $this->stamina;
    }
    function ki(){
        return $this->ki;
    }
    
    function health(){
    	$armored = $this->has_trait('armored')? 1 : 0;
    	return $this->stamina * 5 + $this->stamina * 2 * $armored;
	}
    
    // Calculate this npc's inventory from initial chances.
    function inventory(){
    	if(!isset($this->inventory) && isset($this->inventory_chances) && $this->inventory_chances){
    		$inv = array();
    		foreach($this->inventory_chances as $item=>$chance){
    			if(rand(1, 1000) < (int) ceil((float)$chance * 1000)){ // Calculate success from a decimal/float.
    				// Add the item.
    				$inv[$item] = true;
    			}
    		}
    		$this->inventory = $inv;
    	}
    	return $this->inventory;
    }
    
    // Get the npcs inventory and return true if there is an instance of the item in it.
    function has_item($item){
    	return isset($this->inventory[$item]);
    }
}

// TODO: Abstract all the unique npc behaviors into the generic system.
// Note that gold=>0 prevents all gold collection.
// This function is just a prelude to getting the info all from the database.
function get_npcs(){
	$npcs = array(
		'peasant2'=>array('name'=>'Peasant', 'race'=>'human', 'img'=>'fighter.png', 'strength'=>'10', 'stamina'=>3, 'speed'=>10, 'ki'=>1, 'damage'=>1, 'gold'=>20, 'bounty'=>1, 'traits'=>'villager,sometimes_disguised_ninja', 'inventory'=>array('kunai'=>'.01')),
		'merchant2'=>array('name'=>'Merchant', 'race'=>'human', 'strength'=>'20', 'stamina'=>20, 'speed'=>10, 'ki'=>1, 
			'damage'=>15, 'gold'=>50, 'inventory'=>'phosphor', 'bounty'=>3, 'img'=>'merchant.png', 'inventory'=>array('phosphor'=>'.7'), 'traits'=>'villager'),
		'guard2'=>array('name'=>'Guard', 'short'=>'is a member of the ashigaru foot soldiers, hired for various tasks', 'race'=>'human', 'strength'=>'30', 'stamina'=>30, 'speed'=>12, 'ki'=>1, 
            'damage'=>0, 'gold'=>50, 'inventory'=>'phosphor', 'bounty'=>0, 'img'=>'guard.png', 'inventory'=>array('ginsengroot'=>'.1'), 'traits'=>'partial_match_strength'),
		'fireflies'=>array('name'=>'Fireflies', 'strength'=>0, 'damage'=>0, 'race'=>'insect'), // Baseline weakest mob
		'spider'=>array('name'=>'Spider', 'img'=>'spider_icon.png', 'strength'=>1, 'damage'=>10, 'gold'=>10, 'race'=>'insect', 'strength'=>1, 'stamina'=>1, 'speed'=>1, 'ki'=>1), 
		'viper'=>array('name'=>'Black Viper', 'race'=>'animal', 'strength'=>'1', 'stamina'=>1, 'speed'=>1, 'ki'=>1, 'damage'=>99, 'status'=>POISON, 'gold'=>30),
		'kappa'=>array('name'=>'Kappa', 'strength'=>30, 'speed'=>10, 'stamina'=>80, 'short'=>'is a reptilian creature with a scooped-out head', 'race'=>'kappa', 'img'=>'kappa.jpg', 'inventory'=>array('shell'=>'.5'), 'traits'=>'armored'),
		'nureonna'=>array('name'=>'Nureonna', 'strength'=>30, 'speed'=>50, 'stamina'=>40, 'img'=>'nureonna', 'race'=>'yokai', 'img'=>'nureonna.jpg', 'status'=>POISON, 'inventory'=>array('charcoal'=>1)),
        'tengu'=>array('name'=>'Tengu', 'short'=>'is a large winged demon', 'strength'=>70, 'speed'=>20, 'stamina'=>100, 'race'=>'tengu', 'inventory'=>array('tetsubo'=>'.05')),
		'ushioni'=>array('name'=>'Ushi-Oni', 'strength'=>90, 'stamina'=>130, 'speed'=>50, 'race'=>'ushioni', 'img'=>'ushioni2.jpg'),
		'ryu'=>array('name'=>'Ryu', 'strength'=>150, 'stamina'=>200, 'speed'=>190, 'short'=>'is a serpent-dragon, with the gleam of intelligence in it\'s eyes and the glint of death on it\'s claws',  'img'=>'hokusai-dragon.jpg', 'race'=>'ryu', 'traits'=>'armored'),
	);
	if(defined('DEBUG') && DEBUG){
		$npcs += array(
			'monk'=>array('name'=>'Monk', 'strength'=>10, 'stamina'=>10, 'speed'=>10, 'ki'=>30, 'race'=>'human', 'inventory'=>array('prayerwheel'=>'.2'), 'traits'=>'deflection,defensive,self_heal'),
            'geisha'=>array('name'=>'Geisha', 'strength'=>5, 'stamina'=>10, 'speed'=>15, 'ki'=>10, 'gold'=>20, 'bounty'=>30, 'race'=>'human', 'inventory'=>array('sake'=>'.2', 'mirror'=>'.01', 'kimono'=>'.01', 'tessen'=>'.01'), 'traits'=>'packdynamic,speed,guarded,villager'),
			'pig'=>array('name'=>'Wild pig', 'short'=>'rolls about in the muck contentedly', 'strength'=>1, 'speed'=>10, 'damage'=>2, 'race'=>'animal'),
			'chicken'=>array('name'=>'chicken', 'short'=>'saunters around like it owns the place', 'strength'=>1, 'speed'=>5, 'damage'=>0, 'race'=>'bird'),
			'bees'=>array('name'=>'Swarm of Bees', 'short'=>'swarms and buzzes through the air', 'strength'=>17, 'speed'=>70, 'damage'=>50, 'gold'=>0, 'race'=>'insect'),
			'goat'=>array('name'=>'Goat', 'short'=>'chews on anything it can get to', 'strength'=>10, 'speed'=>25, 'damage'=>3, 'race'=>'animal'),
			'horse'=>array('name'=>'Horse', 'short'=>'', 'strength'=>10, 'speed'=>50, 'stamina'=>30, 'damage'=>10, 'race'=>'animal'),
			'ox'=>array('name'=>'Ox', 'short'=>'', 'strength'=>50, 'speed'=>20, 'stamina'=>40, 'damage'=>20, 'race'=>'animal'),
			'dog'=>array('name'=>'Dog', 'short'=>'barks wildly', 'strength'=>20, 'speed'=>20, 'stamina'=>10, 'damage'=>10, 'race'=>'animal'),
			'tiger'=>array('name'=>'Tiger', 'short'=>'circles in for the kill', 'strength'=>60, 'speed'=>60, 'stamina'=>60, 'damage'=>60, 'race'=>'animal'),
	        'koi'=>array('name'=>'Koi', 'short'=>'swims through the water', 'img'=>'koi.jpg', 'strength'=>0, 'speed'=>5, 'stamina'=>2, 'damage'=>1, 'race'=>'fish', 'inventory'=>array('sushi'=>'.5')),
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
			'oni'=>array('name'=>'Oni', 'strength'=>5, 'stamina'=>5, 'speed'=>15, 'ki'=>10, 'short'=>'a horned demon', 'img'=>'hokusai-oni.jpg', 'race'=>'oni'),
		);
	}
	return $npcs;
}

?>
