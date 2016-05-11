<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;

/**
 * Holds game-specific logging functionality
 */
class GameLog {

    /**
     * Record to the game log
     *
     * @param string $log_message
     * @param int $priority Simple priority level, higher is more important
     */
    public static function log($log_message, $priority=0){
        $priority = (int) $priority; // Prevent non-int priority levels
        $log = fopen(LOGS.'game.log', 'a');
        fwrite($log, ($priority>0? "[PRIORITY ".$priority."]" : '').$log_message);
        fclose($log);
    }

    /**
     * Records kills/xp to the levelling_log
     */
    public static function recordLevelUp($who) {
        $amount = 1;

        DatabaseConnection::getInstance();

        // *** UPDATE THE LEVEL INCREASE LOG *** //
        $statement = DatabaseConnection::$pdo->prepare("SELECT * FROM levelling_log WHERE _player_id = :player AND killsdate = now()");
        $statement->bindValue(':player', $who);
        $statement->execute();
        $notYetANewDay = $statement->fetch();  //Throws back a row result if there is a pre-existing record.

        if ($notYetANewDay != NULL) {
            //if record already exists.
            $statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET levelling=levelling + :amount WHERE _player_id = :player AND killsdate=now() LIMIT 1");
            $statement->bindValue(':amount', $amount);
            $statement->bindValue(':player', $who);
        } else {	// if no prior record exists, create a new one.
            $statement = DatabaseConnection::$pdo->prepare("INSERT INTO levelling_log (_player_id, killpoints, levelling, killsdate) VALUES (:player, '0', :amount, now())");  //inserts all except the autoincrement ones
            $statement->bindValue(':amount', $amount);
            $statement->bindValue(':player', $who);
        }

        $statement->execute();
    }

    /**
     * Update the levelling log with the increased kills.
     * 
     * TODO: This should become deprecated once kills only increase,
     * though right now resurrects still cost a single kill sometimes.
     */
    public static function updateLevellingLog($who, $amount) {
        DatabaseConnection::getInstance();

        $amount = (int)$amount;

        if ($amount == 0) {
            return;
        } else if ($amount > 0) {
            $record_check = '>';
        } else {
            $record_check = '<';
        }

        // *** UPDATE THE KILLS LOG ***
        $statement = DatabaseConnection::$pdo->prepare(
            "SELECT * FROM levelling_log WHERE _player_id = :player AND killsdate = now() AND killpoints $record_check 0 LIMIT 1");
        //Check for an existing record of either negative or positive types.
        $statement->bindValue(':player', $who);
        $statement->execute();

        $notYetANewDay = $statement->fetch();  //positive if todays record already exists
        if ($notYetANewDay != NULL) {
            // If an entry already exists, update it.
            $statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET killpoints = killpoints + :amount WHERE _player_id = :player AND killsdate = now() AND killpoints $record_check 0");  //increase killpoints
        } else {
            $statement = DatabaseConnection::$pdo->prepare(
                "INSERT INTO levelling_log (_player_id, killpoints, levelling, killsdate) VALUES (:player, :amount, '0', now())");
            //create a new record for today
        }

        $statement->bindValue(':amount', $amount);
        $statement->bindValue(':player', $who);
        $statement->execute();
    }

    /**
     * Record the kills/xp results of a duel attack by a pc
     */
    public static function sendLogOfDuel($attacker, $defender, $won, $killpoints) {
        $killpoints = (int)$killpoints;

        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("INSERT INTO dueling_log values (default, :attacker, :defender, :won, :killpoints, now())");

        //Log of Dueling information.
        $statement->bindValue(':attacker', $attacker);
        $statement->bindValue(':defender', $defender);
        $statement->bindValue(':won', $won, \PDO::PARAM_BOOL);
        $statement->bindValue(':killpoints', $killpoints);
        $statement->execute();
    }

    /**
     * Find the player who killed the most today
     *
     * @return string The name of the top active player
     */
    public static function findViciousKiller() {
        $result = DatabaseConnection::$pdo->query('SELECT uname FROM levelling_log JOIN players ON player_id = _player_id WHERE killsdate = cast(now() AS date) GROUP BY uname, killpoints ORDER BY killpoints DESC LIMIT 1');
        return $result->fetchColumn();
    }

    /**
     * Update the information of a viewing observer, or player.
     */
    public static function updateActivityInfo($request, $session) {
        // ******************** Usage Information of the browser *********************
        $remoteAddress = ''.$request->getClientIp();
        $userAgent     = (isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 250) : NULL); // Truncated at 250 char.
        $referer       = (isset($_SERVER['HTTP_REFERER'])    ? substr($_SERVER['HTTP_REFERER'], 0, 250)    : '');   // Truncated at 250 char.

        // ************** Setting anonymous and player usage information

        DatabaseConnection::getInstance();

        if (!$session->has('online')) {	// *** Completely new session, update latest activity log. ***
            if ($remoteAddress) {	// *** Delete prior to trying to re-insert into the people online. ***
                $statement = DatabaseConnection::$pdo->prepare('DELETE FROM ppl_online WHERE ip_address = :ip OR session_id = :sessionID');

                $statement->bindValue(':ip',        $remoteAddress);
                $statement->bindValue(':sessionID', $session->getId());

                $statement->execute();
            }

            // *** Update viewer data. ***
            $statement = DatabaseConnection::$pdo->prepare('INSERT INTO ppl_online (session_id, activity, ip_address, refurl, user_agent) VALUES (:sessionID, now(), :ip, :referer, :userAgent)');

            $statement->bindValue(':sessionID', $session->getId());
            $statement->bindValue(':ip',        $remoteAddress);
            $statement->bindValue(':referer',   $referer);
            $statement->bindValue(':userAgent', $userAgent);

            $statement->execute();

            $session->set('online', true);
        } else {	// *** An already existing session. ***
            $statement = DatabaseConnection::$pdo->prepare('UPDATE ppl_online SET activity = now(), member = :member WHERE session_id = :sessionID');
            $statement->bindValue(':sessionID', $session->getId());
            $statement->bindValue(':member', $session->get('authenticated', false), \PDO::PARAM_BOOL);
            $statement->execute();
        }
    }

}
