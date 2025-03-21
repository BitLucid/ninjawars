<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Event;
use NinjaWars\core\data\Communication;
use NinjaWars\core\data\Player;
use PDO;
use debug;

/**
 * Functions for use in the deities.
 */
class Deity
{
    public const VICIOUS_KILLER_STAT = 4; // the ID of the vicious killer stat
    public const MIDNIGHT_HEAL_SKILL = 'midnightheal'; // the identity of the midnight heal skill
    public const LEVEL_REGEN_INCREASE = false;
    public const STAMINA_REGEN_INCREASE = true;
    public const LEVEL_REVIVE_INCREASE = false;
    public const STAMINA_REVIVE_INCREASE = true;
    public const DEFAULT_REGEN = 1;
    public const BASE_HEALTH = NEW_PLAYER_INITIAL_HEALTH; // e.g. a new, unmodified character, maybe 100 or 150 for dragon

    public $logger;

    public function __construct(GameLog $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Increase Ki for the recently active
     * Add 1 to player's ki when they've been active in the last few minutes.
     */
    public function increaseKi()
    {
        DatabaseConnection::getInstance();
        $s = DatabaseConnection::$pdo->prepare("update players set ki = ki + :regen_rate where last_started_attack > (now() - :regen_timeout::interval)");
        $s->bindValue(':regen_timeout', KI_REGEN_TIMEOUT);
        $s->bindValue(':regen_rate', KI_REGEN_PER_TICK);
        $s->execute();
    }


    /**
     * Redo pc rankings
     *
     * @return int Number of ranked players
     */
    public function rerank()
    {
        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        DatabaseConnection::$pdo->query('TRUNCATE player_rank RESTART IDENTITY');
        DatabaseConnection::$pdo->query("SELECT setval('player_rank_rank_id_seq', 1, false)");
        $ranked_players = DatabaseConnection::$pdo->prepare('INSERT INTO player_rank (_player_id, score) SELECT player_id, ((level*:level_weight) + floor(gold/:gold_weight) + (CASE WHEN kills > (5*level) THEN 3000 + least(floor((kills - (5*level)) * .3), 2000) ELSE ((kills/(5*level))*3000) END) - (days*:inactivity_weight)) AS score FROM players WHERE active = 1 ORDER BY score DESC');
        $ranked_players->bindValue(':level_weight', RANK_WEIGHT_LEVEL);
        $ranked_players->bindValue(':gold_weight', RANK_WEIGHT_GOLD);
        $ranked_players->bindValue(':inactivity_weight', RANK_WEIGHT_INACTIVITY);
        $ranked_players->execute();
        DatabaseConnection::$pdo->query('COMMIT');
        return $ranked_players->rowCount();
    }

    /**
     * Zero any negative bounties
     */
    public function fixBounties()
    {
        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        DatabaseConnection::$pdo->query("UPDATE players SET bounty = 0 WHERE bounty < 0"); // Prevent negative bounties
        DatabaseConnection::$pdo->query('COMMIT');
    }

    /**
     * Increase turns by a tick
     */
    public function computeTurns()
    {
        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        DatabaseConnection::$pdo->query("UPDATE players SET turns = 0 WHERE turns < 0");
        // SPEED skill turn gain
        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns+1 FROM class_skill 
            JOIN skill ON skill_id = _skill_id 
            WHERE turns < :threshold AND _skill_id = 3 
                AND class_skill._class_id = players._class_id 
                AND level >= coalesce(class_skill_level, skill_level)");
        $s->bindValue(':threshold', TURN_REGEN_THRESHOLD);
        $s->execute();
        // Regular turn gain
        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns+:regen_rate WHERE turns < :threshold");
        $s->bindValue(':threshold', TURN_REGEN_THRESHOLD);
        $s->bindValue(':regen_rate', TURN_REGEN_PER_TICK);
        $s->execute();
        DatabaseConnection::$pdo->query('COMMIT');
    }

    /**
     * Update the game time counter
     */
    public function computeTime()
    {
        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query("UPDATE time SET amount = amount+1 WHERE time_label = 'hours'"); // Update the hours ticker.
        DatabaseConnection::$pdo->query("UPDATE time SET amount = 0 WHERE time_label = 'hours' AND amount >= 24"); // Rollover the time to hour zero.
    }

    /**
     * Unfreeze and unstealth characters
     */
    public function computeStatuses()
    {
        DatabaseConnection::getInstance();
        assert(FROZEN != 'FROZEN'); // These constants should be ints
        assert(STEALTH != 'STEALTH');
        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET status = status-:frozen WHERE CAST(status&:frozen2 AS bool)"); // Cold Steal Crit Fail Unfreeze
        $s->bindValue(':frozen', FROZEN);
        $s->bindValue(':frozen2', FROZEN);
        $s->execute();

        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET status = status-:stealth WHERE CAST(status&:stealth2 AS bool)"); //stealth lasts 1 hr
        $s->bindValue(':stealth', STEALTH);
        $s->bindValue(':stealth2', STEALTH);
        $s->execute();
    }

    /**
     * This actually toggles the "active" column on players, not the confirm column, and if they log in again, they're instantly active again.
     *
     * @return int|boolean
     */
    public static function unconfirmOlderPlayersOverMinimums($keep_players = 2300, $unconfirm_days_over = 90, $max_to_unconfirm = 30)
    {
        $minimum_days = 30;
        $max_to_unconfirm = (is_numeric($max_to_unconfirm) ? $max_to_unconfirm : 30);
        DatabaseConnection::getInstance();
        $sel_cur = DatabaseConnection::$pdo->query("SELECT count(player_id) FROM players WHERE active = 1");
        $current_players = $sel_cur->fetchColumn();

        if ($current_players < $keep_players) {
            // *** If we're under the minimum, don't inactivate anyone.
            return false;
        }

        // *** Don't unconfirm anyone below the minimum floor.
        $unconfirm_days_over = max($unconfirm_days_over, $minimum_days);

        // Unconfirm at a base of 20 players at a time.
        $unconfirm_within_limits = "UPDATE players
            SET active = :active
            WHERE players.player_id
            IN (
                SELECT player_id FROM players
                WHERE active = 1
                AND days > :age
                ORDER BY player_id DESC	LIMIT :max)";
        $update = DatabaseConnection::$pdo->prepare($unconfirm_within_limits);
        $update->bindValue(':active', 0);
        $update->bindValue(':age', intval($unconfirm_days_over));
        $update->bindValue(':max', $max_to_unconfirm);
        $update->execute();

        return $update->rowCount();
    }

    /**
     * Unnecessary with a created_date, but supported for legacy reasons
     *
     * @return int
     */
    public function updateDays()
    {
        DatabaseConnection::getInstance();
        $players = DatabaseConnection::$pdo->query("UPDATE players SET days = days+1");
        return $players->rowCount();
    }



    /**
     *
     * @return int
     */
    public function pcsUpdate()
    {
        DatabaseConnection::getInstance();
        self::updateDays();
        $status_removal = DatabaseConnection::$pdo->query("UPDATE players SET status = 0");
        // We may not want to wipe status nightly, some day
        return $status_removal->rowCount();
    }

    public function truncateMessages()
    {
        self::shortenChat();
        Event::deleteOldEvents();
    }

    /**
     * Mainly this is just game leaderboard stuff or who did what in the past day
     * In the future it could also handle weather, the game time, and other similar things
     */
    public function rearrangeWorldEnvironment()
    {
        $logger = $this->logger;
        if ($killer = $logger::findViciousKiller()) {
            $update = DatabaseConnection::$pdo->prepare('UPDATE past_stats SET stat_result = :viciousKiller WHERE id = :viciousKillerStat');
            $update->bindValue(':viciousKiller', $killer);
            $update->bindValue(':viciousKillerStat', self::VICIOUS_KILLER_STAT);
            $update->execute();
        }

        DatabaseConnection::$pdo->query("delete from levelling_log where killsdate < (now()- interval '6 months')");
        DatabaseConnection::$pdo->query("delete from dueling_log where date < (now()- interval '3 days')");
    }

    public function processUnconfirms()
    {
        //Nightly Unconfirm of aged players
        $unconfirmed = self::unconfirmOlderPlayersOverMinimums(MIN_PLAYERS_FOR_UNCONFIRM, MIN_DAYS_FOR_UNCONFIRM, MAX_PLAYERS_TO_UNCONFIRM);
        assert($unconfirmed < MAX_PLAYERS_TO_UNCONFIRM + 1);

        return ($unconfirmed === false ? 'Under the Minimum number of players' : $unconfirmed);
    }

    /**
     * Truncate the chat periodically
     *
     * @return int Number of chats removed
     */
    public function shortenChat()
    {
        return Communication::shortenChat();
    }

    /**
     * REGEN!
     * Take all characters, and heal 'em one step closer to their max
     * doesn't apply to dead characters or poisoned characters
     * Poisoned characters get a health decrease
     * Higher levels heal faster, and (may) heal to a higher cap
     * See the balance sheet:
        https://docs.google.com/spreadsheet/ccc?pli=1&key=0AkoUgtBBP00HdGs0Tmk4bC10TXN0SUJYXzdYMVpFZFE#gid=0
     *
     * @param int|null $basic      Per tick regen, usually about 3hp
     * @param bool     $with_extras whether regen increases stamina and other extras
     */
    public function regenCharacters($basic, $with_extras = true) // REGEN!
    {
        assert(POISON != 'POISON');
        $max_with_stamina = $with_extras ? ''.self::BASE_HEALTH.' +(players.stamina * '.Player::HEALTH_PER_STAMINA.')' : self::BASE_HEALTH;
        $add_with_stamina = $with_extras ? '+(players.stamina/50 * '.Player::HEALTH_PER_STAMINA.')' : '';
        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        $s = DatabaseConnection::$pdo->prepare(
            "UPDATE players SET health = numeric_smaller(
                (health+:basic ".$add_with_stamina."),
                ".$max_with_stamina."
                )
                WHERE active = 1 AND health BETWEEN 1 AND (".$max_with_stamina.") AND NOT cast(status&:poison AS bool) "
        );
        $s->bindValue(':basic', $basic, PDO::PARAM_INT);
        $s->bindValue(':poison', POISON);
        $s->execute();

        // Take damage every regen tick from POISON
        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET health = numeric_larger(0, health - :damage) WHERE health > 0 AND CAST((status&:poison) AS bool)"); // *** poisoned takes away life ***
        $s->bindValue(':damage', POISON_DAMAGE);
        $s->bindValue(':poison', POISON);
        $s->execute();

        DatabaseConnection::$pdo->query("UPDATE players SET health = 0 WHERE health < 0"); // *** zeros negative health totals.

        DatabaseConnection::$pdo->query('COMMIT');
    }

