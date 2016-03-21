<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;

// Get the account linked with a character.
function account_info_by_char_id($char_id, $specific=null){
	$res = query_row('select * from accounts join account_players on account_id = _account_id where _player_id = :char_id', 
		array(':char_id'=>array($char_id, PDO::PARAM_INT)));
	if ($specific) {
		if (isset($res[$specific])) {
			$res = $res[$specific];
		} else {
			$res = null;
		}
	}

	return $res;
}

function create_account($ninja_id, $email, $password_to_hash, $confirm, $type=0, $active=1, array $data=null) {
	$ip = $data['ip']?:null;
	DatabaseConnection::getInstance();

	$newID = query_item("SELECT nextval('accounts_account_id_seq')");

	$ins = "INSERT INTO accounts (account_id, account_identity, active_email, phash, type, operational, verification_number, last_ip)
		VALUES (:acc_id, :email, :email2, crypt(:password, gen_salt('bf', 10)), :type, :operational, :verification_number, :ip)";

	$email = strtolower($email);

	$statement = DatabaseConnection::$pdo->prepare($ins);
	$statement->bindParam(':acc_id', $newID);
	$statement->bindParam(':email', $email);
	$statement->bindParam(':email2', $email);
	$statement->bindParam(':password', $password_to_hash);
	$statement->bindParam(':type', $type, PDO::PARAM_INT);
	$statement->bindParam(':operational', $active, PDO::PARAM_INT);
	$statement->bindParam(':verification_number', $confirm);
	$statement->bindParam(':ip', $ip);
	$statement->execute();

	// Create the link between account and player.
	$link_ninja = 'INSERT INTO account_players (_account_id, _player_id, last_login) VALUES (:acc_id, :ninja_id, default)';

	$statement = DatabaseConnection::$pdo->prepare($link_ninja);
	$statement->bindParam(':acc_id', $newID, PDO::PARAM_INT);
	$statement->bindParam(':ninja_id', $ninja_id, PDO::PARAM_INT);
	$statement->execute();

	$sel_ninja_id = 'SELECT player_id FROM players
		JOIN account_players ON player_id = _player_id
		JOIN accounts ON _account_id = account_id
		WHERE account_id = :acc_id ORDER BY level DESC LIMIT 1';

	$verify_ninja_id = query_item($sel_ninja_id, array(':acc_id'=>array($newID, PDO::PARAM_INT)));

	return ($verify_ninja_id != $ninja_id ? false : $newID);
}

// Create the account and the initial ninja for that account.
function create_account_and_ninja($send_name, $params=array()) {
	$send_email  = $params['send_email'];
	$send_pass   = $params['send_pass'];
	$class_identity  = $params['send_class'];
	$confirm     = (int) $params['confirm'];
	$data['ip'] = (isset($params['ip'])? $params['ip'] : null);

    $class_id = query_item(
        'SELECT class_id FROM class WHERE identity = :class_identity',
        [ ':class_identity' => $params['send_class'] ]
    );

    $ninja = new Player();
    $ninja->uname               = $send_name;
    $ninja->verification_number = (int) $params['confirm'];
    $ninja->active              = (int) $params['preconfirm'];
    $ninja->_class_id           = $class_id;
    $ninja->save();

    $ninja_id = $ninja->id();
	return create_account($ninja_id, $send_email, $send_pass, $confirm, 0, 1, $data);
}

// Confirm a player if they completely match.
function confirm_player($char_name, $confirmation=0, $autoconfirm=false) {
	DatabaseConnection::getInstance();
	// Preconfirmed or the email didn't send, so automatically confirm the player.
	$require_confirm = ($autoconfirm ? '' : ' AND 
			(account.verification_number = :confirmation OR players.verification_number = :confirmation2) ');
	// Get the account_id for a player 
	$params = array(':char_name'=>$char_name);
	if($require_confirm){
		$params[':confirmation'] = $confirmation;
		$params[':confirmation2'] = $confirmation;
	}
	$info = query_row('select account_id, player_id from players 
		join account_players on _player_id = player_id 
		join accounts on account_id = _account_id 
		 where uname = :char_name '.$require_confirm, 
			$params);
	if(empty($info)){
		return false;
	} else {
		$account_id = $info['account_id'];
		$player_id = $info['player_id'];
		if(!$account_id || !$player_id){
			return false;
		}
	}

	query('update players set active = 1 where player_id = :player_id',
		array(':player_id'=>$player_id));

	$up = "UPDATE accounts set operational = true, confirmed = 1 where account_id = :account_id";
	$params = array(':account_id'=>$account_id);
	return (bool) rco(query($up, $params));
}

// Get the display name from the identity.
function class_display_name_from_identity($identity) {
	return query_item('SELECT class_name from class where identity = :identity', array(':identity'=>$identity));
}

