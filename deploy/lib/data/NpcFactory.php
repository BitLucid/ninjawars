<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\Npc;

class InvalidNpcException extends \Exception{}

/**
 * Who/what/why/where
 *  Create npcs with static methods.
 *
 */
class NpcFactory {
    public static $data;

    /**
     * Returns a fleshed out npc object
     *
     * @return Npc
     */
    public static function create($identity) {
        $identity = mb_strtolower($identity);
        $npcs = self::npcsData();
        $npc = null;

        if ($identity && array_key_exists($identity, $npcs)) {
            $npc = new Npc($npcs[$identity]);
        }

        return $npc;
    }

    /**
     * Pass the npc in and use the reference to flesh out it's data
     *
     * Get the data from an identity if nothing else
     *
     * @return void
     * @throws InvalidNpcException
     */
    public static function fleshOut($identity, $npc) {
        $npcs_data = self::npcsData();

        if (array_key_exists($identity, $npcs_data) && !empty($npcs_data[$identity])) {
            self::fleshOutFromData($npcs_data[$identity], $npc);
        } else {
            throw new InvalidNpcException('No such npc ['.$identity.'] found to create!');
        }
    }

    /**
     * Create the flesh of an npc from it's data
     *
     * @return void
     */
    public static function fleshOutFromData($data, $npc) {
        $npc->name              = @$data['name'];
        $npc->image             = @$data['img'];
        $npc->short_desc        = @$data['short'];
        $npc->inventory_chances = @$data['inventory'];
        $npc->strength          = (int) @$data['strength'];
        $npc->speed             = (int) @$data['speed'];
        $npc->stamina           = (int) @$data['stamina'];
        $npc->damage            = (int) @$data['damage'];
        $npc->ki                = (int) @$data['ki'];
        $npc->race              = @$data['race'];
        $npc->bounty_mod            = @$data['bounty_mod'];
        $npc->gold              = @$data['gold'];
        $npc->traits_array      = (isset($data['traits']) && is_array($data['traits']) ? $data['traits'] : []);
        $npc->inventory         = null; // The actual instance inventory is intitially just null;
    }

    /**
     * Pull all the npcs from data source.
     *
     * @return Npc[]
     */
    public static function npcs($sort=null) {
        $npcs_data = self::npcsData();
        $npcs = [];

        foreach ($npcs_data as $identity => $npc_data) {
            assert((bool)$identity);
            $npcs[$identity] = new Npc($npc_data);
        }

        if ($sort) {
            $npc = reset($npcs);

            if (is_callable([$npc, $sort])) {
                // Sort the npcs by difficulty
                usort($npcs, function ($a, $b) use ($sort) {
                    $anum = $a->$sort();
                    $bnum = $b->$sort();

                    if ($anum == $bnum) {
                        return 0 ;
                    }

                    return ($anum < $bnum ? -1 : 1);
                });
            }
        }

        return $npcs;
    }

    /**
     * Alternate alias for the npcs static function.
     *
     * @return Npc[]
     */
    public static function all() {
        return self::npcs();
    }

    /**
     * Convenience function to get npcs by difficulty
     *
     * @return Npc[]
     */
    public static function allSortedByDifficulty() {
        return self::npcs('difficulty');
    }

    /**
     * Get npcs excluding zero-difficulty npcs
     *
     * @return Npc[]
     */
    public static function allNonTrivialNpcs() {
        $npcs = self::allSortedByDifficulty();

        $nontrivials = array_filter($npcs, function($npc) {
            return (bool) ($npc->difficulty() > 0);
        });

        return $nontrivials;
    }

    /**
     * Npcs that have essentially nothing interesting or useful defined about them yet.
     *
     * @return Npc[]
     */
    public static function allTrivialNpcs() {
        $npcs = self::allSortedByDifficulty();

        $trivials = array_filter($npcs, function($npc) {
            return (bool) ($npc->difficulty() < 1);
        });

        return $trivials;
    }

    /**
     * Pull all the npcs
     *
     * @return Array
     */
    public static function npcsData() {
        return self::$data;
    }

    public static function customNpcs() {
        return [
            ['name'=>'Peasant',  'identity'=>'peasant',  'image'=>'fighter.png'],
            ['name'=>'Thief',    'identity'=>'thief',    'image'=>'thief.png'],
            ['name'=>'Merchant', 'identity'=>'merchant', 'image'=>'merchant.png'],
            ['name'=>'Guard',    'identity'=>'guard',    'image'=>'guard.png'],
            ['name'=>'Samurai',  'identity'=>'samurai',  'image'=>'samurai.png'],
        ];
    }
}
