<?php
$private    = true;
$alive      = true;
$quickstat  = "player";
$page_title = "Casino";

include "interface/header.php";
?>

<div class="brownTitle">Casino</div>

<div class="description">
You walk down the alley towards a shadowed door. As you enter the small casino, a guard eyes you with caution.
<br /><br />
You walk towards the only table with an attendant. He shows you a shiny coin with a dragon on one side and a house on the other.
<br /><br />
"Place your bet, call the coin in the air, and let's see who's lucky today!"
</div>

<hr />

<?php
$bet = (isset($_POST['bet']) ? intval($_POST['bet']) : 0);

echo "Welcome to the Casino, $username!<br />\n";

echo "<form id=\"coin_flip\" action=\"casino.php\" method=\"post\" name=\"coin_flip\">\n";

if ($bet >= 5 && $bet <= 1000)
{
  if ($bet <= getGold($username))
    {
      $answer = rand (1, 2);
      
      if ($answer == 1)
	{
	  echo "$username wins!<br />\n";
	  addGold($username,$bet);
	}
      else if ($answer == 2)
	{
	  echo "$username loses!<br />\n";
	  subtractGold($username,$bet);
	}  
      
      echo "<br /><a href=\"casino.php\">Try Again?</a><br />\n";
    }
  else
    {
      echo "You do not have that much gold.<br />\n";
    }
}
else
{
  echo "The minimum bet at this table is 5 gold.<br />\n";
  echo "The maximum bet at this table is 1,000 gold.<br />\n";
}

echo "Bet: <input id=\"bet\" type=\"text\" size=\"3\" maxlength=\"4\" name=\"bet\" class=\"textField\" />\n";
echo "&nbsp;&nbsp;<input type=\"submit\" value=\"Place bet\" class=\"formButton\" /><br />\n";

echo "Current Gold: ".getGold($username)."<br />\n";

include "interface/footer.php";
?>
