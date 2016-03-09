<?php
namespace NinjaWars\core\data;

use \Player;
use \PDO;

/**
 * Managing items like shuriken, dimmak
 */
class Item {
    protected $m_name;
    protected $m_plural;
    protected $m_ignoresStealth;
    protected $m_targetDamage;
    protected $m_maxDamage;
    protected $m_turnCost;
    protected $m_turnChange;
    protected $m_maxTurnChange;
    protected $m_covert;
    protected $m_selfUse;
    protected $m_otherUsable;
    protected $m_type;
    protected $m_identity;

    /**
     * Set all the default settings for items, overridden by specified settings
     */
    public function __construct($dirty_content=null) {
        // Potentially, an identity string is what's being passed in.
        if (is_string($dirty_content) && trim($dirty_content) !== '') {
            $data = item_info_from_identity($dirty_content);
            $this->buildFromArray($data);
        }
    }

    /**
     * Builds the item from the database table data
     *
     * @param Array $p_data
     * @return void
     * @note
     * Eventually this perhaps should be abstracted to a factory pattern
     */
    public function buildFromArray($p_data) {
        $this->m_type           = $p_data['item_id'];
        $this->m_identity       = $p_data['item_internal_name'];
        $this->m_name           = $p_data['item_display_name'];
        $this->m_plural         = $p_data['plural'];
        $this->m_turnCost       = ($p_data['turn_cost']     ? $p_data['turn_cost']     : 1);
        $this->m_maxTurnChange  = ($p_data['turn_change']   ? $p_data['turn_change']   : 0);
        $this->m_targetDamage   = ($p_data['target_damage'] ? $p_data['target_damage'] : null);
        $this->m_maxDamage      = $this->m_targetDamage;
        $this->m_ignoresStealth = ($p_data['ignore_stealth']);
        $this->m_covert         = ($p_data['covert']);
        $this->m_selfUse        = ($p_data['self_use']);
        $this->m_otherUsable    = ($p_data['other_usable']);
    }

    /**
     * Returns not the identity, but the display name
     *
     * @return String
     */
    public function getName() {
        return $this->m_name;
    }

    /**
     * Convenience function to get the plural name for the object.
     *
     * @return String
     */
    public function getPluralName() {
        return $this->m_name.$this->m_plural;
    }

    /**
     * @return String
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * The numeric id for this item type.
     *
     * @return int
     */
    public function id() {
        return $this->m_type;
    }

    /**
     * The item's internally used name.
     *
     * @return String
     */
    public function identity() {
        return $this->m_identity;
    }

    /**
     * Gets the list of effects that the item does.
     *
     * @return Array
     */
    public function effects() {
        // Pull the effects array via the external function.
        return $this->itemEffects($this->id());
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
        $this->m_turnChange = (float)$p_turns;
    }

    /**
     * Unmodified ideal maximum turn change
     *
     * @return int
     */
    public function getMaxTurnChange() {
        return $this->m_maxTurnChange;
    }

    /**
     * Returns the turn change that the object will cause.
     *
     * @TODO: should be modified by existing effects, should accept target obj
     * @return int
     */
    public function getTurnChange() {
        return $this->m_turnChange;
    }

    /**
     * Set whether the object will be able to bypass stealth mode
     *
     * @param boolean $p_ignore
     * @return void
     */
    public function setIgnoresStealth($p_ignore) {
        $this->m_ignoresStealth = (boolean)$p_ignore;
    }

    /**
     * Returns whether or not this item can be used against stealthed targets
     *
     * @return boolean
     */
    public function ignoresStealth() {
        return $this->m_ignoresStealth;
    }

    /**
     *
     *
     * @param int $p_damage
     * @return void
     */
    public function setTargetDamage($p_damage) {
        $this->m_targetDamage = (int)$p_damage;
    }

    /**
     * If an item has inherent, non-effects based damage.
     *
     * @return int
     * @note
     * Ex: A shuriken relies exclusively on the slice effect for it's damage.
     */
    public function getTargetDamage() {
        return (int)$this->m_targetDamage;
    }

    /**
     * Get the maximum numeric damage of an object.
     *
     * If a player is passed in, damage is the better of 9 or 2/3rd of the
     * player's strength -4.
     *
     * @param Player $c (optional) Use player to calculate damage
     * @return int
     * @note
     * Some effects-based object will not actually have a pre-known maxDamage
     */
    public function getMaxDamage(Player $c=null) {
        if ($c instanceof Player && $this->hasDynamicDamage()) {
            return max(9, floor($c->strength() * 2/3)-4);
        }

        return $this->m_maxDamage;
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
        return rand(0, $this->m_maxDamage);
    }

    /**
     * @return int
     */
    public function getTurnCost() {
        return $this->m_turnCost;
    }

    /**
     *
     * @param boolean $p_covert
     * @return void
     */
    public function setCovert($p_covert) {
        $this->m_covert = (boolean)$p_covert;
    }

    public function isCovert() {
        return $this->m_covert;
    }

    public function isSelfUsable() {
        return $this->m_selfUse;
    }

    /**
     * Check whether the item is usable upon others.
     *
     * @return boolean
     */
    public function isOtherUsable() {
        return $this->m_otherUsable;
    }

    /**
     * @return int
     */
    public function getType() {
        return $this->m_type;
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
}
