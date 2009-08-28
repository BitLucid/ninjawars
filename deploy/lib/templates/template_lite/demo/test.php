<?php
$timeparts = explode(" ",microtime());
$starttime = $timeparts[1].substr($timeparts[0],1);

require("../src/class.template.php");
$tpl = new Template_Lite;
$tpl->force_compile = true;
$tpl->compile_check = true;
$tpl->cache = false;
$tpl->cache_lifetime = 3600;
$tpl->config_overwrite = false;

$tpl->assign("Name","Fred Irving Johnathan Bradley Peppergill");
$tpl->assign("FirstName",array("John","Mary","James","Henry"));
$tpl->assign("contacts", array(array("phone" => "1", "fax" => "2", "cell" => "3"),
	  array("phone" => "555-5555", "fax" => "555-4444", "cell" => "555-3333")));
$tpl->assign("bold", array("up", "down", "left", "right"));
$tpl->assign("lala", array("up" => "first entry", "down" => "last entry"));

$tpl->display("index.tpl");

$timeparts = explode(" ",microtime());
$endtime = $timeparts[1].substr($timeparts[0],1);
echo bcsub($endtime,$starttime,6);
?>