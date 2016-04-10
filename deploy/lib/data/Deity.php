<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Event;
use NinjaWars\core\data\Message;
use NinjaWars\core\data\GameLog;

/**
 * Functions for use in the deities.
 */
class Deity {
    const VICIOUS_KILLER_STAT = 4; // the ID of the vicious killer stat
    const MIDNIGHT_HEAL_SKILL = 5; // the ID of the midnight heal skill

    /**
     * @param int $minutes Minute interval of tick.
     */
    public static function tick($minutes){
        switch($minutes){
            case 5:
                self::tiny();
            break;
            case 30:
                self::minor();
            break;
            case 60:
                self::major();
            break;
            case 1440:
                self::daily();
            break;
        }
    }

    private static function tiny(){
        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        DatabaseConnection::$pdo->query('TRUNCATE player_rank RESTART IDENTITY');
        DatabaseConnection::$pdo->query("SELECT setval('player_rank_rank_id_seq', 1, false)");

        $ranked_players = DatabaseConnection::$pdo->prepare('INSERT INTO player_rank (_player_id, score) SELECT player_id, ((level*:level_weight) + floor(gold/:gold_weight) + (CASE WHEN kills > (5*level) THEN 3000 + least(floor((kills - (5*level)) * .3), 2000) ELSE ((kills/(5*level))*3000) END) - (days*:inactivity_weight)) AS score FROM players WHERE active = 1 ORDER BY score DESC');
        $ranked_players->bindValue(':level_weight', RANK_WEIGHT_LEVEL);
        $ranked_players->bindValue(':gold_weight', RANK_WEIGHT_GOLD);
        $ranked_players->bindValue(':inactivity_weight', RANK_WEIGHT_INACTIVITY);
        $ranked_players->execute();

        // *** Running from a cron script, we don't want any output unless we have an error ***

        // Add 1 to player's ki when they've been active in the last 5 minutes.
        $s = DatabaseConnection::$pdo->prepare("update players set ki = ki + :regen_rate where last_started_attack > (now() - :interval::interval)");
        $s->bindValue(':interval', KI_REGEN_TIMEOUT);
        $s->bindValue(':regen_rate', KI_REGEN_PER_TICK);
        $s->execute();

        DatabaseConnection::$pdo->query('COMMIT');

        // Err on the side of low revives for this five minute tick.
        $params = [
            'minor_revive_to'      => MINOR_REVIVE_THRESHOLD,
            'major_revive_percent' => MAJOR_REVIVE_PERCENT,
        ];

        list($revived, $dead_count) = self::revivePlayers($params);

        $rand = rand(1, DEITY_LOG_CHANCE_DIVISOR);

        if (DEBUG || $rand === 1) {
            $out_display = [];
            // Only log fiveminute log output randomly about once every 6 hours to cut down on
            // spam in the log.  This log message isn't very important anyway.

            $out_display['Ranked Players'] = $ranked_players->rowCount();
            $out_display['Players who are/were dead'] = $dead_count;
            $out_display['Players Revived'] = $revived;

            // ***********
            // Log output:

            $logMessage = 'DEITY_FIVEMINUTE STARTING: '.date(DATE_RFC1036)."\n";

            foreach ($out_display AS $loopKey => $loopRowResult) {
                $logMessage .= "DEITY_FIVEMINUTE: Result type: ".$loopKey." yeilded result number: ".$loopRowResult." \n";
            }

            $logMessage .= 'DEITY_FIVEMINUTE ENDING: '.date(DATE_RFC1036)."\n";

            self::logStuff($logMessage);
        }
    }

    private static function minor(){
        $out_display = [];
        $logMessage = "DEITY_HALFHOUR STARTING: ".date(DATE_RFC1036)."\n";

        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        DatabaseConnection::$pdo->query("UPDATE players SET turns = 0 WHERE turns < 0"); // if anyone has less than 0 turns, set it to 0

        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns+:rate WHERE turns < :threshold");  // add turns at the regen rate for anyone below the threshold
        $s->bindValue(':rate', TURN_REGEN_PER_TICK);
        $s->bindValue(':threshold', TURN_REGEN_THRESHOLD);
        $s->execute();

        DatabaseConnection::$pdo->query("UPDATE players SET bounty = 0 WHERE bounty < 0"); // if anyone has negative bounty, set it to 0

        $inactivity = DatabaseConnection::$pdo->prepare("DELETE FROM ppl_online WHERE activity < (now() - :maxtime::interval)");
        $inactivity->bindValue(':maxtime', ONLINE_TIMEOUT);
        $inactivity->execute();

        $out_display['Inactive Browsers Deactivated'] = $inactivity->rowCount();

        // *** HEAL Characters a certain amount ***

        self::healCharacters(); // Just use the defaults, function does not return anything at the moment.

        // **************
        // Visual output:

        foreach ($out_display AS $loopKey => $loopRowResult) {
            $logMessage .= "DEITY_HALFHOUR: Result type: $loopKey yeilded result number: $loopRowResult\n";
        }

        $logMessage .= "DEITY_HALFHOUR ENDING: ".date(DATE_RFC1036)."\n";

        self::logStuff($logMessage);
    }

