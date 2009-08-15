<?php
// Object Test function.

function test_PlayerDAO(){
	// in: player_id, out: vo with uname and player_id.
	$player_id_sel = "select player_id from players where uname = 'glassbox'";
	$db = new DBAccess();
	$player_id = $db->QueryItem($player_id_sel);
	$dao = new PlayerDAO($db);
	$player_vo = $dao->get($player_id);
	//var_dump($player_vo);
	assert(isset($player_vo));
	assert(isset($player_vo->uname));
	assert(isset($player_vo->player_id));

	// in: player_id, out: vo with same id.
	$player_id_sel = "select player_id from players where uname = 'glassbox'";
	$db = new DBAccess();
	$player_id = $db->QueryItem($player_id_sel);
	$dao = new PlayerDAO($db);
	$player_vo2 = $dao->get($player_id);
	assert($player_vo2->player_id == $player_id);

	// in: player_id, out: vo with same username.
	$player_id_sel = "select player_id from players where uname = 'glassbox'";
	$db = new DBAccess();
	$player_id = $db->QueryItem($player_id_sel);
	$dao = new PlayerDAO($db);
	$player_vo2 = $dao->get($player_id);
	assert($player_vo2->uname == 'glassbox');

	// in: player_id that doesn't exist, out: null
	$player_id = 999999;
	$dao = new PlayerDAO($db);
	$player_vo2 = $dao->get($player_id);
	assert($player_vo2 === null);

	// in: non-numeric player_id, out: false
	$player_id = 'not-a-player-id';
	$dao = new PlayerDAO($db);
	$player_vo2 = $dao->get($player_id);
	assert($player_vo2 === false);


	// in: player_vo, change the energy, save it. out: get that player, compare energy
	$player_id_sel = "select player_id from players where uname = 'glassbox'";
	$db = new DBAccess();
	$player_id = $db->QueryItem($player_id_sel);
	$dao = new PlayerDAO($db);
	$player_vo_original = $dao->get($player_id);
	assert($player_vo_original->player_id == $player_id);
	$orig_energy = $player_vo_original->energy;
	$player_vo_original->energy = $player_vo_original->energy +2;
	$dao->save($player_vo_original);
	$player_vo_after = $dao->get($player_vo_original->player_id);
	assert($orig_energy == ($player_vo_after->energy -2));


	// in: player_vo, change the energy, save it. out: get that player, compare energy
	$player_id_sel = "select player_id from players where uname = 'glassbox'";
	$db = new DBAccess();
	$player_id = $db->QueryItem($player_id_sel);
	$dao = new PlayerDAO($db);
	$player_vo_original = $dao->get($player_id);
	$starting_clan = $player_vo_original->clan_long_name;
	$player_vo_original->clan_long_name = 'TestClanChange';
	$dao->save($player_vo_original);
	$changed_vo = $dao->get($player_vo_original->player_id);
	$changed_clan = $changed_vo->clan_long_name;
	$changed_vo->clan_long_name = $starting_clan;
	$dao->save($changed_vo);
	assert('TestClanChange' == $changed_clan);

	// in: a player_vo to change and save then delete, out: successful deletion
	$player_id_sel = "select player_id from players where uname = 'glassbox'";
	$db = new DBAccess();
	$player_id = $db->QueryItem($player_id_sel);
	assert($player_id);
	$dao = new PlayerDAO($db);
	$player_vo = $dao->get($player_id);
	assert(isset($player_vo->player_id));
	$player_vo->player_id = null;
	$player_vo->uname = "TestUserName2";
	$player_vo->pname = "dummypassword";
	$dao->save($player_vo);
	$player_id_sel = "select player_id from players where uname = 'TestUserName2'";
	$db = new DBAccess();
	$player_id = $db->QueryItem($player_id_sel);
	assert($player_id);
	$dao = new PlayerDAO($db);
	$player_vo = $dao->get($player_id);
	assert(isset($player_vo->player_id));
	$deleted = $dao->delete($player_vo); // Need a player_id to delete.
	assert($deleted == true);
	$player_id_sel = "select player_id from players where uname = 'TestUserName2'";
	$deleted_id = $db->QueryItem($player_id_sel);
	assert($deleted_id == null);

	// in: a new player_vo to save n delete, out: no such new vo.
	$player_id_sel = "select player_id from players where uname = 'glassbox'";
	$db = new DBAccess();
	$player_id = $db->QueryItem($player_id_sel);
	assert($player_id);
	$dao = new PlayerDAO($db);
	$player_vo1 = $dao->get($player_id);
	assert(isset($player_vo1->player_id));
	$player_vo1->player_id = null;
	$username = "TestUserName2".rand();
	$player_vo1->uname = $username;
	$player_vo1->pname = "dummypassword";
	$dao->save($player_vo1);
	assert(($player_vo1->player_id != 0));
	//var_dump($player_vo1->player_id, $player_vo1->uname);
	$saved_vo1 = $dao->get($player_vo1->player_id);
	$player_from_uname_sel = "select player_id from players where uname = '".$username."'";
	$player_id_from_uname = $db->QueryItem($player_from_uname_sel);
	assert($player_id_from_uname != false);
	//var_dump($player_id_from_uname, $username);
	$player_uname_from_id_sel = "select uname from players where player_id = '".$player_vo1->player_id."'";
	$player_uname = $db->QueryItem($player_uname_from_id_sel);
	//var_dump($saved_vo1->uname); // for some reason the vo is not coming back here.
	assert(isset($saved_vo1->player_id));
	assert($saved_vo1->uname == $username);
	assert($player_id_from_uname == $saved_vo1->player_id);
	$success = $dao->delete($saved_vo1);
	assert($success == true);
}

?>
