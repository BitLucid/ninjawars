<?php
//sendChat("SystemTime","ChatMsg","----------".date("h:i")."----------"); // Display the date change.
echo date("----------".date('h:i')."----------");
/*
var_dump($_COOKIE);
require_once("lib/common/lib_login.php");
setcookie("user_cookie", "glassbox", (time()+60*60*24*365), "/"); // *** 360 days ***
session_start();
if (!isset($_SESSION['hits'])) $_SESSION['hits'] = 0;
++$_SESSION['hits'];

var_dump($_SESSION);
var_dump(session_id());
var_dump(SID);
var_dump(ini_set('session'));
var_dump($_COOKIE);
echo '<p>Session hits: ', $_SESSION['hits'], '</p>';
echo '<p>Refresh the page or click <a href="', $_SERVER['PHP_SELF']."?".SID,
    '">here</a>.';
*/
/*
require_once(DB_ROOT."PlayerVO.php");
require_once(DB_ROOT."PlayerDAO.php");
$p_id = 72941;
$sql = new DBAccess();
//$sql->Query("Select * from players where player_id = ".$p_id);
//var_dump($sql->FetchAssociative());
$dao = new PlayerDAO($sql);
$playerVO = $dao->get($p_id);
$dao->save($playerVO);
var_dump("playerVO");
var_dump($playerVO);
$playerVO->level=32;
$playerVO->player_id = 0;
$playerVO->uname = 'palaxoz'.rand(1, 5000);
var_dump("new VO pre-save");
var_dump($playerVO);
$dao->save($playerVO);
var_dump($playerVO->player_id);
$playerVO = $dao->get($playerVO->player_id);
var_dump("Changed and insert-saved playerVO");
var_dump($playerVO);
 */
?>
