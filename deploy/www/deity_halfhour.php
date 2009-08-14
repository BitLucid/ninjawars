<?php
require_once(LIB_ROOT."specific/lib_deity.php"); // Deity-specific functions
//include SERVER_ROOT."interface/header.php";

$maximum_turn_regen = 100;
$maximum_turns = 300;
$maximum_heal = 150;
$maxtime = '6 hours'; // *** Max time a person is kept online without being active.
$score = get_score_formula();

$sql = new DBAccess();

// Chat the time if no-one chatted recently
chat_timer();

$sql->Update("UPDATE players SET turns = 0 WHERE turns < 0");
$sql->Update("UPDATE players SET turns = turns+1 WHERE turns<".$maximum_turn_regen);  // add 1 turn each half-hour, while under 100.
$sql->Update("UPDATE players SET turns = ".$maximum_turns." WHERE turns > ".$maximum_turns); // max turn code, the max is now 300.
$sql->Update("UPDATE players SET bounty = 0 WHERE bounty < 0");

// Database connection information here

$sql->Query("DELETE FROM ppl_online WHERE activity < (now() - interval '".$maxtime."')");
$out_display['Inactive Browsers Deactivated'] = $sql->a_rows;

// *** NEW HALF-HOURLY HEAL ***


$sql->Update("UPDATE players SET health=
		 CASE WHEN health+8 <= $maximum_heal THEN health+8 ELSE $maximum_heal END 
	     WHERE health >= 1 AND health < $maximum_heal AND NOT cast(status&".POISON." AS bool)");


// *********************
// Visual output:
foreach ($out_display AS $loopKey => $loopRowResult)
{
    $res = "<br>Result type: ".$loopKey." yeilded result number: ".$loopRowResult;
//    error_log('DEITY_HALFHOUR: '.$res); Unnecessary.
    echo $res;
}

//include SERVER_ROOT."interface/footer.php";
?>
