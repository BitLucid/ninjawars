<?php
/**
 * Delete an array of ids or all mail for a certain user.
**/
function delete_mail($ids, $all=false) {
	DatabaseConnection::getInstance();

	$user_id = get_user_id();

	if ($all) { // Delete all a user's mail.
		$del = "DELETE FROM message WHERE send_to = :user";
	} else { // Delete an id list.
		$del = "DELETE FROM message WHERE send_to = :user AND id IN (:id".implode(", :id", array_keys($ids)).")";
	}

	$statement = DatabaseConnection::$pdo->prepare($del);
	$statement->bindValue(':user', $user_id);

	if (!$all) {
		foreach ($ids AS $key=>$value) {
			$statement->bindValue(':id'.$key, $value);
		}
	}

	$statement->execute();

	return $statement->rowCount();
}
