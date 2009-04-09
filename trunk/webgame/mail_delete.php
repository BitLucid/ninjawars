<?php
$page_title = "Deleting Mail";
$quickstat  = false;
$private    = true;
$alive      = false;

include "interface/header.php";
?>
  
<span style="font-weight: bold;color: #ccb094;">Deleting Mail</span>

<br /><br />

<?php
$id = (isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : ''));

if ($id != "")
{
  echo "Message Deleted\n";
  
  $sql->Delete("DELETE FROM mail WHERE id = '$id' AND send_to='".$_SESSION['username']."'");
  $affected_rows = $sql->a_rows;
}

if (isset($_POST["DeleteAll"]) && $_POST["DeleteAll"] === "Delete All")
{
  $sql->Delete("DELETE FROM mail WHERE send_to='".$_SESSION['username']."'");
  $affected_rows = $sql->a_rows;
  
  echo "All your messages have been deleted.\n";

}

if (isset($_POST["DeleteSelected"]) && $_POST["DeleteSelected"] === "Delete Selected")
{
  if (isset($_POST["mailID"]) && sizeof($_POST["mailID"]) > 0)
    {
      $query = "DELETE FROM mail WHERE send_to='".$_SESSION['username']."' AND id IN (";

      $commaBit = false;
      foreach ($_POST["mailID"] AS $key => $value)
	{
	  if ($commaBit)
	    $query .= ",";
	  else
	    $commaBit = true;

	  $query .= " ".$value." ";
	}

      $query .= ")";

      $sql->Delete($query);
      $affected_rows = $sql->a_rows;

      echo "The selected messages have been deleted.\n";
    }
  else
    {
      echo "No messages were selected to be deleted.\n";
    }
}

echo "<a href=\"mail_read.php\" style=\"font-weight: bold;\">Return to Mail</a>\n";
echo "<br />Returning to Inbox...\n";
echo "<meta HTTP-EQUIV=Refresh CONTENT=\"1; URL=mail_read.php\" />";

include "interface/footer.php";
?>
