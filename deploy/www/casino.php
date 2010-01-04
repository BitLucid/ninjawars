<?php
$private    = true;
$alive      = true;
$quickstat  = "player";
$page_title = "Casino";

include SERVER_ROOT."interface/header.php";
?>

<h1>Casino</h1>

<div class="description">
  <div>You walk down the alley towards a shadowed door. As you enter the small casino, a guard eyes you with caution.</div>
  <div style="margin-top: 15px;margin-bottom: 15px;">You walk towards the only table with an attendant. He shows you a shiny coin with a dragon on one side and a house on the other.</div>
  <div>"Place your bet, call the coin in the air, and let's see who's lucky today!"</div>
</div>

<hr>

<div>Welcome to the Casino, <?php echo $username;?>!</div>

<form id="coin_flip" action="casino.php" method="post" name="coin_flip">

<?php
$bet = intval(in('bet'));
$reward = "Fire Scroll";


if ($bet >= 5 && $bet <= 1000) {
	if ($bet <= getGold($username)) {
		$answer = rand (1, 2);

		if ($answer == 1) {
			echo "<div class='ninja-notice'>You win!</div>\n";
			addGold($username, $bet);

			if ($bet == 1000) {
				addItem($username,$reward,1);
			}
		} else if ($answer == 2) {
			echo "<div class='ninja-notice'>You lose!</div>\n";
			subtractGold($username,$bet);
		}

		echo "<a href=\"casino.php\" style=\"display: block;margin-top: 10px;\">Try Again?</a>\n";
	} else {
		echo "<div>You do not have that much gold.</div>\n";
	}
} else {
	echo "<div>The minimum bet at this table is 5 gold.</div>\n";
	echo "<div>The maximum bet at this table is 1,000 gold.</div>\n";
}

?>

<div>
  Bet: <input id="bet" type="text" size="3" maxlength="4" name="bet" class="textField">
  &nbsp;&nbsp;<input type="submit" value="Place bet" class="formButton">
</div>

<?php
echo "<div>Current Gold: ".getGold($username)."</div>\n";

include SERVER_ROOT."interface/footer.php";
?>
