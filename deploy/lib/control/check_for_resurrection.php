<?php
// *** Run the resurrection script.
function check_for_resurrection($echo=FALSE) {
	DatabaseConnection::getInstance();

	$query = DatabaseConnection::$pdo->query("UPDATE players
                    SET status = 0,
                    health = (CASE WHEN _class_id = 4 THEN (150+(level*3)) ELSE 100 END)
                    WHERE active = 1
                    AND health < 0
                    AND resurrection_time = (SELECT amount from time where time_label='hours')
                    AND
                    ( days < 31 OR
        				(
        					((days % cast(floor(days / 10) AS integer)) = 0)
        				)
        			)
                  "); // *** Resurrect and heal all players at this countdown spot.

	if ($echo) {
		$healedPlayers = $query->rowCount();
		echo "Number of healed/resurrected players: ".$healedPlayers;
	}
}
