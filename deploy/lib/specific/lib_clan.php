<?php
// See lib_tags for more functions.

// Show the form for the clan joining, or perform the join.
function render_clan_join($process=null, $username, $clan_name){
   	$sql = new DBAccess();
    if ($process == 1) {
        $confirm = $sql->QueryItem("SELECT confirm FROM players WHERE uname = '$username'");
        $join_request_message = "CLAN JOIN REQUEST: $username has sent you a clan request.
            If you wish to allow this ninja into your clan click the following link:
            <a href='clan_confirm.php?clan_joiner=".rawurlencode($username)
            ."&confirm=$confirm&clan_name=".rawurlencode($clan_name)."'>
            Confirm Request
            </a>.";
        sendMessage($username,$clan_name,$join_request_message);
        echo "<div>***Your request to join this clan has been sent to $clan_name***</div>\n";
    } else {                                            //Clan Join list of available Clans
        $clan_leaders = $sql->FetchAll("SELECT uname,level,clan,clan_long_name FROM players
            WHERE lower(uname) = lower(clan) AND clan_long_name != '' AND confirmed = 1");
        echo "<p>Clans Available to Join</p>
        <p>To send a clan request click on that clan leader's name.</p>
        <ul>";
        foreach($clan_leaders as $leader){
            echo "<li><a href=\"clan.php?command=join&clan_name={$leader['clan']}&process=1\">
                    Join {$leader['clan_long_name']}</a>.
                    Its leader is <a href=\"player.php?player=".rawurlencode($leader['uname'])."\">
                    {$leader['uname']}</a>, level {$leader['level']}.
                    <a href=\"clan.php?command=view&clan_name={$leader['clan']}\">View This Clan</a>
                </li>\n";
        }
        echo "</ul>";
    }
}

?>
