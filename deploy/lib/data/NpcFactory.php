<?php
require_once ROOT.'lib/environment/status_defines.php';


class InvalidNpcException extends Exception{}

/**
 * Who/what/why/where
 *  Create npcs with static methods.
 *
**/
class NpcFactory{
	// Returns a fleshed out npc object
	public static function create($identity){
        $identity = mb_strtolower($identity);
		$npcs = NpcFactory::npcsData();
		$npc = null;
		if($identity && array_key_exists($identity, $npcs)){
			$npc = new Npc($npcs[$identity]);
		}
		return $npc;
	}

	/**
	 * Pass the npc in and use the reference to flesh out it's data, from an identity if nothing else
	**/
	public static function fleshOut($identity, $npc){
		$npcs_data = NpcFactory::npcsData();
		if(array_key_exists($identity, $npcs_data) && !empty($npcs_data[$identity])){
			NpcFactory::fleshOutFromData($npcs_data[$identity], $npc);
		} else {
			throw new InvalidNpcException('No such npc ['.$identity.'] found to create!');
		}
	}

	/**
	 * Create the meat of an npc from it's data
	**/
	public static function fleshOutFromData($data, $npc){
        $npc->setData($data);
        $npc->name = @$data['name'];
        $npc->inventory_chances = @$data['inventory'];
        $npc->traits = @$data['traits'];
        $npc->strength = (int) @$data['strength'];
        $npc->speed = (int) @$data['speed'];
        $npc->stamina = (int) @$data['stamina'];
        $npc->damage = (int) @$data['damage'];
        $npc->ki = (int) @$data['ki'];
        $npc->race = @$data['race'];
        $npc->bounty = @$data['bounty'];
        $npc->traits_array = null;
        $npc->inventory = null; // Initially just null;
	}

	// Pull all the npcs from data source.
	public static function npcs(){
		$npcs_data = NpcFactory::npcsData();
		$npcs = [];
		foreach($npcs_data as $identity=>$npc_data){
			assert((bool)$identity);
			$npcs[$identity] = new Npc($npc_data);
		}
		return $npcs;
	}


