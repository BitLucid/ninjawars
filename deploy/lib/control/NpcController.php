<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\Filter;
use NinjaWars\core\data\Npc;
use NinjaWars\core\control\Combat;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\data\Item;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Event;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;

/**
 * Handles displaying npcs and attacking specific npcs
 */
class NpcController extends AbstractController {
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

    public static $STEALTH_REMOVING_NPCS = ['samurai', 'oni'];

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
        $multiple_rewards = false;
        $oni_killed       = false;
        $item             = null;

        $player->changeTurns(-1*self::ONI_TURN_LOSS);
        $player->harm($oni_health_loss);
        $player->subtractKills(self::ONI_KILL_LOSS);

        if ($player->health() > 0) { // if you survive
            $inventory = new Inventory($player);

            if ($player->turns > self::HIGH_TURNS) { // And your turns are high/you are energetic, you can kill them.
                $oni_killed       = true;
                $item             = Item::findByIdentity('dimmak');
                $quantity         = 1;
                $inventory->add($item->identity(), $quantity);
            } else if ($player->turns > floor(self::HIGH_TURNS/2) && rand()&1) {
                // If your turns are somewhat high/you have some energy, 50/50 chance you can kill them.
                $oni_killed       = true;
                $item             = Item::findByIdentity('ginsengroot');
                $multiple_rewards = true;
                $quantity         = 4;
                $inventory->add($item->identity(), $quantity);
            }
        }

        $player->save();

        return [
            'npc.oni.tpl',
            [
                'victory'          => $oni_killed,
                'item'             => $item,
                'multiple_rewards' => $multiple_rewards,
            ],
        ];
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
        if ($npco->gold() === 0) { // These npcs simply don't give gold.
            return 0;
        }

        // Hack a little off max gold if items received.
        $divisor = 1;
        if ($reward_item) {
            $divisor = self::ITEM_DECREASES_GOLD_FACTOR;
        }

        return rand($npco->minGold(), floor($npco->gold()/$divisor));
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
        $npc_stats        = $npcs[$victim]; // Pull an npcs individual stats with generic fallbacks.
        $npco             = new Npc($npc_stats); // Construct the npc object.
        $display_name     = first_value((isset($npc_stats['name']) ? $npc_stats['name'] : null), ucfirst($victim));
        $status_effect    = (isset($npc_stats['status']) ? $npc_stats['status'] : null);
        $reward_item      = (isset($npc_stats['item']) && $npc_stats['item'] ? $npc_stats['item'] : null);
        $is_quick         = (boolean) ($npco->speed() > $player->speed()); // Beyond basic speed and they see you coming, so show that message.
        $is_weaker        = ($npco->strength() * 3) < $player->strength(); // Npc much weaker?
        $is_stronger      = ($npco->strength()) > ($player->strength() * 3); // Npc More than twice as strong?
        $image            = (isset($npc_stats['img']) ? $npc_stats['img'] : null);
        // Assume defeat...
        $victory          = false;
        $received_gold    = null;
        $received_items   = null;
        $added_bounty     = null;
        $is_rewarded      = null; // Gets items or gold.
        $statuses         = null;
        $status_classes   = null;
        $image_path       = null;

        // If the image exists, set the path to it for use on the page.
        if ($image && file_exists(SERVER_ROOT.'www/images/characters/'.$image)) {
            $image_path = IMAGE_ROOT.'characters/'.$image;
        }

        // ******* FIGHT Logic ***********
        $npc_damage    = $npco->damage();
        $survive_fight = $player->harm($npc_damage);
        $kill_npc      = ($npco->health() < $player->damage());

