<?php
$private    = false;
$alive      = false;
$quickstat  = false;
$page_title = "Member's Page";

include "interface/header.php";
?>

<span class="brownHeading">Members</span>

<p>

This is where Members can use their paid features.<br />

Paid membership will allow you to new and special features of Ninja Wars.<br />

If you wish to be a paid member click on <a href="donate.php" style="font-weight: bold;">Donate</a><br />

<?php

$member = $sql->QueryItem("SELECT member FROM players WHERE uname = '".$_SESSION['username']."'");

if ($member == 1) {echo "You are a Paid member.\n";}
else if ($member != 1) {echo "You are a Free member.\n";}
?>

<hr />

Current Features:

<br /><br />

<a href="change_class.php">Change Ninja Class</a> | (more soon)

<br /><br />

Planned  membership features: Thief Class, Undead Class, and more powerful items.

<br /><br />

Currently the paid features are still being designed, if you have any input, contact our <a href="staff.php">Staff</a>.

<hr />

<?php
include "interface/footer.php";
?>

