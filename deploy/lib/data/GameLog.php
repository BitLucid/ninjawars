<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;

/**
 * Holds game-specific logging functionality
 */
class GameLog {
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
     */
    public static function updateLevellingLog($who, $amount) {
        // TODO: This should be deprecated once we have only upwards kills_total increases, but for now I'm just refactoring.
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

    public static function sendLogOfDuel($attacker, $defender, $won, $killpoints) {
        $killpoints = (int)$killpoints;

        DatabaseConnection::getInstance();
        $statement = DatabaseConnection::$pdo->prepare("INSERT INTO dueling_log values (default, :attacker, :defender, :won, :killpoints, now())");

        //Log of Dueling information.
        $statement->bindValue(':attacker', $attacker);
        $statement->bindValue(':defender', $defender);
        $statement->bindValue(':won', $won);
        $statement->bindValue(':killpoints', $killpoints);
        $statement->execute();
    }

    /**
     * Find the player who killed the most today
     */
    public static function findViciousKiller() {
        $result = DatabaseConnection::$pdo->query('SELECT uname FROM levelling_log JOIN players ON player_id = _player_id WHERE killsdate = cast(now() AS date) GROUP BY uname, killpoints ORDER BY killpoints DESC LIMIT 1');
        return $result->fetchColumn();
    }
}
