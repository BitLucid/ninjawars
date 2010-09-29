<?php
// Get and set/save a changable array of a player's settings.

// TODO: Change this into a simple object.

// Get all the settings from the database as an assoc array.  refresh refreshes the in-memory static storage.
function get_settings($refresh=null) {
	static $settings; // In memory static storage, if any.

	if ($refresh) {
		$settings = null; // Nullify to pull from the database again.
	}
	
	$user_id = get_user_id();

	if (!$settings && $user_id) {
		// If the static var isn't present yet, so get it
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("SELECT settings_store FROM settings WHERE player_id = :player");
		$statement->bindValue(':player', $user_id);
		$statement->execute();

		$serial_settings = $statement->fetchColumn();

		if ($serial_settings) {
			$settings = unserialize($serial_settings);
		}

		if (!$settings) {
			$settings = null;
		}
	}

	return $settings;
}

// Get a single setting from the static settings store.
function get_setting($name) {
	$set = get_settings();
	return (isset($set[$name]) ? $set[$name] : null);
}

// Add a single setting pair to the current settings, & save the result.
function set_setting($name, $setting) {
	$cur = get_settings();
	$new = array($name=>$setting);

	if ($cur) {
		$joined = $new + $cur;
	} else {
		$joined = $new;
	}

	return save_settings($joined);
}

// Save a set of settings & call for a refresh of the static in-memory storage.
function save_settings($settings) {
	$user_id = get_user_id();
	assert($user_id);
    if($user_id){
    	DatabaseConnection::getInstance();
    	$statement = DatabaseConnection::$pdo->prepare("SELECT count(settings_store) FROM settings WHERE player_id = :player");
    	$statement->bindValue(':player', $user_id);
    	$statement->execute();

    	$settings_exist = $statement->fetchColumn();

    	if ($settings_exist) {
    		$statement = DatabaseConnection::$pdo->prepare("UPDATE settings SET settings_store = :settings WHERE player_id = :player");
    		$statement->bindValue(':settings', serialize($settings));
    		$statement->bindValue(':player', $user_id);
    	} else {
    		$statement = DatabaseConnection::$pdo->prepare("INSERT INTO settings (settings_store, player_id) VALUES (:settings, :player)");
    		$statement->bindValue(':settings', serialize($settings));
    		$statement->bindValue(':player', $user_id);
    	}

    	$statement->execute();
    }

	return get_settings($refresh=true); // This refreshes the static, saved settings variable.
}
?>