	// Pull all the npcs, currently from the get_npcs() function as a standing for
	// the database eventually.
	public static function npcsData(){
		// Npc matrix planning document: https://docs.google.com/spreadsheet/ccc?key=0AkoUgtBBP00HdGZ1eUhaekhTb1dnZVh3ZlpoRExWdGc#gid=0
		$npcs = [
	        'firefly'=>['name'=>'Firefly', 'strength'=>0, 'stamina'=>1, 'damage'=>0, 'race'=>'insect', 'gold'=>0],
			'fireflies'=>['name'=>'Fireflies', 'strength'=>0, 'stamina'=>1, 'damage'=>0, 'race'=>'insect'], // Baseline weakest mob
			'spider'=>['name'=>'Spider', 'img'=>'spider_icon.png', 'strength'=>1, 'damage'=>10, 'gold'=>10, 'race'=>'insect', 'strength'=>1, 'stamina'=>1, 'speed'=>1, 'ki'=>1], 
			'viper'=>['name'=>'Black Viper', 'race'=>'animal', 'strength'=>'1', 'stamina'=>1, 'speed'=>1, 'ki'=>1, 'damage'=>99, 'status'=>POISON, 'gold'=>30],
			'kappa'=>['name'=>'Kappa', 'strength'=>30, 'speed'=>10, 'stamina'=>80, 'short'=>'is a reptilian creature with a scooped-out head', 'race'=>'kappa', 'img'=>'kappa.jpg', 'inventory'=>['shell'=>'.5'], 'traits'=>'armored'],
			'nureonna'=>['name'=>'Nureonna', 'strength'=>30, 'speed'=>50, 'stamina'=>40, 'img'=>'nureonna', 'race'=>'yokai', 'img'=>'nureonna.jpg', 'status'=>POISON, 'inventory'=>['charcoal'=>1]],
	        'tengu'=>['name'=>'Tengu', 'short'=>'is a large winged demon', 'strength'=>70, 'speed'=>20, 'stamina'=>100, 'race'=>'tengu', 'inventory'=>['tetsubo'=>'.05']],
			'ushioni'=>['name'=>'Ushi-Oni', 'strength'=>90, 'stamina'=>130, 'speed'=>50, 'race'=>'ushioni', 'img'=>'ushioni2.jpg'],
			'ryu'=>['name'=>'Ryu', 'strength'=>150, 'stamina'=>200, 'speed'=>190, 'short'=>'is a serpent-dragon, with the gleam of intelligence in it\'s eyes and the glint of death on it\'s claws',  'img'=>'hokusai-dragon.jpg', 'race'=>'ryu', 'traits'=>'armored'],
		];
		if(defined('DEBUG') && DEBUG){
			$npcs += [
				'peasant2'=>['name'=>'Peasant', 'race'=>'human', 'img'=>'fighter.png', 'strength'=>'10', 'stamina'=>3, 'speed'=>10, 'ki'=>1, 'damage'=>1, 'gold'=>20, 'bounty'=>1, 'traits'=>'villager,sometimes_disguised_ninja', 'inventory'=>['kunai'=>'.01']],
				'merchant2'=>['name'=>'Merchant', 'race'=>'human', 'strength'=>'20', 'stamina'=>20, 'speed'=>10, 'ki'=>1, 
						'damage'=>15, 'gold'=>50, 'inventory'=>'phosphor', 'bounty'=>3, 'img'=>'merchant.png', 'inventory'=>['phosphor'=>'.7'], 'traits'=>'villager'],
				'guard2'=>['name'=>'Guard', 'short'=>'is a member of the ashigaru foot soldiers, hired for various tasks', 'race'=>'human', 'strength'=>'30', 'stamina'=>30, 'speed'=>12, 'ki'=>1, 
	            'damage'=>0, 'gold'=>50, 'inventory'=>'phosphor', 'bounty'=>0, 'img'=>'guard.png', 'inventory'=>['ginsengroot'=>'.1'], 'traits'=>'partial_match_strength'],
				'monk'=>['name'=>'Monk', 'strength'=>10, 'stamina'=>10, 'speed'=>10, 'ki'=>30, 'race'=>'human', 'inventory'=>['prayerwheel'=>'.2'], 'traits'=>'deflection,defensive,self_heal'],
	            'geisha'=>['name'=>'Geisha', 'strength'=>5, 'stamina'=>10, 'speed'=>15, 'ki'=>10, 'gold'=>20, 'bounty'=>30, 'race'=>'human', 'inventory'=>['sake'=>'.2', 'mirror'=>'.01', 'kimono'=>'.01', 'tessen'=>'.01'], 'traits'=>'packdynamic,speed,guarded,villager'],
				'pig'=>['name'=>'Wild pig', 'short'=>'rolls about in the muck contentedly', 'stamina'=>3, 'strength'=>1, 'speed'=>10, 'damage'=>2, 'race'=>'animal'],
				'chicken'=>['name'=>'Chicken', 'short'=>'saunters around like it owns the place', 'strength'=>1, 'speed'=>5, 'damage'=>0, 'race'=>'bird'],
				'bees'=>['name'=>'Swarm of Bees', 'short'=>'swarms and buzzes through the air', 'strength'=>17, 'speed'=>70, 'damage'=>50, 'gold'=>0, 'race'=>'insect'],
				'goat'=>['name'=>'Goat', 'short'=>'chews on anything it can get to', 'strength'=>10, 'speed'=>25, 'damage'=>3, 'race'=>'animal'],
	            'crow'=>['name'=>'Crow', 'short'=>'caws out it\'s distain', 'strength'=>3, 'speed'=>25, 'damage'=>3, 'race'=>'bird'],
	            'kingfisher'=>['name'=>'Kingfisher', 'short'=>'flashes by on it\'s wings', 'strength'=>3, 'speed'=>30, 'damage'=>3, 'race'=>'bird'],
				'horse'=>['name'=>'Horse', 'short'=>'', 'strength'=>10, 'speed'=>50, 'stamina'=>30, 'damage'=>10, 'race'=>'animal'],
				'ox'=>['name'=>'Ox', 'short'=>'', 'strength'=>50, 'speed'=>20, 'stamina'=>40, 'damage'=>20, 'race'=>'animal'],
				'dog'=>['name'=>'Dog', 'short'=>'barks wildly', 'strength'=>20, 'speed'=>20, 'stamina'=>10, 'damage'=>10, 'race'=>'animal'],
				'tiger'=>['name'=>'Tiger', 'short'=>'circles in for the kill', 'strength'=>60, 'speed'=>60, 'stamina'=>60, 'damage'=>60, 'race'=>'animal'],
		        'koi'=>['name'=>'Koi', 'short'=>'swims through the water', 'img'=>'koi.jpg', 'strength'=>0, 'speed'=>5, 'stamina'=>2, 'damage'=>1, 'race'=>'fish', 'inventory'=>['sushi'=>'.5']],
				'basan'=>['name'=>'Basan', 'img'=>'basan.jpg'], // Uses default race of: creature.
				'kamaitachi'=>['name'=>'Kama-itachi', 'img'=>'kamaitachi.jpg', 'race'=>'yokai'],
				'nuribotoke'=>['name'=>'Nuri-Botoke', 'img'=>'nuribotoke.jpg', 'race'=>'yokai'],
				'hitodama'=>['name'=>'Hitodama', 'short'=>'are spirit orbs of fire', 'img'=>'hitodama.gif', 'race'=>'kami'],
				'karakasaobake'=>['name'=>'Karakasa-obake', 'short'=>'a one-legged umbrella spirit', 'race'=>'kami'],
				'kodama'=>['name'=>'Ko-dama', 'short'=>'is a tree spirit', 'race'=>'kami'],
				'umibozu'=>['name'=>'Umi-Bozu', 'short'=>'are bulbous floating jellyfish', 'img'=>'umibozu.jpg'],
				'shojo'=>['name'=>'Shojo', 'short'=>'is a monkey man', 'strength'=>8, 'speed'=>50, 'stamina'=>20, 'race'=>'yokai'],
				'kamanari'=>['name'=>'Kamanari', 'short'=>'is a gateway spirit that inhabits an iron pot', 'race'=>'kami'],
				'furaribi'=>['name'=>'Furaribi', 'short'=>'is a flame spirit', 'race'=>'kami', 'img'=>'furaribi.jpg'],
				'jorogumo'=>['name'=>'Jorogumo', 'short'=>'spider woman', 'race'=>'yokai', 'img'=>'jorogumo.jpg'],
				'tesso'=>['name'=>'Tesso', 'short'=>'rat man', 'race'=>'yokai', 'img'=>'tesso.jpg'],
				'shoukera'=>['name'=>'Shoukera', 'short'=>'is a muscled demon that lies wait in the shadows', 'race'=>'yokai', 'img'=>'shoukera.jpg'],
				'waira'=>['name'=>'Waira', 'short'=>'is a clawed beast', 'race'=>'yokai', 'img'=>'waira.jpg'],
				'tsurebeotoshi'=>['name'=>'Tsurube-otoshi', 'short'=>'is a fire elemental', 'race'=>'kami', 'img'=>'tsurubeotoshi.jpg'],
				'kasha'=>['name'=>'Kasha', 'img'=>'kasha.jpg', 'race'=>'yokai'],
				'yanari'=>['name'=>'Yanari', 'short'=>'a group of small demons', 'img'=>'yanari.jpg', 'race'=>'yokai'],
				'aobouzu'=>['name'=>'Ao-bouzu', 'short'=>'is a one eyed monk', 'img'=>'aobouzu.jpg', 'race'=>'yokai'],
				'akashita'=>['name'=>'Akashita', 'short'=>'is a storm demon', 'img'=>'akashita.jpg', 'race'=>'kami'],
				'kamakiri'=>['name'=>'Kama-Kiri', 'short'=>'is a hair eating beast', 'img'=>'kamakiri.jpg', 'race'=>'yokai'],
				'hakutaku'=>['name'=>'Hakutaku', 'short'=>'is a winged lion demon', 'img'=>'hakutaku.jpg', 'race'=>'yokai'],
				'hainu'=>['name'=>'Hainu', 'short'=>'is a winged wolf', 'race'=>'yokai'],
				'oni'=>['name'=>'Oni', 'strength'=>5, 'stamina'=>5, 'speed'=>15, 'ki'=>10, 'short'=>'a horned demon', 'img'=>'hokusai-oni.jpg', 'race'=>'oni'],
			];
		}
		return $npcs;
	}
}