<?php
require_once(LIB_ROOT."specific/lib_player.php");


class Skill
{
	// *** Constructor should eventually get a specific skill's stats from the database.

	/**
	 * This should eventually get ids from the database,
	 * for now, consider the ids as the array indexes.
	**/
	public $skills = array(
		'cold steal', 'ice bolt', 'speed',
		'sight', 'deflect', 'chi', 'midnight heal', 'heal',
		'blaze', 'fire bolt',
		'poison touch', 'stealth', 'unstealth', 'steal', 'hidden resurrect',
		'duel', 'attack', 'kampo', 'evasion'
	); // Midnight heal currently doesn't work.

	// Use the class identities as the array keys here, so $skill_map['crane']
	// ... should return an array of crane-specific skills.
	public $skill_map = array(
		'crane' => array(
			'ice bolt' => array('available'=>1)
			, 'speed'  => array('available'=>1)
		)
		, 'dragon' => array(
			'chi'    => array('available'=>1)
			, 'heal' => array('available'=>1)
		)
		, 'tiger' => array(
			'fire bolt' => array('available'=>1)
			, 'blaze'   => array('available'=>1)
		)
		, 'viper' => array(
			'poison touch'       => array('available'=>1)
			, 'hidden resurrect' => array('available'=>1)
		)
		, 'mantis' => array(
			'kampo'       => array('available'=>1)
			, 'evasion'   => array('available'=>1)
		)
		, 'all' => array(
			'attack'          => array('available'=>1)
			, 'duel'          => array('available'=>1)
			, 'sight'         => array('available'=>1)
			, 'deflect'       => array('available'=>1, 'level'=>2)
			, 'stealth'       => array('available'=>1)
			, 'unstealth'     => array('available'=>1)
			, 'steal'         => array('available'=>1, 'level'=>2)
			, 'cold steal'    => array('available'=>1, 'level'=>6)
			, 'midnight heal' => array('available'=>1, 'level'=>20)
		)
	);

	/**
	 * List of skills in the whole game.
	**/
	public function getSkillList() {
		return $this->skills;
	}

	/**
	 * Returns the list fo all skills available to a ninja.
	**/
	public function skills($char_id=null) {
	    if (!$char_id) {
			$char_id = get_char_id();
		}

	    $char = new Player($char_id);
		$char_name = $char->name();

		if ($char->isAdmin()) { // Admins get access to all skills.
			$skills = $this->skill_map['crane'] +
				$this->skill_map['dragon'] +
				$this->skill_map['mantis'] +
				$this->skill_map['tiger'] +
				$this->skill_map['viper'] +
				$this->skill_map['all'];

			return $skills;
		}

		$class = char_class_identity($char_id);
		$class_skills = array();

		if ($class) {
			$class_skills = $this->skill_map[$class];
		}

		return $class_skills + $this->skill_map['all'];
	}

	/**
	 * Check whether the player has the skill.
	**/
	public function hasSkill($skill, $username=null) {
		$skill = strtolower($skill);

		if (!$username) {
			$username = get_username();
		}

		$char_id = get_char_id($username);
		$player_info = get_player_info($char_id);
		$player_level = $player_info['level'];

		$skills = $this->skills($char_id);
		$level_req = (isset($skills[$skill]['level']) ? $skills[$skill]['level'] : 1);

		return (isset($skills[$skill]['available']) && ($player_level >= $level_req));
	}

	/**
	 * Get the list of skills that a character has, in an indexed array.
	**/
	public function hasSkills($username=null) {
		$skills_avail = array();

		foreach ($this->getSkillList() as $loop_skill) {
			if ($this->hasSkill($loop_skill, $username)) {
				$skills_avail[$loop_skill] = $loop_skill;
			}
		}

		return $skills_avail;
	}

	// Get the turn costs of the skills, which default to 1.
	public function getTurnCost($type) {
	    $type = strtolower($type);
		$skillsTypeToTurns = array(
			'cold steal'     => 3
			, 'ice bolt'     => 2
			, 'sight'        => 1
			, 'deflect'      => 3
			, 'blaze'        => 2
			, 'duel'         => 2
			, 'attack'       => 1
			, 'fire bolt'    => 2
			, 'poison touch' => 2
			, 'stealth'      => 2
			, 'unstealth'    => 0
			, 'steal'        => 1
			, 'kampo'        => 1
			, 'evasion'      => 2
			, 'heal'         => 3
		);

		$res = 1; // default

		if (isset($skillsTypeToTurns[$type])) {
			$res = $skillsTypeToTurns[$type];
		}

		return $res; // *** Throws back the turns cost.
	}

	// Check whether the item is usable on yourself.
	public function getSelfUse($type) {
	    $type = strtolower($type);
		$skillsTypeToSelf = array(
			'stealth'     => true
			, 'unstealth' => true
			, 'kampo'     => true
			, 'heal'      => true
		);
		$res = false; // default is that they're not self usable.
		if (isset($skillsTypeToSelf[$type])) {
			$res = true;
		}
		return $res;
	}

	// Whether the skill is usable on someone other than self.
	public function getUsableOnTarget($type) {
	    $type = strtolower($type);
		$skillsUsableOnTarget = array(
			'stealth'     => false
			, 'unstealth' => false
		);
		// By default, skills aren't usable on self.
		return !(isset($skillsUsableOnTarget[$type]));
	}

	public function getIgnoreStealth($type) {
	    $type = strtolower($type);
		$skillsThatIgnoreStealth = array(
			'sight'          => true
			, 'deflect'      => true
			, 'blaze'        => true
			, 'poison touch' => true
			, 'unstealth'    => true
			, 'ice bolt'     => true
			, 'fire bolt'    => true
		);
		// Fire bolt probably shouldn't break stealth now.
		return (isset($skillsThatIgnoreStealth[$type]));
	}

	//public static $skillsNumbers = array(1 => 'cold steal', 2 => 'ice bolt',
	//3 => 'sight', 4 => 'deflect', 5 => 'fire bolt', 6 => 'blaze', 7 => 'poison touch',
	//8 => 'stealth', 9 => 'unstealth', 10 => 'steal', 11 => 'heal');
	// *** Eventually, should be able to get the skill ID from the database, not from that array above.
}
?>