    /**
     * Get sums about all the active pcs, counts of dead/alive
     * @return array
     */
    private static function pcsActiveDeadAlive()
    {
        $pc_data = query_row(
            'select count(player_id) as active, 
                sum(case when health < 1 then 1 else 0 end) as dead 
                from players where active = 1'
        );
        $pc_data['alive'] = $pc_data['active'] - $pc_data['dead'];
        return $pc_data;
    }

    /**
     * Get the game environment hour
     * @return int
     */
    private static function gameHour()
    {
        return query_item('select amount from time where time_label = \'hours\'');
    }

    /**
     * Revive x characters
     *
     * Use the order by clause to determine who revives, by time, days and
     * then by level, using the limit set previously.
     *
     * Here is a runnable version:
     * select uname, player_id, level,floor(($major_revive_percent/100)*$total_active) days, resurrection_time from players where active = 1 AND health < 1 ORDER BY abs(8 - resurrection_time) asc, level desc, days asc
     * @return int
     */
    private static function performNecromancy($revive_amount, $game_hour)
    {
        $stamina_add = self::STAMINA_REVIVE_INCREASE ? '+(players.stamina * '.Player::HEALTH_PER_STAMINA.')' : '';
        // Note: This is limited to only active and dead players already
        $up_revive_players = 'UPDATE players 
            SET status = 0, 
            health =
                CASE WHEN level < coalesce(class_skill_level, skill_level)
                    THEN (:base_heal '.$stamina_add.')
                    ELSE (round(:base_heal_2 + (:base_heal_3 / 2)) '.$stamina_add.') 
                END
            FROM (SELECT skill_id, skill_level, skill_is_active, skill_display_name, skill_internal_name, skill_type, _class_id, class_skill_level
                FROM skill LEFT JOIN class_skill ON skill_id = _skill_id 
                WHERE skill_internal_name = :midnightHeal and skill_is_active
                ) AS class_skill 
            WHERE player_id IN (
                SELECT player_id FROM players 
                WHERE active = 1 AND health < 1 
                ORDER BY abs(:time - resurrection_time) ASC, level DESC, days ASC 
                LIMIT :amount
            ) 
            AND coalesce(class_skill._class_id, players._class_id) = players._class_id';
        // Sadly the midnight heal skill makes this much more complicated than it would otherwise need to be
        DatabaseConnection::getInstance();
        $update = DatabaseConnection::$pdo->prepare($up_revive_players);
        $update->bindValue(':amount', $revive_amount, PDO::PARAM_INT);
        $update->bindValue(':time', $game_hour, PDO::PARAM_INT);
        $update->bindValue(':midnightHeal', self::MIDNIGHT_HEAL_SKILL, PDO::PARAM_STR);
        $update->bindValue(':base_heal', self::BASE_HEALTH);
        $update->bindValue(':base_heal_2', self::BASE_HEALTH);
        $update->bindValue(':base_heal_3', self::BASE_HEALTH);
        $update->execute();

        return $update->rowCount(); // Return the amount revived
    }