    private static function major(){
        $out_display = [];
        $logMessage = "DEITY_HOURLY STARTING: ".date(DATE_RFC1036)."\n";

        // Note that this script should not be web-accessible.
        DatabaseConnection::getInstance();

        // ******************* END OF CONSTANTS ***********************

        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        DatabaseConnection::$pdo->query("UPDATE time SET amount = amount+1 WHERE time_label = 'hours'"); // Update the hours ticker.
        DatabaseConnection::$pdo->query("UPDATE time SET amount = 0 WHERE time_label = 'hours' AND amount >= 24"); // Rollover the time to hour zero.
        DatabaseConnection::$pdo->query("UPDATE players SET turns = 0 WHERE turns < 0");
        DatabaseConnection::$pdo->query("UPDATE players SET bounty = 0 WHERE bounty < 0");
        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns+1 FROM class_skill JOIN skill ON skill_id = _skill_id WHERE turns < :threshold AND _skill_id = 3 AND class_skill._class_id = players._class_id AND level >= coalesce(class_skill_level, skill_level)");    // *** Speed skill turn gain code, replaces Blue/Crane turn gain code ***
        $s->bindValue(':threshold', TURN_REGEN_THRESHOLD);
        $s->execute();

        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns+:regen_rate WHERE turns < :threshold"); // add 2 turns on the hour, up to 100.
        $s->bindValue(':threshold', TURN_REGEN_THRESHOLD);
        $s->bindValue(':regen_rate', TURN_REGEN_PER_TICK);
        $s->execute();

        // *** HEAL ***
        $s = DatabaseConnection::$pdo->prepare(
            "UPDATE players SET health = numeric_smaller(health+:regen_rate, :max_heal) ".
            "WHERE health BETWEEN 1 AND :max_heal2 AND NOT ".
            "CAST(status&:poison AS boolean)"
        );
        $s->bindValue(':max_heal', HEALTH_REGEN_THRESHOLD);
        $s->bindValue(':max_heal2', HEALTH_REGEN_THRESHOLD);
        $s->bindValue(':regen_rate', HEALTH_REGEN_PER_TICK);
        $s->bindValue(':poison', POISON);
        $s->execute();

        assert(POISON != 'POISON');
        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET health = numeric_larger(0, health-:damage) WHERE health > 0 AND CAST((status&:poison) AS bool)"); // *** poisoned takes away life ***
        $s->bindValue(':damage', POISON_DAMAGE);
        $s->bindValue(':poison', POISON);
        $s->execute();

        DatabaseConnection::$pdo->query("UPDATE players SET health = 0 WHERE health < 0"); // *** zeros negative health totals.

        assert(FROZEN != 'FROZEN'); // These constants should be numeric.
        assert(STEALTH != 'STEALTH');
        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET status = status-:frozen WHERE CAST(status&:frozen2 AS bool)"); // Cold Steal Crit Fail Unfreeze
        $s->bindValue(':frozen', FROZEN);
        $s->bindValue(':frozen2', FROZEN);
        $s->execute();

        $s = DatabaseConnection::$pdo->prepare("UPDATE players SET status = status-:stealth WHERE CAST(status&:stealth2 AS bool)"); //stealth lasts 1 hr
        $s->bindValue(':stealth', STEALTH);
        $s->bindValue(':stealth2', STEALTH);
        $s->execute();
        DatabaseConnection::$pdo->query('COMMIT');

        // **************
        // Visual output:

        foreach ($out_display AS $loopKey => $loopRowResult) {
            $logMessage .= "DEITY_HOURLY: Result type: $loopKey yeilded result: $loopRowResult\n";
        }

        $logMessage .= "DEITY_HOURLY ENDING: ".date(DATE_RFC1036)."\n";

        self::logStuff($logMessage);
    }

