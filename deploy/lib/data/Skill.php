<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\Player;

class Skill
{
    // *** Constructor should eventually get a specific skill's stats from the database.

    /**
     * This should eventually get ids from the database,
     * for now, consider the ids as the array indexes.
    **/
    public $skills = [
        'cold steal', 'ice bolt', 'speed',
        'sight', 'deflect', 'chi', 'midnight heal', 'heal',
        'blaze', 'fire bolt',
        'poison touch', 'stealth', 'unstealth', 'steal', 'hidden resurrect',
        'duel', 'attack', 'kampo', 'evasion', 'stalk'
    ];

    // Use the class identities as the array keys here, so $skill_map['crane']
    // ... should return an array of crane-specific skills.
    public $skill_map = [
        'crane' => [
            'ice bolt' => ['available' => 1, 'level' => 2]
            , 'speed'  => ['available' => 1]
            , 'kampo' => ['available' => 1, 'level' => 6]
        ]
        , 'dragon' => [
            'chi'    => ['available' => 1]
            , 'heal' => ['available' => 1, 'level' => 2]
            , 'evasion' => ['available' => 1, 'level' => 6]
        ]
        , 'tiger' => [
            'fire bolt' => ['available' => 1, 'level' => 2]
            , 'blaze'   => ['available' => 1, 'level' => 1]
        ]
        , 'viper' => [
            'poison touch'       => ['available' => 1]
            , 'hidden resurrect' => ['available' => 1]
        ]
        , 'all' => [
            'attack'          => ['available' => 1]
            , 'duel'          => ['available' => 1]
            , 'unstealth'     => ['available' => 1]
            , 'clone kill'    => ['available' => 1, 'level' => 2]
            , 'wrath'	      => ['available' => 1, 'level' => 2]
            , 'stealth'       => ['available' => 1, 'level' => 2]
            , 'sight'         => ['available' => 1, 'level' => 2]
            , 'deflect'       => ['available' => 1, 'level' => 5]
            , 'steal'         => ['available' => 1, 'level' => 5]
            , 'stalk'         => ['available' => 1, 'level' => 6]
            , 'cold steal'    => ['available' => 1, 'level' => 10]
            , 'midnight heal' => ['available' => 1, 'level' => 20] // Because the logic is such a PITA
        ]
    ];

    /**
     * List of skills in the whole game.
    **/
    public function getSkillList()
    {
        return $this->skills;
    }

    /**
     * Returns the list fo all skills available to a ninja.
     */
    private function skills(Player $char)
    {
        if ($char->isAdmin()) { // Admins get access to all skills.
            $skills = $this->skill_map['crane'] +
                $this->skill_map['dragon'] +
                $this->skill_map['tiger'] +
                $this->skill_map['viper'] +
                $this->skill_map['all'];

            return $skills;
        }

        $class = $char->identity;
        $class_skills = [];

        if ($class) {
            $class_skills = $this->skill_map[$class];
        }

        return $class_skills + $this->skill_map['all'];
    }

    /**
     * Check whether the player has the skill.
     */
    public function hasSkill($skill, $char = null)
    {
        if ($char instanceof Player) {
            $player = $char;
        } else {
            $player = Player::findByName($char);
        }

        $skills   = $this->skills($player);
        $skill    = strtolower($skill);
        $levelReq = (isset($skills[$skill]['level']) ? $skills[$skill]['level'] : 1);

        return (isset($skills[$skill]['available']) && ($player->level >= $levelReq));
    }

    /**
     * Get the list of skills that a character has, in an indexed array.
    **/
    public function hasSkills($username = null)
    {
        $skills_avail = [];

        foreach ($this->getSkillList() as $loop_skill) {
            if ($this->hasSkill($loop_skill, $username)) {
                $skills_avail[$loop_skill] = $loop_skill;
            }
        }

        return $skills_avail;
    }

    // Get the turn costs of the skills, which default to 1.
    public function getTurnCost($type)
    {
        $type = strtolower($type);
        $skillsTypeToTurns = [
            'cold steal'     => 3
            , 'ice bolt'     => 2
            , 'sight'        => 1
            , 'attack'       => 1
            , 'duel'         => 2
            , 'deflect'      => 2
            , 'blaze'        => 1
            , 'evasion'      => 1
            , 'fire bolt'    => 2
            , 'poison touch' => 2
            , 'stealth'      => 2
            , 'unstealth'    => 0
            , 'stalk'        => 1
            , 'steal'        => 1
            , 'kampo'        => 1
            , 'heal'         => 3
        ];

        $res = 1; // default

        if (isset($skillsTypeToTurns[$type])) {
            $res = $skillsTypeToTurns[$type];
        }

        return $res; // *** Throws back the turns cost.
    }

    // Check whether the item is usable on yourself.
    public function getSelfUse($type)
    {
        $type = strtolower($type);
        $skillsTypeToSelf = [
            'stealth'     => true
            , 'unstealth' => true
            , 'kampo'     => true
            , 'heal'      => true
            , 'clone kill' => true
            , 'stalk'     => true
        ];
        $res = false; // default is that they're not self usable.
        if (isset($skillsTypeToSelf[$type])) {
            $res = true;
        }
        return $res;
    }

    // Whether the skill is usable on someone other than self.
    public function getUsableOnTarget($type)
    {
        $type = strtolower($type);
        $skillsUsableOnTarget = [
            'stealth'     => false
            , 'unstealth' => false
            , 'stalk'     => false
        ];
        // By default, skills aren't usable on self.
        return !(isset($skillsUsableOnTarget[$type]));
    }

    public function getIgnoreStealth($type)
    {
        $type = strtolower($type);
        $skillsThatIgnoreStealth = [
            'sight'          => true
            , 'deflect'      => true
            , 'blaze'        => true
            , 'poison touch' => true
            , 'unstealth'    => true
            , 'stalk'        => true
            , 'ice bolt'     => true
            , 'fire bolt'    => true
            , 'kampo'        => true
        ];
        // Fire bolt probably shouldn't break stealth now.
        return (isset($skillsThatIgnoreStealth[$type]));
    }

    //public static $skillsNumbers = array(1 => 'cold steal', 2 => 'ice bolt',
    //3 => 'sight', 4 => 'deflect', 5 => 'fire bolt', 6 => 'blaze', 7 => 'poison touch',
    //8 => 'stealth', 9 => 'unstealth', 10 => 'steal', 11 => 'heal');
    // *** Eventually, should be able to get the skill ID from the database, not from that array above.
}
