<?php
// lib_mail


function mail_count($username){
	$mail = 0;
	$sql = new DBAccess();
	$sql->QueryRow("SELECT send_to FROM mail WHERE send_to = '$username' ORDER BY id DESC LIMIT 1");
	$row = $sql->data;
	$mail = $sql->rows;
	return $mail;
}


?>
