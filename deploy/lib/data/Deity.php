<?php
namespace NinjaWars\core\data;

require_once(LIB_ROOT."control/lib_deity.php");

use NinjaWars\core\data\DatabaseConnection;

class Deity{

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

        list($revived, $dead_count) = revive_players($params);

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

        heal_characters(); // Just use the defaults, function does not return anything at the moment.

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

        $out_display = array();

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

        //Skip error logging this for now. $out_display['Inactive Browsers Deactivated'] = $inactivity->rowCount();

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
        $out_display = [];
        $logMessage = "DEITY_NIGHTLY STARTING: ---- ".date(DATE_RFC1036)." ----\n";

        // TODO: Profile the slowdown point(s) of this script.
        // TODO: Need a levelling log deletion.
        // TODO: When the message table is created, delete from mail more stringently.
        // TODO: Set up a backup of the players table.

        $keep_players_until_over_the_number                   = MIN_PLAYERS_FOR_UNCONFIRM;
        $days_players_have_to_be_older_than_to_be_unconfirmed = MIN_DAYS_FOR_UNCONFIRM;
        $maximum_players_to_unconfirm                         = MAX_PLAYERS_TO_UNCONFIRM;

        // *************** DEITY NIGHTLY, manual-run-output occurs at the bottom.*********************

        DatabaseConnection::getInstance();
        DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
        $affected_rows['Increase Days Of Players'] = update_days();

        //DatabaseConnection::$pdo->query("UPDATE players SET status = status-".POISON." WHERE status&".POISON);  // Black Poison Fix
        $status_removal = DatabaseConnection::$pdo->query("UPDATE players SET status = 0");  // Hmmm, gets rid of all status effects, we may want to make that not have that limitation, some day.
        $affected_rows['Statuses Removed'] = $status_removal->rowCount();

        $deleted = shorten_chat(); // run the shortening of the chat.
        $affected_rows['deleted chats'] = $deleted;

        update_most_vicious_killer_stat();// Update the vicious killer stat.

        //Nightly Unconfirm old players script settings.
        $unconfirmed = unconfirm_older_players_over_minimums($keep_players_until_over_the_number, $days_players_have_to_be_older_than_to_be_unconfirmed, $maximum_players_to_unconfirm, $just_testing=false);
        assert($unconfirmed < $maximum_players_to_unconfirm+1);

        $affected_rows['Players Unconfirmed'] = ($unconfirmed === false ? 'Under the Minimum number of players' : $unconfirmed);

        // Delete from inventory where owner is unconfirmed or non-existent.
        $deleted_items = DatabaseConnection::$pdo->query("DELETE FROM inventory WHERE owner IN (SELECT owner FROM inventory LEFT JOIN players ON owner = player_id WHERE uname IS NULL GROUP BY owner)");
        $affected_rows['deleted items'] = $deleted_items->rowCount();

        $deleted_items = DatabaseConnection::$pdo->query("delete from levelling_log where killsdate < (now() - interval '2 months')");
        $affected_rows['deleted levelling_logs'] = $deleted_items->rowCount();

        $deleted_mail = delete_old_messages(); // As per the mail function in lib_deity.
        $deleted_events = delete_old_events();
        $affected_rows['Old Messages Deletion'] = $deleted_mail;

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

}