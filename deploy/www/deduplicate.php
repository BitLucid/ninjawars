<?php
$count = query_item('SELECT count(*) FROM duped_unames WHERE lower(uname) = (SELECT lower(uname) FROM duped_unames WHERE player_id = :player_id)', array(':player_id'=>SESSION::get('active_player')));
$data = query_row('SELECT relative_age, lower(uname) AS uname FROM duped_unames WHERE player_id = :player_id', array(':player_id'=>SESSION::get('active_player')));
$locked = query_row('SELECT locked FROM duped_unames WHERE locked AND lower(uname) = (SELECT lower(uname) FROM duped_unames WHERE player_id = :player_id)', array(':player_id'=>SESSION::get('active_player')));
$age = $data['relative_age'];
$old_name = $data['uname'];

if (isset($_POST['new_name'])) {	// *** User has decided to change name ***
	$new_name = trim($_POST['new_name']);

	if ($new_name) { // *** new name is not empty ***
		if (strtolower($new_name) == $old_name) {	// *** trying to change name to the same name ***
			$error = 'You must enter a new name if you wish to change it.';
		} else {	// *** initial tests done, proceed to advanced tests ***
			// *** Check to see if new name is already in use ***
			if (query_row('SELECT uname FROM players WHERE lower(uname) = :new_name', array(':new_name'=>strtolower($new_name)))) {
				$error = 'The name you have chosen is already in use. Please choose another.';
			} else if (!username_is_valid($new_name)) { // *** Check to see if new name passes naming standards ***
				$error = 'The name you have chosen is unacceptable.';
			} else { // *** If everything is OK ***
				query('UPDATE players SET uname = :new_name WHERE player_id = :player_id'
					 , array(':new_name'=>$new_name, ':player_id'=>SESSION::get('active_player')));

				if ($count > 2) {	// *** Still more dupes for this name, but clean this one out ***
					query('DELETE FROM duped_unames WHERE player_id = :player_id'
						, array(':player_id'=>SESSION::get('active_player')));

					// *** Update the relative ages of the remaining dupes ***
					if ($age == 1) {
						query('UPDATE duped_unames SET relative_age = relative_age - 1 WHERE lower(uname) = :old_name'
							, array(':old_name'=>$old_name));
					} else if ($age == 2) {
						query('UPDATE duped_unames SET relative_age = 2 WHERE relative_age = 3 AND lower(uname) = :old_name'
							, array(':old_name'=>$old_name));
					}
				} else {	// *** Dupe problem solved, delete records from dupe table ***
					query('DELETE FROM duped_unames WHERE lower(uname) = :old_name'
						, array(':old_name'=>strtolower($old_name)));
				}
			}
		}
	} else if (isset($_POST['lock']) && $_POST['lock'] === '1') { // *** User has decided to lock name ***
		if ($locked) {	// *** Username is already locked ***
			$error = 'The username is already locked, you must choose a new name.';
		} else { // *** Everything is OK, proceed ***
			query('UPDATE duped_unames SET locked = true WHERE player_id = :player_id'
				, array(':player_id'=>SESSION::get('active_player')));
		}
	} else {	// *** New name is empty ***
		$error = 'You must enter a new name if you wish to change it.';
	}
}

display_page('deduplicate.tpl', 'Deduplication', get_defined_vars());
?>
