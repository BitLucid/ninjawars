<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT."control/lib_inventory.php");

use NinjaWars\core\data\Npc;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\extensions\SessionFactory;
use \Player as Player;
use NinjaWars\core\data\Item;

/**
 * Handles displaying npcs and attacking specific npcs
 */
class NpcController { //extends controller
    const ALIVE                      = true;
    const PRIV                       = false;
    const HIGH_TURNS                 = 50;
    const ITEM_DECREASES_GOLD_DIVISOR = 1.11;
    const ONI_DAMAGE_CAP             = 20;
    const RANDOM_ENCOUNTER_DIVISOR   = 400;
    const SAMURAI_REWARD_DMG         = 100;
    const ONI_TURN_LOSS              = 10;
    const ONI_KILL_LOSS              = 1;
    const MIN_LEVEL_FOR_BOUNTY       = 5;
    const MAX_LEVEL_FOR_BOUNTY       = 50;
    public static $STATIC_NPCS                = ['peasant', 'thief', 'merchant', 'guard', 'samurai'];
    public static $STEALTH_REMOVING_NPCS      = ['samurai', 'oni'];

    private $randomness = null;

    /**
     *
     */
    public function __construct($options=[]) {
        if (isset($options['randomness']) && is_callable($options['randomness'])) {
            $this->randomness = $options['randomness'];
        } else {
            $this->randomness = function() {
                return mt_rand() / mt_getrandmax();
            };
        }
    }

    /**
     * Run the random encounter
     *
     * @note
     * Currently only random enc. is an Oni attack! Yay! They take turns and a
     * kill and do a little damage.
     */
    private function randomEncounter(Player $player) {
        $oni_health_loss  = rand(1, self::ONI_DAMAGE_CAP);
        $turns            = $player->subtractTurns(self::ONI_TURN_LOSS);
        $multiple_rewards = false;
        $oni_killed       = false;
        $item             = null;

        $player->set_turns($turns);
        $player->vo->health = subtractHealth($player->id(), $oni_health_loss);
        $player->subtractKills(self::ONI_KILL_LOSS);

        if ($player->health() > 0) { // if you survive
            if ($player->turns() > self::HIGH_TURNS) { // And your turns are high/you are energetic, you can kill them.
                $oni_killed       = true;
                $item             = new Item('dimmak');
                $quantity         = 1;
                add_item($player->id(), $item->identity(), $quantity);
            } else if ($player->turns() > floor(self::HIGH_TURNS/2) && rand()&1) { // If your turns are somewhat high/you have some energy, 50/50 chance you can kill them.
                $oni_killed       = true;
                $item             = new Item('ginsengroot');
                $multiple_rewards = true;
                $quantity         = 4;
                add_item($player->id(), $item->identity(), $quantity);
            }
        }

        $npc_template = 'npc.oni.tpl';

        $combat_data = [
            'victory'          => $oni_killed,
            'item'             => $item,
            'multiple_rewards' => $multiple_rewards,
        ];

        return [$npc_template, $combat_data];
    }

    /**
     * Wrapper for session storage of thief attacking
     *
     * @return int
     */
    private function getThiefCounter() {
        return SessionFactory::getSession()->get('thief_counter', 1);
    }

    /**
     * Wrapper for session storage of thief attacking
     *
     * @param int $num
     * @return void
     */
    private function setThiefCounter($num) {
        SessionFactory::getSession()->set('thief_counter', $num);
    }

    /**
     * The reward for defeating an npc, less if items popped
     *
     * @param Npc $npco
     * @param boolean $reward_item Any items were rewarded.
     * @return int
     * @note
     * If npc gold explicitly set to 0, reward gold will be totally skipped
     * "rich" npcs will have a higher gold minimum
     */
    private function calcReceivedGold(Npc $npco, $reward_item) {
        if($npco->gold() === 0){ // These npcs simply don't give gold.
            return 0;
        }
        // Hack a little off max gold if items received.
        $divisor = 1;
        if($reward_item){
            $divisor = self::ITEM_DECREASES_GOLD_FACTOR;
        }
        return rand($npco->min_gold(), floor($npco->gold()/$divisor));
    }