    /**
     * Revive up to a small max in minor hours, and a stable percent on major hours.
     *
     * Generally the objective is to cause a minor-minor-major pattern:
     * Revive to a fixed minimum number of alive every time (minor or major),
     * and revive beyond the minimum, by percent, every major time
     * to slowly creep towards 100% by healing a percent of whoever is dead
     * For example, if 1,000 out of 1,000 are dead:
     * Minor revive heals to the minimum of 100
     * Minor revive heals none (100 are already alive)
     * Major revive heals 50 from those that are dead (number 5% of all actives)
     * minor revive heals 0 (because at least 100 are already alive)
     * minor revive heals 0 (because at least 100 are already alive)
     * Major revive heals another 50 from those that are dead (if any)
     * etc
     *
     * Defaults
     * sample_use: revive_players(30, 3));
     * @param array('minor_revive_to'=>100, 'major_revive_percent'=>5)
     * @return mixed
     */
    public function revivePlayers($set = [])
    {
        $minor_revive_to      = (isset($set['minor_revive_to']) ? $set['minor_revive_to'] : 100);
        $major_revive_percent = (isset($set['major_revive_percent']) ? $set['major_revive_percent'] : 5);
        $major_hour           = 3; // Hour for the major revive.

        $pc_data = self::pcsActiveDeadAlive();
        assert($pc_data['active'] > 0);

        // If none dead, return false.
        if ($pc_data['dead'] < 1) {
            return [0, 0];
        }

        $game_hour = self::gameHour();
        assert(is_numeric($game_hour));

        // Calculate the revive amount
        if (!(($game_hour % $major_hour) == 0)) { // minor
            if ($pc_data['alive'] >= $minor_revive_to) {
                return [0, $pc_data['dead']]; // No revives called for yet!
            } else {  // else revive minor_revive_to - total_alive.
                $revive_amount = (int) floor($minor_revive_to - $pc_data['alive']);
            }
        } else { // major.
            $percent_int = (int) floor(($major_revive_percent / 100) * $pc_data['active']);
            // Use the max of either pcs dead, or revives requested
            $revive_amount = min($percent_int, $pc_data['dead']);
        }

        assert(isset($revive_amount));
        $revived = self::performNecromancy($revive_amount, $game_hour);
        return [$revived, $pc_data['dead']];
    }
}
