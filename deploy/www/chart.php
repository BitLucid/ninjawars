<?php
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Advancement Chart";

include SERVER_ROOT."interface/header.php";
?>

<div class="brownTitle">Dojo Chart</div>
<div class="description">
<br>
Hanging on the wall of the dojo is a scroll outlining the training requirements for all ninjas.
<br><br>
</div>

<?php
echo "<a href=\"dojo.php\">Return to Dojo</a><hr>\n";
?>
Shows how many kills you need to progress and how your stats will change:
<table style="border: 0;\">
<tr>
  <td>
  Level
  </td>

  <td>
  Kills
  </td>

  <td>
  Str
  </td>

  <td>
  Max HP
  </td>
</tr>

<?php
$level_chart = 1;
$kills_chart = 0;
$str_chart   = 5;
$hp_chart = 150;
$MAX_LEVEL = 150;
$MAX_HP = 150+(($MAX_LEVEL-1)*25);
for ($i=1 ; $i<=$MAX_LEVEL ; $i++)
  {
    echo "<tr>\n";
    echo "  <td>\n";
    echo    $level_chart."\n";
    echo "  </td>\n";
    
    echo "  <td>\n";
    echo    $kills_chart."\n";
    echo "  </td>\n";
    
    echo "  <td>\n";
    echo    $str_chart."\n";
    echo "  </td>\n";
    
    echo "  <td>\n";
    echo    $hp_chart."\n";
    echo "  </td>\n";
    echo "</tr>\n";
    
    $level_chart = $level_chart + 1;
    $kills_chart = $kills_chart + 5;
    $str_chart = $str_chart + 5;
    $hp_chart += ($hp_chart <= $MAX_HP ? 25 : 0);
  }

echo "</table>\n";

include SERVER_ROOT."interface/footer.php";
?>

