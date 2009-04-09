function leave_clan()
{
  var leave = confirm("Do you really want to exit the clan?");
  if (leave == true) {
    window.location = "clan.php?command=leave";
  }
  return false;
}
