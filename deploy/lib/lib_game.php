<?php
function getMemberCount() {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->query("SELECT count(session_id) FROM ppl_online WHERE member AND activity > (now() - CAST('30 minutes' AS interval)) UNION SELECT count(session_id) FROM ppl_online WHERE member");
	$members = $statement->fetchColumn();
	$membersTotal = $statement->fetchColumn();

	return array('active'=>$members, 'total'=>$membersTotal);
}
?>
