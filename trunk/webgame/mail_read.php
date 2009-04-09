<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Mail";
include "interface/header.php";
?>

<span class="brownHeading">Mail</span>

<br /><br />

<a href="mail.php">Send Mail</a>

<br /><br />

<?php
$mail_list_length = (isset($_GET['mail_list_length']) ? $_GET['mail_list_length'] : 20);

$sql->Query("SELECT id, send_to, send_from, message FROM mail WHERE send_to = '$username' ORDER BY id DESC LIMIT $mail_list_length");

echo "<span style=\"font-weight: bold\">Inbox</span>\n";
echo "<form id=\"mail_delete\" action=\"mail_delete.php\" method=\"POST\" name=\"mail_delete\">\n";
echo "Viewing Messages: 1-".$sql->rows."<br />\n";
echo "<input id=\"DeleteSelected\" type=\"submit\" value=\"Delete Selected\" name=\"DeleteSelected\" class=\"formButton\" />\n";
echo "<input id=\"DeleteAll\" type=\"submit\" value=\"Delete All\" name=\"DeleteAll\" class=\"formButton\" />\n";
echo "<hr />\n";


if ($sql->rows == 0)
{
  echo "You have no messages.\n";
}
else
{
  echo "(Click on a Ninja's name to Send a Reply Msg.)<br />\n";
  echo "<table style=\"border:1 solid #000000;\">\n";
  echo "<tr>\n";
  echo "  <th>\n";
  echo "  Delete\n";
  echo "  </th>\n";
  
  echo "  <th>\n";
  echo "  From\n";
  echo "  </th>\n";
  
  echo "  <th>\n";
  echo "  Message\n";
  echo "  </th>\n";
  echo "</tr>\n";
  $i = 0; 
  foreach($sql->FetchAll() AS $loopMessage)
  {
      $id      = $loopMessage['id'];
      $from    = $loopMessage['send_from'];
      //$to      = $loopMessage['send_to']; // *** Unneeded 'cause redundant.
      $message = $loopMessage['message'];
      
      echo "<tr>\n";
      echo "  <td valign=\"top\" style=\"text-align: center;\">\n";
      echo "  <input type=\"checkbox\" name=\"mailID[".$i++."]\" value=\"$id\" />\n";
      echo "  </td>\n";
      
      echo "  <td valign=\"top\">\n";
      echo "  <a href=\"player.php?player=$from\">$from</a>\n";
      echo "  </td>\n";
	  
      echo "  <td>\n";
      echo    $message."\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
  
  echo "</table>\n";
}

echo "</form>\n";

if ($mail_list_length < 200 && $sql->rows != 0 && $i == 20)
{
	echo "<a href=\"mail_read.php?mail_list_length=200\">View Older Mail</a><br />\n";
}

include "interface/footer.php";
?>

