<?php
/**
 * Delete an array of ids or all mail for a certain user.
**/
function delete_mail($ids, $all=false) {
	DatabaseConnection::getInstance();

	$deleted = 0;
	$user_d = get_user_id();

	if ($all) { // Delete all a user's mail.
		$del = "DELETE FROM message WHERE send_to = :user";
	} else { // Delete an id list.
		$del = "DELETE FROM message WHERE send_to = :user AND id IN ('".implode("', '", $ids)."')";
	}

	$statement = DatabaseConnection::$pdo->preapre($del);
	$statement->bindValue(':user', $user_id);

	$statement->execute();

	return $statement->rowCount();
}
?>