    private static function daily(){
        $logMessage = "DEITY_NIGHTLY STARTING: ---- ".date(DATE_RFC1036)." ----\n";

        // TODO: Profile the slowdown point(s) of this script.
        // TODO: Need a levelling log deletion.

        $keep_players_until_over_the_number                   = MIN_PLAYERS_FOR_UNCONFIRM;
        $days_players_have_to_be_older_than_to_be_unconfirmed = MIN_DAYS_FOR_UNCONFIRM;
        $maximum_players_to_unconfirm                         = MAX_PLAYERS_TO_UNCONFIRM;

        // *************** DEITY NIGHTLY, manual-run-output occurs at the bottom.*********************

        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        $affected_rows = [];
        $affected_rows['Increase Days Of Players'] = self::updateDays();

        $status_removal = DatabaseConnection::$pdo->query("UPDATE players SET status = 0");  // Hmmm, gets rid of all status effects, we may want to make that not have that limitation, some day.
        $affected_rows['Statuses Removed'] = $status_removal->rowCount();

        $deleted = Message::shortenChat();
        $affected_rows['deleted chats'] = $deleted;

        if ($killer = GameLog::findViciousKiller()) {
            $update = DatabaseConnection::$pdo->prepare('UPDATE past_stats SET stat_result = :viciousKiller WHERE id = :viciousKillerStat');
            $update->bindValue(':viciousKiller', $killer);
            $update->bindValue(':viciousKillerStat', self::VICIOUS_KILLER_STAT);
            $update->execute();
    }

    //Nightly Unconfirm old players script settings.
    $unconfirmed = self::unconfirmOlderPlayersOverMinimums($keep_players_until_over_the_number, $days_players_have_to_be_older_than_to_be_unconfirmed, $maximum_players_to_unconfirm, $just_testing=false);
    assert($unconfirmed < $maximum_players_to_unconfirm+1);

    $affected_rows['Players Unconfirmed'] = ($unconfirmed === false ? 'Under the Minimum number of players' : $unconfirmed);

    // Delete from inventory where owner is unconfirmed or non-existent.
    $deleted_items = DatabaseConnection::$pdo->query("DELETE FROM inventory WHERE owner IN (SELECT owner FROM inventory LEFT JOIN players ON owner = player_id WHERE uname IS NULL GROUP BY owner)");
    $affected_rows['deleted items'] = $deleted_items->rowCount();

    $deleted_items = DatabaseConnection::$pdo->query("delete from levelling_log where killsdate < (now() - interval '2 months')");
    $affected_rows['deleted levelling_logs'] = $deleted_items->rowCount();

    $deleted_events = Event::deleteOldEvents();
    $affected_rows['Old Events Deletion'] = $deleted_events;

    $level_log_delete = DatabaseConnection::$pdo->query("delete from levelling_log where killsdate < now()- interval '3 months'");
    $affected_rows['levelling log deletion'] = $level_log_delete->rowCount(); // Keep only the last 3 months of logs.

    $duel_log_delete = DatabaseConnection::$pdo->query("delete from dueling_log where date != cast(now() AS date) AND date != cast(now() AS date)-1"); // Keep only the last two days of duels.
    $affected_rows['dueling log deletion'] = $duel_log_delete->rowCount();

    $level_1_delete = DatabaseConnection::$pdo->query("delete from players where active = 0 and level = 1 and created_date < (now() - interval '5 days')"); // Delete old level 1's.
    DatabaseConnection::$pdo->query('COMMIT');
    $affected_rows['old level 1 players deletion'] = $level_1_delete->rowCount();


    $logMessage .= "DEITY_NIGHTLY: Deity reset occurred at server date/time: ".date('l jS \of F Y h:i:s A').".\n";
    $logMessage .= 'DEITY_NIGHTLY: Mail deleted: ('.$affected_rows['Old Messages Deletion'].")\n";
    $logMessage .= "DEITY_NIGHTLY: Items: ".$affected_rows['deleted items']."\n";
    $logMessage .= 'DEITY_NIGHTLY: Players unconfirmed: ('.$unconfirmed.").  30 is the current default maximum.\n";
    $logMessage .= "DEITY_NIGHTLY: Chats deleted (if a deletion value is returned): $deleted\n";

    // **************
    // Visual output:

    foreach ($affected_rows AS $loopKey => $loopRowResult) {
        $logMessage .= "DEITY_NIGHTLY: Result type: $loopKey yeilded result: $loopRowResult\n";
    }

    $logMessage .= "DEITY_NIGHTLY ENDING: ---- ".date(DATE_RFC1036)." ---- \n";

    self::logStuff($logMessage);

    }

