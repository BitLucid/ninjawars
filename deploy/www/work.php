<?php
$private    = false;
$alive      = true;
$quickstat  = "player";
$page_title = "Working in the Village";

include SERVER_ROOT."interface/header.php";
?>

<h1>Working in the Village</h1>

<?php
$work_multiplier = 30;

$description2 = "<div class=\"description\">\n".
                "<div style='margin-bottom: 10px;'>On your way back from the fields, you pass by a few young children ".
	          "chasing grasshoppers in the tall grass.</div>\n".
                "<div>The foreman hands you a small pouch of gold as he says ".
                "\"Care to put a little more work in? I'll keep paying you.\"</div>\n".
                "</div>\n";

$description1 = "<div class=\"description\">\n".
	                "<div style='margin-bottom: 10px;'>On your way to the foreman's office, you pass by several workers ".
                "drenched in sweat from working in the heat all day.</div>\n".
                "<div>The foreman barely looks up at you as he busies himself with paperwork ".
                "and a cigarette. \"So, how much work can we expect from you?\"</div>\n".
                "</div>\n";

$description = $description1;

$worked = intval(in('worked'));

if ($worked > 0)
{
	$turns = getTurns($username);
	$gold  = getGold($username);

	if ($worked > $turns)
	{
		$description .= "<div>You have chosen to do more work than turns you have.</div>\n";
	}
	else
	{
		$new_gold  = $worked * $work_multiplier;   // *** calc amount worked ***

		$gold  = addGold($username, $new_gold);
		$turns = subtractTurns($username, $worked);

		$description = $description2."<div style='margin-bottom: 10px;'>You have worked for $worked turns and earned $new_gold gold.</div>\n";
	}
}

echo $description;
echo "<p>";
echo "<div>You can earn money by working in the Village</div>\n";
echo "<div>Village work will exchange turns for gold.</div>\n";
echo "<div>The current work exchange rate: 1 Turn = ".$work_multiplier." Gold.</div>\n";
echo "<div>Work in the Village?</div>\n";
echo "<form id=\"work\" action=\"work.php\" method=\"post\" name=\"work\">\n";
echo "<div>\n";
echo "<input id=\"worked\" type=\"text\" size=\"3\" maxlength=\"3\" name=\"worked\" class=\"textField\">\n";
echo "<input id=\"workButton\" type=\"submit\" value=\"Turns\" name=\"workButton\" class=\"formButton\">\n";
echo "</div>\n";
echo "</form>\n";
echo "</p>";
?>
<!-- Google Ad -->
<script type="text/javascript"><!--
google_ad_client = "pub-9488510237149880";
/* 300x250, created 12/17/09 */
google_ad_slot = "9563671390";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>

<?php
include SERVER_ROOT."interface/footer.php";
?>