    /**
     * Handle Standard Abstract Npcs
     *
     * @param String $victim
     * @param Player $player
     * @param Array $npcs
     * @return array [$npc_template, $combat_data]
     */
    private function attackAbstractNpc($victim, Player $player, $npcs) {
        $npc_stats              = $npcs[$victim]; // Pull an npcs individual stats with generic fallbacks.
        $npco                   = new Npc($npc_stats); // Construct the npc object.
        $display_name           = first_value((isset($npc_stats['name']) ? $npc_stats['name'] : null), ucfirst($victim));
        $status_effect          = (isset($npc_stats['status']) ? $npc_stats['status'] : null);
        // TODO: Calculate and display damage verbs
        $reward_item            = (isset($npc_stats['item']) && $npc_stats['item'] ? $npc_stats['item'] : null);
        $is_quick               = (boolean) ($npco->speed() > $player->speed()); // Beyond basic speed and they see you coming, so show that message.
        $bounty_mod             = (isset($npc_stats['bounty']) ? $npc_stats['bounty'] : null);
        $is_weaker              = ($npco->strength() * 3) < $player->strength(); // Npc much weaker?
        $is_stronger            = ($npco->strength()) > ($player->strength() * 3); // Npc More than twice as strong?
        $image                  = (isset($npc_stats['img']) ? $npc_stats['img'] : null);
        // Assume defeat...
        $victory                = false;
        $received_gold          = null;
        $received_display_items = null;
        $added_bounty           = null;
        $is_rewarded            = null; // Gets items or gold.
        $display_statuses       = $display_statuses_classes = null;
        $image_path             = null;

        if ($image && file_exists(SERVER_ROOT.'www/images/characters/'.$image)) {
            // If the image exists, set the path to it for use on the page.
            $image_path = IMAGE_ROOT.'characters/'.$image;
        }

        // Get percent of total initial health.

        // ******* FIGHT Logic ***********
        $npc_damage = $npco->damage(); // An instance of damage.
        $survive_fight = $player->vo->health = subtractHealth($player->id(), $npc_damage);
        $kill_npc = ($npco->health() < $player->damage());

        if ($survive_fight > 0) {
            // The ninja survived, they get any gold the npc has.
            $received_gold = $this->calcReceivedGold($npco, (bool) $reward_item);
            add_gold($player->id(), $received_gold);
            $received_display_items = array();

            if ($kill_npc) {
                $victory = true;
                // Victory occurred, reward the poor sap.
                if ($npco->inventory()) {
                    foreach (array_keys($npco->inventory()) as $l_item) {
                        $item_info = item_info_from_identity($l_item);
                        $received_display_items[] = $item_info['item_display_name'];
                        add_item($player->id(), $item_info['item_internal_name'], 1);
                    }
                }

                // Add bounty where applicable.
                if ((bool)$bounty_mod &&
                    $player->vo->level > self::MIN_LEVEL_FOR_BOUNTY &&
                    $player->vo->level <= self::MAX_LEVEL_FOR_BOUNTY
                ) {
                    $added_bounty = floor($player->level() / 3 * $bounty_mod);
                    addBounty($player->id(), ($added_bounty));
                }
            }

            $is_rewarded = (bool) $received_gold || (bool)count($received_display_items);

            if (isset($npc_stats['status']) && null !== $npc_stats['status']) {
                $player->addStatus($npc_stats['status']);
                // Get the statuses and status classes for display.
                $display_statuses = implode(', ', get_status_list());
                $display_statuses_classes = implode(' ', get_status_list()); // TODO: Take healthy out of the list since it's redundant.
            }
        }

        // Settings to display results.
        $npc_template = 'npc.abstract.tpl';
        $combat_data = [
            'victim'                   => $victim,
            'display_name'             => $display_name,
            'attack_damage'            => $npc_damage,
            'status_effect'            => $status_effect,
            'display_statuses'         => $display_statuses,
            'display_statuses_classes' => $display_statuses_classes,
            'received_gold'            => $received_gold,
            'received_display_items'   => $received_display_items,
            'is_rewarded'              => $is_rewarded,
            'victory'                  => $victory,
            'survive_fight'            => $survive_fight,
            'kill_npc'                 => $kill_npc,
            'image_path'               => $image_path,
            'npc_stats'                => $npc_stats,
            'is_quick'                 => $is_quick,
            'added_bounty'             => $added_bounty,
            'is_villager'              => $npco->has_trait('villager'),
            'race'                     => $npco->race(),
            'is_weaker'                => $is_weaker,
            'is_stronger'              => $is_stronger,
        ];

        return [$npc_template, $combat_data];
    }