    private static function logStuff($logMessage){
        $log = fopen(LOGS.'deity.log', 'a');
        fwrite($log, $logMessage);
        fclose($log);
    }

    /**
     * This actually toggles the "active" column on players, not the confirm column, and if they log in again, they're instantly active again.
     */
    private static function unconfirmOlderPlayersOverMinimums($keep_players=2300, $unconfirm_days_over=90, $max_to_unconfirm=30, $just_testing=true) {
        $change_confirm_to = ($just_testing ? '1' : '0'); // Only unconfirm players when not testing.
        $minimum_days = 30;
        $max_to_unconfirm = (is_numeric($max_to_unconfirm) ? $max_to_unconfirm : 30);
        DatabaseConnection::getInstance();
        $sel_cur = DatabaseConnection::$pdo->query("SELECT count(*) FROM players WHERE active = 1");
        $current_players = $sel_cur->fetchColumn();

        if ($current_players < $keep_players) {
            // *** If we're under the minimum, don't inactivate anyone.
            return false;
    }

    // *** Don't unconfirm anyone below the minimum floor.
    $unconfirm_days_over = max($unconfirm_days_over, $minimum_days);

    // Unconfirm at a maximum of 20 players at a time.
    $unconfirm_within_limits = "UPDATE players
        SET active = :active
        WHERE players.player_id
        IN (
            SELECT player_id FROM players
            WHERE active = 1
            AND days > :age
            ORDER BY player_id DESC	LIMIT :max)";
$update = DatabaseConnection::$pdo->prepare($unconfirm_within_limits);
$update->bindValue(':active', $change_confirm_to);
$update->bindValue(':age', intval($unconfirm_days_over));
$update->bindValue(':max', $max_to_unconfirm);
$update->execute();

return $update->rowCount();
    }
    /**
     */
    private static function updateDays() {
        DatabaseConnection::getInstance();
        $players = DatabaseConnection::$pdo->query("UPDATE players SET days = days+1");
        return $players->rowCount();
    }

    /**
     * Take all characters, and heal them one step closer to their maximum base.
     */
    private static function healCharacters($basic=8, $with_level=true){
        $maximum_heal = Player::maxHealthByLevel(3);
    /*
    Goal:  Faster regen for higher level.
    See the balance sheet:
    https://docs.google.com/spreadsheet/ccc?pli=1&key=0AkoUgtBBP00HdGs0Tmk4bC10TXN0SUJYXzdYMVpFZFE#gid=0
     */
        $max_hp = Player::maxHealthByLevel(MAX_PLAYER_LEVEL);


        $level_add = '+ cast(floor(level/10) AS int)';
        if(!$with_level){
            $level_add = '';
        }
        // Take level, divide by 10, throw away remainder, and add ten for every whole tenth level.
        // e.g. 99 / 10 = 9.9 floored = 9 * 10 = 90
        $level_limit = 'cast((floor(level /10) * 10) AS int)';
        // Add an amount
        $s = DatabaseConnection::$pdo->prepare(
            "UPDATE players SET health = numeric_smaller(
                (health+:basic ".$level_add."),
        cast((:max_heal + ".$level_limit.") AS int))
        WHERE health > 0 AND NOT cast(status&:poison AS bool) AND health < (:max_heal2 + ".$level_limit.")");
        // Heal a character that is alive, and isn't at their level max yet.
        $s->bindValue(':basic', $basic);
        $s->bindValue(':max_heal', $maximum_heal);
        $s->bindValue(':max_heal2', $maximum_heal);
        $s->bindValue(':poison', POISON);
        $s->execute();
        DatabaseConnection::$pdo->query('COMMIT');
        // Higher levels now heal faster.
        // Higher levels should now also heal to a larger maximum, level dependent.
        // e.g. level 100 gets +100 in how many hitpoints they'll heal up to,
        // level 99 gets +90 in how many hitpoints they'll heal up to.
    }

