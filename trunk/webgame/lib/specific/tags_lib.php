<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php");

// File for creating the clan ranking tag cloud.


function clan_counts(){
    $db = new DBAccess();
    $sel = "select sum(level-3-round(days/5)) as sum, clan_long_name from players group by clan_long_name order by sum desc";
    $res = $db->FetchAll($sel);
    return $res;
}

function clan_size(){
    $res = array();
    $counts = clan_counts();
    $largest = reset($counts);
    $max = $largest['sum'];
    foreach($counts as $clan_info){
        // make percentage of highest, multiply by 10 and round to give a 1-10 size
        $res[$clan_info['clan_long_name']] =
             floor(( (($clan_info['sum']-1 <1? 0 : $clan_info['sum']-1)) /$max)*10)+1;
    }
    return $res;
}

function render_clan_tags(){
    $res = "<h4 id='clan-tags-title'>All Clans</h4>";
    $clans = clan_size();
    foreach($clans as $clan => $size){
        $res .= "<div class='clan-tag size$size'><a href='?command=view&clan_long_name=".urlencode($clan)."'>$clan</a></div>";
    }
    return "<div id='clan-tags'>$res</div>";
}

?>
