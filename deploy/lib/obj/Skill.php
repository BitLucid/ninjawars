<?php
class Skill
{
	// *** Constructor should eventually get a specific skill's stats from the database.

	/**
	 * This should eventually get ids from the database,
	 * for now, consider the ids as the array indexes.
	**/
	public $skills = array(
		'cold steal', 'ice bolt', 'speed',
		'sight', 'deflect', 'chi', 'midnight heal',
		'blaze', 'fire bolt',
		'poison touch', 'stealth', 'unstealth', 'steal', 'hidden resurrect',
		'duel', 'attack',
	); // Midnight heal currently doesn't work.

	// Temporarily trying a to move the skills out of the classes, to see how players make use of it.
	public $skill_map = array(
		'Blue'    => array(
			'ice bolt' => array('available'=>1)
			, 'speed'  => array('available'=>1)
		)
		, 'White' => array(
			'chi'             => array('available'=>1)
			, 'midnight heal' => array('available'=>1)
		)
		, 'Red'   => array(
			'fire bolt' => array('available'=>1)
			, 'blaze' => array('available'=>1)
		)
		, 'Black' => array(
			'poison touch'       => array('available'=>1)
			, 'hidden resurrect' => array('available'=>1)
		)
		, 'All'   => array(
			'attack'       => array('available'=>1)
			, 'duel'       => array('available'=>1)
			, 'sight'      => array('available'=>1)
			, 'deflect'    => array('available'=>1, 'level'=>2)
			, 'stealth'    => array('available'=>1)
			, 'unstealth'  => array('available'=>1)
			, 'steal'      => array('available'=>1, 'level'=>2)
			, 'cold steal' => array('available'=>1, 'level'=>6)
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
	public function skills($username) {
		if (!$username) { $username = get_username(); }

		if (false && DEBUG && $username == 'glassbox') {
			$skills = $this->skill_map['Blue'] +
				$this->skill_map['White'] +
				$this->skill_map['Red'] +
				$this->skill_map['Black'] +
				$this->skill_map['All'];
			return $skills;
		}
		$class = getClass($username);
		$class_skills = array();
		if($class){
			$class_skills = $this->skill_map[$class];
		}
		return $class_skills + $this->skill_map['All'];
	}

	/**
	 * Check whether the player has the skill.
	**/
	public function hasSkill($skill, $username=null) {
		$skill = strtolower($skill);

		if(!$username) { $username = get_username(); }
		$player_info = get_player_info(get_user_id($username));
		$player_level = $player_info['level'];

		$skills = $this->skills($username);
		$level_req = @$skills[$skill]['level']? $skills[$skill]['level'] : 1;

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

	public function getTurnCost($type) {
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
		);

		$res = 1; // default

		if (isset($skillsTypeToTurns[strtolower($type)])) {
			$res = $skillsTypeToTurns[strtolower($type)];
		}

		return $res; // *** Throws back the turns cost.
	}

	public function getSelfUse($type) {
		$skillsTypeToSelf = array(
			'stealth'     => true
			, 'unstealth' => true
		);

		$res = false; // default

		if (isset($skillsTypeToSelf[strtolower($type)])) {
			$res = true;
		}

		return $res;
	}

	// Whether the skill is usable on someone other than self.
	public function getUsableOnTarget($type) {
		$skillsUsableOnTarget = array(
			'stealth'     => false
			, 'unstealth' => false
		);

		return !(isset($skillsUsableOnTarget[strtolower($type)]));
	}

	public function getIgnoreStealth($type) {
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

		return (isset($skillsThatIgnoreStealth[strtolower($type)]));
	}

	//public static $skillsNumbers = array(1 => 'cold steal', 2 => 'ice bolt',
	//3 => 'sight', 4 => 'deflect', 5 => 'fire bolt', 6 => 'blaze', 7 => 'poison touch',
	//8 => 'stealth', 9 => 'unstealth', 10 => 'steal');
	// *** Eventually, should be able to get the skill ID from the database, not from that array above.
}
?>