        if ($survive_fight > 0) {
            // The ninja survived, they get any gold the npc has.
            $received_gold = $this->calcReceivedGold($npco, (bool) $reward_item);
            $player->set_gold($player->gold + $received_gold);
            $received_items = array();

            if ($kill_npc) {
                $victory = true;
                // Victory occurred, reward the poor sap.
                if ($npco->inventory()) {
                    $inventory = new Inventory($player);

                    foreach (array_keys($npco->inventory()) as $l_item) {
                        $item = Item::findByIdentity($l_item);
                        $received_items[] = $item->getName();
                        $inventory->add($item->identity(), 1);
                    }
                }

                // Add bounty where applicable for npcs.
                if ($npco->bountyMod() > 0 &&
                    $player->level > self::MIN_LEVEL_FOR_BOUNTY &&
                    $player->level <= self::MAX_LEVEL_FOR_BOUNTY
                ) {
                    $added_bounty = Combat::runBountyExchange($player, $npco, $npco->bountyMod());
                }
            }

            $is_rewarded = (bool) $received_gold || (bool)count($received_items);

            if (isset($npc_stats['status']) && null !== $npc_stats['status']) {
                $player->addStatus($npc_stats['status']);
                // Get the statuses and status classes for display.
                $statuses = implode(', ', Player::getStatusList());
                $status_classes = implode(' ', Player::getStatusList());
            }
        }

        $player->save();

        return [
            'npc.abstract.tpl',
            [
                'victim'                   => $victim,
                'display_name'             => $display_name,
                'attack_damage'            => $npc_damage,
                'status_effect'            => $status_effect,
                'display_statuses'         => $statuses,
                'display_statuses_classes' => $status_classes,
                'received_gold'            => $received_gold,
                'received_display_items'   => $received_items,
                'is_rewarded'              => $is_rewarded,
                'victory'                  => $victory,
                'survive_fight'            => $survive_fight,
                'kill_npc'                 => $kill_npc,
                'image_path'               => $image_path,
                'npc_stats'                => $npc_stats,
                'is_quick'                 => $is_quick,
                'added_bounty'             => $added_bounty,
                'is_villager'              => $npco->hasTrait('villager'),
                'race'                     => $npco->race(),
                'is_weaker'                => $is_weaker,
                'is_stronger'              => $is_stronger,
            ]
        ];
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
     * @return Response
     * @todo remove REQUEST_URI access and use params
     * @see http://nw.local/npc/attack/villager
     * @see http://nw.local/npc/attack/guard/
     */
    public function attack() {
        $url_part = $_SERVER['REQUEST_URI'];

        if (preg_match('#\/(\w+)(\/)?$#', $url_part, $matches)) {
            $victim = $matches[1];
        } else {
            $victim = null; // No match, victim is null.
        }

        $today = date("F j, Y, g:i a");  // Today var is only used for creating mails.

        $turn_cost      = 1;
        $health         = true;
        $combat_data    = [];
        $player         = Player::find(SessionFactory::getSession()->get('player_id'));
        $error_template = 'npc.no-one.tpl'; // Error template also used down below.
        $npc_template   = $error_template; // Error condition by default.
        $npcs           = NpcFactory::npcsData();
        $possible_npcs  = array_merge(array_column(NpcFactory::customNpcs(), 'identity'), array_keys($npcs));
        $victim         = (in_array($victim, $possible_npcs) ? $victim : null); // Filter to only the correct options.

        $standard_npcs  = [
            'peasant'  => 'attackVillager',
            'merchant' => 'attackMerchant',
            'guard'    => 'attackGuard',
        ];

        $method = null;

        if ($player && $player->turns > 0 && !empty($victim)) {
            // Strip stealth when attacking special NPCs
            if ($player->hasStatus('stealth') && in_array(strtolower($victim), self::$STEALTH_REMOVING_NPCS)) {
                $player->subtractStatus(STEALTH);
            }

            if ($this->startRandomEncounter()) {
                $method = 'randomEncounter';
            } elseif (array_key_exists($victim, $npcs)) {
                list($npc_template, $combat_data) = $this->attackAbstractNpc($victim, $player, $npcs);
            } else if (array_key_exists($victim, $standard_npcs)) {
                $method = $standard_npcs[$victim];
            } else if ($victim == "samurai") {
                if ($player->level < 2) {
                    $turn_cost = 0;
                    $npc_template = 'npc.samurai-too-weak.tpl';
                } else if ($player->kills < 1) {
                    $turn_cost = 0;
                    $npc_template = 'npc.samurai-too-tired.tpl';
                } else {
                    $method = 'attackSamurai';
                }
            } else if ($victim == 'thief') {
                // Check the counter to see whether they've attacked a thief multiple times in a row.
                $counter = $this->getThiefCounter();

                $this->setThiefCounter($counter+1); // Incremement the current state of the counter.

                if ($counter > 20 && rand(1, 3) == 3) {
                    // Only after many attacks do you have the chance to be attacked back by the group of thieves.
                    $this->setThiefCounter(0); // Reset the counter to zero.
                    $method = 'attackGroupOfThieves';
                } else {
                    $method = 'attackNormalThief';
                }
            }

            if (is_callable([$this, $method], false)) {
                list($npc_template, $combat_data) = $this->$method($player);
            }

            if ($player->health() <= 0) { // FINAL CHECK FOR DEATH
                $player->death();
                $health = false;
                Event::create((int)"SysMsg", $player->id(), "DEATH: You have been killed by a $victim on $today");
            }

            // Subtract the turn cost for attacking an npc
            // almost always 1 apart from perhaps oni or group-of-thieves
            $player->changeTurns(-1*$turn_cost);

            $player->save();
        }

        // Uses a sub-template inside for specific npcs.
        $parts = [
            'victim'       => $victim, // merge may override in theory
            'npc_template' => $npc_template,
            'attacked'     => 1,
            'turns'        => $player? $player->turns : null,
            'health'       => $health,
        ];

        return new StreamedViewResponse('Battle', 'npc.tpl', $parts + $combat_data, ['quickstat' => 'player']);
    }

