<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\data\Character;
use NinjaWars\core\data\Player;

/**
 *  who/what/why/where The various generic npcs that can be fought or interacted with
 *  villager npcs could have bounties
 *  npcs can have shared traits that provide special abilities
 *  Generally they are interacted with from the /enemies page
 */
class Npc implements Character {
    private $data;
    const RICH_MIN_GOLD_DIVISOR = 1.3;
    const MIN_GOLD = 0; // Could become data driven later

    public function __construct($content) {
        if (is_string($content) && trim($content)) {
            NpcFactory::fleshOut($content, $this);
        } else {
            NpcFactory::fleshOutFromData($content, $this);
        }
    }

    public function name() {
        return $this->name;
    }

    public function identity() {
        return $this->name;
    }

    public function image() {
        return $this->image;
    }

    public function shortDesc() {
        return $this->short_desc;
    }

    /**
     * Calculcate the max damage of an npc.  Needed for effectiveness calc.
     */
    public function maxDamage(Character $enemy=null) {
        $dam = ((1+ ($this->strength * 2)) + $this->damage);
        // Mirror some of their enemy's strength
        if ($this->hasTrait('partial_match_strength') && $enemy instanceof Character) {
            $add = max(0, floor($enemy->strength() / 3)); // Enemy str/3 or at minimum 0
            $dam = $dam + $add;
        }

        return $dam;
    }

    /**
     * Calculate the initial naive damage from npcs.
     */
    public function damage(Character $char = null) {
        return rand(0, $this->maxDamage($char));
    }

    /**
     * Calculate difficulty, naively at the moment.
     */
    public function difficulty() {
        // Just add together all the points of the mob, so to speak.
        $adds_bounty = ($this->bountyMod() > 0 ? 1 : 0);
        $armored = ($this->hasTrait('armored') ? 1 : 0);
        $complex = count($this->traits_array);
        $matches_strength = ($this->hasTrait('partial_match_strength') ? 1 : 0);

        return 0
            + $this->strength * 2
            + $this->damage
            + floor($this->maxHealth() / 10)
            + (int) ($this->maxHealth() > 1) // Have more than 1 health, so not totally devoid of content
            + $adds_bounty
            + $armored * 5
            + $complex * 3
            + $matches_strength * 5
            ;
    }

    /**
     * @param string $trait
     */
    public function hasTrait($trait) {
        return in_array($trait, $this->traits_array);
    }

    public function traits() {
        return $this->traits_array;
    }

    public function speed() {
        return $this->speed;
    }

    public function strength() {
        return $this->strength;
    }

    public function stamina() {
        return $this->stamina;
    }

    public function ki() {
        return $this->ki;
    }

    public function health() {
        return $this->maxHealth(); // For now, since there aren't npc instances currently.
    }

    /**
     * Get their starting health, minimum of 1.
     */
    public function maxHealth() {
        $armored = ($this->hasTrait('armored') ? 1 : 0);
        return 1 + ($this->stamina * 5) + ($this->stamina * 2 * $armored);
    }

    /**
     * Instantiate a random chance of the inventory item being present.
     */
    private function inventory_present($chance) {
        return rand(1, 1000) < (int) ceil((float)$chance * 1000);
    }

    /**
     * Calculate this npc's inventory from initial chances.
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
     */
    public function hasItem($item) {
        return isset($this->inventory[$item]);
    }

    /**
     * Get the race of the npc.
     */
    public function race() {
        if (!$this->race) {
            return 'creature';
        } else {
            return $this->race;
        }
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
     */
    public function gold() {
        return $this->gold;
    }

    /**
     * Get min gold for an npc.
     */
    public function minGold() {
        return (int) ($this->hasTrait('rich') ? floor($this->gold()/self::RICH_MIN_GOLD_DIVISOR) : self::MIN_GOLD);
    }
}
