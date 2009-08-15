<?php
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Doshin Office";

include SERVER_ROOT."interface/header.php";

$location    = "Doshin Office";

$description = "You walk up to the Doshin Office to find the door locked. ".
               "The Doshin are busy protecting the borders of the village from thieves.\n".
               "<br><br>\n".
               "Nailed to the door is an official roster of wanted criminals and the bounties offered for their heads.\n".
               "A few men that do seem to be associated with the doshin doze near the entrance.  Every so often someone approaches and slips them something that clinks and jingles.";

$target  = in('target');
$command = in('command');
$amount  = intval(in('amount'));
$bribe   = intval(in('bribe'));

if ($command == "Offer Bounty")
{
	$target_bounty = getBounty($target);

	if ($target_bounty < 5000)
	{
		if ($amount > 0)
		{
			if (($target_bounty + $amount) > 5000)
			{
				$amount = (5000 - $target_bounty);

				echo "The doshin will only accept $amount gold towards $target's bounty.<br>\n";
			}

			if (getGold($username) >= $amount)
			{
				addBounty($target, $amount);
				subtractGold($username, $amount);
				sendMessage($username, $target, "$username has offered $amount gold in reward for your head!");

				echo "You have offered $amount towards bringing $target to justice.<br>\n";
				$quickstat = "player";
			}
			else
			{
				echo "You do not have that much gold.<br>\n";
			}
		}
		else
		{
			echo "You did not offer a valid amount of gold.<br>\n";
		}
	}
	else
	{
		echo "The bounty on $target may go no higher.<br>\n";
	}
}
else if ($command == "Bribe")
{
	switch(true)
	{
		case ($bribe <= getGold($username) && $bribe > 0):
			subtractGold($username,$bribe);
			subtractBounty($username,($bribe/2));

			$location    = "Behind the Doshin Office";
			$description = "\"We'll see what we can do,\" one of the Doshin tells you as you hand off your gold. He then directs you out through a back alley.\n".
                   "<br><br>\n".
                   "You find yourself in a dark alley. A rat scurries by. To your left lies the main street of the village.\n";
			$quickstat = "player";
			break;
		case $bribe < 0:  // A negative bribe was put in, which on the 21st of March, 2007, was a road to instant wealth, as a bribe of -456345 would increase both your bounty and your gold by 456345, so this will flag players as bugabusers until it becomes a standard-use thing.
			if (getGold($username) > 1000) //  *** If they have more than 1000 gold, their bounty will be mostly removed by this event.
			{
				$bountyGoesToNearlyZero = (getBounty($username) * .7);
				subtractBounty($username, $bountyGoesToNearlyZero);
			}

			subtractGold($username, floor(getGold($username) *.8));  //Takes away 80% of the players gold.

			$location    = "The Rat-infested Alley behind the Doshin Office";
			$description = "\"Trying to steal from the Doshin, eh!\" one of the men growls.<br>Where before there were only relaxing men idly ignoring their duties there are now unsheathed katanas and glaring eyes.<br>A group of the Doshin advance on you before you can escape and proceed to rough you up with fists and the hilts of their katana.  Finally, they take most of your gold and toss you into the alley behind the building.\n".
                   "<br><br>\n".
                   "Bruised and battered, you find yourself in a dark alley. A rat scurries by. To your left lies the main street of the village.\n";
			$quickstat = "player";
		break;
		default:
			echo "The Doshin ignore your ill-funded attempt to bribe them.\n";
		break;
	}
}

echo "<div class=\"brownTitle\">$location</div>\n";

echo "<div class=\"description\">\n";
echo $description;
echo "</div>\n";

echo "<p>\n";

if (getBounty($username) > 0)
{
	echo "<form id=\"bribe_form\" action=\"doshin_office.php\" method=\"post\" name=\"bribe_form\">\n";
	echo "<div>\n";
	echo "<input id=\"bribe\"type=\"text\" size=\"4\" maxlength=\"6\" name=\"bribe\" class=\"textField\">\n";
	echo "<input id=\"command\" type=\"submit\" value=\"Bribe\" name=\"command\" class=\"formButton\">\n";
	echo "</div>\n";
	echo "</form>\n";
}

$row = $sql->Query("SELECT uname,bounty,class,level,clan,clan_long_name FROM players WHERE bounty > 0 AND confirmed = 1 and health > 0 ORDER BY bounty DESC");

$row = $sql->data;

if ($sql->rows)
{
	echo "Click on a Name to view a Ninja's profile. (You can place a bounty on them from their profile)<br><br>\n";

	echo "Total Wanted Ninja: ".$sql->rows."\n";

	echo "<hr>\n";

	echo "<table cellpadding=\"2\" cellspacing=\"1\" class=\"playerTable\">\n";
	echo "<tr>\n";
	echo "  <th class=\"playerTable\">\n";
	echo "  Name\n";
	echo "  </th>\n";

	echo "  <th class=\"playerTable\">\n";
	echo "  Bounty\n";
	echo "  </th>\n";

	echo "  <th class=\"playerTable\">\n";
	echo "  Level\n";
	echo "  </th>\n";

	echo "  <th class=\"playerTable\">\n";
	echo "  Class\n";
	echo "  </th>\n";

	echo "  <th class=\"playerTable\">\n";
	echo "  Clan\n";
	echo "  </th>\n";
	echo "</tr>\n";

	for ($i = 0; $i < $sql->rows; $i++)
	{
		$sql->Fetch($i);
		$name        = $sql->data[0]; // username
		$bounty      = $sql->data[1]; // bounty
		$class       = $sql->data[2]; // class
		$level       = $sql->data[3]; // level
		$clan        = $sql->data[4]; // clan
		$clan_l_name = $sql->data[5]; // clan long name

		$class       = ($class == "" ? "(none)" : $class);
		$clan_link   = ($clan == "" ? "-" : "<a href=\"clan.php?command=view&amp;clan_name=$clan\">".$clan);
		$clan_l_name = ($clan_l_name == "" ? $clan_link : "<a href=\"clan.php?command=view&amp;clan_name=$clan\">".$clan_l_name);

		echo "<tr>\n";
		echo "  <td class=\"playerTable\">\n";
		echo "  <a href=\"player.php?player=$name\">$name</a>\n";
		echo "  </td>\n";

		echo "  <td class=\"playerTable\">\n";
		echo    $bounty."\n";
		echo "  </td>\n";

		echo "  <td class=\"playerTable\">\n";
		echo    $level."\n";
		echo "  </td>\n";

		echo "  <td class=\"playerTable\">\n";
		echo    $class."\n";
		echo "  </td>\n";

		echo "  <td class=\"playerTable\">\n";
		echo    $clan_l_name."\n";
		echo "  </td>\n";
		echo "</tr>\n";
	}

	echo "</table>\n";
}
else
{
	echo "The Doshin do not currently have any open bounties. Your village is safe.<br>\n";
}

include SERVER_ROOT."interface/footer.php";
?>
