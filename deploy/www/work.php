<?php
$private    = true;
$alive      = true;
$quickstat  = "player";
$page_title = "Working in the Village";

include SERVER_ROOT."interface/header.php";
?>

<div class="brownTitle">Working in the Village</div>

<?php
$work_multiplier = 30;

$description2 = "<div class=\"description\">\n".
                "On your way back from the fields, you pass by a few young children ".
	          "chasing grasshoppers in the tall grass.\n".
                "<br><br>\n".
                "The foreman hands you a small pouch of gold as he says ".
                "\"Care to put a little more work in? I'll keep paying you.\"\n".
                "</div>\n";

$description1 = "<div class=\"description\">\n".
	                "On your way to the foreman's office, you pass by several workers ".
                "drenched in sweat from working in the heat all day.\n".
                "<br><br>\n".
                "The foreman barely looks up at you as he busies himself with paperwork ".
                "and a cigarette. \"So, how much work can we expect from you?\"\n".
                "</div>\n";

$description = $description1;

$worked = intval(in('worked'));
if ($worked > 0)
{
  $turns = getTurns($username);
  $gold  = getGold($username);
 
  if ($worked > $turns)
    {
      $description .= "You have chosen to do more work than turns you have.<br>\n";
    }
  else
    {
      $new_gold  = $worked * $work_multiplier;   // *** calc amount worked ***

      $gold  = addGold($username,$new_gold);
      $turns = subtractTurns($username,$worked);

     
      $description = $description2.
	"You have worked for $worked turns and earned $new_gold gold.<br><br>\n";
    }
}

echo $description;
echo "<p>";
echo "You can earn money by working in the Village<br>\n";
echo "Village work will exchange turns for gold.<br>\n";
echo "The current work exchange rate: 1 Turn = ".$work_multiplier." Gold.<br>\n";
echo "Work in the Village?<br>\n";
echo "<form id=\"work\" action=\"work.php\" method=\"post\" name=\"work\">\n";
echo "<input id=\"worked\" type=\"text\" size=\"3\" maxlength=\"3\" name=\"worked\" class=\"textField\">\n";
echo "<input id=\"workButton\" type=\"submit\" value=\"Turns\" name=\"workButton\" class=\"formButton\">\n";
echo "</form>\n";
echo "</p>";

include SERVER_ROOT."interface/footer.php";
?>