    /**
     * Revive up to a small max in minor hours, and a stable percent on major hours.
     * Defaults
     * sample_use: revive_players(array('just_testing'=>true));
     * @param array('minor_revive_to'=>100, 'major_revive_percent'=>5,
     *      'just_testing'=>false)
     */
    private static function revivePlayers($params=array()) {
        // Previous min/max was 2-4% always, ~3000 players, so 60-120 each time.

        $minor_revive_to      = (isset($params['minor_revive_to']) ? $params['minor_revive_to'] : 100); // minor_revive_to, default 100
        $major_revive_percent = (isset($params['major_revive_percent']) ? $params['major_revive_percent'] : 5); // major_revive_percent, default 5%
        $just_testing         = isset($params['just_testing']);
        $major_hour           = 3; // Hour for the major revive.

        /*
         * General idea should be:
         * 1: revive to 100
         * 2: revive to 100 (probably 0)
         * 3: revive 150, (250 total) to a max of 80% of total, ~2500.
         * 4: revive to 100 (almost certainly no more)
         * 5: revive to 100 (almost certainly no more)
         * 6: revive 150, (400 total) to a max of 80% of total, ~2500
         * 7: ...etc.
         */

        // Determine the total dead (& active).
        $sel_dead = DatabaseConnection::$pdo->query('SELECT count(*) FROM players WHERE health < 1 AND active = 1');
        $dead_count = $sel_dead->fetchColumn();

        // If none dead, return false.
        if (!$dead_count) {
            return array(0, 0);
        }

        // Determine the total active.
        $sel_total_active = DatabaseConnection::$pdo->query('SELECT count(*) FROM players WHERE active = 1');
        $total_active = $sel_total_active->fetchColumn();

        // Calc the total alive.
        $total_alive = ($total_active - $dead_count);

        // Determine major or minor based on the hour.
        $sel_current_time = DatabaseConnection::$pdo->query("SELECT amount FROM time WHERE time_label = 'hours'");
        $current_time = $sel_current_time->fetchColumn();
        assert(is_numeric($current_time));

        $major = (($current_time % $major_hour) == 0);

        // If minor, and total_alive is more than minor_revive_to-1, return 0/total.
        if (!$major) { // minor
            if ($total_alive > ($minor_revive_to-1)) { // minor_revive_to already met.
                return array(0, $dead_count);
            } else {  // else revive minor_revive_to - total_alive.
                $revive_amount = floor($minor_revive_to - $total_alive);
            }
        } else { // major.
            $percent_int = floor(($major_revive_percent/100)*$total_active);

            if ($dead_count < $percent_int) {
                // If major, and total_dead is less than target_num (major_revive_percent*total, floored)
                // just revive those that are dead.
                $revive_amount = $dead_count;
            } else {
                // Else revive target_num (major_revive_percent*total, floored)
                $revive_amount = $percent_int;
            }
        }

        assert(isset($revive_amount));
        assert(isset($current_time));
        assert(isset($just_testing));
        assert(isset($dead_count));
        assert(isset($major));
        // Actually perform the revive on those integers.
        // Use the order by clause to determine who revives, by time, days and then by level, using the limit set previously.
        //select uname, player_id, level,floor(($major_revive_percent/100)*$total_active) days, resurrection_time from players where active = 1 AND health < 1 ORDER BY abs(8 - resurrection_time) asc, level desc, days asc
        $select = 'SELECT player_id FROM players WHERE active = 1 AND health < 1 '.
            ' ORDER BY abs(:time - resurrection_time) ASC, level DESC, days ASC LIMIT :amount';

        $up_revive_players= 'UPDATE players SET status = 0 ';

        if (!$just_testing) {
            $up_revive_players .= ', health =
                CASE WHEN level >= coalesce(class_skill_level, skill_level)
                    THEN (150+(level*3))
                    ELSE (100+(level*3)) END
                        FROM (SELECT * FROM skill LEFT JOIN class_skill ON skill_id = _skill_id WHERE skill_id = :midnightHeal)
                        AS class_skill ';
        }

        $up_revive_players .= ' WHERE player_id IN ('.$select.') ';

        if (!$just_testing) {
            $up_revive_players .= ' AND coalesce(class_skill._class_id, players._class_id) = players._class_id';
        }

        $update = DatabaseConnection::$pdo->prepare($up_revive_players);
        $update->bindValue(':amount', intval($revive_amount));
        $update->bindValue(':time', intval($current_time));
        $update->bindValue(':midnightHeal', self::MIDNIGHT_HEAL_SKILL);
        $update->execute();
        $truly_revived = $update->rowCount();

        // Return the 'revived/total' actually revived.
        return array($truly_revived, $dead_count);
    }
}
