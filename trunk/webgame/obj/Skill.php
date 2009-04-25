<?php
class Skill
{
    // *** Constructor should eventually get a specific skill's stats from the database.

    /**
     * This should eventually get ids from the database,
     * for now, the ids are just the array indexes.
    **/
    public $skills = array(
        'cold steal', 'ice bolt',
        'sight', 'deflect',
        'blaze', 'fire bolt',
        'poison touch', 'stealth', 'unstealth', 'steal',
        'duel', 'attack',
        );

    public $skill_map = array(
        'Blue' => array('cold steal'=>1, 'ice bolt'=>1),
        'White' => array('sight'=>1, 'deflect'=>1),
        'Red' => array('blaze'=>1, 'fire bolt'=>1),
        'Black' => array('poison touch'=>1, 'stealth'=>1, 'unstealth'=>1, 'steal'=>1),
        'All' => array('attack'=>1, 'duel'=>1),
        );

    /**
     * List of skills in the whole game.
    **/
    function getSkillList()
    {
        return $this->skills;
    }
    /**
     * Returns the list fo all skills available to a ninja.
    **/
    function skills($username){
        if(!$username) { $username = get_username(); }
        if(DEBUG && $username == 'glassbox'){
            $skills = $this->skill_map['Blue'] +
                $this->skill_map['White'] +
                $this->skill_map['Red'] +
                $this->skill_map['Black'] +
                $this->skill_map['All'];
            return $skills;
        }
        return $this->skill_map[getClass($username)] + $this->skill_map['All'];
    }

    /**
     * Check whether the player has the skill.
    **/
    function hasSkill($skill, $username=null){
        $skill = strtolower($skill);
        if(!$username){ $username = get_username(); }
        $skills = $this->skills($username);
        if(isset($skills[$skill])){
            return true; // The player has those skills.
        }
        return false;
    }

	function getTurnCost($type)
	{
		$skillsTypeToTurns = array('cold steal' => 2, 'ice bolt' => 3, 'sight' => 2,
		 'deflect' => 4, 'blaze' => 4, 'duel' => 2, 'attack' => 1, 'fire bolt' => 3,
		  'poison touch' => 1, 'stealth' => 2, 'unstealth' => 0, 'steal' => 1);
		 $res = 1; // default
		 if (isset($skillsTypeToTurns[strtolower($type)])){
		 	$res = $skillsTypeToTurns[strtolower($type)];
		 }
		return $res; // *** Throws back the turns cost.
	}
	
	function getSelfUse($type)
	{
		$skillsTypeToSelf = array('stealth' => true, 'unstealth' => true);
		$res = false; // default
		if (isset($skillsTypeToSelf[strtolower($type)])){
			$res = true;
		}
		return $res;
	}
	
	// Whether the skill is usable on someone other than self.
	function getUsableOnTarget($type)
	{
		$skillsUsableOnTarget = array('stealth' => false, 'unstealth' => false);
		$res = true; // default
		if (isset($skillsUsableOnTarget[strtolower($type)])){
			$res = false;
		}
		return $res;
	}
	
	function getIgnoreStealth($type)
	{
		$skillsThatIgnoreStealth = array('sight' => true,
		 'deflect' => true, 'blaze' => true, 'poison touch' => true, 'unstealth' => true,
		 'ice bolt' => true, 'fire bolt' => true, 'cold steal' => true);
		$res = false; // default
		if (isset($skillsThatIgnoreStealth[strtolower($type)])){
			$res = true;
		}
		return $res;
	}

	//public static $skillsNumbers = array(1 => 'cold steal', 2 => 'ice bolt',
	//3 => 'sight', 4 => 'deflect', 5 => 'fire bolt', 6 => 'blaze', 7 => 'poison touch',
	//8 => 'stealth', 9 => 'unstealth', 10 => 'steal');
	// *** Eventually, should be able to get the skill ID from the database, not from that array above.
}
?>