    /**
     * Injectable randomness.
     *
     * @return boolean
     * @note
     * Used to be rand(1, 400) === 1
     */
    private function startRandomEncounter() {
        $randomness = $this->randomness;
        return (boolean) (ceil($randomness() * self::RANDOM_ENCOUNTER_DIVISOR) == self::RANDOM_ENCOUNTER_DIVISOR);
    }

    /**
     * Attack a specific npc
     *
     * @todo remove REQUEST_URI access and use params
     * @see http://nw.local/npc/attack/villager
     * @see http://nw.local/npc/attack/guard/
     */
    public function attack() {
        // This used to pull directly from $victim get param

        $url_part = $_SERVER['REQUEST_URI'];

        // Test urls:

        if (preg_match('#\/(\w+)(\/)?$#', $url_part, $matches)) {
            $victim = $matches[1];
        } else {
            $victim = null; // No match, victim is null.
        }

        $today = date("F j, Y, g:i a");  // Today var is only used for creating mails.

        $turn_cost      = 1;
        $health         = true;
        $combat_data    = [];
        $player         = new Player(self_char_id());
        $char_id        = $player->id();
        $error_template = 'npc.no-one.tpl'; // Error template also used down below.
        $npc_template   = $error_template; // Error condition by default.
        $ninja_str      = $player->getStrength();
        $npcs           = NpcFactory::npcsData();
        $possible_npcs  = array_merge(self::$STATIC_NPCS, array_keys($npcs));
        $victim         = restrict_to($victim, $possible_npcs); // Filter to only the correct options.

        if ($player->turns() > 0 && !empty($victim)) {
            // Strip stealth when attacking special NPCs
            if ($player->hasStatus('stealth') && in_array(strtolower($victim), self::$STEALTH_REMOVING_NPCS)) {
                $player->subtractStatus(STEALTH);
            }

            if ($this->startRandomEncounter()) {
                list($npc_template, $combat_data) = $this->randomEncounter($player);
            } elseif (array_key_exists($victim, $npcs)) {
                /**** Abstracted NPCs *****/
                list($npc_template, $combat_data) = $this->attackAbstractNpc($victim, $player, $npcs);

                // ******************** START of logic for specific npcs ************************
            } else if ($victim == 'peasant') { // *** PEASANT, was VILLAGER ***
                $villager_attack = rand(0, 10); // *** Villager Damage ***
                $just_villager   = rand(0, 20);
                $added_bounty    = 0;

                if ($player->vo->health = $victory = subtractHealth($char_id, $villager_attack)) {  // *** Player defeated villager ***
                    $villager_gold = rand(0, 20);   // *** Vilager Gold ***
                    add_gold($char_id, $villager_gold);

                    $attacker_level = $player->vo->level;

                    // *** Bounty or no bounty ***
                    if ($attacker_level > 1) {
                        if ($attacker_level <= 20) {
                            $added_bounty = floor($attacker_level / 3);
                            addBounty($char_id, ($added_bounty));
                        }
                    }   // *** End of if > 5 ***

                    if (!$just_villager) {
                        // Something beyond just a villager, drop a shuriken
                        add_item($char_id, 'shuriken', 1);
                    }
                } else {    // *** Player lost against villager ***
                    $villager_gold = $attacker_level = $added_bounty = 0;
                }

                $npc_template = 'npc.peasant.tpl';
                $combat_data = [
                    'just_villager' => $just_villager,
                    'attack'        => $villager_attack,
                    'gold'          => $villager_gold,
                    'level'         => $attacker_level,
                    'bounty'        => $added_bounty,
                    'victory'       => $victory,
                ];
            } else if ($victim == "samurai") {
                $attacker_level       = $player->vo->level;
                $attacker_kills       = $player->vo->kills;
                $weakness_error       = false;
                $samurai_damage_array = null;
                $samurai_gold         = null;
                $victory              = false;
                $drop                 = null;
                $drop_display         = null;
                $turn_cost            = 1;

                if ($attacker_level < 2 || $attacker_kills < 1) {
                    $turn_cost = 0;
                    $weakness_error = 'You are too weak to attack the samurai.';
                } else {
                    $samurai_damage_array = [
                        rand(1, $player->strength()),
                        rand(10, 10 + round($player->strength() * 1.2)),
                    ];

                    $does_ninja_succeed = rand(0, 1);

                    if ($does_ninja_succeed) {
                        $samurai_damage_array[] = rand(30 + round($player->strength() * 0.2), 30 + round($player->strength() * 1.7));
                    } else {
                        $samurai_damage_array[] = abs($player->health() - $samurai_damage_array[0] - $samurai_damage_array[1]);  //Instant death.
                    }

                    $ninja_health = $player->health(); // Get starting value and iterate it down.

                    for ($i = 0; $i < 3 && $ninja_health > 0; ++$i) {
                        $ninja_health = $ninja_health - $samurai_damage_array[$i];
                    }

                    if ($ninja_health > 0) { // Ninja still has health after all three attacks
                        $victory = true;

                        $samurai_gold = rand(50, 50 + $samurai_damage_array[2] + $samurai_damage_array[1]);

                        add_gold($char_id, $samurai_gold);
                        $player->addKills(1);

                        // If samurai dmg high, but ninja lived, give rewards
                        if ($samurai_damage_array[2] > self::SAMURAI_REWARD_DMG) {
                            $drop = true;

                            if (rand(0, 1)) {
                                $drop_display = 'mushroom powder';
                                $dropItem = 'amanita';
                            } else {
                                $drop_display = 'a strange herb';
                                $dropItem = 'ginsengroot';
                            }

                            add_item($char_id, $dropItem, 1);
                        }

                        // If the final damage was the exact max damage
                        if ($samurai_damage_array[2] == $player->strength() * 3) {
                            $drop         = true;
                            $drop_display = 'a black scroll';
                            add_item($char_id, "dimmak", 1);
                        }

                        $player->vo->health = setHealth($char_id, $ninja_health);
                    } else {
                        $player->vo->health = setHealth($char_id, 0);

                        $victory   = false;
                        $ninja_str = $samurai_gold = 0;
                    }
                }   // *** End valid turns and kills for the attack. ***

                $npc_template = 'npc.samurai.tpl';
                $combat_data = [];

                if (!$weakness_error) {
                    $combat_data = [
                        'samurai_damage_array' => $samurai_damage_array,
                        'gold'                 => $samurai_gold,
                        'victory'              => $victory,
                        'ninja_str'            => $ninja_str,
                        'level'                => $attacker_level,
                        'attacker_kills'       => $attacker_kills,
                        'drop'                 => $drop,
                        'drop_display'         => $drop_display,
                    ];
                }
            } else if ($victim == 'merchant') {
                $merchant_attack = rand(15, 35);  // Merchant Damage
                $added_bounty    = 0;

                // Player killed merchant
                if ($player->vo->health = $victory = subtractHealth($char_id, $merchant_attack)) {
                    $merchant_gold = rand(20, 70);  // Merchant Gold
                    add_gold($char_id, $merchant_gold);

                    if ($merchant_attack > 34) {
                        add_item($char_id, 'phosphor', 1);
                    }

                    if ($player->vo->level > 10) {
                        $added_bounty = 5 * floor(($player->vo->level - 5) / 3);
                        addBounty($char_id, $added_bounty);
                    }
                } else { // Merchant killed player
                    $merchant_attack = $merchant_gold = 0;
                }

                $npc_template = 'npc.merchant.tpl';
                $combat_data  = [
                    'attack'  => $merchant_attack,
                    'gold'    => $merchant_gold,
                    'bounty'  => $added_bounty,
                    'victory' => $victory,
                ];
            } else if ($victim == 'guard') { // The Player attacks the guard
                $guard_attack = rand(1, $player->strength() + 10); // Guard Damage
                $herb         = false;
                $added_bounty = 0;

                if ($player->vo->health = $victory = subtractHealth($char_id, $guard_attack)) {
                    $guard_gold = rand(1, $player->strength() + 40);  // Guard Gold
                    add_gold($char_id, $guard_gold);

                    if ($player->vo->level > 15) {
                        $added_bounty = 10 * floor(($player->vo->level - 10) / 5);
                        addBounty($char_id, $added_bounty);
                    }

                    // 1/9 chance of getting an herb for Kampo
                    if (rand(1, 9) == 9) {
                        $herb = true;
                        add_item($char_id, 'ginsengroot', 1);
                    } else {
                        $herb = false;
                    }
                } else {    // *** The Guard kills the player ***
                    $guard_attack = $guard_gold = $added_bounty = 0;
                }

                $npc_template = 'npc.guard.tpl';
                $combat_data  = [
                    'attack'  => $guard_attack,
                    'gold'    => $guard_gold,
                    'bounty'  => $added_bounty,
                    'victory' => $victory,
                    'herb'    => $herb,
                ];
            } else if ($victim == 'thief') {
                // Check the counter to see whether they've attacked a thief multiple times in a row.
                $counter = $this->getThiefCounter();

                $this->setThiefCounter($counter+1); // Incremement the current state of the counter.

                if ($counter > 20 && rand(1, 3) == 3) {
                    // Only after many attacks do you have the chance to be attacked back by the group of thieves.
                    $this->setThiefCounter(0); // Reset the counter to zero.
                    $group_attack= rand(50, 150);

                    if ($player->vo->health = $victory = subtractHealth($char_id, $group_attack)) { // The den of thieves didn't accomplish their goal
                        $group_gold = rand(100, 300);

                        if ($group_attack > 120) { // Powerful attack gives an additional disadvantage
                            $player->subtractKills(1);
                        }

                        add_gold($char_id, $group_gold);
                        add_item($char_id, 'phosphor', 1);
                    } else {    // If the den of theives killed the attacker.
                        $group_gold = 0;
                    }

                    $npc_template = 'npc.thief-group.tpl';
                    $combat_data = [
                        'attack'  => $group_attack,
                        'gold'    => $group_gold,
                        'victory' => $victory,
                    ];
                } else { // Normal attack on a single thief.
                    $thief_attack = rand(0, 35);  // *** Thief Damage  ***

                    if ($player->vo->health = $victory = subtractHealth($char_id, $thief_attack)) {
                        $thief_gold = rand(0, 40);  // *** Thief Gold ***

                        if ($thief_attack > 30) {
                            subtract_gold($char_id, $thief_gold);
                        } else if ($thief_attack < 30) {
                            add_gold($char_id, $thief_gold);
                            add_item($char_id, 'shuriken', 1);
                        }
                    } else {
                        $thief_gold = 0;
                    }

                    $npc_template = 'npc.thief.tpl';
                    $combat_data = [
                        'attack'  => $thief_attack,
                        'gold'    => $thief_gold,
                        'victory' => $victory,
                    ];
                }
            }

            // ************ End of specific npc logic *******************

            // ************ FINAL CHECK FOR DEATH ***********************
            if ($player->health() <= 0) {
                $health = false;
                sendMessage("SysMsg", $player->name(), "DEATH: You have been killed by a $victim on $today");
            }

            // Subtract the turn cost for attacking an npc
            // almost always 1 apart from perhaps oni or group-of-thieves
            $turns = $player->subtractTurns($turn_cost);
            $player->set_turns($turns);
        }

        $template = 'npc.tpl';
        $title    = 'Battle';

        // Uses a sub-template inside for specific npcs.
        $parts = [
            'victim'       => $victim, // merge may override in theory
            'npc_template' => $npc_template,
            'attacked'     => 1,
            'turns'        => $player->turns(),
            'health'       => $health,
        ];

        $parts = $parts + $combat_data; // Merge in combat data.
        $options = ['quickstat' => 'player'];

        return [
            'template' => $template,
            'title'    => $title,
            'parts'    => $parts,
            'options'  => $options,
        ];
    }

