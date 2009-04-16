<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php"); // *** Absolute path include of everything.

$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Ninja List";

include "interface/header.php";

// INIT

$username = get_username();
$searched   = in('searched');
$hide = in('hide', 'dead'); // Defaults to not showing dead ninja.
$alive_only = ($hide == 'dead'? true : false);
$rank_spot  = in('rank_spot');
$page = in('page', 1); // Page will get changed down below.
$dead_count = $sql->QueryItem("SELECT count(player_id) FROM rankings WHERE alive = false");
$alive_count = 0;

// TODO: Make the list default to displaying the first page, for logged-out viewing.


// Page sub functions

// Display the recently active players
function display_active($limit=5, $alive_only=true){
    $where_cond = ($alive_only? 'and health>0' : '');
    $sel = "select uname, player_id from players where confirmed=1 $where_cond order by last_started_attack desc limit $limit";
    $sql = new DBAccess();
    $res = $sql->QueryAssoc($sel);
    //var_dump($sel, $res);
    ?>
    <div class='active-players'>
        <ul>
            <li><span>Lurking ninja: </span></li>
            <?php
            foreach($res as $ninja){
                echo "<li class='active-ninja'>
                        <a href='player.php?target_id=".$ninja['player_id']."'>
                            ".$ninja['uname']."
                        </a>
                      </li>";
            } ?> 
        </ul>
    </div>
    <?php
}

// Displays the search section of the page.
function display_search_form($hide, $page, $searched, $dead_count){
    echo "<div class='list-all-players-search centered'>";
    echo "  <form action=\"list_all_players.php\" method=\"get\">";
    echo "      <input type=\"textbox\" name=\"searched\" 
        class='textField' style=\"font-family:Verdana, Arial;font-size:xx-small;\" />\n";
    echo "      <input type=\"hidden\" name=\"hide\" value=\"$hide\" />\n";
    echo "      <input type=\"submit\" class=\"formButton\" value=\"Search for Ninja\" />\n";
    if ($hide == "dead"){
        echo "<a href=\"list_all_players.php?page=$page&hide=none&searched=$searched\">(Show $dead_count dead ninja)</a>\n";
    } else {
        echo "<a href=\"list_all_players.php?page=$page&hide=dead&searched=$searched\">(Hide $dead_count dead ninja)</a>\n";
    }
    echo "  </form>\n";
    echo "</div>";

} 


// Display first/previous/page/next/last
function player_list_nav($page, $hide, $searched, $record_limit, $totalrows, $numofpages){
  echo "<div class='player-list-nav'>\n";
  echo "<form action=\"list_all_players.php\" method=\"get\">\n";
  if($page != 1) {
      $pageprev = $page-1;
      echo "<a href=\"list_all_players.php?hide=$hide&page=1&searched=$searched\">&lt;First</a> | ";
      echo("<a href=\"list_all_players.php?page=$pageprev&searched=$searched&hide=$hide\">&lt;&lt;Previous $record_limit</a>&nbsp;| ");
    } else {
      echo "&lt;First | &lt;&lt;Previous $record_limit&nbsp; | ";
    }
  echo "<span class='current-page'>";
  echo "<input type=\"hidden\" name=\"hide\" value=\"$hide\" />";
  echo "<input type=\"submit\" class=\"formButton\" value=\"Page\" />";
  echo "<input type=\"hidden\" name=\"searched\" value=\"$searched\" />";
  echo "<input type=\"textbox\" name=\"page\" value=\"$page\" style=\"font-family:Verdana, Arial;font-size:xx-small;\" size=3/>";
  echo "/$numofpages ";
  echo "</span>";
  
  if(($totalrows - ($record_limit * $page)) > 0){
      $pagenext   = $page+1;
      echo(" | <a href=\"list_all_players.php?page=$pagenext&searched=$searched&hide=$hide\">Next $record_limit&gt;</a>");
      echo " | <a href=\"list_all_players.php?page=$numofpages&hide=$hide&searched=$searched\">Last&gt;&gt;</a>\n";
    } else {
      echo(" | Next $record_limit&gt;");
      echo " | Last&gt;&gt;\n";
    }
  echo "</form>\n";
  echo "</div>\n";

} // End of display functions.


// START OF PAGE
echo "<div class='title centered'>Ninja List</div>";

display_search_form($hide, $page, $searched, $dead_count);