    private function attackGuard(Player $player) {
        $damage = rand(1, $player->strength() + 10);
        $herb   = false;
        $gold   = 0;
        $bounty = 0;

        if ($victory = $player->harm($damage)) {
            $gold = rand(1, $player->strength() + 40);
            $player->set_gold($player->gold + $gold);

            if ($player->level > 15) {
                $bounty = 10 * floor(($player->level - 10) / 5);
                $player->set_bounty($player->bounty + $bounty);
            }

            // chance of getting an herb for Kampo
            if (rand(1, 9) == 9) {
                $herb = true;
                $inventory = new Inventory($player);
                $inventory->add('ginsengroot', 1);
            }
        } else {
            $damage = 0;
        }

        return [
            'npc.guard.tpl',
            [
                'attack'  => $damage,
                'gold'    => $gold,
                'bounty'  => $bounty,
                'victory' => $victory,
                'herb'    => $herb,
            ],
        ];
    }

    private function attackVillager(Player $player) {
        $damage        = rand(0, 10);
        $just_villager = rand(0, 20);
        $bounty        = 0;
        $gold          = 0;

        if ($victory = $player->harm($damage)) {
            $gold = rand(0, 20);
            $player->set_gold($player->gold + $gold);

            // *** Bounty or no bounty ***
            if ($player->level > 1 && $player->level <= 20) {
                $bounty = floor($player->level / 3);
                $player->set_bounty($player->bounty + $bounty);
            }

            if (!$just_villager) {
                // Something beyond just a villager, drop a shuriken
                $inventory = new Inventory($player);
                $inventory->add('shuriken', 1);
            }
        }

        $player->save();

        return [
            'npc.peasant.tpl',
            [
                'just_villager' => $just_villager,
                'attack'        => $damage,
                'gold'          => $gold,
                'level'         => $player->level,
                'bounty'        => $bounty,
                'victory'       => $victory,
            ],
        ];
    }

