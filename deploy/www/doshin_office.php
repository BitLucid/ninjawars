<?php
$alive      = false;
$private    = false;
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
$username = get_username();

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
				echo "<div>You do not have that much gold.</div>\n";
			}
		}
		else
		{
			echo "<div>You did not offer a valid amount of gold.</div>\n";
		}
	}
	else
	{
		echo "<div>The bounty on $target may go no higher.</div>\n";
	}
}
else if ($command == "Bribe")
{
	if ($bribe <= getGold($username) && $bribe > 0)
	{
		subtractGold($username,$bribe);
		subtractBounty($username,($bribe/2));

		$location    = "Behind the Doshin Office";
		$description = "\"We'll see what we can do,\" one of the Doshin tells you as you hand off your gold. He then directs you out through a back alley.\n".
                   "<br><br>\n".
                   "You find yourself in a dark alley. A rat scurries by. To your left lies the main street of the village.\n";
		$quickstat = "player";
	}
	else if ($bribe < 0)  // A negative bribe was put in, which on the 21st of March, 2007, was a road to instant wealth, as a bribe of -456345 would increase both your bounty and your gold by 456345, so this will flag players as bugabusers until it becomes a standard-use thing.
	{
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
	}
	else
	{
		echo "The Doshin ignore your ill-funded attempt to bribe them.\n";
	}
}

echo "<h1>$location</h1>\n";

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

$row = $sql->Query("SELECT uname,bounty,class,level,clan_id,clan_name FROM players LEFT JOIN clan_player ON player_id = _player_id LEFT JOIN clan ON clan_id = _clan_id WHERE bounty > 0 AND confirmed = 1 and health > 0 ORDER BY bounty DESC");

$row = $sql->data;

if ($sql->rows)
{
	echo "Click on a Name to view a Ninja's profile. (You can place a bounty on them from their profile)<br><br>\n";

	echo "Total Wanted Ninja: ".$sql->rows."\n";

	echo "<hr>\n";

	echo "<table class=\"playerTable\">\n";
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
		$clan        = $sql->data[4]; // clanID
		$clan_l_name = $sql->data[5]; // clan name

		$class       = ($class == "" ? "(none)" : $class);
		$clan_l_name = ($clan_l_name == "" ? '' : "<a href=\"clan.php?command=view&amp;clan_long_name=".$clan_l_name."\">".$clan_l_name."</a>");

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
	echo "<p>The Doshin do not currently have any open bounties. Your village is safe.</p>\n";
}

include SERVER_ROOT."interface/footer.php";
?>
