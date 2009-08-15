<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php");

// File for creating the clan ranking tag cloud.


function clan_size(){
    $res = array();
    $db = new DBAccess();
    $sel = "select sum(level-3-round(days/5)) as sum, clan_long_name from players group by clan_long_name order by sum desc";
    $counts = $db->FetchAll($sel);
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
    $clans = clan_size();
    //$clans = @natsort2d($clans, 'level');
    $res = "<div id='clan-tags'>
                <h4 id='clan-tags-title'>
                    All Clans
                </h4>
            <ul>";
    foreach($clans as $clan => $size){
        $res .= "<li class='clan-tag size$size'>
                <a href='?command=view&clan_long_name=".urlencode($clan)."'>$clan</a>
            </li>";
    }
    $res .= "</ul>
            </div>";
    return $res;
}

// Not exactly just tags, but good enough for me.

/* Display the clan member name list or tag list */
function render_clan_view($clan, $clan_name=null, $clan_long_searched=null, $sql){
    $search = "clan = '$clan_name'";
    if($clan_long_searched){
        $search = "clan_long_name = '$clan_long_searched'";
    }
    $members = $sql->FetchAll("SELECT uname, clan, clan_long_name, level, days FROM players WHERE $search AND confirmed = 1 order by level desc");
    $max_list = $sql->FetchAll("SELECT max(level) as max FROM players WHERE $search AND confirmed = 1");
    $max_array = reset($max_list);
    $max = $max_array['max'];
    //$members = @natsort2d($members, 'days');
    $res = "<div id='clan-members'>
            <h3 id='clan-members-title'>".($clan_long_searched? "Clan $clan_long_searched" : $clan."'s Clan")."</h3>
            <div id='clan-members-count'>Clans Members: ".count($members)."</div>
            <ul id='clan-members-list'>";
    foreach($members as $member){
        $member['size'] = floor(( (($member['level']-$member['days'] <1? 0 : $member['level']-$member['days'])) /$max)*2)+1;
        if($member['uname'] == $member['clan']){
            $member['size'] = $member['size']+2;
            $member['size'] = ($member['size']>2? 2 : $member['size']);
        }
        $res .= "<li class='member size{$member['size']}'>
                <a href='player.php?player={$member['uname']}'>{$member['uname']}</a>
            </li>";
    }
    $res .= "</ul>
            </div>";
    return $res;
}


// Helper function.
/*
function natsort2d($arrIn, $index = null) {
    $arrTemp = array();
    $arrOut = array();
    foreach ( $arrIn as $key=>$value ) {
        reset($value);
        $arrTemp[$key] = is_null($index)
                            ? current($value)
                            : $value[$index];
    }
    natsort($arrTemp);
    foreach ( $arrTemp as $key=>$value ) {
        $arrOut[$key] = $arrIn[$key];
    }
    $arrIn = $arrOut;
    return $arrIn;
}*/

?>
