<?php
// Get and set/save a changable array of a player's settings.

// TODO: Change this into a simple object.

function get_settings($refresh=null) {
	static $settings;

	if ($refresh) {
		$settings = null; // Nullify to pull from the database again.
	}

	if (!$settings) {
		// If the static var isn't present yet, so get it
		$user_id = get_user_id();
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

function get_setting($name) {
	$set = get_settings();
	return (isset($set[$name]) ? $set[$name] : null);
}

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

function save_settings($settings) {
	$user_id = get_user_id();
	assert($user_id);

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

	return get_settings($refresh=true);
}
?>