    /**
     * Obtain the npcs data.
     *
     * @return Array
     */
    private function npcs() {
        return [
            'abstract_npcs' => NpcFactory::npcsData(),
            'custom_npcs'=> [
                ['name'=>'Peasant',  'identity'=>'peasant',  'image'=>'fighter.png'],
                ['name'=>'Thief',    'identity'=>'thief',    'image'=>'thief.png'],
                ['name'=>'Merchant', 'identity'=>'merchant', 'image'=>'merchant.png'],
                ['name'=>'Guard',    'identity'=>'guard',    'image'=>'guard.png'],
                ['name'=>'Samurai',  'identity'=>'samurai',  'image'=>'samurai.png'],
            ]
        ];
    }

    /**
     * Get the list of npcs in a subtemplate.
     *
     * @return ViewSpec
     */
    public function index() {
        $all_npcs   = $this->npcs();
        $other_npcs = $all_npcs['abstract_npcs'];
        $npcs       = $all_npcs['custom_npcs'];
        $template   = 'npc.list.tpl';
        $title      = 'Npcs';
        // Uses a sub-template inside for specific npcs.
        $parts      = ['npcs' => $npcs, 'other_npcs' => $other_npcs];
        $options    = ['quickstats' => 'player'];

        return [
            'template' => $template,
            'title'    => $title,
            'parts'    => $parts,
            'options'  => $options,
        ];
    }
}
