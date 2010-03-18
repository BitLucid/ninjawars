<?php
// *** Run the resurrection sql.
function check_for_resurrection($echo=FALSE) {
	DatabaseConnection::getInstance();

	$query = DatabaseConnection::$pdo->query("UPDATE players
                    SET status = 0,
                    health = (CASE WHEN class='White' THEN (150+(level*3)) ELSE 100 END)
                    WHERE confirmed = 1
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
?>
