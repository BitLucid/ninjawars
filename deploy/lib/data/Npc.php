<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\data\Character;
use NinjaWars\core\data\Player;

/**
 * who/what/why/where The various generic npcs that can be fought or interacted with
 * villager npcs could have bounties
 * npcs can have shared traits that provide special abilities
 * Generally they are interacted with from the /enemies page
 */
class Npc implements Character {
    const RICH_MIN_GOLD_DIVISOR = 1.3;
    const MIN_GOLD = 0; // Could become data driven later

    public $traits_array;
    public $name;
    public $image;
    public $short_desc;
    public $strength;
    public $damage;
    public $speed;
    public $stamina;
    public $ki;
    public $inventory_chances;
    public $inventory;
    public $race;
    public $gold;
    public $bounty_mod;

    public function __construct($content) {
        if (is_string($content) && trim($content)) {
            NpcFactory::fleshOut($content, $this);
        } else {
            NpcFactory::fleshOutFromData($content, $this);
        }
    }

    /**
     * @return String
     */
    public function name() {
        return $this->name;
    }

    /**
     * @return String
     */
    public function identity() {
        return $this->name;
    }

    /**
     * @return String
     */
    public function image() {
        return $this->image;
    }

    /**
     * @return String
     */
    public function shortDesc() {
        return $this->short_desc;
    }

    /**
     * Calculcate the max damage of an npc.  Needed for effectiveness calc.
     *
     * @return int
     */
    public function maxDamage(Character $enemy=null): int {
        $dam = ((1+ ($this->strength * 2)) + $this->damage);
        // Mirror some of their enemy's strength
        if ($this->hasTrait('partial_match_strength') && $enemy instanceof Character) {
            $add = max(0, floor($enemy->getStrength() / 3)); // Enemy str/3 or at minimum 0
            $dam = $dam + $add;
        }

        return (int) $dam;
    }

    /**
     * Calculate the initial naive damage from npcs.
     *
     * @return int
     */
    public function damage(Character $char = null) {
        // Horned enemies do a little extra damage
        return rand(0, $this->maxDamage($char)) 
            + ($this->hasTrait('horned') ? (int) max(0, floor($this->getStrength()/8)) : 0);
    }

    /**
     * Calculate difficulty, naively at the moment.
     *
     * @return int
     */
    public function difficulty() {
        // Just add together all the points of the mob, so to speak.
        $adds_bounty      = ($this->bountyMod() > 0 ? 1 : 0);
        $armored          = ($this->hasTrait('armored') ? 1 : 0);
        $complex          = count($this->traits_array);
        $matches_strength = ($this->hasTrait('partial_match_strength') ? 1 : 0);
        $horned           = $this->hasTrait('horned') ? 1 : 0;
        $gang             = $this->hasTrait('gang') ? 1 : 0;
        $insubstantial           = $this->hasTrait('wispy') ? 1 : 0;

        return 0
            + ($this->strength * 2)
            + $this->damage
            + floor($this->getMaxHealth() / 10)
            + ((int) ($this->getMaxHealth() > 1)) // Have more than 1 health, so not totally devoid of content
            + $adds_bounty
            + ($armored * 5)
            + ($complex * 3)
            + ($matches_strength * 5)
            + ($horned * 2)
            + ($gang * 2)
            + ($insubstantial * 1)
            ;
    }

    /**
     * @param string $trait
     * @return boolean
     */
    public function hasTrait($trait) {
        return in_array($trait, $this->traits_array);
    }

    /**
     * @return Array
     */
    public function traits() {
        return $this->traits_array;
    }

    /**
     * @return int
     */
    public function getSpeed() {
        return $this->speed;
    }

    /**
     * @return int
     */
    public function getStrength() {
        return $this->strength;
    }

    /**
     * @return int
     */
    public function getStamina() {
        return $this->stamina;
    }

    /**
     * @return int
     */
    public function ki() {
        return $this->ki;
    }

    /**
     * @return int
     */
    public function getHealth() {
        return $this->getMaxHealth(); // For now, since there aren't npc instances currently.
    }

    /**
     * Get their starting health, minimum of 1.
     *
     * @return int
     */
    public function getMaxHealth() {
        $armored = ($this->hasTrait('armored') ? 1 : 0);
        return 1 + ($this->stamina * 5) + ($this->stamina * 2 * $armored);
    }

    /**
     * Instantiate a random chance of the inventory item being present.
     *
     * @return boolean
     */
    private function inventory_present($chance) {
        return rand(1, 1000) < (int) ceil((float)$chance * 1000);
    }

    /**
     * Calculate this npc's inventory from initial chances.
     *
     * @return Array
     */
    public function inventory() {
        if (!isset($this->inventory) && isset($this->inventory_chances) && $this->inventory_chances) {
            $inv = array();
            foreach ($this->inventory_chances as $item=>$chance) {
                if ($this->inventory_present($chance)) { // Calculate success from a decimal/float.
                    // Add the item.
                    $inv[$item] = true;
                }
            }

            $this->inventory = $inv;
        }

        return $this->inventory;
    }

    /**
     * Get the npcs inventory and return true if there is an instance of the item in it.
     *
     * @return boolean
     */
    public function hasItem($item) {
        return isset($this->inventory[$item]);
    }

    /**
     * Get the race of the npc.
     *
     * @return String
     */
    public function race() {
        return $this->race ?? 'creature';
    }

    /**
     * Additional bounty added by killing this char
     *
     * @return int
     * @note
     * Only npcs with a bounty mod will put a bounty on your head at all.
     */
    public function bountyMod() {
        return $this->bounty_mod;
    }

    /**
     * Max gold
     *
     * @return int
     */
    public function gold() {
        return $this->gold;
    }

    /**
     * Get min gold for an npc.
     *
     * @return int
     */
    public function minGold() {
        return (int) ($this->hasTrait('rich') ? floor($this->gold()/self::RICH_MIN_GOLD_DIVISOR) : self::MIN_GOLD);
    }
}