    private function attackSamurai(Player $player) {
        $gold         = 0;
        $victory      = false;
        $drop         = false;
        $drop_display = null;

        $damage = [
            rand(1, $player->strength()),
            rand(10, 10 + round($player->strength() * 1.2)),
        ];

        if (rand(0, 1)) {
            $damage[] = rand(30 + round($player->strength() * 0.2), 30 + round($player->strength() * 1.7));
        } else { //Instant death.
            $damage[] = abs($player->health - $damage[0] - $damage[1]);
        }

        for ($i = 0; $i < count($damage) && $player->health > 0; ++$i) {
            $player->harm($damage[$i]);
        }

        if ($player->health > 0) { // Ninja still has health after all attacks
            $victory = true;

            $gold = rand(50, 50 + $damage[2] + $damage[1]);

            $player->addKills(1);
            $player->set_gold($player->gold + $gold);

            $inventory = new Inventory($player);

            // If samurai dmg high, but ninja lived, give rewards
            if ($damage[2] > self::SAMURAI_REWARD_DMG) {
                $drop = true;

                if (rand(0, 1)) {
                    $drop_display = 'mushroom powder';
                    $dropItem = 'amanita';
                } else {
                    $drop_display = 'a strange herb';
                    $dropItem = 'ginsengroot';
                }

                $inventory->add($dropItem, 1);
            }

            // If the final damage was the exact max damage
            if ($damage[2] == $player->strength() * 3) {
                $drop         = true;
                $drop_display = 'a black scroll';
                $inventory->add('dimmak', 1);
            }
        }

        $player->save();

        return [
            'npc.samurai.tpl',
            [
                'samurai_damage_array' => $damage,
                'gold'                 => $gold,
                'victory'              => $victory,
                'ninja_str'            => $player->strength(),
                'level'                => $player->level,
                'attacker_kills'       => $player->kills,
                'drop'                 => $drop,
                'drop_display'         => $drop_display,
            ],
        ];
    }

    private function attackGroupOfThieves(Player $player) {
        $damage = rand(50, 150);

        if ($victory = $player->harm($damage)) {
            // The den of thieves didn't accomplish their goal
            $gold = rand(100, 300);

            if ($damage > 120) { // Powerful attack gives an additional disadvantage
                $player->subtractKills(1);
            }

            $player->set_gold($player->gold + $gold);

            $inventory = new Inventory($player);
            $inventory->add('phosphor', 1);
        } else {    // If the den of theives killed the attacker.
            $gold = 0;
        }

        $player->save();

        return [
            'npc.thief-group.tpl',
            [
                'attack'  => $damage,
                'gold'    => $gold,
                'victory' => $victory,
            ],
        ];
    }

    /**
     * Attack merchant
     */
    private function attackMerchant($player) {
        $damage = rand(15, 35);
        $bounty = 0;

        // Player killed NPC
        if ($victory = $player->harm($damage)) {
            $gold = rand(20, 70);
            $player->set_gold($player->gold + $gold);

            if ($damage > 34) {
                $inventory = new Inventory($player);
                $inventory->add('phosphor', 1);
            }

            if ($player->level > 10) {
                $bounty = 5 * floor(($player->level - 5) / 3);
                $player->set_bounty($player->bounty + $bounty);
            }
        } else { // NPC killed player
            $damage = $gold = 0;
        }

        $player->save();

        return [
            'npc.merchant.tpl',
            [
                'attack'  => $damage,
                'gold'    => $gold,
                'bounty'  => $bounty,
                'victory' => $victory,
            ],
        ];
    }

    /**
     * Normal attack on a single thief.
     */
    private function attackNormalThief(Player $player) {
        $damage = rand(0, 35);  // Damage done
        $gold   = 0;

        if ($victory = $player->harm($damage)) {
            $gold = rand(0, 40);  // Gold in question

            if ($damage > 30) { // Steal gold
                $player->set_gold(max(0, $player->gold - $gold));
            } else if ($damage < 30) { // award gold and item
                $player->set_gold($player->gold + $gold);
                $inventory = new Inventory($player);
                $inventory->add('shuriken', 1);
            }
        }

        $player->save();

        return [
            'npc.thief.tpl',
            [
                'attack'  => $damage,
                'gold'    => $gold,
                'victory' => $victory,
            ],
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
            'custom_npcs'   => NpcFactory::customNpcs(),
        ];
    }

    /**
     * Get the list of npcs in a subtemplate.
     *
     * @return Response
     */
    public function index() {
        $all_npcs   = $this->npcs();
        $other_npcs = $all_npcs['abstract_npcs'];
        $npcs       = $all_npcs['custom_npcs'];
        $template   = 'npc.list.tpl';
        $title      = 'Npcs';
        $parts      = ['npcs' => $npcs, 'other_npcs' => $other_npcs];
        $options    = ['quickstats' => 'player'];

        return new StreamedViewResponse($title, $template, $parts, $options);
    }
}
