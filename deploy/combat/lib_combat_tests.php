<?php
// lib_attack_test.php


function test_attack_legal_object(){
	// in: same ninja, out: fail
	$attacker = 'glassbox';
	$target = 'glassbox';
	$params = array('required_turns'=>5, 'attacked'=>1);
	$AttackLegal = new AttackLegal($attacker, $target, $params);
	$attack_checked = $AttackLegal->check();
	$attack_error = $AttackLegal->getError();
	assert($attack_checked == false);

	// in: two different characters, out: legal
	$attacker = 'glassbox';
	$target = 'test';
	$params = array('required_turns'=>5, 'attacked'=>1);
	$AttackLegal = new AttackLegal($attacker, $target, $params);
	$attack_checked = $AttackLegal->check();
	$attack_error = $AttackLegal->getError();
	assert($attack_checked == true); // Sometimes hits the time limit when unit testing.
	echo($attack_error);

	// In: excessive required turns, out: failure
	$attacker = 'glassbox';
	$target = 'test';
	$params = array('required_turns'=>1000000, 'attacked'=>1);
	$AttackLegal = new AttackLegal($attacker, $target, $params);
	$attack_checked = $AttackLegal->check();
	$attack_error = $AttackLegal->getError();
	assert($attack_checked == false);

	// In: different name and attacker_id, out: legal
	$attacker = 10;
	$target = 'test';
	$params = array('required_turns'=>1, 'attacked'=>1);
	$AttackLegal = new AttackLegal($attacker, $target, $params);
	$attack_checked = $AttackLegal->check();
	$attack_error = $AttackLegal->getError();
	assert($attack_checked == true);
}







?>
