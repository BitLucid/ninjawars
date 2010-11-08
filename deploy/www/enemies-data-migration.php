<?php
$playerID = $enemyID = null;

DatabaseConnection::getInstance();

$statement = DatabaseConnection::$pdo->query('SELECT player_id FROM players');

$insertStatement =  DatabaseConnection::$pdo->prepare('INSERT INTO enemies (_player_id, _enemy_id) VALUES (:pid, :eid)');
$insertStatement->bindParam(':pid', $playerID);
$insertStatement->bindParam(':eid', $enemyID);

foreach ($statement AS $row)
{
	$playerID = $row['player_id'];
	$enemy_list = _get_setting($playerID, 'enemy_list', true);

	if (!empty($enemy_list)) {
		foreach ($enemy_list AS $enemyID) {
			if ($enemyID != 0 && $enemyID != $playerID) {
				try {
					$insertStatement->execute();
				} catch (Exception $e) {
				}
			}
		}
	}
}
?>
