<?php

logout_user();

$page = 'logout';
$pages = array('logout'=>array('title'=>'Logged out', 'template'=>'logout.tpl'));

display_static_page($page, $pages); // Display exceedingly simple