if($hide != 'dead'){
    $dead_count = 0; // Set the count of dead rows to zero for later listing.
}
// Display the clear search and create the sql search params.
$where_clause = "";
if ($searched != ""){ // *** Search section
  $page = "searched";
  if ($searched == 0){
		echo "<p>Searching for: ".stripslashes($searched)." <a href=\"list_all_players.php\">(Clear Search)</a><p>\n";
    	if (strlen($searched) == 1){
		  $where_clause = "WHERE (uname ILIKE '".strtoupper($searched)."%')";
		}else{
		  $where_clause = "WHERE (uname ~* '$searched')";
		}
    	if ($hide == "dead"){
		  $where_clause.=" AND alive = true";
		}
    }else{
      if ($hide == "dead"){
		  $where_clause = "WHERE alive = true";
		}
    }
} else{ // *** Normal display section.
  $where_clause = 'WHERE score >= '.(is_numeric($rank_spot)? $rank_spot : 0);
  if ($hide == "dead"){
    $where_clause .= " AND alive = true";
  }
}
echo "<p>\n";
// Run the players with or without a search requirement.
$record_limit = 20; // *** The number of players that gets shown per page.
$query_count  = "SELECT count(player_id) FROM rankings ".$where_clause;
$totalrows    = $sql->QueryItem($query_count);
$rank = $sql->QueryItem("SELECT rank_id FROM rankings WHERE uname = '".$username."'");
$rank = ($rank > 0 ? $rank : $totalrows+1);
// Determine the current page spot navigated to.
if ($searched > 0){
  $page = ceil($searched/$record_limit);
} else if ($page == "searched") {
  $page = in('page', 1);
} else {
  if (!$rank_spot) {
	  $rank_spot = $rank;
	} else {
		$rank_spot = ($rank_spot > 0 ? $rank_spot : $totalrows+1);
	}
  $page = in('page', ceil(($rank_spot-$dead_count)/$record_limit));
  if ($page == ""){
      $page       = ($dead_count > $rank_spot ? 1 : $page);
    }
}









$numofpages = ceil($totalrows/$record_limit);
$limitvalue   = ($page*$record_limit) - $record_limit;
// Get the ninja information.
$sql->Query("SELECT rank_id, uname, class, level, alive, days, clan, player_id 
	FROM rankings ".$where_clause."  ORDER BY rank_id ASC, player_id ASC 
	LIMIT $record_limit OFFSET $limitvalue");
$row = $sql->data;
$ninja_count = $sql->rows;

// Start of the displaying all the player entries.
if ($ninja_count == 0) { // Search found nothing display.
  echo "<p class='notice'>No ninja to display.</p>";
  echo "<p><a href=\"list_all_players.php?hide=$hide\">Back to Ninja List</a></p>";
} else { 
  if ($searched > 0) {
      $searched = "";
    }
    
  // Display the nav
  player_list_nav($page, $hide, $searched, $record_limit, $totalrows, $numofpages);
  
if(!$searched){ // Will not display active ninja on a search page.
    display_active(5, $alive_only); // Display the currently active ninjas
}
  
  // Table headers.
  echo "<table class=\"playerTable outer-table\">\n";
  echo "<tr class='playerTableHead'>\n";
  
  echo "  <th>\n";
  echo "  Rank\n";
  echo "  </th>\n";
  echo "  <th>\n";
  echo "  Name\n";
  echo "  </th>\n";
  echo "  <th>\n";
  echo "  Level\n";
  echo "  </th>\n";
  echo "  <th>\n";
  echo "  Class\n";
  echo "  </th>\n";
  echo "  <th>\n";
  echo "  Clan\n";
  echo "  </th>\n";
  echo "  <th>\n";
  echo "  Alive\n";
  echo "  </th>\n";

  echo "</tr>\n";
  
  // Loop over and display each of the Display each of the player table entries.
  $i=0;
  $players_to_loop = $sql->FetchAll();
  foreach($players_to_loop AS $playerRow)
  {
  	$i++;
      $rank = $playerRow['rank_id'];
      $name = htmlentities($playerRow['uname']); // username
      $class = $playerRow['class']; // class
      $level = $playerRow['level']; // level
      $isAlive = ($playerRow['alive'] == 1);
      $alive = ($isAlive? "&nbsp;" : "Dead"); // alive/dead display
      $days = $playerRow['days']; // days
      $clan = htmlentities($playerRow['clan']);        // clan
      $player_id = $playerRow['player_id'];
	// *** Changes the color of the row if dead.      
      echo "<tr class=\"playerRow ".($isAlive? "AliveRow" : "DeadRow")." ".($i%2? "odd" : "even")."\">\n";
      echo "  <td class=\"playerCell rankCell\">\n";
      echo "  $rank\n";
      echo "  </td>\n";

      echo "  <td class=\"playerCell nameCell\">\n";
      echo "  <a href=\"player.php?player=$name&linkbackpage=$page\">$name</a>\n";
      echo "  </td>\n";
      
      // TODO: make level category a static resource instead of always recalculated.
      $level_cat = level_category($level);
      echo "  <td class=\"playerCell levelCell \">\n";
      echo "<div class='{$level_cat['css']}'>".$level_cat['display']." [".$level."]</div> \n";
      echo "  </td>\n";
      
      echo "  <td class=\"playerCell classCell\">\n";
      echo "<div class='$class'><img src='".WEB_ROOT."images/small".$class."Shuriken.gif' alt=''>\n"; // *** Display an image of the right colored shuriken.
      echo    $class."</div>\n";
	  echo "  </td>\n";

      echo "  <td class=\"playerCell clanCell\">\n";
	  echo    $clan."\n";
      echo "  </td>\n";
      
      echo "  <td class=\"playerCell aliveCell\">\n";
      echo    $alive."\n";
      echo "  </td>\n";

      echo "</tr>\n";
    }
    
    
  // End the player table  
  echo "</table>\n";

  if ($searched > 0){
      $searched = ""; // Reset the searched string to blank.
    }

  // Display the nav
  player_list_nav($page, $hide, $searched, $record_limit, $totalrows, $numofpages);
}

echo "</p>\n";

include "interface/footer.php";
?>




