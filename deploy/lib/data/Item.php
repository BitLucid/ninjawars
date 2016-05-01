<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\Player;
use Illuminate\Database\Eloquent\Model;
use \PDO;

/**
 * Managing items like shuriken, dimmak
 * @property-read int item_id
 * @property-read string item_internal_name
 * @property-read string item_display_name
 * @property-read int item_cost
 * @property-read string image
 * @property-read boolean for_sale
 * @property-read string usage
 * @property-read boolean ignore_stealth
 * @property-read boolean covert Whether the item breaks off stealth
 * @property-read int turn_cost
 * @property-read int target_damage
 * @property-read int turn_change
 * @property-read boolean self_use
 * @property-read string plural
 * @property-read boolean other_usable
 * @property-read string traits (comma separated)
 */
class Item extends Model{
    protected $table = 'item';
    protected $primaryKey = 'item_id';
    protected $guarded = ['item_id', 'created_at'];

    const MIN_DYNAMIC_DAMAGE = 9;

    /**
     * Returns not the identity, but the display name
     *
     * @return String
     */
    public function getName() {
        return $this->item_display_name;
    }

    /**
     * Convenience function to get the plural name for the object.
     *
     * @return String
     */
    public function getPluralName() {
        return $this->item_display_name.$this->plural;
    }

    /**
     * @return String
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * The item's internally used name.
     *
     * @return String
     */
    public function identity() {
        return $this->item_internal_name;
    }

    /**
     * Gets the list of effects that the item does.
     *
     * @return Array
     */
    public function effects() {
        // Pull the effects array via the external function.
        return $this->itemEffects($this->item_id);
    }

    /**
     * Checks whether the item causes a certain effect.
     *
     * @param String $effect_identity
     * @return boolean
     */
    public function hasEffect($effect_identity) {
        $effects = $this->effects();

        return (
            $effect_identity &&
            is_array($effects) &&
            array_key_exists(strtolower($effect_identity), $effects)
        );
    }

    /**
     * Note that this just determines the -maximum- turn change.
     *
     * @param int $p_turns
     * @return void
     */
    public function setTurnChange($p_turns) {
        $this->turn_change = (float)$p_turns;
    }

    /**
     * Unmodified ideal maximum turn change
     *
     * @return int
     */
    public function getMaxTurnChange() {
        return (int) $this->turn_change;
    }

    /**
     * Returns the turn change that the object will cause.
     *
     * @TODO: should be modified by existing effects, should accept target obj
     * @return int
     */
    public function getTurnChange() {
        return (int) $this->turn_change;
    }

    /**
     * Set whether the object will be able to bypass stealth mode
     *
     * @param boolean $p_ignore
     * @return void
     */
    public function setIgnoresStealth($p_ignore) {
        $this->ignore_stealth = (boolean)$p_ignore;
    }

    /**
     * Returns whether or not this item can be used against stealthed targets
     *
     * @return boolean
     */
    public function ignoresStealth() {
        return $this->ignore_stealth;
    }

    /**
     *
     *
     * @param int $p_damage
     * @return void
     */
    public function setTargetDamage($p_damage) {
        $this->target_damage = (int)$p_damage;
    }

    /**
     * If an item has inherent, non-effects based damage.
     *
     * @return int
     * @note
     * Ex: A shuriken relies exclusively on the slice effect for it's damage.
     */
    public function getTargetDamage() {
        return (int)$this->target_damage;
    }

    /**
     * Get the maximum numeric damage of an object.
     *
     * If a player is passed in, damage is the better of 9 or 2/3rd of the
     * player's strength -4.
     *
     * @param Player|null $pc (optional) Use player to calculate damage
     * @return int
     * @note
     * Some effects-based object will not actually have a pre-known maxDamage
     */
    public function getMaxDamage(Player $pc=null) {
        if ($pc instanceof Player && $this->hasDynamicDamage()) {
            return max(static::MIN_DYNAMIC_DAMAGE, (int) floor($pc->getStrength() * 2/3)-4);
        } else {
            return $this->target_damage;
        }
    }

    /**
     * If the Item calculates damage dynamically based on strength.
     *
     * @return boolean
     * @note
     * Currently just slicing weapons have this trait.
     */
    private function hasDynamicDamage() {
        return $this->hasEffect('slice');
    }

    /**
     * @return int
     */
    public function getRandomDamage() {
        return rand(0, $this->getMaxDamage());
    }

    /**
     * @return int
     * Turn cost to use
     */
    public function getTurnCost() {
        return $this->turn_cost;
    }

    /**
     *
     * @param boolean $p_covert
     * @return void
     */
    public function setCovert($p_covert) {
        $this->covert = (boolean)$p_covert;
    }

    /**
     * @return boolean
     */
    public function isCovert() {
        return $this->covert;
    }

    /**
     * @return boolean
     */
    public function isSelfUsable() {
        return $this->self_use;
    }

    /**
     * Check whether the item is usable upon others.
     *
     * @return boolean
     */
    public function isOtherUsable() {
        return $this->other_usable;
    }

    /**
     * @return int
     */
    public function getType() {
        return $this->item_id;
    }

    /**
     * Pull an item's effects.
     *
     * @param int $itemId
     * @return array
     */
    private function itemEffects($itemId) {
        $sel = 'SELECT '.
            'effect_identity, effect_name, effect_verb, effect_self '.
            'FROM effects JOIN item_effects ON _effect_id = effect_id '.
            'WHERE _item_id = :item_id';
        $data = query_array($sel, [':item_id' => [$itemId, PDO::PARAM_INT]]);
        $res = array();

        foreach ($data as $effect) {
            $res[strtolower($effect['effect_identity'])] = $effect;
        }

        return $res;
    }

    /**
     * Get an item model by it's identity string.
     * @return Item
     */
    public static function findByIdentity($identity){
        return self::where('item_internal_name', trim(strtolower($identity)))->first();
    }
}
