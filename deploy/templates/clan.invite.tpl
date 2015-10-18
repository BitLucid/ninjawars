<!-- Other outcomes are handled by the action message, this form will be displayed success or failure -->
<form id='clan_invite' action='clan.php' name='clan_rename'>
  <div>
    <input id='command' type='hidden' value='invite' name='command'>
    <input type='submit' class='formButton' value='Invite'><br>
    <input id='person_invited' type='text' name='person_invited' class='textField' placeholder="Name of ninja" required>
  </div>
</form>
