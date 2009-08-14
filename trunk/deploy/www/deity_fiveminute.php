<?php
require_once(LIB_ROOT."specific/lib_deity.php"); // Deity-specific functions
//include "interface/header.php";

$sql = new DBAccess();
$sql->Query("truncate player_rank");
$sql->Query("SELECT setval('player_rank_rank_id_seq1', 1, false);");
$sql->Query("insert into player_rank (_player_id, score) select player_id, ((level*900) + (CASE WHEN gold < 1 THEN 0 ELSE floor(gold/200) END) + (kills*3) - (days*5)) AS score from players WHERE confirmed = 1 ORDER BY score DESC");
$out_display['Ranked Players'] = $sql->a_rows;


// *********************
// Visual output:
foreach ($out_display AS $loopKey => $loopRowResult)
{
    $res = "<br>Result type: ".$loopKey." yeilded result number: ".$loopRowResult;
//    error_log('DEITY_FIVEMINUTE: '.$res);
    echo $res;
}
//error_log('DEITY_FIVEMINUTE: End.');
//include "interface/footer.php";
?>




